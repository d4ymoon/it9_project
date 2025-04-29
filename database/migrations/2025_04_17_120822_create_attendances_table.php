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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');  // The day the shift started

            $table->dateTime('time_in')->nullable();      // Start of working time
            $table->dateTime('break_out')->nullable();    // Start of break (optional)
            $table->dateTime('break_in')->nullable();     // End of break (optional)
            $table->dateTime('time_out')->nullable();     // End of working time

            $table->string('status')->nullable();       
            $table->unique(['employee_id', 'date']);   // One record per day per employee
            $table->timestamps();
            
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
