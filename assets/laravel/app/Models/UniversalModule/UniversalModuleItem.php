<?php

namespace App\Models\UniversalModule;

use App\Contracts\ViewableModelInterface;
use App\Models\Interfaces\UrlInterface;
use App\Models\Media\File;
use App\Models\Web\Language;
use App\Models\Web\ViewData;
use App\Services\FrontWebTools\ToolbarOptions;
use App\Services\UniversalModules\UniversalModule;
use App\Structures\Enums\SingletonEnum;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\HasLanguage;
use App\Traits\HasUrl;
use App\Traits\MediaImageTrait;
use App\Traits\OpenGraphTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UniversalModuleItem
 * @package App\Models\UniversalModule
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string prefix
 * @property string content
 * @property int order
 * @property int language_id
 * @property bool enabled
 * @property string|null url
 * @property string seo_title
 * @property string|null seo_description
 * @property bool seo_index
 * @property bool seo_follow
 * @property bool seo_sitemap
 *
 * @method static \Illuminate\Database\Eloquent\Builder|self ofPrefix(string $prefix)
 * @method static \Illuminate\Database\Eloquent\Builder|self enabled()
 */
class UniversalModuleItem extends Model implements UrlInterface, ViewableModelInterface
{
    use AdvancedEloquentTrait, MediaImageTrait, HasLanguage, OpenGraphTrait;
    use HasUrl {
        createUrls as protected createUrlsByTrait;
        updateUrls as protected updateUrlsByTrait;
        friendlifyUrlAttribute as protected friendlifyUrlAttributeByTrait;
    }

    /**
     * Fetched images of item (so each image is loaded only once).
     *
     * @var \App\Models\Media\File[]|bool[]
     */
    protected $fetchedFiles = [];

    /**
     * @var string
     */
    protected $table = "universal_module_items";

    /**
     * @var array
     */
    protected $fillable = [
        'prefix', 'order', 'enabled', 'url', 'open_graph',
        'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'order' => 'int',
        'language_id' => 'int',
        'enabled' => 'boolean',
        'seo_index' => 'boolean',
        'seo_follow' => 'boolean',
        'seo_sitemap' => 'boolean',
    ];

    /**
     * Get all items.
     *
     * @param string $prefix
     * @param \App\Models\Web\Language $language
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAll(string $prefix, Language $language): Collection
    {
        return self::getAllQuery($prefix, $language)->get();
    }


    /**
     * Get all items query.
     *
     * @param string $prefix
     * @param \App\Models\Web\Language $language
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getAllQuery(string $prefix, Language $language)
    {
        $module = SingletonEnum::universalModules()->findOrFail($prefix);
        $query = self::ofPrefix($prefix);

        // Ordering
        if ($module->isAllowedOrdering()) {
            $query->orderBy('order');
        }

        // Multilangual apart
        if ($module->isMultilangualApart()) {
            $query->where('language_id', $language->getKey());
        }

        return $query;
    }


    /**
     * Return unserialized data from content.
     *
     * @return array
     */
    protected function getUnserializedContent(): array
    {
        return unserialize($this->content) ?: [];
    }


    /**
     * Return unserialized data from content attribute.
     *
     * @param \App\Models\Web\Language|null $language
     * @return array
     */
    public function getContent(?Language $language = null): array
    {
        $content = $this->getUnserializedContent();
        if ($this->getModule()->isMultilangualLocalizable()) {
            if (!$language) {
                $language = SingletonEnum::languagesCollection()->getContentLanguage();
            }

            return $content[$language->language_code] ?? array_first($content) ?? [];
        }

        return $content;
    }


    public function getName(?Language $language = null): string
    {
        $content = $this->getContent();
        return $content['name'] ?? 'Missing name';
    }


    /**
     * Get attribute from unserialized content.
     *
     * @param string $attribute
     * @param \App\Models\Web\Language|null $language
     * @return mixed
     */
    public function getAttributeOfContent(string $attribute, ?Language $language = null)
    {
        $content = $this->getContent($language);
        return $content[$attribute] ?? null;
    }


    /**
     * Set content value.
     *
     * @param array $content
     * @param \App\Models\Web\Language $language
     */
    public function setContent(array $content, Language $language)
    {
        $currentContent = $this->getUnserializedContent();
        if ($this->getModule()->isMultilangualLocalizable()) {
            $currentContent[$language->language_code] = $content;
        } else {
            $currentContent = $content;
        }

        $this->attributes['content'] = serialize($currentContent);
    }


    /**
     * Get universal module.
     *
     * @return \App\Services\UniversalModules\UniversalModule
     */
    public function getModule(): UniversalModule
    {
        return SingletonEnum::universalModules()->findOrFail($this->prefix);
    }


