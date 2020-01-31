<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AlterArticlesAddOgTags - add columns for opengraph tags to articles table.
 * @author    Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
class AlterArticlesAddOgTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'articles',
            function (Blueprint $table) {
                $table->string('og_title')->nullable()->default(null);
                $table->string('og_type')->nullable()->default(null);
                $table->string('og_image')->nullable()->default(null);
                $table->string('og_url')->nullable()->default(null);
                $table->string('og_description')->nullable()->default(null);
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'articles',
            function (Blueprint $table) {
                $table->dropColumn(
                    [
                        'og_title', 'og_type', 'og_image', 'og_url', 'og_description'
                    ]
                );
            }
        );
    }
}
