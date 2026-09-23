<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance Patient Medical Profiles Table
        Schema::table('patient_medical_profiles', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->string('blood_type', 10)->nullable()->after('patient_id');
            $table->string('emergency_contact_name')->nullable()->after('medical_notes');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
        });

        // 2. Diagnoses Table (Structured clinical diagnosis with ICD/name)
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->string('diagnosis_name');
            $table->string('icd_code')->nullable();
            $table->string('diagnosis_type')->default('primary'); // primary, secondary, differential
            $table->text('clinical_notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'created_at']);
        });

        // 3. Treatment Plans Table (Recommendations, lifestyle guidance, follow-up dates)
        Schema::create('treatment_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('medical_record_id')->unique()->constrained('medical_records')->onDelete('cascade');
            $table->text('recommendations');
            $table->text('lifestyle_guidance')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });

        // 4. Medication Catalogs Table (Standard drug list)
        Schema::create('medication_catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name');
            $table->string('generic_name')->nullable();
            $table->string('form')->default('tablet');
            $table->string('strength')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Medical Documents Table (Files metadata with private disk path and signed URL abstraction)
        Schema::create('medical_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('uploaded_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('medical_record_id')->nullable()->constrained('medical_records')->onDelete('set null');
            $table->string('document_type')->default('general_report'); // lab_test, xray, mri, ct, prescription_scan
            $table->string('original_filename');
            $table->string('storage_disk')->default('private');
            $table->string('storage_path');
            $table->string('mime_type');
            $table->bigInteger('file_size_bytes');
            $table->string('checksum_sha256', 64)->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_documents');
        Schema::dropIfExists('medication_catalogs');
        Schema::dropIfExists('treatment_plans');
        Schema::dropIfExists('diagnoses');

        Schema::table('patient_medical_profiles', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'blood_type', 'emergency_contact_name', 'emergency_contact_phone']);
        });
    }
};
