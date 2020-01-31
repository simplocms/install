<?php

namespace App\Models\Article;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\StructuredDataTypeInterface;
use App\Contracts\ViewableModelInterface;
use App\Models\Interfaces\UrlInterface;
use App\Models\Web\ViewData;
use App\Services\FrontWebTools\ToolbarOptions;
use App\Structures\StructuredData\Types\TypeWebPage;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\OpenGraphTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUrl;
use App\Models\User;
use App\Traits\HasLanguage;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Flag
 * @package App\Models\Article
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string name
 * @property string url
 * @property string description
 * @property bool use_tags
 * @property bool use_grid_editor
 * @property string seo_title
 * @property string seo_description
 * @property bool seo_index
 * @property bool seo_follow
 * @property bool seo_sitemap
 * @property bool should_bound_articles_to_category
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 * @property \Carbon\Carbon deleted_at
 *
 * @property-read \App\Models\User author
 * @property-read \App\Models\Article\Category[]|\Illuminate\Database\Eloquent\Collection categories
 * @property-read \App\Models\Article\Article[]|\Illuminate\Database\Eloquent\Collection articles
 */
class Flag extends Model implements UrlInterface, ViewableModelInterface, ConvertableToStructuredDataInterface
{
    use SoftDeletes, AdvancedEloquentTrait, HasUrl, HasLanguage, OpenGraphTrait;

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'article_flags';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'url', 'use_tags', 'use_grid_editor', 'open_graph',
        'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap',
        'should_bound_articles_to_category', 'description'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'use_tags' => 'boolean',
        'use_grid_editor' => 'boolean',
        'seo_index' => 'boolean',
        'seo_follow' => 'boolean',
        'seo_sitemap' => 'boolean',
        'should_bound_articles_to_category' => 'boolean',
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
     * User creator.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_user_id');
    }


    /**
     * Categories with the flag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany(Category::class, 'flag_id');
    }


    /**
     * Articles with the flag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'flag_id');
    }


    /**
     * Set options for front-web toolbar.
     *
     * @param \App\Services\FrontWebTools\ToolbarOptions $options
     */
    public function setFrontWebToolbarOptions(ToolbarOptions $options): void
    {
        // do not insert any controls
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
            'title' => $this->seo_title ?? $this->name,
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
            'description' => $this->seo_description ?? $this->description,
            'headline' => $this->seo_title ?? $this->name,
        ]);
    }
}
