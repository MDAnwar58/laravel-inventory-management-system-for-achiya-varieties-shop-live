<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_order_products', function (Blueprint $table) {
            $table->boolean('retail_price_status')->default(false);
            $table->enum('stock_w_type', ['none', 'kg', 'ft', 'yard', 'm'])->default('none');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_order_products', function (Blueprint $table) {
            $table->dropColumn(['retail_price_status', 'stock_w_type']);
        });
    }
};
