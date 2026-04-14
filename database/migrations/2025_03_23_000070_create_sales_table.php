<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_kind', 16); // credit | cash
            $table->timestamp('sold_at');
            $table->foreignId('sold_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 32)->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->string('credit_status', 24)->nullable(); // pending, partial, paid — solo por cobrar
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['sale_kind', 'sold_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
