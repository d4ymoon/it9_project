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
            $table->date('hire_date');
            $table->string('bank_acct'); 
            $table->enum('status', ['active', 'inactive'])->default('active');
        
            // Replace enum with foreign key
            $table->unsignedBigInteger('shift_id'); // <- Add this line
        
            // Foreign key constraints
            $table->foreign('position_id')
                ->references('id')->on('positions')
                ->onUpdate('cascade')->onDelete('restrict');
        
            $table->foreign('shift_id')
                ->references('id')->on('shifts')
                ->onUpdate('cascade')->onDelete('restrict'); // Optional: set to cascade or set null
        
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
