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
        Schema::create('footers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('footer_translations', function (Blueprint $table) { 
            $table->id(); 
            $table->foreignId('footer_id')->constrained()->cascadeOnDelete(); 
            $table->string('locale')->index(); // ru, en, zh 
            $table->string('title')->nullable(); 
            $table->longText('content')->nullable(); // основной контент 
            $table->text('extra_field_1')->nullable(); 
            $table->text('extra_field_2')->nullable(); 
            $table->text('extra_field_3')->nullable(); 
            $table->text('extra_field_4')->nullable(); 
            $table->timestamps(); 
            $table->unique(['footer_id', 'locale']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_translations');
        Schema::dropIfExists('footers');
    }
};
