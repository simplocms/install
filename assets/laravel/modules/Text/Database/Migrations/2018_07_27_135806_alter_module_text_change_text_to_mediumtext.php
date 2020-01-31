<?php

use Illuminate\Database\Migrations\Migration;

class AlterModuleTextChangeTextToMediumtext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE module_text_configurations MODIFY content MEDIUMTEXT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // no need
    }
}
