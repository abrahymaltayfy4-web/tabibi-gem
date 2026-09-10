<?php

namespace Tests\Feature;

use App\Models\Specialty;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\SpecialtySeeder::class);
    }

    public function test_patient_can_register_successfully(): void
    {
        $response = $this->postJson('/api/v1/auth/register/patient', [
            'full_name' => 'محمد عبدالله',
            'phone' => '+967770001122',
            'email' => 'patient@tabibi.ye',
            'password' => 'password123',
            'gender' => 'Male',
            'blood_group' => 'O+',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'uuid', 'full_name', 'phone', 'email', 'roles'],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', ['phone' => '+967770001122']);
        $this->assertDatabaseHas('patient_profiles', ['first_name' => 'محمد']);
    }

    public function test_doctor_can_register_successfully(): void
    {
        $specialty = Specialty::first();

        $response = $this->postJson('/api/v1/auth/register/doctor', [
            'full_name' => 'د. خالد العمري',
            'phone' => '+967779998877',
            'email' => 'doctor@tabibi.ye',
            'password' => 'password123',
            'primary_specialty_id' => $specialty->id,
            'license_number' => 'YEM-88493',
            'consultation_price' => 6000.00,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', ['phone' => '+967779998877']);
        $this->assertDatabaseHas('doctor_profiles', ['license_number' => 'YEM-88493']);
    }

    public function test_user_can_login_and_access_me_endpoint(): void
    {
        // Register patient first
        $this->postJson('/api/v1/auth/register/patient', [
            'full_name' => 'علي صالح',
            'phone' => '+967775554433',
            'password' => 'password123',
        ]);

        // Login
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'phone_or_email' => '+967775554433',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('data.token');

        // Access Me
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('data.full_name', 'علي صالح');

        // Logout
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200);
    }
}
