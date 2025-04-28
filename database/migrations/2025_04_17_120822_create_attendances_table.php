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
            $table->date('date');                   
            $table->time('morning_time_in')->nullable();      
            $table->time('morning_time_out')->nullable();   
            $table->time('afternoon_time_in')->nullable();
            $table->datetime('afternoon_time_out')->nullable();  
            $table->enum('status', ['Present', 'Absent', 'Leave'])->nullable()->default(null);
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
