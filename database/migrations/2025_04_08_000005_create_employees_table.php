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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_number');
            $table->string('email')->unique();
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('shift_id'); 
            $table->date('hire_date');
            $table->string('bank_acct'); 
            $table->enum('status', ['active', 'inactive'])->default('active');
        
           
        
            $table->foreign('position_id')
                ->references('id')->on('positions')
                ->onUpdate('cascade')->onDelete('restrict');
        
            $table->foreign('shift_id')
                ->references('id')->on('shifts')
                ->onUpdate('cascade')->onDelete('restrict'); 
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
