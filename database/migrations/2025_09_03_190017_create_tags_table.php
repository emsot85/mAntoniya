<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index(); // 'ru', 'en', 'zh'
            $table->string('title');
            $table->timestamps();

            $table->unique(['tag_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_translations');
        Schema::dropIfExists('tags');
    }
};
