<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUniversalModuleEntitiesAddAllItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('universal_module_entities', function (Blueprint $table) {
            $table->boolean('all_items')->default(false);
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
            $table->dropColumn(['all_items']);
        });
    }
}
