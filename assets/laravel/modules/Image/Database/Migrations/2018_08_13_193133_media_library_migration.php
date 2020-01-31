<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MediaLibraryMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('module_image_configurations', function (Blueprint $table) {
                $table->media('image_id');
                $table->dropColumn('image');
            });
        } catch (\Exception $e) {
            // this migration can throw an exception, when the already installed plugin was migrated to the
            // new MediaLibrary (so the table was already modified)
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // no way back
    }
}
