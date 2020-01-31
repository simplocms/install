<?php
/**
 * FileCache.php created by Patrik Václavek
 */

namespace App\Services\MediaLibrary;

use App\Structures\Enums\SingletonEnum;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Intervention\Image\Image;

class FileCache
{
    /** @var string - Short path to the file */
    protected $path;

    /**
     * FileCache constructor.
     * @param string $path - short path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }


    /**
     * Helper method for chainability.
     *
     * @param string $path
     * @return \App\Services\MediaLibrary\FileCache
     */
    public static function forFile(string $path): FileCache
    {
        return new self($path);
    }


    /**
     * Register cache key.
     *
     * @param string $cacheKey
     */
    protected function registerCacheKey(string $cacheKey): void
    {
        $cacheMap = $this->getCacheMap();
        $cacheMap[$cacheKey] = time();

        resolve('cache')->forever($this->getCacheMapKey(), $cacheMap);
    }


    /**
     * Returns cache key if cache exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasCache(string $key): bool
    {
        $cacheMap = $this->getCacheMap();
        return isset($cacheMap[$this->getCacheKey($key)]);
    }


    /**
     * Get cache key for given key.
     *
     * @param string $key
     * @return string
     */
    protected function getCacheKey(string $key): string
    {
        return md5($key);
    }


    /**
     * Get name of cache file for given file.
     *
     * @param string $cacheKey - hash of the key
     * @return string
     */
    protected function getCacheFileName(string $cacheKey): string
    {
        return md5("{$this->path}-$cacheKey");
    }


    /**
     * Get cache map.
     *
     * @return array
     */
    protected function getCacheMap(): array
    {
        return resolve('cache')->get($this->getCacheMapKey()) ?? [];
    }


    /**
     * Get cache map key for specified file.
     *
     * @return string
     */
    protected function getCacheMapKey(): string
    {
        $fileKey = md5($this->path);
        return "media:$fileKey";
    }


    /**
     * Get image.
     *
     * @param string $key
     * @return \App\Services\MediaLibrary\AbstractProcessableFile|null
     */
    public function getCachedFile(string $key): ?AbstractProcessableFile
    {
        if ($this->hasCache($key)) {
            try {
                return SingletonEnum::mediaLibrary()->getCachedFile(
                    $this->getCacheFileName($this->getCacheKey($key))
                );
            } catch (\Exception $e) {
                // cached file is probably gone
            }
        }

        return null;
    }


    /**
     * Cache image.
     * Returns cached (+optimized) image.
     *
     * @param string $key
     * @param \App\Services\MediaLibrary\AbstractProcessableFile $file
     * @return \App\Services\MediaLibrary\AbstractProcessableFile
     */
    public function cacheFile(string $key, AbstractProcessableFile $file): AbstractProcessableFile
    {
        $mediaLibrary = SingletonEnum::mediaLibrary();
        $cacheKey = $this->getCacheKey($key);
        $cacheFile = $this->getCacheFileName($cacheKey);

        $mediaLibrary->storeFileToCache($cacheFile, $file);
        $this->registerCacheKey($cacheKey);
        return $mediaLibrary->getCachedFile($cacheFile);
    }


    /**
     * Clear file cache.
     */
    public function clearCache(): void
    {
        $cacheMap = $this->getCacheMap();

        if (!$cacheMap) {
            return;
        }

        foreach ($cacheMap as $cacheKey => $timestamp) {
            SingletonEnum::mediaLibrary()->removeCacheFile($this->getCacheFileName($cacheKey));
        }

        resolve('cache')->forget($this->getCacheMapKey());
    }


    /**
     * Move file cache.
     * @param string $path
     */
    public function moveCache(string $path): void
    {
        $cacheMap = $this->getCacheMap();

        if (!$cacheMap) {
            return;
        }

        $cache = new FileCache($path);

        foreach ($cacheMap as $cacheKey => $timestamp) {
            $success = SingletonEnum::mediaLibrary()->moveCacheFile(
                $this->getCacheFileName($cacheKey), $cache->getCacheFileName($cacheKey)
            );

            if ($success) {
                $cache->registerCacheKey($cacheKey);
            }
        }

        resolve('cache')->forget($this->getCacheMapKey());
    }
}
