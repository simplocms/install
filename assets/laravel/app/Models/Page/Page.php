<?php

namespace App\Models\Page;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\ProvidesBreadcrumbsInterface;
use App\Contracts\PublishableModelInterface;
use App\Contracts\StructuredDataTypeInterface;
use App\Contracts\ViewableModelInterface;
use App\Events\PageSaving;
use App\Models\Interfaces\UrlInterface;
use App\Models\Media\File;
use App\Models\Web\Language;
use App\Models\Web\ViewData;
use App\Services\FrontWebTools\ToolbarOptions;
use App\Services\Pages\ABTestingScope;
use App\Structures\Collections\BreadcrumbsCollection;
use App\Structures\DataTypes\Breadcrumb;
use App\Structures\Enums\SingletonEnum;
use App\Structures\StructuredData\Types\TypeWebPage;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\MediaImageTrait;
use App\Traits\OpenGraphTrait;
use App\Traits\PlannedPublishingTrait;
use Baum\Node;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUrl;
use App\Models\Interfaces\UsesGridEditor;
use App\Traits\GridEditor\GridEditorModel;
use App\Traits\HasLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;

/**
 * Class Page
 * @package App\Models\Page
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property int language_id
 * @property string name
 * @property string|null view
 * @property int|null image_id
 * @property bool published
 * @property string|null url
 * @property string|null seo_title
 * @property string|null seo_description
 * @property bool seo_index
 * @property bool seo_follow
 * @property bool seo_sitemap
 * @property bool is_homepage
 * @property int|null parent_id
 * @property int|null testing_a_id
 * @property int|null testing_b_id
 * @property int|null depth
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 * @property \Carbon\Carbon deleted_at
 *
 * @property-read \App\Models\Page\Content[]|\Illuminate\Database\Eloquent\Collection contents
 * @property-read \App\Models\Page\Page|null parent
 * @property-read \App\Models\Media\File|null image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Page\Page[] children
 * @property-read \App\Models\Page\Page|null testingVariantA
 * @property-read \App\Models\Page\Page|null testingVariantB
 *
 * @method static \Illuminate\Database\Eloquent\Builder published()
 * @method static \Illuminate\Database\Eloquent\Builder withTestingCounterparts()
 * @method static \Illuminate\Database\Eloquent\Builder withoutTestingCounterparts()
 * @method static \Illuminate\Database\Eloquent\Builder withoutTestingInvolved()
 * @method static \Illuminate\Database\Eloquent\Builder onlyTestingCounterparts()
 */
