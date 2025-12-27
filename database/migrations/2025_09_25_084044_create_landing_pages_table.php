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
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title_part_1');
            $table->string('hero_title_part_2');
            $table->text('short_des');
            $table->string('features_title');
            $table->string('features_sub_title', 500);
            $table->string('support_hour');
            $table->string('contact_title');
            $table->string('contact_sub_title');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
