<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use App\Models\Article\Article;
use App\Models\Article\Flag;

class CreateArticleFlagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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

            $table->softDeletes();
            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => 'ArticleFlagsSeeder'
        ]);

        Schema::table('categories', function (Blueprint $table) {
            $table->string('flag', 255)->change();
            $table->foreign('flag')->references('url')->on('article_flags')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->string('flag')->nulable()->default(null);
            $table->foreign('flag')->references('url')->on('article_flags')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        foreach (Flag::all() as $flag) {
            Article::whereNull('flag')->whereLanguage($flag->language_id)->update(['flag' => $flag->url]);
        }        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['flag']);
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['flag']);
            $table->dropColumn('flag');
        });
        Schema::dropIfExists('article_flags');
    }
}
