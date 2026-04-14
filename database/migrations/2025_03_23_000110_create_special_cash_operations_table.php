<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Depósito/cambio (pago QR mayor + entrega de vuelto en efectivo) y cambio de billetes.
     */
    public function up(): void
    {
        Schema::create('special_cash_operations', function (Blueprint $table) {
            $table->id();
            $table->string('operation_type', 32); // deposit_change | bill_break
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('performed_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('qr_amount', 14, 2)->default(0);
            $table->decimal('cash_in', 14, 2)->default(0);
            $table->decimal('cash_out', 14, 2)->default(0);
            $table->text('description')->nullable();
            $table->json('bill_breakdown')->nullable();
            $table->timestamps();

            $table->index(['operation_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_cash_operations');
    }
};
