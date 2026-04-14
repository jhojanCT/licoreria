<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cierre diario caja chica. Campos admin_* solo visibles/ajustables con permiso administrador.
     */
    public function up(): void
    {
        Schema::create('daily_cash_closures', function (Blueprint $table) {
            $table->id();
            $table->date('business_date');
            $table->foreignId('closed_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('expected_cash', 14, 2)->default(0);
            $table->decimal('counted_cash', 14, 2)->default(0);
            $table->decimal('difference_cash', 14, 2)->default(0);
            $table->decimal('total_qr_day', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('admin_reviewed_at')->nullable();
            $table->foreignId('admin_reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('admin_adjustment', 14, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->unique('business_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_cash_closures');
    }
};
