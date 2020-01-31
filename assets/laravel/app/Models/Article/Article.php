<?php

namespace App\Models\Article;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\ProvidesBreadcrumbsInterface;
use App\Contracts\PublishableModelInterface;
use App\Contracts\StructuredDataTypeInterface;
use App\Contracts\ViewableModelInterface;
use App\Contracts\PhotogalleryInterface;
use App\Models\Interfaces\UrlInterface;
use App\Models\Media\File;
use App\Models\User;
use App\Models\Web\Language;
use App\Models\Web\ViewData;
use App\Services\FrontWebTools\ToolbarOptions;
use App\Structures\Collections\BreadcrumbsCollection;
use App\Structures\DataTypes\Breadcrumb;
use App\Structures\Enums\PublishingStateEnum;
use App\Structures\Enums\SingletonEnum;
use App\Structures\StructuredData\Types\TypeArticle;
use App\Structures\StructuredData\Types\TypeOrganization;
use App\Structures\StructuredData\Types\TypeWebPage;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\MediaImageTrait;
use App\Traits\OpenGraphTrait;
use App\Traits\PhotogalleryTrait;
use App\Traits\PlannedPublishingTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUrl;
use App\Models\Interfaces\UsesGridEditor;
use App\Traits\GridEditor\GridEditorModel;
use App\Traits\HasLanguage;
use Illuminate\Support\Collection;

/**
 * Class Article
 * @package App\Models\Article
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string title
 * @property string url
 * @property string|null perex
 * @property string|null text
 * @property int state
 * @property string|null seo_title
 * @property string|null seo_description
 * @property bool seo_index
 * @property bool seo_follow
 * @property bool seo_sitemap
 * @property int|null image_id
 * @property int|null video_id
 * @property int user_id
 * @property \Carbon\Carbon|null deleted_at
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 *
 * @property-read \App\Models\Media\File|null image
 * @property-read \App\Models\Media\File|null video
 * @property-read \App\Models\Article\Content[]|\Illuminate\Database\Eloquent\Collection contents
 * @property-read \App\Models\Article\Flag flag
 * @property-read \App\Models\User user
 * @property-read \App\Models\Article\Category[]|\Illuminate\Database\Eloquent\Collection categories
 * @property-read \App\Models\Article\Tag[]|\Illuminate\Database\Eloquent\Collection tags
 *
 * @method static \Illuminate\Database\Eloquent\Builder published()
 */
