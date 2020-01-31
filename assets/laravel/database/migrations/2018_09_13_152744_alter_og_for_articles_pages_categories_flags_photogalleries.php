<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOgForArticlesPagesCategoriesFlagsPhotogalleries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (['pages', 'articles', 'categories', 'article_flags', 'photogalleries'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->text('open_graph')->nullable();
            });
        }

        // Migrate pages
        \App\Models\Page\Page::withTestingCounterparts()->get()->each([$this, 'migrateModel']);
        // Migrate articles
        \App\Models\Article\Article::all()->each([$this, 'migrateModel']);

        foreach (['pages', 'articles'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropMedia('og_image_id');
                $table->dropColumn(['og_title', 'og_type', 'og_url', 'og_description']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (['pages', 'articles'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('og_title')->nullable();
                $table->string('og_type')->nullable();
                $table->media('og_image_id');
                $table->string('og_url')->nullable();
                $table->string('og_description')->nullable();
            });
        }

        // Rollback pages
        \App\Models\Page\Page::withTestingCounterparts()->get()->each([$this, 'rollbackModel']);
        // Rollback articles
        \App\Models\Article\Article::all()->each([$this, 'rollbackModel']);

        foreach (['pages', 'articles', 'categories', 'article_flags', 'photogalleries'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['open_graph']);
            });
        }
    }


    /**
     * Migrate model OG tags.
     *
     * @param \Illuminate\Database\Eloquent\Model|\App\Traits\AdvancedEloquentTrait $model
     */
    public function migrateModel(\Illuminate\Database\Eloquent\Model $model)
    {
        if ($model->og_title || $model->og_type || $model->og_url || $model->og_description || $model->og_image_id) {
            $model->update([
                'open_graph' => [
                    'title' => $model->og_title,
                    'type' => $model->og_type ??
                        ($model instanceof \App\Models\Article\Article ? 'article' : 'website'),
                    'url' => $model->og_url,
                    'description' => $model->og_description,
                    'image_id' => $model->og_image_id
                ]
            ]);
        }
    }


    /**
     * Rollback model OG tags.
     *
     * @param \Illuminate\Database\Eloquent\Model|\App\Traits\OpenGraphTrait $model
     */
    public function rollbackModel(\Illuminate\Database\Eloquent\Model $model)
    {
        $model->update([
            'og_title' => $model->open_graph->get('title'),
            'og_type' => $model->open_graph->get('type'),
            'og_url' => $model->open_graph->get('url'),
            'og_description' => $model->open_graph->get('description'),
            'og_image_id' => $model->open_graph->get('image_id')
        ]);
    }
}
