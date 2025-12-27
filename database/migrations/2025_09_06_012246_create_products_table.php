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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // foreign keys
            $table->unsignedBigInteger('item_type_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();

            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->string('name');
            $table->string('slug');

            $table->decimal('price', 30, 2);
            $table->decimal('discount_price', 30, 2)->nullable();
            $table->decimal('cost_price', 30, 2);

            $table->integer('stock')->default(0);
            $table->integer('low_stock_level')->default(5);

            $table->integer('purchase_limit')->default(1);
            $table->longText('desc')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'deactive'])->default('active');

            // seo
            $table->string('meta_title')->nullable();
            $table->text('meta_desc')->nullable();
            $table->string('meta_keywords')->nullable();

            // foreign key with relations
            $table->foreign('item_type_id')->references('id')->on('item_types')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('brand_id')->references('id')->on('brands')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('category_id')->references('id')->on('categories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')
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
        Schema::dropIfExists('products');
    }
};