class Article extends Model implements
    UrlInterface,
    UsesGridEditor,
    ViewableModelInterface,
    PhotogalleryInterface,
    PublishableModelInterface,
    ProvidesBreadcrumbsInterface,
    ConvertableToStructuredDataInterface
{
    use SoftDeletes,
        AdvancedEloquentTrait,
        HasUrl,
        GridEditorModel,
        HasLanguage,
        MediaImageTrait,
        PlannedPublishingTrait,
        PhotogalleryTrait,
        OpenGraphTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'articles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'url', 'perex', 'text', 'image_id', 'publish_at', 'unpublish_at', 'state',
        'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap', 'open_graph',
        'video_id'
    ];

    /**
     * The attributes that are set to null when the value is empty
     *
     * @var array
     */
    protected $nullIfEmpty = [
        'perex', 'text', 'image_id', 'seo_title', 'seo_description', 'video_id'
    ];

    /**
     * @var array
     */
    public $dates = ['deleted_at', 'publish_at', 'unpublish_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'image_id' => 'int',
        'video_id' => 'int',
        'user_id' => 'int',
        'state' => 'int',
        'seo_index' => 'boolean',
        'seo_follow' => 'boolean',
        'seo_sitemap' => 'boolean',
    ];

    /**
     * Use grid editor versions?
     *
     * @var bool
     */
    protected $useGridEditorVersions = true;

    /**
     * Categories to synchronize when saved.
     *
     * @var int[]
     */
    protected $categoriesToSave;

    /**
     * Cached breadcrumbs.
     *
     * @var \App\Structures\Collections\BreadcrumbsCollection
     */
    protected $cachedBreadcrumbs;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::saved(function (Article $article) {
            if (!is_null($article->categoriesToSave)) {
                $article->saveCategories();
            }
        });
    }


    /**
     * Contents (versions).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'article_id', 'id');
    }


    /**
     * Flag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flag(): BelongsTo
    {
        return $this->belongsTo(Flag::class, 'flag_id');
    }


    /**
     * User who created article
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }


    /**
     * Photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos(): HasMany
    {
        return $this->hasManyPhotos('article_photos', 'article_id');
    }


    /**
     * Categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class, 'articles_categories',
            'article_id', 'category_id'
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
            Tag::class, 'article_tags', 'article_id', 'tag_id'
        );
    }


    /**
     * Relation to image file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }


    /**
     * Relation to video file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(File::class, 'video_id');
    }


    /**
     * Get view data of the model.
     *
     * @param \App\Models\Web\ViewData $data
     * @return \App\Models\Web\ViewData
     */
    public function getViewData(ViewData $data): ViewData
    {
        $data->title = $this->seo_title ?: $this->title;
        $data->description = $this->seo_description ?? str_limit($this->perex, 320);
        $data->index = $this->seo_index;
        $data->follow = $this->seo_follow;

        $data->og_title = $this->open_graph->get('title', $data->title);
        $data->og_description = $this->open_graph->get('description', $data->description);
        $data->og_type = $this->open_graph->get('type', 'website');
        $data->og_url = $this->open_graph->get('url') ?? $this->full_url;

        if ($this->open_graph->hasImage()) {
            $data->og_image = $this->open_graph->makeImageLink()->getUrl();
        } elseif ($this->hasImage('image')) {
            $data->og_image = $this->makeImageLink('image')->getUrl();
        }

        $data->custom_meta = [
            'article:published_time' => optional($this->publish_at)->toIso8601String(),
            'article:modified_time' => optional($this->updated_at)->toIso8601String(),
            'article:expiration_time' => optional($this->unpublish_at)->toIso8601String(),
            'article:author' => optional($this->user)->name,
            'article:section' => optional($this->categories->first())->name,
            'article:tag' => $this->flag->use_tags ? $this->tags->pluck('name')->toArray() : null,
            'twitter:creator' => optional($this->user)->twitter_account,
        ];

        if ($this->categories->count() > 1) {
            $canonical = $this->full_url;

            if ($canonical !== url()->current()) {
                $data->canonical = $canonical;
            }
        }

        return $data;
    }


    /**
     * Save article categories.
     */
    protected function saveCategories(): void
    {
        $result = $this->categories()->sync($this->categoriesToSave);

        // If is set flag to update url addresses, or some categories were attached or detached, update url addresses.
        if (!$this->wasRecentlyCreated && ($result['attached'] || $result['detached'])) {
            $this->updateUrls();
        }
    }


    /**
     * Update article tags.
     *
     * @param array $tagNames
     */
    public function syncTags(array $tagNames): void
    {
        $tagIds = [];

        foreach ($tagNames as $tagName) {
            $tag = Tag::findNamed($tagName);

            if (!$tag) {
                $tag = Tag::query()->create(['name' => $tagName]);
            }

            $tagIds[] = $tag->getKey();
        }

        $this->tags()->sync($tagIds);
    }


    /**
     * Replicate article with all its dependencies.
     *
     * @return \App\Models\Article\Article
     * @throws \App\Exceptions\GridEditorException
     */
    public function replicateFull(): Article
    {
        /** @var \App\Models\Article\Article $newArticle */
        $newArticle = $this->replicate();

        $newArticle->title = $newArticle->title . ' - Kopie';
        $newArticle->url = $newArticle->url . '-kopie';

        $newArticle->setCategoriesToSave($this->categories()->pluck('id')->toArray());
        $newArticle->save();

        // TODO: fix replicating photos in gallery?
//        // Photos.
//        /** @var \App\Models\Photogallery\Photo $photo */
//        foreach ($this->photos as $photo) {
//            $photo->setRelation('article', $this)->replicateToArticle($newArticle);
//        }

        // Versions.
        if ($this->flag->use_grid_editor) {
            /** @var \App\Models\Article\Content $content */
            foreach ($this->contents as $content) {
                $content->replicateFull([
                    'article_id' => $newArticle->getKey()
                ]);
            }
        }

        // Flag
        if ($this->flag->use_tags) {
            $newArticle->syncTags($this->tags()->pluck('name')->toArray());
        }

        return $newArticle;
    }


    /**
     * Get superior models that influence url address.
     *
     * @return \App\Models\Interfaces\UrlInterface[]
     */
    public function getUrlSuperiorModels(): array
    {
        if ($this->workWithSingleUrl()) {
            return [];
        }

        if (!$this->exists || $this->wasRecentlyCreated) {
            return Category::query()->whereIn('id', $this->categoriesToSave)->get()->all();
        }

        return $this->categories->all();
    }


    /**
     * Set categories ids to synchronize after saving article.
     *
     * @param int[] $categoriesIds
     *
     * @return void
     */
    public function setCategoriesToSave(array $categoriesIds): void
    {
        $this->categoriesToSave = $categoriesIds;
    }


    /**
     * Select only categories of specified flag.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\Article\Flag|int $flag
     */
    public function scopeWhereFlag($query, $flag)
    {
        $query->where('flag_id', is_scalar($flag) ? $flag : $flag->getKey());
    }


    /**
     * Set options for front-web toolbar.
     *
     * @param \App\Services\FrontWebTools\ToolbarOptions $options
     */
    public function setFrontWebToolbarOptions(ToolbarOptions $options): void
    {
        if ($this->isPublic()) {
            $options->addStatus(
                trans('admin/article/general.status.published'), ToolbarOptions::STATUS_SUCCESS
            );
        } else {
            $options->addStatus(
                trans('admin/article/general.status.unpublished'), ToolbarOptions::STATUS_DANGER
            );
        }

        $options->addControl(
            trans('admin/article/general.frontweb_toolbar.btn_create'),
            route('admin.articles.create', $this->flag->url),
            'plus-circle'
        )
            ->addControl(
                trans('admin/article/general.frontweb_toolbar.btn_edit'),
                route('admin.articles.edit', [
                    'flag' => $this->flag->url,
                    'id' => $this->getKey()
                ]),
                'edit'
            );
    }


    /**
     * Get article content.
     *
     * @return string
     */
    public function getContent(): string
    {
        if ($this->flag->use_grid_editor) {
            return $this->getActiveContent()->getHtml([
                'language' => $this->language
            ]);
        }

        return "<div class='_cms-content'>{$this->text}</div>";
    }


    /**
     * Get article raw content.
     *
     * @return string
     */
    public function getRawContent(): string
    {
        if ($this->flag->use_grid_editor) {
            return $this->getActiveContent()->getHtml([
                'language_id' => $this->language_id
            ]);
        }

        return $this->text;
    }


    /**
     * Check if model is public.
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->state === PublishingStateEnum::PUBLISHED && $this->isInPublicPeriod();
    }


    /**
     * Get breadcrumbs of the model.
     *
     * @return \App\Structures\DataTypes\Breadcrumb[]|\App\Structures\Collections\BreadcrumbsCollection
     */
    public function getBreadcrumbs(): BreadcrumbsCollection
    {
        if ($this->cachedBreadcrumbs) {
            return $this->cachedBreadcrumbs;
        }

        $parent = null;
        if ($this->flag->should_bound_articles_to_category) {
            /** @var \App\Models\Article\Category $parent */
            if ($this->activeParentUrl && $this->activeParentUrl->model === Category::class) {
                $parent = $this->activeParentUrl->getInstance();
            } else {
                $parent = $this->categories->first();
            }
        }

        if ($parent) {
            $breadcrumbs = $parent->getBreadcrumbs();
        } else {
            $flagBreadcrumb = new Breadcrumb($this->flag->name, $this->flag->full_url, $this->flag);
            $breadcrumbs = new BreadcrumbsCollection([$flagBreadcrumb]);
        }

        $breadcrumbs->push(new Breadcrumb($this->title, $this->full_url, $this));

        return $this->cachedBreadcrumbs = $breadcrumbs;
    }


    /**
     * Get structured data type.
     *
     * @return \App\Contracts\StructuredDataTypeInterface
     */
    public function toStructuredData(): StructuredDataTypeInterface
    {
        return new TypeArticle([
            'name' => $this->title,
            'url' => $this->full_url,
            'description' => $this->seo_description ?? $this->perex,
            'image' => $this->hasImage('image') ?
                $this->makeImageLink('image')->allowedFormats(['png', 'jpeg', 'gif'])->getUrl() : null,
            'inLanguage' => $this->language,
            'dateCreated' => $this->created_at,
            'dateModified' => $this->updated_at,
            'datePublished' => $this->publish_at,
            'expires' => $this->unpublish_at,
            'author' => $this->user,
            'publisher' => new TypeOrganization([
                'name' => SingletonEnum::settings()->get(
                    'company_name',
                    // default value is site name with fallback to app name:
                    SingletonEnum::settings()->get('site_name', trans('general.settings.site_name', [], $this->language->language_code))
                ),
                'logo' => SingletonEnum::settings()->makeImageLink('logo')
                    ->fitCanvas(600, 60)
                    ->allowedFormats(['png', 'jpeg', 'gif'])
                    ->toStructuredData()
            ]),
            'articleBody' => $this->getRawContent(),
            'mainEntityOfPage' => new TypeWebPage([
                'url' => $this->full_url,
                'breadcrumb' => $this->getBreadcrumbs()
            ]),
            'headline' => $this->seo_title ?? $this->title,
        ]);
    }


    /**
     * Select only published articles
     *
     * @param \Illuminate\Database\Eloquent\Builder|self $query
     */
    public function scopePublished($query)
    {
        $query->publishedByDate()->where('state', PublishingStateEnum::PUBLISHED);
    }


    /**
     * Search articles for given term.
     *
     * @param string $term
     * @param \App\Models\Web\Language $language
     * @return \Illuminate\Support\Collection
     */
    public static function search(string $term, Language $language): Collection
    {
        if (!strlen($term)) {
            return collect([]);
        }

        /** @var \App\Models\Article\Flag[] $flags */
        $flags = Flag::get();
        $flagsWithGridEditor = [];
        $flagsWithoutGridEditor = [];

        // Separate flags into two groups - ones that use grid editor and ones that do not.
        foreach ($flags as $flag) {
            if ($flag->use_grid_editor) {
                $flagsWithGridEditor[$flag->getKey()] = $flag;
            } else {
                $flagsWithoutGridEditor[$flag->getKey()] = $flag;
            }
        }

        $articles = collect([]);

        // Find articles that use grid editor and match the search term.
        if ($flagsWithGridEditor) {
            $articlesWithGrid = Article::whereLanguage($language)->published()
                ->whereIn('flag_id', array_keys($flagsWithGridEditor))
                ->with([
                    'contents' => function (HasMany $query): void {
                        $query->where('is_active', true);
                    },
                ])
                ->get();

            $results = self::searchGridContent(
                $term, $articlesWithGrid, $language,
                function (Article $article) use ($term) {
                    return $article->matchesSearchTerm($term);
                }
            );

            $articles = $articles->merge($results);
        }

        // Find articles that do not use grid editor and match the search term.
        if ($flagsWithoutGridEditor) {
            $articlesWithoutGrid = Article::whereLanguage($language)->published()
                ->whereIn('flag_id', array_keys($flagsWithoutGridEditor))
                ->get();

            foreach ($articlesWithoutGrid as $article) {
                $article->setRelation('flag', $flagsWithoutGridEditor[$article->flag_id]);
                if ($article->matchesSearchTerm($term) ||
                    mb_stripos(strip_tags($article->getRawContent()), $term) !== false
                ) {
                    $articles->push($article);
                }
            }
        }

        return $articles;
    }


    /**
     * Check if article matches search term on common fields.
     *
     * @internal
     * @param string $term
     * @return bool
     */
    private function matchesSearchTerm(string $term): bool
    {
        return mb_stripos($this->title, $term) !== false ||
            mb_stripos($this->seo_title, $term) !== false ||
            mb_stripos($this->perex, $term) !== false;
    }


    /**
     * Has model single url?
     *
     * @return bool
     */
    public function workWithSingleUrl(): bool
    {
        return !$this->flag->should_bound_articles_to_category;
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
     * Get allowed MIME types for related video via attribute `video_id`.
     *
     * @return string[]
     */
    public static function getAllowedVideoMimeTypes(): array
    {
        return ['video/mp4', 'video/webm', 'video/ogg'];
    }
}
