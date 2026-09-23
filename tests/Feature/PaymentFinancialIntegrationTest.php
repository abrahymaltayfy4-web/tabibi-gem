<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\DoctorEarning;
use App\Models\DoctorProfile;
use App\Models\Invoice;
use App\Models\PatientProfile;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Specialty;
use App\Models\User;
use App\Shared\Enums\AppointmentStatus;
use App\Shared\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentFinancialIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $patientUser;

    protected User $doctorUser;

    protected User $adminUser;

    protected PatientProfile $patient;

    protected DoctorProfile $doctor;

    protected AppointmentType $appointmentType;

    protected Appointment $appointment;

    protected function setUp(): void
    {
        parent::setUp();

        $specialty = Specialty::create(['code' => 'cardiology', 'name_ar' => 'أمراض القلب', 'name_en' => 'Cardiology']);

        $this->doctorUser = User::factory()->create(['full_name' => 'د. خالد العمري', 'email' => 'doctor.khaled@tabibi.test']);
        $this->doctor = DoctorProfile::create([
            'user_id' => $this->doctorUser->id,
            'primary_specialty_id' => $specialty->id,
            'license_number' => 'LIC-YEM-112233',
            'verification_status' => 'Approved',
            'consultation_price' => 20000,
            'consultation_duration_minutes' => 30,
            'is_active_clinic' => true,
        ]);

        $this->patientUser = User::factory()->create(['full_name' => 'ناصر حسن', 'email' => 'patient.nasser@tabibi.test']);
        $this->patient = PatientProfile::create([
            'user_id' => $this->patientUser->id,
            'first_name' => 'ناصر',
            'last_name' => 'حسن',
            'gender' => 'Male',
            'date_of_birth' => '1990-01-01',
        ]);

        $this->adminUser = User::factory()->create(['full_name' => 'مدقق المنصة المالي', 'email' => 'auditor@tabibi.test']);

        $this->appointmentType = AppointmentType::create(['name_ar' => 'استشارة مرئية', 'name_en' => 'Video Consultation', 'code' => 'video']);

        $this->appointment = Appointment::create([
            'uuid' => (string) Str::uuid(),
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'appointment_type_id' => $this->appointmentType->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'start_time' => '14:00:00',
            'end_time' => '14:30:00',
            'price_snapshot' => 20000,
            'status' => 'AwaitingPayment',
        ]);
    }

    public function test_patient_can_initiate_checkout_and_complete_payment_with_invoice_and_15_percent_commission(): void
    {
        // Step 1: Initiate Checkout
        $checkoutResponse = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/payments/checkout/{$this->appointment->id}");

        $checkoutResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['payment_id', 'transaction_reference', 'checkout_url']]);

        $txRef = $checkoutResponse->json('data.transaction_reference');

        // Step 2: Gateway Callback Completion
        $callbackResponse = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/payments/sandbox-callback/{$txRef}");

        $callbackResponse->assertStatus(200)
            ->assertJsonPath('data.status', PaymentStatus::SUCCEEDED->value)
            ->assertJsonPath('data.appointment_status', AppointmentStatus::CONFIRMED->value);

        // Step 3: Verify Invoice Generated
        $payment = Payment::where('transaction_reference', $txRef)->first();
        $this->assertNotNull($payment);

        $invoice = Invoice::where('payment_id', $payment->id)->first();
        $this->assertNotNull($invoice);
        $this->assertStringStartsWith('INV-YER-', $invoice->invoice_number);
        $this->assertEquals(20000.00, (float) $invoice->total_amount);

        // Step 4: Verify 15% Platform Commission Earning Calculation
        // Gross: 20,000 YER -> 15% Platform Fee = 3,000 YER -> Net Doctor Earning = 17,000 YER
        $earning = DoctorEarning::where('appointment_id', $this->appointment->id)->first();
        $this->assertNotNull($earning);
        $this->assertEquals(20000.00, (float) $earning->gross_amount);
        $this->assertEquals(3000.00, (float) $earning->platform_fee_amount);
        $this->assertEquals(17000.00, (float) $earning->net_doctor_amount);
        $this->assertEquals('Pending', $earning->status);
    }

    public function test_doctor_can_view_earnings_and_request_payout_and_admin_approves(): void
    {
        // Setup available earning for doctor
        DoctorEarning::create([
            'appointment_id' => $this->appointment->id,
            'doctor_id' => $this->doctor->id,
            'gross_amount' => 20000,
            'platform_fee_amount' => 3000,
            'net_doctor_amount' => 17000,
            'status' => 'Available',
        ]);

        // Doctor views earnings ledger
        $earningsResponse = $this->actingAs($this->doctorUser, 'sanctum')
            ->getJson('/api/v1/doctor/earnings');

        $earningsResponse->assertStatus(200)
            ->assertJsonPath('data.available_balance_yer', 17000)
            ->assertJsonPath('data.platform_fee_total_yer', 3000);

        // Doctor requests payout of 15,000 YER
        $payoutRequest = $this->actingAs($this->doctorUser, 'sanctum')
            ->postJson('/api/v1/doctor/payouts/request', [
                'amount' => 15000,
            ]);

        $payoutRequest->assertStatus(201)
            ->assertJsonPath('data.status', 'Requested');

        $payoutId = $payoutRequest->json('data.id');

        // Admin Financial Auditor approves payout
        $approveResponse = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson("/api/v1/admin/payouts/{$payoutId}/approve", [
                'transfer_reference' => 'PAY-KUR-99887766',
                'notes' => 'تم التحويل عبر صرافة الكريمي بنجاح.',
            ]);

        $approveResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'Completed')
            ->assertJsonPath('data.transfer_reference', 'PAY-KUR-99887766');

        $this->assertDatabaseHas('doctor_earnings', [
            'doctor_id' => $this->doctor->id,
            'status' => 'PaidOut',
        ]);
    }

    public function test_manual_payment_proof_upload_and_admin_verification(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('bank_receipt.pdf', 300, 'application/pdf');

        $manualResponse = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/payments/manual-proof/{$this->appointment->id}", [
                'file' => $file,
                'reference_number' => 'RECEIPT-KUR-445566',
            ]);

        $manualResponse->assertStatus(201)
            ->assertJsonPath('data.status', PaymentStatus::PROCESSING->value);

        $paymentId = $manualResponse->json('data.payment_id');

        // Admin approves manual proof
        $adminVerifyResponse = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson("/api/v1/admin/payments/{$paymentId}/verify-manual", [
                'status' => 'Approved',
            ]);

        $adminVerifyResponse->assertStatus(200)
            ->assertJsonPath('data.payment_status', PaymentStatus::SUCCEEDED->value);
    }

    public function test_admin_can_process_refund_and_cancel_appointment(): void
    {
        $payment = Payment::create([
            'uuid' => (string) Str::uuid(),
            'appointment_id' => $this->appointment->id,
            'patient_id' => $this->patient->id,
            'amount' => 20000,
            'currency' => 'YER',
            'payment_status' => PaymentStatus::SUCCEEDED->value,
            'payment_gateway' => 'Sandboxed_YER',
            'transaction_reference' => 'TX-REFUND-TEST-100',
        ]);

        $refundResponse = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson("/api/v1/admin/payments/{$payment->id}/refund", [
                'reason' => 'إلغاء الموعد بناءً على طلب المريض قبل 24 ساعة.',
            ]);

        $refundResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'Completed')
            ->assertJsonPath('data.amount', '20000.00');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_status' => PaymentStatus::REFUNDED->value,
        ]);
    }
}
