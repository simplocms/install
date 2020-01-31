<?php

namespace Modules\ArticlesList\Models;

use App\Components\Forms\AbstractForm;
use App\Helpers\Functions;
use App\Helpers\ViewHelper;
use App\Models\Article\Category;
use App\Models\Article\Tag;
use App\Models\Web\Language;

class GridEditorForm extends AbstractForm
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'module-articleslist::configuration.form';

    /**
     * Configuration.
     *
     * @var \Modules\ArticlesList\Models\Configuration
     */
    protected $configuration;

    /**
     * @var \App\Models\Web\Language
     */
    private $language;

    /**
     * Configuration form for grid editor.
     *
     * @param \Modules\ArticlesList\Models\Configuration $configuration
     * @param \App\Models\Web\Language $language
     * @throws \Exception
     */
    public function __construct(Configuration $configuration, Language $language)
    {
        parent::__construct();
        $this->configuration = $configuration;
        $this->language = $language;

        $this->addScript($configuration->getModule()->mix('configuration.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        $views = ViewHelper::getDemarcatedViews('modules.articles_list');
        $sortTypes = SortTypeEnum::labels();

        /** @var \App\Models\Article\Category[] $categoriesFlat */
        $categoriesFlat = Category::whereLanguage($this->language)
            ->with('flag')
            ->get();

        $categories = [];
        foreach ($categoriesFlat as $category) {
            $categories[$category->flag->name][$category->getKey()] = $category->name;
        }

        $categories = Functions::associativeArrayToSequentialArray(
            $categories, 'id', 'label', 'children'
        );

        $tags = Tag::query()->pluck('name', 'id')->toArray();

        return \array_merge([
            'configuration' => $this->configuration
        ], \compact('views', 'sortTypes', 'categories', 'tags'));
    }
}
