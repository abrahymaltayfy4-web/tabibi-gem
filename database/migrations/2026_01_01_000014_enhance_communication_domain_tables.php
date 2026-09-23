<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Consultation Events Table (Logging participant joins, leaves, status changes, connection issues)
        Schema::create('consultation_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
            $table->foreignId('actor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('event_name'); // ParticipantJoined, ParticipantLeft, CallStarted, CallEnded, ConnectionLost, Reconnected, CameraDisabled, MicrophoneDisabled
            $table->json('payload_json')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['consultation_id', 'created_at']);
        });

        // 2. Communication Abuse & Incident Reports Table
        Schema::create('communication_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('consultation_id')->nullable()->constrained('consultations')->onDelete('cascade');
            $table->foreignId('message_id')->nullable()->constrained('messages')->onDelete('set null');
            $table->string('category'); // technical_issue, inappropriate_behavior, spam, quality_report
            $table->text('description');
            $table->string('status')->default('open'); // open, in_review, resolved
            $table->foreignId('resolved_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_reports');
        Schema::dropIfExists('consultation_events');
    }
};
