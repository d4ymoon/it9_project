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
        // Drop old tables in the correct order (considering foreign key constraints)
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('loan_types');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't recreate these tables as they're being replaced or removed
    }
}; 