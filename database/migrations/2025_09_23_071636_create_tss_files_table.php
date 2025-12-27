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
        Schema::create('tss_files', function (Blueprint $table) {
            $table->id();
            $table->text('text')->nullable();          // original text
            $table->string('lang', 5)->nullable();     // language code
            $table->string('file_path');   // stored mp3 file path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tss_files');
    }
};
