<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_availability_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['doctor_id', 'day_of_week', 'is_active']);
        });

        Schema::create('doctor_availability_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->date('exception_date');
            $table->boolean('is_day_off')->default(true);
            $table->time('override_start_time')->nullable();
            $table->time('override_end_time')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'exception_date']);
        });

        Schema::create('doctor_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('availability_rule_id')->constrained('doctor_availability_rules')->onDelete('cascade');
            $table->time('break_start_time');
            $table->time('break_end_time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_breaks');
        Schema::dropIfExists('doctor_availability_exceptions');
        Schema::dropIfExists('doctor_availability_rules');
    }
};
