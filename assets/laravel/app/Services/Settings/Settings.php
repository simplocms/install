<?php

namespace App\Services\Settings;

use App\Models\Media\File;
use App\Models\Web\Language;
use App\Models\Web\SettingRecord;
use App\Services\MediaLibrary\ImageBuilder;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\Collection;

/**
 * Class Settings
 * @package App\Services\Settings
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
class Settings
{
    const TYPE_INT = 'int';
    const TYPE_BOOL = 'bool';
    const TYPE_MEDIA_FILE = 'media_file';
    const TYPE_IMAGE = 'image';
    const TYPE_DICTIONARY = 'dictionary';

    /** @var \App\Models\Media\File[]|false[] */
    protected $fileCache;

    /**
     * Settings constructor.
     */
    public function __construct()
    {
        $this->fileCache = [];
    }


    /**
     * Get setting record value by its name.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return SettingRecord::get($name, $default);
    }


    /**
     * Get setting record value by its name.
     *
     * @param string $name
     * @param string $value
     */
    public function put(string $name, string $value): void
    {
        SettingRecord::put($name, $value);
    }


    /**
     * Get setting record value by its name.
     *
     * @param string $name
     */
    public function forget(string $name): void
    {
        SettingRecord::forget($name);
    }


    /**
     * Set multiple records value.
     *
     * <code>
     * [
     *   'name'  => 'value',
     *   'name2' => 'value2',
     * ];
     * </code>
     *
     * @param array[string]string $values
     */
    public function set(array $values): void
    {
        foreach ($values as $name => $value) {
            if (is_null($value)) {
                SettingRecord::forget($name);
            } else {
                SettingRecord::put($name, $value);
            }
        }
    }


    /**
     * Get setting record boolean value by its name.
     *
     * @param string $name
     * @param bool $default
     * @return bool
     */
    public function getBoolean(string $name, bool $default = false): bool
    {
        return boolval($this->get($name, $default));
    }


    /**
     * Get setting record integer value by its name.
     *
     * @param string $name
     * @param int $default
     * @return int
     */
    public function getInt(string $name, int $default = -1): int
    {
        return intval($this->get($name, $default));
    }


    /**
     * Get setting record integer value by its name.
     *
     * @param string $name
     * @param mixed $default
     * @param \App\Models\Web\Language|null $language
     * @return mixed
     */
    public function getDictionary(string $name, $default = null, Language $language = null)
    {
        if (is_null($language)) {
            $language = SingletonEnum::languagesCollection()->getContentLanguage();
        }

        return $this->get($name . '_' . $language->language_code, $default);
    }


    /**
     * Has image on specified settings record?
     *
     * @param string $name
     * @return bool
     */
    public function hasImage(string $name): bool
    {
        // image already cached
        if (!$this->hasMediaFile($name)) {
            return false;
        }

        return $this->getMediaFile($name)->isSelectableImage();
    }


    /**
     * Has image on specified settings record?
     *
     * @param string $name
     * @return bool
     */
    public function hasMediaFile(string $name): bool
    {
        // image already cached
        if (isset($this->fileCache[$name])) {
            return $this->fileCache[$name] !== false;
        }

        $id = $this->getInt($name, 0);
        if (!$id) {
            return false;
        }

        /** @var \App\Models\Media\File $image */
        $this->fileCache[$name] = File::findPrefetched($id) ?? false;
        return !!$this->fileCache[$name];
    }


    /**
     * Get file by its name in configuration?
     *
     * @param string $name
     * @return \App\Models\Media\File
     */
    public function getMediaFile(string $name): File
    {
        // file already cached
        if (isset($this->fileCache[$name]) && $this->fileCache[$name]) {
            return $this->fileCache[$name];
        }

        return $this->fileCache[$name] = File::findOrFail($this->getInt($name));
    }


    /**
     * Get instance of image builder.
     * Placeholder is going to be returned when relation returns null OR file that is not image!
     *
     * @param string $name
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function makeImageLink(string $name): ImageBuilder
    {
        $file = $this->hasImage($name) ? $this->getMediaFile($name) : File::imagePlaceholder();

        return $file->makeLink();
    }


    /**
     * Get all.
     *
     * @param string[]|\App\Models\Web\Language[] $names
     * @return \Illuminate\Support\Collection
     */
    public function getAll(array $names = []): Collection
    {
        if (!count($names)) {
            return SettingRecord::all()->pluck('value', 'name');
        }

        $result = collect([]);
        foreach ($names as $indexOrName => $nameOrCast) {
            if (is_numeric($indexOrName)) {
                $result->put($nameOrCast, $this->get($nameOrCast));
            } elseif ($nameOrCast instanceof Language) {
                $result->put($indexOrName, $this->getDictionary($indexOrName, null, $nameOrCast));
            } else {
                $result->put($indexOrName, $this->getCasted($indexOrName, $nameOrCast));
            }
        }

        return $result;
    }


    /**
     * Get settings collector instance.
     *
     * @param array $names
     * @return \App\Services\Settings\Collector
     */
    public function collect(array $names = []): Collector
    {
        return new Collector($this, $names);
    }


    /**
     * Get casted value under specified name to specified type.
     *
     * @param string $name
     * @param string $type
     * @param null $default
     * @return \App\Models\Media\File|bool|int|mixed|null
     */
    public function getCasted(string $name, string $type, $default = null)
    {
        $value = null;

        switch ($type) {
            case self::TYPE_INT:
                $value = $this->get($name);
                if (!is_null($value)) {
                    $value = intval($value);
                }
                break;
            case self::TYPE_BOOL:
                $value = $this->get($name);
                if (!is_null($value)) {
                    $value = boolval($value);
                }
                break;
            case self::TYPE_MEDIA_FILE:
                if ($this->hasMediaFile($name)) {
                    $value = $this->getMediaFile($name);
                }
                break;
            case self::TYPE_IMAGE:
                if ($this->hasImage($name)) {
                    $value = $this->getMediaFile($name);
                }
                break;
            case self::TYPE_DICTIONARY:
                $value = $this->getDictionary($name);
                break;
        }

        return $value ?? $default;
    }
}
