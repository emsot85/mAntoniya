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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();

            // SEO / slug
            $table->string('slug')->unique()->nullable();

            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            // Картинка
            $table->string('image')->nullable();

            // Автор
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();

            // Количество просмотров и лайков
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);

            // JSON-поля
            $table->json('videos')->nullable();   // [{ "platform": "youtube", "url": "..." }]
            $table->json('buttons')->nullable();  // [{ "title": "Купить", "url": "..." }]

            // Публикация
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft'); // "черновик", "опубликовано", "заархивировано"
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);

            $table->timestamps();
        });

        Schema::create('article_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index(); // ru, en, zh

            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            // Контент
            $table->string('title')->nullable();
            $table->longText('description')->nullable();

            // Дополнительные поля
            $table->longText('extra_field_1')->nullable();
            $table->longText('extra_field_2')->nullable();
            $table->longText('extra_field_3')->nullable();

            $table->timestamps();

            $table->unique(['article_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_translations');
        Schema::dropIfExists('articles');
    }
};
