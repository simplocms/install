<?php

namespace App\Components\Forms;

use App\Models\Article\Article;
use App\Models\Article\Flag;
use App\Components\Forms\Templates\AbstractFormWithGridEditor;
use App\Models\Article\Tag;
use App\Models\User;
use App\Structures\Enums\PublishingStateEnum;


class ArticleForm extends AbstractFormWithGridEditor
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.articles.form';

    /**
     * Article.
     *
     * @var \App\Models\Article\Article
     */
    protected $article;

    /**
     * Flag.
     *
     * @var \App\Models\Article\Flag
     */
    protected $flag;

    /**
     * Use grid editor versions?
     *
     * @var boolean
     */
    protected $useVersions = true;

    /**
     * Article form.
     *
     * @param \App\Models\Article\Flag $flag
     * @param \App\Models\Article\Article $article
     * @throws \Exception
     */
    public function __construct(Flag $flag, Article $article)
    {
        parent::__construct();
        $this->article = $article;
        $this->flag = $flag;

        if ($this->flag->use_grid_editor) {
            $this->addGridEditorScriptsAndStyle();
        } else {
            $this->addCKEditorScript();
        }

        $this->addScript(url('plugin/js/pickadate.js'));
        $this->addScript(url('plugin/js/fancytree.js'));

        if ($this->flag->use_tags) {
            $this->addScript(url('plugin/js/typeahead.js'));
            $this->addScript(url('plugin/js/bootstrap-tagsinput.js'));
        }

        $this->addScript(mix('js/articles.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        $routeAttributes = ['flag' => $this->flag->url, 'article' => $this->article->getKey()];

        $data = [
            'flag' => $this->flag,
            'article' => $this->article,
            'articleCategories' => $this->getArticleCategories(),
            'categoriesTreeUrl' => route('admin.articles.categories_tree', $routeAttributes),
            'formDataJson' => $this->article->getFormAttributesJson([
                'title', 'url', 'perex', 'text', 'open_graph', 'state', 'user_id', 'image_id',
                'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap',
                'publish_at_date', 'publish_at_time', 'unpublish_at_date', 'unpublish_at_time',
                'video_id',
            ]),
            'submitUrl' => $this->article->exists ?
                route('admin.articles.update', $routeAttributes) :
                route('admin.articles.store', $this->flag->url),
            'photos' => $this->article->photos()->with('image')->get(),
            'publishingStates' => PublishingStateEnum::toJson(),
            'canChangeUser' => auth()->user()->isAdmin()
        ];

        if ($this->flag->use_tags) {
            $data['tags'] = Tag::query()->pluck('name');
        }

        if ($data['canChangeUser']) {
            $data['users'] = User::all()->pluck('name', 'id');
        }

        return $this->flag->use_grid_editor ? $this->getGridEditorData($data) : $data;
    }


    /**
     * Get content of the grid editor.
     *
     * @return \App\Models\Article\Content
     */
    protected function getGridEditorContent(): \App\Models\Interfaces\IsGridEditorContent
    {
        return $this->article->getActiveContent() ?: new \App\Models\Article\Content;
    }


    /**
     * Get url of the version switch for the grid editor.
     *
     * @return string
     */
    protected function getGridEditorVersionSwitchUrl(): string
    {
        return $this->article->exists ? route('admin.articles.getVersionContent', [
            'flag' => $this->flag->url,
            'article' => $this->article->getKey()
        ]) : '';
    }


    /**
     * Get versions of the grid editor content.
     *
     * @return array
     */
    protected function getGridEditorVersions(): array
    {
        return $this->article->getGridEditorVersions();
    }


    /**
     * Get categories of the article as a string of ids separated with comma.
     *
     * @return string
     */
    private function getArticleCategories(): string
    {
        if ($this->article->exists) {
            return $this->article->categories()->pluck('id')->toJson();
        }

        return '[]';
    }
}
