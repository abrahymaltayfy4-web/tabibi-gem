<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
            $table->text('chief_complaint');
            $table->text('examination_notes')->nullable();
            $table->text('diagnosis_notes');
            $table->text('treatment_plan')->nullable();
            $table->boolean('is_finalized')->default(true);
            $table->timestamps();

            $table->index(['patient_id', 'created_at']);
            $table->index(['doctor_id', 'created_at']);
        });

        Schema::create('medical_record_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->integer('version_number')->default(1);
            $table->text('chief_complaint');
            $table->text('examination_notes')->nullable();
            $table->text('diagnosis_notes');
            $table->text('treatment_plan')->nullable();
            $table->string('change_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('code')->unique();
            $table->string('active_ingredient')->nullable();
            $table->string('form')->default('tablet'); // tablet, syrup, injection, ointment
            $table->string('strength')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'issued_at']);
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->string('medication_name');
            $table->foreignId('medication_id')->nullable()->constrained('medications')->onDelete('set null');
            $table->string('dosage'); // e.g. 500mg
            $table->string('frequency'); // e.g. 3 times daily
            $table->string('duration'); // e.g. 7 days
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medications');
        Schema::dropIfExists('medical_record_versions');
        Schema::dropIfExists('medical_records');
    }
};
