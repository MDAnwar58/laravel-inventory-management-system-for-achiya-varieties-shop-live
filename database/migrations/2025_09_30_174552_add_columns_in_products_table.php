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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock_w', 11, 3)->nullable();
            $table->enum('stock_w_type', ['none', 'kg', 'ft', 'yard', 'm'])->default('none');
            $table->decimal('retail_price', 30, 2)->nullable();
            $table->decimal('retail_price_discount', 30, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock_w', 'stock_w_type', 'retail_price', 'retail_price_discount']);
        });
    }
};
