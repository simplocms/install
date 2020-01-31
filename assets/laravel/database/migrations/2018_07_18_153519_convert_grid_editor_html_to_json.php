<?php

use Illuminate\Database\Migrations\Migration;

class ConvertGridEditorHtmlToJson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Page\Content::get()->map(function (\App\Models\Page\Content $content) {
            if ($content->content) {
                $content->update([
                    'content' => \App\Helpers\GridEditorHtmlToJsonConverter::run($content->content)
                ]);
            }
        });

        \App\Models\Article\Content::get()->map(function (\App\Models\Article\Content $content) {
            if ($content->content) {
                $content->update([
                    'content' => \App\Helpers\GridEditorHtmlToJsonConverter::run($content->content)
                ]);
            }
        });

        \App\Models\Widget\Content::get()->map(function (\App\Models\Widget\Content $content) {
            if ($content->content) {
                $content->update([
                    'content' => \App\Helpers\GridEditorHtmlToJsonConverter::run($content->content)
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // no way back :(
    }
}
