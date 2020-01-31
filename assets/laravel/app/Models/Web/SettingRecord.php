<?php

namespace App\Models\Web;

use App\Events\SettingRecordDeleted;
use App\Events\SettingRecordSaved;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class SettingRecord
 * @package App\Models\Web
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string name
 * @property string value
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 */
class SettingRecord extends Model
{
    /**
     * Settings table
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = ['name', 'value'];


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Cache record when is retrieved from DB
        self::retrieved(function (SettingRecord $record) {
            $record->storeCache();
        });

        // Cache record when is saved (created/updated)
        self::saved(function (SettingRecord $record) {
            $record->storeCache();
            event(new SettingRecordSaved($record));
        });

        // Forget cache when is deleted
        self::deleted(function (SettingRecord $record) {
            self::forgetCache($record->name);
            event(new SettingRecordDeleted($record));
        });
    }


    /**
     * Store record into the cache.
     */
    protected function storeCache(): void
    {
        Cache::forever($this->getCacheKey(), $this->value);
    }


    /**
     * Get cache key.
     *
     * @return string
     */
    protected function getCacheKey(): string
    {
        return self::cacheKey($this->name);
    }


    /**
     * Get cache key for given record name.
     *
     * @param string $name
     * @return string
     */
    protected static function cacheKey(string $name): string
    {
        return "cms-settings::{$name}";
    }


    /**
     * Check if record is cached.
     *
     * @param string $name
     * @return bool
     */
    public static function isCached(string $name): bool
    {
        return Cache::has(self::cacheKey($name));
    }


    /**
     * Get record from the cache.
     *
     * @param string $name
     * @return string|null|false
     */
    protected static function getCache(string $name)
    {
        return Cache::get(self::cacheKey($name));
    }


    /**
     * Find settings record by its name.
     *
     * @param string $name
     * @return \App\Models\Web\SettingRecord|null
     */
    protected static function findNamed(string $name): ?SettingRecord
    {
        $record = self::query()->where('name', $name)->first();

        // Cache non-existing record.
        if (is_null($record)) {
            Cache::forever(self::cacheKey($name), false);
        }

        return $record;
    }


    /**
     * Get setting record value by its name.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        // Try to find in cache
        if (self::isCached($name)) {
            $cacheValue = self::getCache($name);
            return $cacheValue === false ? $default : $cacheValue;
        }

        return self::findNamed($name)->value ?? $default;
    }


    /**
     * Put setting record under given name.
     *
     * @param string $name
     * @param string $value
     */
    static function put(string $name, string $value)
    {
        $existingRecord = self::findNamed($name);
        if ($existingRecord) {
            $existingRecord->update([
                'value' => $value
            ]);
        } else {
            self::create([
                'name' => $name,
                'value' => $value
            ]);
        }
    }


    /**
     * Forget setting record with given name.
     *
     * @param string $name
     */
    public static function forget(string $name): void
    {
        $record = self::findNamed($name);
        if ($record) {
            $record->delete();
        }

        self::forgetCache($name);
    }


    /**
     * Forget cache value under given name.
     *
     * @param string $name
     */
    protected static function forgetCache(string $name): void
    {
        Cache::forever(self::cacheKey($name), false);
    }
}
