<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSeoArticlesCategoriesFlagsPagesPhotogalleries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['seo_keywords']);
            $table->boolean('seo_index')->default(true);
            $table->boolean('seo_follow')->default(true);
            $table->boolean('seo_sitemap')->default(true);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['seo_keywords', 'listed']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['seo_keywords']);
            $table->boolean('seo_index')->default(true);
            $table->boolean('seo_follow')->default(true);
            $table->boolean('seo_sitemap')->default(true);
        });

        Schema::table('article_flags', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->default(null);
            $table->text('seo_description')->nullable()->default(null);
            $table->boolean('seo_index')->default(true);
            $table->boolean('seo_follow')->default(true);
            $table->boolean('seo_sitemap')->default(true);
        });

        Schema::table('photogalleries', function (Blueprint $table) {
            $table->dropColumn(['seo_keywords']);
            $table->boolean('seo_index')->default(true);
            $table->boolean('seo_follow')->default(true);
            $table->boolean('seo_sitemap')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('seo_keywords')->nullable()->default(null);
            $table->dropColumn(['seo_index', 'seo_follow', 'seo_sitemap']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->text('seo_keywords')->nullable()->default(null);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->text('seo_keywords')->nullable()->default(null);
            $table->dropColumn(['seo_index', 'seo_follow', 'seo_sitemap']);
        });

        Schema::table('article_flags', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap']);
        });

        Schema::table('photogalleries', function (Blueprint $table) {
            $table->text('seo_keywords')->nullable()->default(null);
            $table->dropColumn(['seo_index', 'seo_follow', 'seo_sitemap']);
        });
    }
}
