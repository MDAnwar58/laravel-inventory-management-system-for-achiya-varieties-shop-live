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
        Schema::create('sales_order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->decimal('price', 15, 2); // unit price
            $table->decimal('sub_total', 15, 2)->nullable(); // (price * qty) - discount
            $table->decimal('total', 15, 2); // subtotal + tax
            $table->decimal('qty', 11, 3);
            $table->integer('discount_percent')->nullable();
            $table->decimal('tax', 15, 2)->nullable();

            $table->foreign('sales_order_id')->references('id')->on('sales_orders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('product_id')->references('id')->on('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('user_id')->references('id')->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_products');
    }
};
