<?php

namespace Modules\ArticlesList\Models;

use App\Models\Article\Article;
use App\Models\Article\Category;
use App\Models\Article\Content;
use App\Models\Article\Tag;
use App\Models\Interfaces\ModuleConfigurationInterface;
use App\Models\Module\Module;
use App\Structures\Enums\SingletonEnum;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

/**
 * Class Configuration
 * @package Modules\ArticlesList\Models
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string view
 * @property-read int[] category_ids
 * @property-read int[] tag_ids
 * @property int sort_type
 * @property int limit
 *
 * @property-read \App\Models\Article\Category[]|\Illuminate\Database\Eloquent\Collection categories
 * @property-read \App\Models\Article\Tag[]|\Illuminate\Database\Eloquent\Collection tags
 */
class Configuration extends Model implements ModuleConfigurationInterface
{
    use AdvancedEloquentTrait;

    /**
     * @var string Table name of the model
     */
    protected $table = 'module_articleslist_configurations';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sort_type' => 'int',
        'limit' => 'int',
    ];

    /**
     * @var int[]|null
     */
    protected $categoryIdsToSave;

    /**
     * @var int[]|null
     */
    protected $tagIdsToSave;

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = ['view', 'sort_type', 'limit'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::saved(function (Configuration $configuration) {
            if ($configuration->categoryIdsToSave !== null) {
                $configuration->categories()->sync($configuration->categoryIdsToSave);
                $configuration->categoryIdsToSave = null;
            }

            if ($configuration->tagIdsToSave !== null) {
                $configuration->tags()->sync($configuration->tagIdsToSave);
                $configuration->tagIdsToSave = null;
            }
        });
    }


    /**
     * Categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class, 'module_articleslist_configurations_categories',
            'configuration_id',
            'category_id'
        );
    }


    /**
     * Tags.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'module_articleslist_configurations_tags',
            'configuration_id',
            'tag_id'
        );
    }


    /**
     * @return int[]
     */
    public function getCategoryIdsAttribute(): array
    {
        if ($this->categoryIdsToSave !== null) {
            return $this->categoryIdsToSave;
        }

        return $this->categories->pluck('id')->toArray();
    }


    /**
     * @return int[]
     */
    public function getTagIdsAttribute(): array
    {
        if ($this->tagIdsToSave !== null) {
            return $this->tagIdsToSave;
        }

        return $this->tags->pluck('id')->toArray();
    }


    /**
     * Get default configuration
     *
     * @return Configuration
     */
    public static function getDefault()
    {
        return new self([
            'limit' => 0,
            'sort_type' => SortTypeEnum::PUBLISH_DATE
        ]);
    }


    /**
     * @return \Illuminate\Support\Collection|int[]|null
     */
    private function getArticlesIds(): Collection
    {
        $categoryArticleIds = new Collection();
        $tagArticleIds = new Collection();

        if ($this->categories->isNotEmpty()) {
            $categoryArticleIds = DB::table('articles_categories')
                ->whereIn('category_id', $this->category_ids)
                ->pluck('article_id');
        }

        if ($this->tags->isNotEmpty()) {
            $tagArticleIds = DB::table('article_tags')
                ->whereIn('tag_id', $this->tag_ids)
                ->pluck('article_id');
        }

        if ($this->categories->isNotEmpty() && $this->tags->isNotEmpty()) {
            return $categoryArticleIds->intersect($tagArticleIds);
        }

        if ($this->categories->isNotEmpty()) {
            return $categoryArticleIds;
        }

        if ($this->tags->isNotEmpty()) {
            return $tagArticleIds;
        }

        return null;
    }


    /**
     * @param int|null $exceptId
     * @return \Illuminate\Support\Collection|\App\Models\Article\Article[]
     */
    private function getArticles(?int $exceptId = null): Collection
    {
        $query = Article::published();
        $ids = $this->getArticlesIds();

        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }

        if ($exceptId !== null) {
            $query->whereKeyNot($exceptId);
        }

        if ($this->limit) {
            $query->limit($this->limit);
        }

        switch ($this->sort_type) {
            case SortTypeEnum::PUBLISH_DATE:
                $query->orderPublish();
                break;
            case SortTypeEnum::TITLE:
                $query->orderBy('title');
                break;
            case SortTypeEnum::RANDOM:
                $query->inRandomOrder();
                break;
        }

        return $query->get();
    }


    /**
     * Render module
     *
     * @param array $renderAttributes
     * @return string
     * @throws \Throwable
     */
    public function render(array $renderAttributes = []): string
    {
        $renderedModel = $renderAttributes['rendered_content'] ?? null;

        $exceptId = null;

        if ($renderedModel instanceof Content) {
            $exceptId = $renderedModel->getArticleId();
        }

        if (!View::exists($this->view)) {
            return view('module-articleslist::missing_view', ['name' => $this->view])->render();
        }

        $configuration = $this;
        $articles = $this->getArticles($exceptId);
        return view($this->view, compact('configuration', 'articles'))->render();
    }


    /**
     * Fill model with input values with mutators inside.
     *
     * @param array $inputs
     * @return $this
     */
    public function inputFill(array $inputs)
    {
        $this->categoryIdsToSave = $inputs['category_ids'] ?? [];
        $this->tagIdsToSave = $inputs['tag_ids'] ?? [];
        return $this->fill($inputs);
    }


    /**
     * @return \App\Models\Module\Module
     */
    public function getModule(): Module
    {
        return SingletonEnum::modules()->find('ArticlesList');
    }


    /**
     * @param mixed[]|null $except
     * @return \Modules\ArticlesList\Models\Configuration
     */
    public function replicate(array $except = null): Configuration
    {
        /** @var \Modules\ArticlesList\Models\Configuration $replica */
        $replica = parent::replicate($except);

        $replica->categoryIdsToSave = $this->categories()->pluck('id');
        $replica->tagIdsToSave = $this->tags()->pluck('id');

        return $replica;
    }
}
