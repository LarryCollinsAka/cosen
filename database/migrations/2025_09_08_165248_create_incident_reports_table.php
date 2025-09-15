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
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->bigIncrements('id')->primary();
            $table->string('title');
            $table->text('description');
            // We will remove this column as the AI will determine the category
            // $table->string('type');
            $table->string('location')->nullable();
            $table->string('status')->default('pending'); // pending, in progress, resolved
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category')->nullable(); // New column for AI classification
            $table->string('severity')->nullable(); // New column for AI classification
            $table->string('image_path')->nullable(); // Add a column to store image path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
