<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id'); // patients.id is int unsigned
            $table->unsignedBigInteger('doctor_id'); // doctors.id is bigint unsigned
            $table->text('review');
            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->timestamps();

            // Foreign keys
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');

            // Indexes for better performance
            $table->index(['doctor_id', 'created_at']);
            $table->index(['patient_id', 'doctor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
