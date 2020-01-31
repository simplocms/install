<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('widgets', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->string('name');

            $table->unsignedInteger('author_user_id')->nullable()->default(null);
            $table->foreign('author_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('SET NULL');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('widget_contents', function (Blueprint $table) {
            $table->increments('id');

            $table->string('widget_id');
            $table->foreign('widget_id')
                ->references('id')->on('widgets')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('language_id')->nullable()->default(null);
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->text('content');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('module_entities', function (Blueprint $table) {
            $table->unsignedInteger('widget_content_id')->nullable()->default(null);
            $table->foreign('widget_content_id')
                ->references('id')->on('widget_contents')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('module_entities', function (Blueprint $table) {
            $table->dropForeign(['widget_id']);
            $table->dropColumn(['widget_id']);
        });

        Schema::dropIfExists('widgets');
    }
}
