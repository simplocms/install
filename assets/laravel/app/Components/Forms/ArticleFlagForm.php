<?php

namespace App\Components\Forms;

use App\Models\Article\Flag;

class ArticleFlagForm extends AbstractForm
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.article_flags.form';

    /**
     * Flag.
     *
     * @var \App\Models\Article\Flag
     */
    protected $flag;

    /**
     * Flag form.
     *
     * @param \App\Models\Article\Flag $flag
     */
    public function __construct(Flag $flag)
    {
        parent::__construct();
        $this->flag = $flag;
        
        $this->addScript(url('plugin/js/bootstrap-maxlength.js'));

        $this->addScript(mix('js/article-flags.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return [
            'flag' => $this->flag,
            'formDataJson' => $this->flag->getFormAttributesJson([
                'name', 'url', 'use_tags', 'use_grid_editor', 'open_graph',
                'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap',
                'should_bound_articles_to_category', 'description',
            ]),
            'submitUrl' => $this->getSubmitUrl()
        ];
    }


    /**
     * Get submit url for form.
     *
     * @return string
     */
    private function getSubmitUrl(): string
    {
        if ($this->flag->exists) {
            return route('admin.article_flags.update', $this->flag->id);
        } 

        return route('admin.article_flags.store');
    }
}
