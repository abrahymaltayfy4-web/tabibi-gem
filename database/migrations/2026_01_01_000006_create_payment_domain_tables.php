<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('appointment_id')->unique()->constrained('appointments')->onDelete('restrict');
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('YER');
            $table->string('payment_status')->default('Pending'); // Enum PaymentStatus
            $table->string('payment_gateway')->default('Sandbox_YER');
            $table->string('transaction_reference')->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['payment_status', 'transaction_reference']);
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->string('gateway_name');
            $table->json('request_payload_json')->nullable();
            $table->json('response_payload_json')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->text('reason')->nullable();
            $table->string('status')->default('Requested'); // Requested, UnderReview, Approved, Rejected, Completed
            $table->foreignId('approved_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('payment_id')->unique()->constrained('payments')->onDelete('restrict');
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->decimal('total_amount', 12, 2);
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });

        Schema::create('doctor_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->unique()->constrained('appointments')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('platform_fee_amount', 12, 2);
            $table->decimal('net_doctor_amount', 12, 2);
            $table->string('status')->default('Pending'); // Pending, Available, PaidOut
            $table->timestamp('payout_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_earnings');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payments');
    }
};
