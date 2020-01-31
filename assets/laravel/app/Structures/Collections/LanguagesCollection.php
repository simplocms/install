<?php

namespace App\Structures\Collections;

use App\Helpers\UrlFactory;
use App\Models\Web\Language;
use Illuminate\Support\Collection;

final class LanguagesCollection extends Collection
{
    /**
     * @var \App\Models\Web\Language[]
     */
    protected $items = [];

    /**
     * @var int[]
     */
    private $languageIdsByLanguageCodes;

    /**
     * @var \App\Models\Web\Language
     */
    private $defaultLanguage;

    /**
     * @var \App\Models\Web\Language
     */
    private $contentLanguage;

    /**
     * @var bool
     */
    private $isContentLanguageFallback;

    /**
     * Initialize collection of languages.
     */
    public function initialize(): void
    {
        $this->languageIdsByLanguageCodes = [];

        /** @var \App\Models\Web\Language[]|\Illuminate\Database\Eloquent\Collection $languages */
        $languages = Language::enabled()->get();

        foreach ($languages as $language) {
            if ($language->default) {
                $this->defaultLanguage = $language;
            }

            $this->items[$language->getKey()] = $language;
            $this->languageIdsByLanguageCodes[$language->language_code] = $language->getKey();
        }

        // fallback
        if (!$this->defaultLanguage) {
            $this->defaultLanguage = $this->first();
        }
    }


    /**
     * @param string $code
     * @return \App\Models\Web\Language|null
     */
    public function findByCode(string $code): ?Language
    {
        $id = $this->languageIdsByLanguageCodes[$code] ?? null;
        return $id ? $this->get($id) : null;
    }


    /**
     * Get default language.
     *
     * @return \App\Models\Web\Language
     */
    public function getDefault(): Language
    {
        return $this->defaultLanguage;
    }


    /**
     * Get content language.
     *
     * @return \App\Models\Web\Language
     */
    public function getContentLanguage(): Language
    {
        if ($this->contentLanguage) {
            return $this->contentLanguage;
        }

        $this->isContentLanguageFallback = false;
        if (is_null(request()->route()) || in_array('admin', request()->route()->computedMiddleware)) {
            $languageId = \Session::get('language', null);
            $this->contentLanguage = $this->get($languageId);
        } else {
            $this->contentLanguage = UrlFactory::resolveLanguage(request()->getRequestUri());
        }

        if (!$this->contentLanguage) {
            $this->contentLanguage = $this->getDefault();
            $this->isContentLanguageFallback = true;
        }

        return $this->contentLanguage;
    }


    /**
     * Change content language.
     *
     * @param \App\Models\Web\Language $language
     */
    public function changeContentLanguage(Language $language): void
    {
        $this->contentLanguage = $language;
    }


    /**
     * Is content language fallback?
     *
     * @return bool
     */
    public function isContentLanguageFallback(): bool
    {
        return $this->isContentLanguageFallback;
    }
}
