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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('slug')->unique()->nullable();
            $table->string('type')->nullable(); // например: "home", "news", "faq" (для системных страниц)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index(); // ru, en, zh
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable(); // основной контент
            
            $table->text('extra_field_1')->nullable();
            $table->text('extra_field_2')->nullable();
            $table->text('extra_field_3')->nullable();

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            $table->timestamps();

            $table->unique(['page_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_translations');
        Schema::dropIfExists('pages');
    }
};
