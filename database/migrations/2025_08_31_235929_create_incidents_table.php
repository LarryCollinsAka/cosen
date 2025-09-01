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
        Schema::create('incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->string('type', 50)->default('general');
            $table->string('status', 50)->default('reported');
            $table->double('latitude', 10, 6)->nullable();
            $table->double('longitude', 10, 6)->nullable();
            $table->string('photo_url')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained(); // Links to a users table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
