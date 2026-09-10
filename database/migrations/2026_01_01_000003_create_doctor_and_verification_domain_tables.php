<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('code')->unique();
            $table->string('icon_svg')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->foreignId('primary_specialty_id')->constrained('specialties')->onDelete('restrict');
            $table->string('license_number')->unique();
            $table->string('verification_status')->default('Draft');
            $table->decimal('consultation_price', 12, 2)->default(5000.00); // YER Currency
            $table->integer('consultation_duration_minutes')->default(30);
            $table->boolean('is_active_clinic')->default(true);
            $table->text('bio_ar')->nullable();
            $table->text('bio_en')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(5.00);
            $table->integer('rating_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['verification_status', 'is_active_clinic']);
        });

        Schema::create('doctor_specialties', function (Blueprint $table) {
            $table->foreignId('doctor_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->foreignId('specialty_id')->constrained('specialties')->onDelete('cascade');
            $table->primary(['doctor_id', 'specialty_id']);
        });

        Schema::create('doctor_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->string('degree_name');
            $table->string('university');
            $table->integer('graduation_year');
            $table->string('document_path')->nullable();
            $table->timestamps();
        });

        Schema::create('doctor_verification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->string('status')->default('Pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('doctor_verification_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verification_request_id')->constrained('doctor_verification_requests')->onDelete('cascade');
            $table->string('document_type'); // e.g. License, Passport, Degree
            $table->string('document_number')->nullable();
            $table->string('file_path');
            $table->string('file_checksum')->nullable();
            $table->timestamps();
        });

        Schema::create('doctor_verification_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verification_request_id')->constrained('doctor_verification_requests')->onDelete('cascade');
            $table->foreignId('actor_id')->constrained('users')->onDelete('cascade');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_verification_history');
        Schema::dropIfExists('doctor_verification_documents');
        Schema::dropIfExists('doctor_verification_requests');
        Schema::dropIfExists('doctor_qualifications');
        Schema::dropIfExists('doctor_specialties');
        Schema::dropIfExists('doctor_profiles');
        Schema::dropIfExists('specialties');
    }
};
