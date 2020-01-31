<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleImageConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_image_configurations', function (Blueprint $table) {
            $table->increments('id');

            $table->text('image');
            $table->text('alt');
            $table->boolean('is_sized');
            $table->unsignedInteger('width')->nullable()->default(null);
            $table->unsignedInteger('height')->nullable()->default(null);

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
        Schema::dropIfExists('module_image_configurations');
    }
}
