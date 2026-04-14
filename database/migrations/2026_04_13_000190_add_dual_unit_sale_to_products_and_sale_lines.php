<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedSmallInteger('units_per_pack')->nullable()->after('default_sale_price');
            $table->decimal('price_per_single_unit', 12, 2)->nullable()->after('units_per_pack');
        });

        Schema::table('sale_lines', function (Blueprint $table) {
            $table->string('sale_unit', 8)->nullable()->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['units_per_pack', 'price_per_single_unit']);
        });

        Schema::table('sale_lines', function (Blueprint $table) {
            $table->dropColumn('sale_unit');
        });
    }
};
