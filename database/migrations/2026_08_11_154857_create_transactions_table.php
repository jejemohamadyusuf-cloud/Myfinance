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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('transaction_number');
            $table->enum('type', ['cash_in', 'cash_out', 'bank_in', 'bank_out']);
            $table->date('date');
            $table->text('description')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('posted');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['tenant_id', 'transaction_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('transactions');
        Schema::enableForeignKeyConstraint();
    }
};
