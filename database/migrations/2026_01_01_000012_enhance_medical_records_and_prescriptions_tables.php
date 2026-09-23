<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Patient Medical History Table (Longitudinal history: chronic conditions, allergies, surgeries)
        Schema::create('patient_medical_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->string('category'); // chronic_condition, allergy, past_surgery, family_history, past_medication
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('diagnosed_date')->nullable();
            $table->string('status')->default('active'); // active, resolved, inactive
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['patient_id', 'category']);
        });

        // 2. Structured Symptoms Table
        Schema::create('symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('severity')->default('moderate'); // mild, moderate, severe
            $table->string('duration')->nullable(); // e.g., '3 days'
            $table->string('onset')->nullable(); // e.g., 'sudden', 'gradual'
            $table->string('frequency')->nullable(); // e.g., 'constant', 'intermittent'
            $table->timestamps();
        });

        // 3. Enhance prescriptions table with status, number, amendment tracking
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('prescription_number')->nullable()->unique()->after('uuid');
            $table->string('status')->default('Draft')->after('prescription_number'); // Draft, Finalized, Cancelled, Amended
            $table->foreignId('parent_prescription_id')->nullable()->after('status')->constrained('prescriptions')->onDelete('set null');
            $table->text('amendment_reason')->nullable()->after('parent_prescription_id');
            $table->foreignId('finalized_by_user_id')->nullable()->after('amendment_reason')->constrained('users')->onDelete('set null');
            $table->timestamp('finalized_at')->nullable()->after('finalized_by_user_id');
        });

        // 4. Enhance prescription_items table with detailed medication attributes
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->string('generic_name')->nullable()->after('medication_name');
            $table->string('form')->nullable()->after('generic_name'); // tablet, syrup, injection, ointment, capsule
            $table->string('strength')->nullable()->after('form'); // e.g. 500mg, 10mg/ml
            $table->string('route')->nullable()->after('frequency'); // oral, topical, intravenous, inhalation
            $table->integer('quantity')->default(1)->after('duration');
            $table->text('notes')->nullable()->after('instructions');
        });

        // 5. Prescription Amendments Audit Table
        Schema::create('prescription_amendments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->foreignId('amended_prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->text('reason');
            $table->json('snapshot_json');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_amendments');

        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropColumn(['generic_name', 'form', 'strength', 'route', 'quantity', 'notes']);
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['parent_prescription_id']);
            $table->dropForeign(['finalized_by_user_id']);
            $table->dropColumn(['prescription_number', 'status', 'parent_prescription_id', 'amendment_reason', 'finalized_by_user_id', 'finalized_at']);
        });

        Schema::dropIfExists('symptoms');
        Schema::dropIfExists('patient_medical_histories');
    }
};
