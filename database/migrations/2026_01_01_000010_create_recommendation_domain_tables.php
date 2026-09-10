<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->text('symptoms_description');
            $table->string('category_code')->nullable();
            $table->decimal('max_price_yer', 12, 2)->nullable();
            $table->string('preferred_gender')->nullable();
            $table->timestamps();
        });

        Schema::create('recommendation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('recommendation_requests')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->decimal('match_score', 5, 2);
            $table->json('match_reasons_json')->nullable();
            $table->integer('rank_position');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_results');
        Schema::dropIfExists('recommendation_requests');
    }
};
