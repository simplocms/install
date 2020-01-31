<?php

namespace App\Models\Article;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\ProvidesBreadcrumbsInterface;
use App\Contracts\PublishableModelInterface;
use App\Contracts\StructuredDataTypeInterface;
use App\Contracts\ViewableModelInterface;
use App\Models\Interfaces\UrlInterface;
use App\Models\User;
use App\Models\Web\Url;
use App\Models\Web\ViewData;
use App\Services\FrontWebTools\ToolbarOptions;
use App\Structures\Collections\BreadcrumbsCollection;
use App\Structures\DataTypes\Breadcrumb;
use App\Structures\StructuredData\Types\TypeWebPage;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\FullTextSearchTrait;
use App\Traits\OpenGraphTrait;
use Baum\Node;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUrl;
use App\Traits\HasLanguage;

/**
 * Class Category
 * @package App\Models\Article
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string name
 * @property string url
 * @property string description
 * @property string seo_title
 * @property string seo_description
 * @property bool seo_index
 * @property bool seo_follow
 * @property bool seo_sitemap
 * @property int parent_id
 * @property int language_id
 * @property int flag_id
 * @property int user_id
 * @property bool show
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 * @property \Carbon\Carbon deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Article\Article[] articles
 * @property-read \App\Models\User author
 * @property-read \App\Models\Article\Flag flag
 */
