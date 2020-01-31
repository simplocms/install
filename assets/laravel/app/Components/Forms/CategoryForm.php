<?php

namespace App\Components\Forms;

use App\Models\Article\Category;
use App\Models\Article\Flag;

class CategoryForm extends AbstractForm
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.categories.form';

    /**
     * Flag.
     *
     * @var \App\Models\Article\Flag
     */
    protected $flag;

    /**
     * Category.
     *
     * @var \App\Models\Article\Category
     */
    protected $category;

    /**
     * Category form.
     *
     * @param \App\Models\Article\Flag $flag
     * @param \App\Models\Article\Category $category
     * @throws \Exception
     */
    public function __construct(Flag $flag, Category $category)
    {
        parent::__construct();
        $this->category = $category;
        $this->flag = $flag;
        
        $this->addScript(url('plugin/js/bootstrap-tagsinput.js'));
        $this->addScript(url('plugin/js/bootstrap-maxlength.js'));
        $this->addScript(mix('js/categories.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return [
            'category' => $this->category,
            'submitUrl' => $this->getSubmitUrl(),
            'formDataJson' => $this->category->getFormAttributesJson([
                'name', 'url', 'parent_id', 'show', 'open_graph', 'description',
                'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap'
            ]),
            'cancelUrl' => route('admin.categories.index', $this->flag->url),
            'categories' => $this->getCategories()
        ];
    }


    /**
     * Get submit url for form.
     *
     * @return string
     */
    private function getSubmitUrl(): string
    {
        if ($this->category->exists) {
            return route('admin.categories.update', [
                'flag' => $this->flag->url, 
                'category' => $this->category->id
            ]);
        } 

        return route('admin.categories.store', $this->flag->url);
    }


    /**
     * Get categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getCategories()
    {
        $query = Category::whereLanguage($this->flag->language_id)
            ->whereFlag($this->flag);

        if ($this->category->exists) {
            $query->where('id', '<>', $this->category->id);
        }

        return $query->get()->toHierarchy();
    }
}
