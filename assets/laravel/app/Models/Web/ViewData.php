<?php

namespace App\Models\Web;

use App\Services\ResponseManager\Link;
use App\Structures\Enums\SingletonEnum;
use App\Structures\StructuredData\Serializer;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class ViewData
 * @package App\Models\Web
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string|null title
 * @property string|null description
 * @property string|null keywords
 * @property \App\Models\Web\Language language
 * @property \App\Models\Web\Theme theme
 * @property bool index
 * @property bool follow
 * @property string|null og_title
 * @property string|null og_type
 * @property string|null og_url
 * @property string|null og_description
 * @property string|null og_image
 * @property array|null custom_meta
 * @property-write string canonical
 */
class ViewData extends Model
{
    use AdvancedEloquentTrait;

    /** @var \App\Contracts\ViewableModelInterface */
    protected $currentModel;

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'keywords', 'language', 'theme', 'index', 'follow',
        'og_title', 'og_type', 'og_url', 'og_description', 'og_image', 'custom_meta'
    ];

    /**
     * Default values for attributes.
     *
     * @var array
     */
    protected $defaults = [];

    /**
     * The attributes that are set to null when the value is empty
     *
     * @var array
     */
    protected $nullIfEmpty = [
        'title', 'description', 'keywords',
        'og_title', 'og_type', 'og_url', 'og_description', 'og_image'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'index' => 'boolean',
        'follow' => 'boolean'
    ];


    /**
     * Set defaults.
     *
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }


    /**
     * Return default value if attribute does not exist.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (is_null($value) && isset($this->defaults[$key])) {
            return $this->defaults[$key];
        }

        return $value;
    }


    /**
     * Get robots meta tag content.
     *
     * @return string
     */
    public function getRobotsMeta(): string
    {
        $index = $this->index === false || !SingletonEnum::responseManager()->shouldIndex() ? 'NOINDEX' : 'INDEX';
        $follow = $this->follow === false || !SingletonEnum::responseManager()->shouldFollow() ? 'NOFOLLOW' : 'FOLLOW';

        return "$index, $follow";
    }


    /**
     * Get meta tags.
     *
     * @return array
     */
    public function getMetaTags(): array
    {
        return array_filter([
                'twitter:card' => 'summary',
                'twitter:site' => SingletonEnum::settings()->get('twitter_account'),
                'description' => $this->description,
                'og:title' => $this->og_title,
                'og:type' => $this->og_type ?? 'website',
                'og:url' => $this->og_url ?? null,
                'og:description' => $this->og_description,
                'og:image' => $this->og_image,
                'robots' => $this->getRobotsMeta(),
                'generator' => 'simploCMS; https://www.simplocms.com/',
            ] + ($this->custom_meta ?? []));
    }


    /**
     * Get title attribute.
     *
     * @return string
     */
    public function getTitleAttribute(): string
    {
        return $this->getTitle('seo_title', config('app.default_settings.seo_title'));
    }


    /**
     * Get title attribute.
     *
     * @return string
     */
    public function getOgTitleAttribute(): string
    {
        return $this->getTitle('og_title', config('app.default_settings.og_title'));
    }


    /**
     * Get seo description attribute.
     *
     * @return string
     */
    public function getDescriptionAttribute(): ?string
    {
        $description = $this->attributes['description'] ?? null;
        if ($description) {
            return $description;
        }

        return SingletonEnum::settings()->getDictionary(
            'seo_description', $this->defaults['description'] ?? null
        );
    }


    /**
     * Get OpenGraph description attribute.
     *
     * @return string
     */
    public function getOgDescriptionAttribute(): ?string
    {
        $description = $this->attributes['og_description'] ?? null;
        if ($description) {
            return $description;
        }

        return SingletonEnum::settings()->getDictionary(
            'og_description', $this->description ?? $this->defaults['og_description']  ?? null
        );
    }


    /**
     * Get og image url attribute.
     *
     * @return string
     */
    public function getOgImageAttribute(): string
    {
        $imageUrl = $this->attributes['og_image'] ?? null;
        if ($imageUrl) {
            return $imageUrl;
        }

        return SingletonEnum::settings()->makeImageLink('og_image')->getUrl();
    }


    /**
     * @param string $url
     */
    public function setCanonicalAttribute(string $url)
    {
        SingletonEnum::responseManager()->addLink(new Link($url, 'canonical'));
    }


    /**
     * Fill site name into given string.
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    protected function getTitle(string $key, string $default): string
    {
        $title = SingletonEnum::settings()->getDictionary($key, $default);

        if (Str::contains($title, '%site_name%')) {
            $language = SingletonEnum::languagesCollection()->getContentLanguage();
            $title = str_replace(
                '%site_name%',
                SingletonEnum::settings()->getDictionary(
                    'site_name',
                    trans('general.settings.site_name', [], $language->language_code)
                ),
                $title
            );
        }

        return str_replace('%title%', $this->attributes[$key] ?? $this->attributes['title'] ?? '--', $title);
    }


    /**
     * Set current model.
     *
     * @param mixed $model
     */
    public function setCurrentModel($model)
    {
        $this->currentModel = $model;
    }


    /**
     * Get structured data.
     *
     * @param array $data
     * @return string
     */
    public function getStructuredData(array $data = []): string
    {
        return Serializer::make($this->currentModel, $data);
    }
}
