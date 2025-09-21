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
        Schema::create('bottom_blocks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        
        Schema::create('bottom_block_translations', function (Blueprint $table) { 
            $table->id(); 
            $table->foreignId('bottom_block_id')->constrained()->cascadeOnDelete(); 
            $table->string('locale')->index(); // ru, en, zh 
            $table->string('title')->nullable(); 
            $table->longText('content')->nullable(); // основной контент 
            $table->text('extra_field_1')->nullable(); 
            $table->text('extra_field_2')->nullable(); 
            $table->text('extra_field_3')->nullable(); 
            $table->text('extra_field_4')->nullable(); 
            
            // Картинка 
            $table->string('image')->nullable(); 
            
            // JSON-поля 
            $table->json('videos')->nullable(); // [{ "platform": "youtube", "url": "..." }] 
            $table->json('buttons')->nullable(); // [{ "title": "Купить", "url": "..." }] 

            $table->unique(['bottom_block_id', 'locale']);
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bottom_block_translations');
        Schema::dropIfExists('bottom_blocks');
    }
};
