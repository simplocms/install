<?php

namespace App\Traits;

use App\Models\Web\Url;
use App\Observers\UrlObserver;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasUrl
 * @package App\Traits
 * @mixin \Illuminate\Database\Eloquent\Model
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property-read string full_url
 */
trait HasUrl
{
    /**
     * Cached full url address of the model.
     * @var string
     */
    private $fullUrlCached;

    /**
     * @var \App\Models\Web\Url
     */
    protected $activeParentUrl;

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

        return $this->fullUrlCached = SingletonEnum::urlFactory()->getFullModelUrl(
            get_class($this), $this->{$this->primaryKey}
        );
    }


    /**
     * Get active row of URL record for the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getUrlScope()
    {
        return Url::query()->where('model', self::class)
            ->where('model_id', $this->getOriginal($this->primaryKey));
    }


    /**
     * Find the model by url address.
     *
     * @param string $url
     * @return self|null
     */
    static function findByUrl($url)
    {
        return self::query()->where('url', $url)->first();
    }


    /**
     * Create model's url.
     *
     * @return void
     */
    public function createUrls()
    {
        foreach ($this->getUrlsData() as $data) {
            Url::query()->create($data);
        }
    }


    /**
     * Update model's url.
     *
     * @return void
     */
    public function updateUrls()
    {
        if ($this->workWithSingleUrl()) {
            $existingUrls = $this->getUrlScope()->get()->keyBy('url');
            /** @var \App\Models\Web\Url $originalUrl */
            $originalUrl = $existingUrls->first();
            $urlData = $this->getUrlsData()[0];

            if (!$originalUrl) {
                $this->createUrls();
                return;
            }

            // Redirect is handled by Url model in this case.
            $originalUrl->changeUrl($urlData['url'], $this->canManipulateUrlSubordinates());
            $existingUrls->each(function (Url $url) use ($originalUrl): void {
                // delete all urls except original url, which was changed
                if ($url->getKey() !== $originalUrl->getKey()) {
                    $url->delete();
                }
            });
        } else {
            $slugs = $this->getUrlSlugs();
            $existingUrls = $this->getUrlScope()->get()->keyBy('url');
            $originalSlug = $this->getOriginal('url');
            $shouldUpdateUrls = $this->isDirty('url');

            foreach ($slugs as $index => $slug) {
                $originalUrl = is_null($slug['prefix']) ? $originalSlug : $slug['prefix'] . '/' . $originalSlug;
                $exist = $existingUrls->has($originalUrl);

                if ($exist) {
                    // Redirect is handled by Url model in this case.
                    if ($shouldUpdateUrls) {
                        $existingUrls->get($originalUrl)->changeUrl(
                            $slug['slug'], $this->canManipulateUrlSubordinates()
                        );
                    }

                    $existingUrls->forget($originalUrl);
                } else {
                    Url::query()->create($this->getUrlModelData($slug['slug']));
                }
            }

            $existingUrls->each(function (Url $url) {
                $url->delete();
            });
        }
    }


    /**
     * Delete model's url.
     *
     * @return void
     */
    public function deleteUrls()
    {
        if ($this->canManipulateUrlSubordinates()) {
            $originalUrl = $this->getUrlScope()->first();

            if ($originalUrl) {
                $this->getUrlScope()->delete();
                Url::query()->where('url', 'LIKE', "{$originalUrl->url}/%")->delete();
            }
        } else {
            $this->getUrlScope()->delete();
        }
    }


    /**
     * Restore model's url.
     *
     * @return void
     */
    public function restoreUrls()
    {
        $this->createUrls();
    }


    /**
     * Get data for Url model
     *
     * @return array[]
     */
    public function getUrlsData(): array
    {
        $urlsData = [];

        foreach ($this->getUrlSlugs() as $urlSlug) {
            $urlsData[] = $this->getUrlModelData($urlSlug['slug']);
        }

        return $urlsData;
    }


    /**
     * Get data for \App\Models\Web\Url model.
     *
     * @param string $urlSlug
     *
     * @return array
     */
    public function getUrlModelData(string $urlSlug): array
    {
        return [
            'url' => $urlSlug,
            'model' => self::class,
            'model_id' => $this->{$this->primaryKey}
        ];
    }


    /**
     * Friendlify the url attribute.
     *
     * @return void
     */
    public function friendlifyUrlAttribute()
    {
        $originalUrl = $this->getAttribute('url');
        $url = $this->getFriendlyUrlAttribute($originalUrl);
        $this->attributes['url'] = $this->getUniqueUrlAttribute($url);
    }


    /**
     * Get friendly url attribute.
     *
     * @param string $urlAttribute
     *
     * @return string
     */
    protected function getFriendlyUrlAttribute(string $urlAttribute): string
    {
        return str_slug($urlAttribute);
    }


    /**
     * Get unique url attribute for the model.
     *
     * @param string $urlAttributeBase - Base for the url attribute. Will be used if available.
     *
     * @return string
     */
    protected function getUniqueUrlAttribute(string $urlAttributeBase): string
    {
        $urlSlugs = $this->getUrlSlugs($urlAttributeBase);
        $existingUrls = $this->getExistingUrlConflicts($urlSlugs);

        $isUnique = $this->areUrlSlugsUnique($urlSlugs, $existingUrls);

        // Return prefered url if is not already used.
        if ($isUnique) {
            return $urlAttributeBase;
        }

        $tryNumber = 0;
        do {
            $tryNumber++;
            $uniqueUrlAttribute = $urlAttributeBase . '-' . $tryNumber;
            $urlSlugs = $this->getUrlSlugs($uniqueUrlAttribute);
            $isUnique = $this->areUrlSlugsUnique($urlSlugs, $existingUrls);
        } while (!$isUnique);

        return $uniqueUrlAttribute;
    }


    /**
     * Get existing url conflicts for the model.
     *
     * @param array[] $urlSlugs
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getExistingUrlConflicts(array $urlSlugs)
    {
        $query = Url::query();

        $query->where(function (Builder $query) use ($urlSlugs) {
            foreach ($urlSlugs as $urlSlug) {
                $query->orWhere('url', 'LIKE', "{$urlSlug['slug']}%");
            }
        });

        if ($this->exists && !$this->wasRecentlyCreated) {
            $query->where(function (Builder $query) {
                $query->where('model', '<>', self::class)
                    ->orWhere('model_id', '<>', $this->{$this->primaryKey});
            });
        }

        if ($this->workWithSingleUrl()) {
            return $query->pluck('id', 'url');
        }

        return $query->pluck('url');
    }


    /**
     * Check if specified url slugs are unique in specified collection of existing url.
     *
     * @param array $urlSlugs
     * @param \Illuminate\Support\Collection $existingUrls
     *
     * @return boolean
     */
    protected function areUrlSlugsUnique(array $urlSlugs, \Illuminate\Support\Collection $existingUrls): bool
    {
        if ($this->workWithSingleUrl()) {
            return !$existingUrls->has($urlSlugs[0]['slug']);
        }

        $slugs = array_map(function (array $slugData): string {
            return $slugData['slug'];
        }, $urlSlugs);

        return $existingUrls->intersect($slugs)->isEmpty();
    }


    /**
     * Get superior models that influence url address.
     *
     * @return \App\Models\Interfaces\UrlInterface[]
     */
    public function getUrlSuperiorModels(): array
    {
        return [];
    }


    /**
     * Get model's url slug.
     *
     * @param string $urlAttribute
     *
     * @return array[]
     */
    public function getUrlSlugs(string $urlAttribute = null): array
    {
        $superiorModels = $this->getUrlSuperiorModels();

        if (!$superiorModels) {
            return [$this->createUrlSlug($urlAttribute)];
        }

        $urlSlugs = [];

        /** @var \App\Models\Interfaces\UrlInterface $superiorModel */
        foreach ($superiorModels as $superiorModel) {
            $superiorUrlSlugs = $superiorModel->getUrlSlugs();

            foreach ($superiorUrlSlugs as $superiorUrlSlug) {
                $rrs = $this->createUrlSlug($urlAttribute, $superiorUrlSlug['slug']);
                $urlSlugs[] = $rrs;
            }
        }

        return $urlSlugs;
    }


    /**
     * Get model's url slug.
     *
     * @param string $prefix
     * @param string|null $urlAttribute
     *
     * @return string[]
     */
    public function createUrlSlug(string $urlAttribute = null, string $prefix = null): array
    {
        if (is_null($urlAttribute)) {
            $urlAttribute = $this->getAttribute('url');
        }

        if (is_null($prefix)) {
            $prefix = $this->getUrlPrefix();
        }

        return [
            'prefix' => $prefix,
            'suffix' => $urlAttribute,
            'slug' => is_null($prefix) ? $urlAttribute : $prefix . '/' . $urlAttribute
        ];
    }


    /**
     * Get prefix of url.
     *
     * @return string|null
     */
    public function getUrlPrefix()
    {
        if ($this->language) {
            return $this->language->language_code;
        }

        return null;
    }


    /**
     * Has model subordinate urls?
     *
     * @return bool
     */
    public function hasSubordinatesOfUrl(): bool
    {
        return $this->hasUrlSubordinates ?? false;
    }


    /**
     * Can model automatically manipulate subordinate urls?
     *
     * @return bool
     */
    public function canManipulateUrlSubordinates(): bool
    {
        return $this->hasSubordinatesOfUrl() && $this->workWithSingleUrl();
    }


    /**
     * Has model single url?
     *
     * @return bool
     */
    public function workWithSingleUrl(): bool
    {
        return $this->hasSingleUrl ?? false;
    }


    /**
     * Should url be updated?
     *
     * @return bool
     */
    public function shouldUpdateUrls(): bool
    {
        return $this->isDirty('url');
    }


    /**
     * Sync urls manually?
     *
     * @return bool
     */
    public function syncUrlsManually(): bool
    {
        return $this->manualUrlsSync ?? false;
    }


    /**
     * Set active parent url.
     *
     * @param \App\Models\Web\Url $url
     */
    public function setActiveParentUrl(Url $url): void
    {

    }


    /**
     * Register url observer.
     */
    public static function registerUrlObserver(): void
    {
        $className = UrlObserver::class . '@';

        foreach (['creating', 'updating', 'created', 'updated', 'deleted', 'restored'] as $event) {
            static::registerModelEvent($event, $className . $event);
        }
    }
}
