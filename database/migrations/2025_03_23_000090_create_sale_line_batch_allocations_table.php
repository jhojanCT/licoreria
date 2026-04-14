<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_line_batch_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_batch_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 14, 3);
            $table->timestamps();

            $table->unique(['sale_line_id', 'inventory_batch_id'], 'sale_line_batch_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_line_batch_allocations');
    }
};
