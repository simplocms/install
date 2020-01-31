<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\UniversalModule\UniversalModuleEntity;
use App\Models\UniversalModule\UniversalModuleItem;

class CreateUniversalModuleEntityItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universal_module_entity_item', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('universal_module_entity_id')->unsigned();
            $table->integer('universal_module_item_id')->unsigned();

            $table->foreign('universal_module_entity_id')->references('id')->on('universal_module_entities')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('universal_module_item_id')->references('id')->on('universal_module_items')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        foreach (UniversalModuleEntity::all() as $entity) {
            $items = [];
            foreach ((array) unserialize($entity->items) as $item) {
                if (UniversalModuleItem::where('id', $item)->exists()) {
                    $items[] = $item;
                }
            }
            $entity->items()->sync($items);
        }

        Schema::table('universal_module_entities', function (Blueprint $table) {
            $table->dropColumn('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('universal_module_entities', function (Blueprint $table) {
            $table->text('items')->nullable();
        });

        foreach (UniversalModuleEntity::all() as $entity) {
            $items = $entity->items()->pluck('universal_module_items.id')->toArray();

            $entity->items = serialize($items);
            $entity->save();
        }

        Schema::dropIfExists('universal_module_entity_item');
    }
}
