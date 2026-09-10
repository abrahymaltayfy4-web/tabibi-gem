<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['Male', 'Female'])->default('Male');
            $table->string('blood_group', 5)->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('patient_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->string('governorate');
            $table->string('city');
            $table->string('street')->nullable();
            $table->string('building')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('patient_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->string('contact_name');
            $table->string('relationship');
            $table->string('phone_number');
            $table->timestamps();
        });

        Schema::create('patient_medical_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->unique()->constrained('patient_profiles')->onDelete('cascade');
            $table->integer('height_cm')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->string('smoking_status')->nullable();
            $table->string('alcohol_status')->nullable();
            $table->text('medical_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('patient_allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_medical_profile_id')->constrained('patient_medical_profiles')->onDelete('cascade');
            $table->string('allergy_name');
            $table->enum('severity', ['Mild', 'Moderate', 'Severe'])->default('Mild');
            $table->text('reaction')->nullable();
            $table->timestamps();
        });

        Schema::create('patient_chronic_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_medical_profile_id')->constrained('patient_medical_profiles')->onDelete('cascade');
            $table->string('condition_name');
            $table->integer('diagnosed_year')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_chronic_conditions');
        Schema::dropIfExists('patient_allergies');
        Schema::dropIfExists('patient_medical_profiles');
        Schema::dropIfExists('patient_emergency_contacts');
        Schema::dropIfExists('patient_addresses');
        Schema::dropIfExists('patient_profiles');
    }
};
