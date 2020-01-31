<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleArticlesListConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_articleslist_configurations', function (Blueprint $table) {
            $table->increments('id');

            $table->string('view');
            $table->unsignedSmallInteger('sort_type');
            $table->unsignedInteger('limit');

            $table->timestamps();
        });

        Schema::create('module_articleslist_configurations_tags', function (Blueprint $table) {
            $table->unsignedInteger('configuration_id');
            $table->foreign('configuration_id', 'm_articleslist_c_tags_fk_configuration_id')
                ->references('id')->on('module_articleslist_configurations')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('tag_id');
            $table->foreign('tag_id', 'm_articleslist_c_tags_fk_tag_id')
                ->references('id')->on('tags')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['configuration_id', 'tag_id'], 'm_articleslist_c_tags_pk');
        });

        Schema::create('module_articleslist_configurations_categories', function (Blueprint $table) {
            $table->unsignedInteger('configuration_id');
            $table->foreign('configuration_id', 'm_articleslist_c_categories_fk_configuration_id')
                ->references('id')->on('module_articleslist_configurations')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('category_id');
            $table->foreign('category_id', 'm_articleslist_c_categories_fk_category_id')
                ->references('id')->on('categories')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(
                ['configuration_id', 'category_id'], 'module_articleslist_configurations_categories_pk'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_articleslist_configurations_categories');
        Schema::dropIfExists('module_articleslist_configurations_tags');
        Schema::dropIfExists('module_articleslist_configurations');
    }
}
