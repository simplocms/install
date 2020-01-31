<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('text')->nullable()->default(null)->change();
        });

        Schema::create('article_contents', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('articles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->text('content')->nullable()->default(null);
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('author_user_id')->nullable()->default(null);
            $table->foreign('author_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('SET NULL');

            $table->softDeletes();
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
        Schema::dropIfExists('article_contents');
    }
}
