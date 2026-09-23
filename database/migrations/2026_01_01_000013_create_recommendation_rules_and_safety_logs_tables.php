<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Recommendation Rules Table (For rule-based specialty mapping and weights)
        Schema::create('recommendation_rules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('symptom_keyword');
            $table->string('specialty_code');
            $table->decimal('weight', 5, 2)->default(1.00);
            $table->boolean('is_active')->default(true);
            $table->integer('rule_version')->default(1);
            $table->timestamps();

            $table->index(['symptom_keyword', 'is_active']);
            $table->index(['specialty_code', 'is_active']);
        });

        // 2. Safety Event Logs Table (Emergency detection & Prompt Security events)
        Schema::create('safety_event_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('event_type'); // EMERGENCY_DETECTED, PROMPT_INJECTION_BLOCKED, AI_PROVIDER_FAILED
            $table->text('symptom_snippet')->nullable();
            $table->string('action_taken');
            $table->json('details_json')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['event_type', 'created_at']);
        });

        // 3. Recommendation Feedback Table (Patient feedback on recommended doctor)
        Schema::create('recommendation_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('recommendation_requests')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->boolean('is_accepted')->default(false);
            $table->foreignId('selected_doctor_id')->nullable()->constrained('doctor_profiles')->onDelete('set null');
            $table->tinyInteger('utility_score')->nullable(); // 1 to 5
            $table->text('feedback_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_feedbacks');
        Schema::dropIfExists('safety_event_logs');
        Schema::dropIfExists('recommendation_rules');
    }
};
