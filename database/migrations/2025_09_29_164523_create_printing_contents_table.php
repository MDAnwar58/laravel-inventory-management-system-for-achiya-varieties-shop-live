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
        Schema::create('printing_contents', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 15);
            $table->string('phone_number2', 15)->nullable();
            $table->string('location');
            $table->text('short_desc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printing_contents');
    }
};
