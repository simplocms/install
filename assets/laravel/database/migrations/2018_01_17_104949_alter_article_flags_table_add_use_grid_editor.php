<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterArticleFlagsTableAddUseGridEditor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_flags', function (Blueprint $table) {
            $table->boolean('use_tags')->default(false);
            $table->boolean('use_grid_editor')->default(false);
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
            $table->dropColumn('use_tags');
            $table->dropColumn('use_grid_editor');
        });
    }
}
