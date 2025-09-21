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
         // bottom_block_translations
        Schema::table('bottom_block_translations', function (Blueprint $table) {
             $table->boolean('auto_translate')->default(false);
        });

        // footer_translations
        Schema::table('footer_translations', function (Blueprint $table) {
             $table->boolean('auto_translate')->default(false);
        });

        // menu_item_translations
        Schema::table('menu_item_translations', function (Blueprint $table) {
             $table->boolean('auto_translate')->default(false);
        });

        // page_translations
        Schema::table('page_translations', function (Blueprint $table) {
            $table->boolean('auto_translate')->default(false);
        });

        // article_translations
        Schema::table('article_translations', function (Blueprint $table) {
             $table->boolean('auto_translate')->default(false);
        });

        // category_translations
        Schema::table('category_translations', function (Blueprint $table) {
             $table->boolean('auto_translate')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bottom_block_translations', function (Blueprint $table) {
            $table->dropColumn('auto_translate');
        });

        Schema::table('footer_translations', function (Blueprint $table) {
            $table->dropColumn('auto_translate');
        });

        Schema::table('menu_item_translations', function (Blueprint $table) {
            $table->dropColumn('auto_translate');
        });

        Schema::table('page_translations', function (Blueprint $table) {
            $table->dropColumn('auto_translate');
        });

        Schema::table('article_translations', function (Blueprint $table) {
            $table->dropColumn('auto_translate');
        });

        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn('auto_translate');
        });
    }
};
