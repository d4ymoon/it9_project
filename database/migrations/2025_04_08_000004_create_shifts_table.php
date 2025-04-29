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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time');           // When shift starts (e.g., 08:00)
            $table->time('break_start_time')->nullable(); // Optional break start (e.g., 12:00)
            $table->time('break_end_time')->nullable();   // Optional break end (e.g., 13:00)
            $table->time('end_time');             // When shift ends (e.g., 17:00 or 06:00 for next day)
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