    /**
     * Check if given attribute is image.
     *
     * @param string $key
     * @return bool
     */
    public function isAttributeImage(string $key): bool
    {
        return $this->getModule()->hasImage($key);
    }


    /**
     * Get instance of image file.
     * Placeholder is going to be returned when fetched file is null OR is not image!
     *
     * Use only when generating image preview.
     *
     * @param string $key
     * @param \App\Models\Web\Language|null $language
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function makeImageLink(string $key, ?Language $language = null): \App\Services\MediaLibrary\ImageBuilder
    {
        $file = $this->hasImage($key, $language) ? $this->getFile($key, $language) : File::imagePlaceholder();
        return $file->makeLink();
    }


    /**
     * Has image?
     *
     * @param string $key
     * @param \App\Models\Web\Language|null $language
     * @return bool
     */
    public function hasImage(string $key, ?Language $language = null): bool
    {
        $file = $this->getFile($key, $language);
        return $file && $file->isSelectableImage();
    }


    /**
     * Fetch file.
     *
     * @param string $key
     * @param \App\Models\Web\Language|null $language
     * @return \App\Models\Media\File|null
     */
    public function getFile(string $key, ?Language $language = null): ?File
    {
        if (isset($this->fetchedFiles[$key])) {
            return $this->fetchedFiles[$key] ?: null;
        }

        $fileId = $this->getAttributeOfContent($key, $language);
        $this->fetchedFiles[$key] = ($fileId ? File::find($fileId) : null) ?? false;
        return $this->fetchedFiles[$key] ?: null;
    }


    /**
     * Select all items of specified prefix.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $prefix
     */
    public function scopeOfPrefix($query, string $prefix)
    {
        $query->where('prefix', $prefix);
    }


    /**
     * Toggle item.
     *
     * @return \App\Models\UniversalModule\UniversalModuleItem
     */
    public function toggle(): UniversalModuleItem
    {
        $this->enabled = !$this->enabled;
        return $this;
    }


    /**
     * Update item's url.
     *
     * @return void
     */
    public function updateUrls()
    {
        if ($this->getModule()->hasUrl()) {
            $this->updateUrlsByTrait();
        } else {
            $this->deleteUrls();
        }
    }


    /**
     * Create item's url.
     *
     * @return void
     */
    public function createUrls()
    {
        if ($this->getModule()->hasUrl()) {
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
        if ($this->getModule()->hasUrl()) {
            $this->friendlifyUrlAttributeByTrait();
        }
    }


    /**
     * Get full url address of the model.
     *
     * @return string
     * @throws \Exception
     */
    public function getFullUrl(): ?string
    {
        if (!$this->getModule()->hasUrl()) {
            return null;
        }

        if ($this->fullUrlCached) {
            return $this->fullUrlCached;
        }

        return $this->fullUrlCached = SingletonEnum::urlFactory()->getFullModelUrl(
            get_class($this), $this->{$this->primaryKey}
        );
    }


    /**
     * Get prefix of url.
     *
     * @return string|null
     */
    public function getUrlPrefix()
    {
        $module = $this->getModule();
        $prefix = $module->getUrlPrefix();

        if ($module->isMultilangualApart() && $this->language->language_code) {
            $prefix = $prefix === null ? $this->language->language_code : $this->language->language_code . '/' . $prefix;
        }

        return $prefix;
    }


    /**
     * @param \Illuminate\Database\Query\Builder $query
     */
    public function scopeEnabled($query): void
    {
        $query->where('enabled', 1);
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
        $data->og_url = $this->open_graph->get('url') ?? $this->full_url;

        if ($this->open_graph->hasImage()) {
            $data->og_image = $this->open_graph->makeImageLink()->getUrl();
        }

        return $data;
    }


    /**
     * Set options for front-web toolbar.
     *
     * @param \App\Services\FrontWebTools\ToolbarOptions $options
     */
    public function setFrontWebToolbarOptions(ToolbarOptions $options): void
    {
        if ($this->enabled) {
            $options->addStatus(
                trans('admin/universal_modules.status.enabled'), ToolbarOptions::STATUS_SUCCESS
            );
        } else {
            $options->addStatus(
                trans('admin/universal_modules.status.disabled'), ToolbarOptions::STATUS_DANGER
            );
        }

        $options->addControl(
            trans('admin/universal_modules.frontweb_toolbar.btn_create'),
            route('admin.universalmodule.create', ['prefix' => $this->prefix]),
            'plus-circle'
        )
            ->addControl(
                trans('admin/universal_modules.frontweb_toolbar.btn_edit'),
                route('admin.universalmodule.edit', ['prefix' => $this->prefix, 'id' => $this->getKey()]),
                'edit'
            );
    }
}
