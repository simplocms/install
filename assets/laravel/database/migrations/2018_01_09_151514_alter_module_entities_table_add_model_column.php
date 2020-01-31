<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterModuleEntitiesTableAddModelColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('module_entities', function (Blueprint $table) {
            $table->string('model')->nullable()->default(null);
            $table->unsignedInteger('model_id')->nullable()->default(null);
            $table->index(['model', 'model_id']);
        });

        /** @var \App\Models\Module\Entity $entity */
        foreach(App\Models\Module\Entity::all() as $entity) {
            if ($entity->page_content_id) {
                $entity->update([
                    'model' => App\Models\Page\Content::class,
                    'model_id' => $entity->page_content_id
                ]);
            } else if ($entity->widget_content_id) {
                $entity->update([
                    'model' => App\Models\Widget\Content::class,
                    'model_id' => $entity->widget_content_id
                ]);
            }
        }

        Schema::table('module_entities', function (Blueprint $table) {
            $table->dropForeign(['page_content_id']);
            $table->dropForeign(['widget_content_id']);
            $table->dropColumn(['page_content_id', 'widget_content_id']);
            $table->string('model')->nullable(false)->default(null)->change();
            $table->unsignedInteger('model_id')->nullable(false)->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