class Category extends Node implements
    UrlInterface,
    ViewableModelInterface,
    PublishableModelInterface,
    ProvidesBreadcrumbsInterface,
    ConvertableToStructuredDataInterface
{
    use SoftDeletes, AdvancedEloquentTrait, HasUrl, HasLanguage, OpenGraphTrait, FullTextSearchTrait;

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'url', 'parent_id', 'show', 'open_graph', 'description',
        'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap'
    ];

    /**
     * The attributes that are set to null when the value is empty
     *
     * @var array
     */
    protected $nullIfEmpty = [
        'seo_title', 'seo_description', 'parent_id', 'description',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Searchable columns for full-text search.
     *
     * @var array
     */
    protected $searchable = ['name', 'description'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'parent_id' => 'int',
        'language_id' => 'int',
        'flag_id' => 'int',
        'user_id' => 'int',
        'show' => 'boolean',
        'seo_index' => 'boolean',
        'seo_follow' => 'boolean',
        'seo_sitemap' => 'boolean',
    ];

    /**
     * Indicates model has single url.
     *
     * @var bool
     */
    protected $hasSingleUrl = true;

    /**
     * Indicates model has subordinate urls.
     *
     * @var bool
     */
    protected $hasUrlSubordinates = true;

    /**
     * Cached breadcrumbs.
     *
     * @var \App\Structures\Collections\BreadcrumbsCollection
     */
    protected $cachedBreadcrumbs;

    /**
     * Select only published categories
     *
     * @param $query
     */
    public function scopePublished($query)
    {
        $query->where('show', 1);
    }


    /**
     * Select only categories of specified flag.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\Article\Flag|int $flag
     */
    public function scopeWhereFlag($query, $flag)
    {
        $query->where('flag_id', is_scalar($flag) ? $flag : $flag->id);
    }


    /**
     * Get tree of categories. Article can be specified for pre-selecting categories.
     * @param \Illuminate\Database\Eloquent\Builder|null $query
     * @param Article|null $article
     * @return mixed
     */
    static function getTree($query = null, Article $article = null)
    {
        $articleCategories = $article && $article->exists ?
            $article->categories()->pluck('id') : collect([]);

        $categories = is_null($query) ? self::all() : $query->get()->toHierarchy();

        return self::recursiveCategoryTree($categories, $articleCategories);
    }


    /**
     * Recursively build category tree
     *
     * @param $categories
     * @param $articleCategories
     * @return array
     */
    static function recursiveCategoryTree($categories, $articleCategories)
    {
        $result = [];

        foreach ($categories as $category) {

            $value = (object)[
                'key' => $category->id,
                'title' => $category->name,
            ];

            if ($articleCategories->contains($category->id)) {
                $value->selected = true;
            }

            if ($category->children) {
                $value->expanded = true;
                $value->children = self::recursiveCategoryTree($category->children, $articleCategories);
            }

            $result[] = $value;
        }

        return $result;
    }


    /**
     * Articles that belong to category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function articles()
    {
        return $this->belongsToMany('App\Models\Article\Article', 'articles_categories');
    }


    /**
     * User author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }


    /**
     * Flag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flag()
    {
        return $this->belongsTo(Flag::class, 'flag_id');
    }


    /**
     * Restore category's url.
     *
     * @return void
     */
    public function restoreUrl()
    {
        $urlData = $this->getUrlsData()[0];

        if (Url::findUrl($urlData['url'])) {
            return;
        }

        $newUrl = Url::create($urlData);

        /** @var Article $article */
        foreach ($this->articles as $article) {
            $article->updateUrls();
        }
    }


    /**
     * Get view data of the model.
     *
     * @param \App\Models\Web\ViewData $data
     * @return \App\Models\Web\ViewData
     */
    public function getViewData(ViewData $data): ViewData
    {
        return $data->fill([
            'title' => $this->seo_title ?: $this->name,
            'description' => $this->seo_description ?? str_limit($this->description, 320),
            'index' => $this->seo_index,
            'follow' => $this->seo_follow,
            'og_title' => $this->open_graph->get('title'),
            'og_description' => $this->open_graph->get('description'),
            'og_type' => $this->open_graph->get('type'),
            'og_url' => $this->open_graph->get('url'),
            'og_image' => $this->open_graph->hasImage() ? $this->open_graph->makeImageLink()->getUrl() : null
        ]);
    }


    /**
     * Get prefix of url.
     *
     * @return string|null
     */
    public function getUrlPrefix()
    {
        return $this->language->language_code . '/' . $this->flag->url;
    }


    /**
     * Set options for front-web toolbar.
     *
     * @param \App\Services\FrontWebTools\ToolbarOptions $options
     */
    public function setFrontWebToolbarOptions(ToolbarOptions $options): void
    {
        if ($this->show) {
            $options->addStatus(
                trans('admin/category/general.status.published'), ToolbarOptions::STATUS_SUCCESS
            );
        } else {
            $options->addStatus(
                trans('admin/category/general.status.unpublished'), ToolbarOptions::STATUS_DANGER
            );
        }

        $options->addControl(
            trans('admin/category/general.frontweb_toolbar.btn_create'),
            route('admin.categories.create', ['flag' => $this->flag->url]),
            'plus-circle'
        )
            ->addControl(
                trans('admin/category/general.frontweb_toolbar.btn_edit'),
                route('admin.categories.edit', [
                    'flag' => $this->flag->url,
                    'id' => $this->getKey()
                ]),
                'edit'
            );
    }


    /**
     * Check if model is public.
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->show;
    }


    /**
     * Get breadcrumbs of the model.
     *
     * @return \App\Structures\Collections\BreadcrumbsCollection|\App\Structures\DataTypes\Breadcrumb[]
     */
    public function getBreadcrumbs(): BreadcrumbsCollection
    {
        if ($this->cachedBreadcrumbs) {
            return $this->cachedBreadcrumbs;
        }

        $breadcrumbs = $this->getAncestorsAndSelf()->map(function (Category $category) {
            return new Breadcrumb($category->name, $category->full_url, $category);
        });
        $breadcrumbs->prepend(new Breadcrumb($this->flag->name, $this->flag->full_url, $this->flag));

        return $this->cachedBreadcrumbs = new BreadcrumbsCollection($breadcrumbs);
    }


    /**
     * Get structured data type.
     *
     * @return \App\Contracts\StructuredDataTypeInterface
     */
    public function toStructuredData(): StructuredDataTypeInterface
    {
        return new TypeWebPage([
            'name' => $this->name,
            'url' => $this->full_url,
            'inLanguage' => $this->language,
            'dateCreated' => $this->created_at,
            'dateModified' => $this->updated_at,
            'breadcrumb' => $this->getBreadcrumbs(),
            'description' => $this->seo_description ?? $this->description,
            'headline' => $this->seo_title ?? $this->name,
        ]);
    }
}
