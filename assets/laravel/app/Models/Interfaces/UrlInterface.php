<?php

namespace App\Models\Interfaces;
use App\Models\Web\Url;

/**
 * Interface UrlInterface
 * @package App\Models\Interfaces
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property-read string full_url
 */
interface UrlInterface {

    /**
     * Register url observer.
     */
    public static function registerUrlObserver(): void;

    /**
     * Get data for Url model
     *
     * @return array[]
     */
    public function getUrlsData(): array;

    /**
     * Get active row of URL record for the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getUrlScope();

    /**
     * Get full url address of the model.
     *
     * @return string
     */
    public function getFullUrlAttribute();

    /**
     * Create model's url.
     *
     * @return void
     */
    public function createUrls();

    /**
     * Update model's url.
     *
     * @return void
     */
    public function updateUrls();

    /**
     * Delete model's url.
     *
     * @return void
     */
    public function deleteUrls();

    /**
     * Restore model's url.
     *
     * @return void
     */
    public function restoreUrls();

    /**
     * Get model's url slugs.
     *
     * @param string $urlAttribute
     * 
     * @return array
     */
    public function getUrlSlugs(string $urlAttribute = null): array;

    /**
     * Friendlify url attribute.
     *
     * @return void
     */
    public function friendlifyUrlAttribute();

    /**
     * Should update url.
     *
     * @return bool
     */
    public function shouldUpdateUrls(): bool;

    /**
     * Synchronize urls manually?
     *
     * @return bool
     */
    public function syncUrlsManually(): bool;

    /**
     * Does the model work with single url address?
     *
     * @return bool
     */
    public function workWithSingleUrl(): bool;

    /**
     * Set active parent url.
     *
     * @param \App\Models\Web\Url $url
     */
    public function setActiveParentUrl(Url $url): void;
}
