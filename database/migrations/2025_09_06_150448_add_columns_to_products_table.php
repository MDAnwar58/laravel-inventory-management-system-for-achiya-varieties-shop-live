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
            $table->string('stock_updated')->nullable();
            $table->timestamp('stock_updated_at')->nullable();
            $table->string('change_price')->nullable();
            $table->timestamp('change_price_updated_at')->nullable();
            $table->string('sold_units')->nullable();
            $table->timestamp('solded_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock_updated', 'stock_updated_at', 'change_price', 'change_price_updated_at', 'sold_units', 'solded_at']);
        });
    }
};
