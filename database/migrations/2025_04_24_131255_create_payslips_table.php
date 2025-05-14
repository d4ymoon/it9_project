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
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('pay_period'); // e.g. '2025-04-01_to_2025-04-15'
            $table->string('period_type'); // monthly or semi-monthly
            $table->integer('hours_worked')->default(0);
            $table->decimal('basic_pay', 10, 2)->default(0);
            $table->decimal('overtime_pay', 10, 2)->default(0);
            $table->integer('overtime_hours')->default(0);
            $table->decimal('loan_deductions', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
}; 