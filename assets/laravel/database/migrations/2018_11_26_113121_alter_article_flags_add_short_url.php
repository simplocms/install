<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterArticleFlagsAddShortUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_flags', function (Blueprint $table) {
            $table->boolean('should_bound_articles_to_category')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_flags', function (Blueprint $table) {
            $table->dropColumn(['should_bound_articles_to_category']);
        });
    }
}