class Page extends Node implements
    UrlInterface,
    UsesGridEditor,
    ViewableModelInterface,
    PublishableModelInterface,
    ProvidesBreadcrumbsInterface,
    ConvertableToStructuredDataInterface
{
    use SoftDeletes,
        AdvancedEloquentTrait,
        GridEditorModel,
        HasLanguage,
        MediaImageTrait,
        PlannedPublishingTrait,
        OpenGraphTrait;
    use HasUrl {
        createUrls as protected createUrlsByTrait;
        updateUrls as protected updateUrlsByTrait;
        friendlifyUrlAttribute as protected friendlifyUrlAttributeByTrait;
    }

    /**
     * Mass assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'published', 'parent_id', 'url', 'view', 'image_id',
        'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap',
        'publish_at', 'unpublish_at', 'is_homepage', 'open_graph',
        'testing_a_id', 'testing_b_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'publish_at', 'unpublish_at'];

    /**
     * Use grid editor versions? For GridEditorModel trait.
     *
     * @var bool
     */
    protected $useGridEditorVersions = true;

    /**
     * Indicates model has subordinate urls.
     *
     * @var bool
     */
    protected $hasUrlSubordinates = false;

    /**
     * Attributes that are set tu null when they receive empty value.
     *
     * @var array
     */
    protected $nullIfEmpty = [
        'seo_title', 'seo_description', 'image_id', 'content', 'view', 'parent_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'parent_id' => 'int',
        'image_id' => 'int',
        'testing_a_id' => 'int',
        'testing_b_id' => 'int',
        'seo_index' => 'boolean',
        'seo_follow' => 'boolean',
        'seo_sitemap' => 'boolean',
        'is_homepage' => 'boolean',
        'published' => 'boolean',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saving' => PageSaving::class
    ];

    /**
     * Indicates model has single url. For GridEditorModel trait.
     *
     * @var bool
     */
    protected $hasSingleUrl = true;

    /**
     * Cached breadcrumbs.
     *
     * @var \App\Structures\Collections\BreadcrumbsCollection
     */
    protected $cachedBreadcrumbs;

    /**
     * Page constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->hasUrlSubordinates = !config('admin.simple_page_url');
        parent::__construct($attributes);
    }


    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ABTestingScope());
    }


    /**
     * Contents - versions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Content::class, 'page_id', 'id');
    }


    /**
     * Parent page.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Testing variant B (counterpart).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testingVariantB(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'testing_b_id');
    }

    /**
     * Tested page - variant A.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testingVariantA(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'testing_a_id');
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
     * Get default page.
     *
     * @param \App\Models\Web\Language|int $language
     * @return \App\Models\Page\Page|null
     */
    public static function getHomepage($language): ?Page
    {
        return Page::query()->where('is_homepage', 1)
            ->whereLanguage($language)
            ->first();
    }


    /**
     * Update page's url.
     *
     * @return void
     */
    public function updateUrls()
    {
        if ($this->is_homepage) {
            $this->deleteUrls();
        } else {
            $this->updateUrlsByTrait();
        }
    }


    /**
     * Create page's url.
     *
     * @return void
     */
    public function createUrls()
    {
        if (!$this->is_homepage) {
            $this->createUrlsByTrait();
        }
    }


    /**
     * Friendlify the url attribute.
     *
     * @return void
     */
    public function friendlifyUrlAttribute()
    {
        if (!$this->is_homepage) {
            $this->friendlifyUrlAttributeByTrait();
        }
    }


    /**
     * Get tree of categories. Article can be specified for pre-selecting categories.
     *
     * @param \Illuminate\Database\Eloquent\Builder|null $query
     * @param array $used
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getTree($query = null, array $used = []): Collection
    {
        $pages = is_null($query) ? self::all() : $query->get()->toHierarchy();
        return self::recursivePageTree($pages, $used);
    }


    /**
     * Recursively build category tree
     *
     * @param \App\Models\Page\Page[] $pages
     * @param int[] $used
     *
     * @return \Illuminate\Support\Collection
     */
    protected static function recursivePageTree($pages, array $used): Collection
    {
        $result = collect([]);

        foreach ($pages as $page) {
            $value = (object)[
                'key' => $page->getKey(),
                'title' => $page->name,
            ];

            if (in_array($page->getKey(), $used)) {
                $value->selected = true;
            }

            if ($page->children) {
                $value->expanded = true;
                $value->children = self::recursivePageTree($page->children, $used);
            }

            $result->push($value);
        }

        return $result;
    }


    /**
     * Get view data - returns data supposed for web page.
     *
     * @param \App\Models\Web\ViewData|null $data
     *
     * @return \App\Models\Web\ViewData
     */
    public function getViewData(ViewData $data): ViewData
    {
        $data->title = $this->seo_title ?: $this->name;
        $data->description = $this->seo_description;
        $data->index = $this->seo_index;
        $data->follow = $this->seo_follow;

        $data->og_title = $this->open_graph->get('title', $data->title);
        $data->og_description = $this->open_graph->get('description', $this->seo_description);
        $data->og_type = $this->open_graph->get('type', 'website');

        if ($this->isTestingCounterpart()) {
            $variantA = $this->testingVariantA;
            $data->og_url = $variantA->open_graph->get('url') ?? $variantA->full_url;
        } else {
            $data->og_url = $this->open_graph->get('url') ?? $this->full_url;
        }

        if ($this->open_graph->hasImage()) {
            $data->og_image = $this->open_graph->makeImageLink()->getUrl();
        } elseif ($this->hasImage('image')) {
            $data->og_image = $this->makeImageLink('image')->getUrl();
        }

        return $data;
    }


    /**
     * Replicate page with all its dependencies.
     *
     * @param array $attributes
     * @return \App\Models\Page\Page
     * @throws \App\Exceptions\GridEditorException
     */
    public function replicateFull(array $attributes = []): Page
    {
        $copyNameSuffix = trans(
            'admin/pages/general.page_duplicate_name_suffix', [], $this->language->language_code
        );

        /** @var Page $newPage */
        $newPage = $this->replicate();
        $newPage->forceFill(
            \array_merge([
                'is_homepage' => false,
                'name' => $newName = $newPage->name . ' - ' . $copyNameSuffix,
                'url' => $this->is_homepage ? $newName : $newPage->url
            ], $attributes)
        );
        $newPage->save();

        // Parent page.
        if ($newPage->parent_id) {
            $newPage->makeChildOf(Page::findOrFail($newPage->parent_id));
        } else {
            $newPage->makeRoot();
        }

        // Versions.
        /** @var \App\Models\Page\Content $content */
        foreach ($this->contents as $content) {
            $content->replicateFull([
                'page_id' => $newPage->getKey()
            ]);
        }

        return $newPage;
    }


    /**
     * Check if page is public.
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        if (!$this->published) {
            return false;
        }

        return $this->isInPublicPeriod();
    }


    /**
     * Get superior models that influence url address.
     *
     * @return \App\Models\Interfaces\UrlInterface[]
     */
    public function getUrlSuperiorModels(): array
    {
        // Simple url
        if (config('admin.simple_page_url') || !$this->parent_id || $this->parent->is_homepage) {
            return [];
        }

        return [$this->parent];
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
                trans('admin/pages/general.status.published'), ToolbarOptions::STATUS_SUCCESS
            );
        } else {
            $options->addStatus(
                trans('admin/pages/general.status.unpublished'), ToolbarOptions::STATUS_DANGER
            );
        }

        $options->addControl(
            trans('admin/pages/general.frontweb_toolbar.btn_create'),
            route('admin.pages.create'),
            'plus-circle'
        )
            ->addControl(
                trans('admin/pages/general.frontweb_toolbar.btn_edit'),
                route('admin.pages.edit', $this->getKey()),
                'edit'
            );

        if (($isCounterpart = $this->isTestingCounterpart()) || $this->hasTestingCounterpart()) {
            $pageId = $isCounterpart ? $this->testing_a_id : $this->getKey();
            $cookie = $this->getABTestCookieName();

            $options->activateSwitch(
                'variant',
                [
                    'a' => 'A',
                    'b' => 'B'
                ]
            )
                ->setActive(Cookie::get($cookie, $isCounterpart ? 'b' : 'a'))
                ->setActionPost(route('admin.pages.ab_testing', $pageId));
        }
    }


    /**
     * Get full url address of the model.
     *
     * @return string
     * @throws \Exception
     */
    public function getFullUrlAttribute()
    {
        if ($this->fullUrlCached) {
            return $this->fullUrlCached;
        }

        if ($this->is_homepage) {
            return $this->fullUrlCached = SingletonEnum::urlFactory()->getHomepageUrl();
        }

        return $this->fullUrlCached = SingletonEnum::urlFactory()->getFullModelUrl(
            get_class($this), $this->{$this->primaryKey}
        );
    }


    /**
     * Get page content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->getActiveContent()->getHtml([
            'language' => $this->language
        ]);
    }


    /**
     * Get breadcrumbs of the model
     *
     * @return \App\Structures\DataTypes\Breadcrumb|\App\Structures\Collections\BreadcrumbsCollection
     */
    public function getBreadcrumbs(): BreadcrumbsCollection
    {
        if ($this->cachedBreadcrumbs) {
            return $this->cachedBreadcrumbs;
        }

        $breadcrumbs = $this->getAncestorsAndSelf()->map(static function (Page $page) {
            return new Breadcrumb($page->name, $page->full_url, $page);
        });

        if ($this->isTestingCounterpart()) {
            self::withoutScope(ABTestingScope::class, function () use ($breadcrumbs) {
                $breadcrumbs->push(new Breadcrumb($this->name, $this->testingVariantA->full_url, $this));
            });
        }

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
            'primaryImageOfPage' => $this->hasImage('image') ?
                $this->makeImageLink('image')->allowedFormats(['png', 'jpeg', 'gif'])->getUrl() : null,
            'inLanguage' => $this->language,
            'dateCreated' => $this->created_at,
            'dateModified' => $this->updated_at,
            'datePublished' => $this->publish_at,
            'expires' => $this->unpublish_at,
            'breadcrumb' => $this->getBreadcrumbs(),
            'description' => $this->seo_description,
            'headline' => $this->seo_title ?? $this->name,
        ]);
    }


    /**
     * Select only published pages.
     *
     * @param \Illuminate\Database\Eloquent\Builder|self $query
     */
    public function scopePublished($query)
    {
        $query->publishedByDate()
            ->where('published', true)
            ->whereNull('testing_a_id');
    }


    /**
     * Search in pages.
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

        $pages = Page::whereLanguage($language)->published()
            ->with([
                'contents' => function (HasMany $query): void {
                    $query->where('is_active', true);
                },
            ])
            ->get();

        return self::searchGridContent($term, $pages, $language, function (Page $page) use ($term) {
            return mb_stripos($page->name, $term) !== false || mb_stripos($page->seo_title, $term) !== false;
        });
    }

    /**
     * Check if page is testing counterpart (for variant B)
     *
     * @return bool
     */
    public function isTestingCounterpart(): bool
    {
        return $this->testing_a_id !== null;
    }

    /**
     * Check if page has testing counterpart (for variant A)
     *
     * @return bool
     */
    public function hasTestingCounterpart(): bool
    {
        return $this->testing_b_id !== null;
    }

    /**
     * @return \App\Models\Page\Page
     */
    public function getTestingVariantB(): Page
    {
        /** @var \App\Models\Page\Page $variantB */
        $variantB = $this->testingVariantB()->withTestingCounterparts()->first();
        $variantB->setRelation('testingVariantA', $this); // optimization
        return $variantB;
    }

    /**
     * Before rendering page, apply AB testing if on.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Page\Page
     */
    public function beforeRender(Request $request): Page
    {
        $renderModel = $this;

        if ($this->hasTestingCounterpart()) {
            $cookie = $this->getABTestCookieName();
            $prefer = $request->cookie($cookie);

            try {
                if ($prefer === 'b' || (empty($prefer) && \random_int(0, 1))) {
                    $renderModel = $this->getTestingVariantB();
                    $prefer = 'b';
                } else {
                    $prefer = 'a';
                }
            } catch (\Exception $e) {
                $prefer = 'a';
            }

            $AB_Testing = [
                'uid' => $this->getKey() . '/' . $prefer,
                'name' => $this->name,
                'version' => $prefer
            ];

            SingletonEnum::responseManager()->injectView(
                view('vendor._ab_testing_events', compact('AB_Testing'))
            );

            Cookie::queue($cookie, $prefer, 43200);
        }

        return $renderModel;
    }

    /**
     * @return string
     */
    private function getABTestCookieName(): string
    {
        $cookiePrefix = config('cms.cookie_prefix');
        return $cookiePrefix . 'abt_' . $this->getKey();
    }
}
