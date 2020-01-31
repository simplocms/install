<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUniversalModuleItemAddEnabledAndUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('universal_module_items', function (Blueprint $table) {
            $table->boolean('enabled')->default(true);
            $table->string('url')->nullable()->default(null);
            $table->text('open_graph')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
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
        Schema::table('universal_module_items', function (Blueprint $table) {
            $table->dropColumn([
                'enabled', 'url', 'open_graph', 'seo_title', 'seo_description',
                'seo_index', 'seo_follow', 'seo_sitemap'
            ]);
        });
    }
}
