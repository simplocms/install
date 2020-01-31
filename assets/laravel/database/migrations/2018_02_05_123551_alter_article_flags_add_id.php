<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterArticleFlagsAddId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['flag']);
            $table->unsignedInteger('flag_id');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['flag']);
            $table->unsignedInteger('flag_id');
        });

        Schema::table('article_flags', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropForeign(['author_user_id']);
        });

        Schema::rename('article_flags', 'article_flags_old');

        Schema::create('article_flags', function (Blueprint $table) {
            $table->increments('id');

            $table->string('url');
            $table->string('name');

            $table->unsignedInteger('language_id');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('author_user_id')->nullable()->default(null);
            $table->foreign('author_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('SET NULL');

            $table->boolean('use_tags')->default(false);
            $table->boolean('use_grid_editor')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });

        $flags = (new \App\Models\Article\Flag)->setTable('article_flags_old')->get();
        (new \App\Models\Web\Url)->where('model', \App\Models\Article\Flag::class)->forceDelete();

        foreach ($flags as $flag) {
            $newFlag = new \App\Models\Article\Flag();
            $newFlag->forceFill($flag->getAttributes());
            $newFlag->save();

            (new \App\Models\Article\Article)->where('flag', $newFlag->url)
                ->update(['flag_id' => $newFlag->id]);

            (new \App\Models\Article\Category)->where('flag', $newFlag->url)
                ->update(['flag_id' => $newFlag->id]);
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['flag']);
            $table->foreign('flag_id')->references('id')->on('article_flags')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['flag']);
            $table->foreign('flag_id')->references('id')->on('article_flags')
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
        Schema::table('articles', function (Blueprint $table) {
            $table->string('flag');
            $table->dropForeign(['flag_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('flag');
            $table->dropForeign(['flag_id']);
        });

        Schema::table('article_flags', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropForeign(['author_user_id']);
        });

        Schema::rename('article_flags', 'article_flags_old');

        Schema::create('article_flags', function (Blueprint $table) {
            $table->string('url');
            $table->primary(['url']);

            $table->string('name');

            $table->unsignedInteger('language_id');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('author_user_id')->nullable()->default(null);
            $table->foreign('author_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('SET NULL');

            $table->boolean('use_tags')->default(false);
            $table->boolean('use_grid_editor')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });

        $flags = (new \App\Models\Article\Flag)->setTable('article_flags_old')->get();
        (new \App\Models\Web\Url)->where('model', \App\Models\Article\Flag::class)->forceDelete();

        foreach ($flags as $flag) {
            $newFlag = new \App\Models\Article\Flag();
            $newFlag->forceFill(array_except($flag->getAttributes(), ['id']));
            $newFlag->save();

            (new \App\Models\Article\Article)->where('flag_id', $flag->id)
                ->update(['flag' => $newFlag->url]);

            (new \App\Models\Article\Category)->where('flag_id', $flag->id)
                ->update(['flag' => $newFlag->url]);
        }

        Schema::drop('article_flags_old');

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['flag_id']);
            $table->foreign('flag')->references('url')->on('article_flags')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['flag_id']);
            $table->foreign('flag')->references('url')->on('article_flags')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
