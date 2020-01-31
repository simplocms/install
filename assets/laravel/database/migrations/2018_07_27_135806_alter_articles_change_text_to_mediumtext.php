<?php

use Illuminate\Database\Migrations\Migration;

class AlterArticlesChangeTextToMediumtext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE articles MODIFY text MEDIUMTEXT;');

        // Migrate Text module if is installed.
        $textModule = \App\Models\Module\InstalledModule::findNamed('Text');
        if ($textModule && $textModule->module) {
            $textModule->module->migrate();
        }
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
