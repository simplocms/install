<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_directories', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->text('path');
            $table->string('storage');

            $table->unsignedInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('media_directories')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('media_files', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('extension')->nullable();
            $table->text('path');
            $table->string('storage');
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->string('description')->nullable();
            $table->string('image_resolution')->nullable();

            $table->unsignedInteger('directory_id')->nullable();
            $table->foreign('directory_id')->references('id')->on('media_directories')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('media_directories_closures', function (Blueprint $table) {
            $table->unsignedInteger('ancestor_id');
            $table->foreign('ancestor_id')
                ->references('id')->on('media_directories')
                ->onDelete('CASCADE');

            $table->unsignedInteger('descendant_id');
            $table->foreign('descendant_id')
                ->references('id')->on('media_directories')
                ->onDelete('CASCADE');

            $table->unsignedSmallInteger('depth');

            $table->primary(['ancestor_id', 'descendant_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_directories_closures');
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('media_directories');
    }
}
