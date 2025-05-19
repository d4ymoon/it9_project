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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('loan_type');
            $table->decimal('loan_amount', 10, 2);
            $table->decimal('interest_rate', 5, 2); // % per month, e.g., 1.5 means 1.5%
            $table->decimal('deduction_percentage', 5, 2);
            $table->date('start_date');
            $table->decimal('remaining_balance', 10, 2);
            $table->enum('status', ['active', 'paid', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
