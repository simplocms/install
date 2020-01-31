<?php

namespace App\Services\Settings;
use App\Models\Web\Language;

/**
 * Class Settings.
 * @package App\Services\Settings
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
class Collector
{
    /** @var \App\Services\Settings\Settings */
    protected $settings;

    /** @var string[]|\App\Models\Web\Language[] */
    protected $names;

    /** @var mixed[] */
    protected $defaults;

    /**
     * Collector constructor.
     * @param \App\Services\Settings\Settings $settingsInstance
     * @param array $names
     */
    public function __construct(Settings $settingsInstance, array $names)
    {
        $this->settings = $settingsInstance;
        $this->names = $names;
        $this->defaults = [];
    }

    /**
     * Get value from settings under specified name.
     *
     * @param string $name
     * @param null $default
     * @return \App\Services\Settings\Collector
     */
    public function get(string $name, $default = null): Collector
    {
        $this->names[] = $name;
        $this->setDefault($name, $default);
        return $this;
    }


    /**
     * Get dictionary value from settings under specified name.
     *
     * @param string $name
     * @param null $default
     * @return \App\Services\Settings\Collector
     */
    public function getDictionary(string $name, $default = null): Collector
    {
        $this->names[$name] = Settings::TYPE_DICTIONARY;
        $this->setDefault($name, $default);
        return $this;
    }


    /**
     * Get value for given language from dictionary in settings under specified name.
     *
     * @param string $name
     * @param \App\Models\Web\Language $language
     * @param null $default
     * @return \App\Services\Settings\Collector
     */
    public function getLocalized(string $name, Language $language, $default = null): Collector
    {
        $this->names[$name] = $language;
        $this->setDefault($name, $default);
        return $this;
    }


    /**
     * Get image value from settings under specified name.
     *
     * @param string $name
     * @return \App\Services\Settings\Collector
     */
    public function getImage(string $name): Collector
    {
        $this->names[$name] = Settings::TYPE_IMAGE;
        return $this;
    }


    /**
     * Get media file value from settings under specified name.
     *
     * @param string $name
     * @return \App\Services\Settings\Collector
     */
    public function getMediaFile(string $name): Collector
    {
        $this->names[$name] = Settings::TYPE_MEDIA_FILE;
        return $this;
    }


    /**
     * Get integer value from settings under specified name.
     *
     * @param string $name
     * @param int $default
     * @return \App\Services\Settings\Collector
     */
    public function getInt(string $name, int $default = null): Collector
    {
        $this->names[$name] = Settings::TYPE_INT;
        $this->setDefault($name, $default);
        return $this;
    }


    /**
     * Get boolean value from settings under specified name.
     *
     * @param string $name
     * @param bool $default
     * @return \App\Services\Settings\Collector
     */
    public function getBool(string $name, bool $default = null): Collector
    {
        $this->names[$name] = Settings::TYPE_BOOL;
        $this->setDefault($name, $default);
        return $this;
    }


    /**
     * Set default value.
     *
     * @param string $name
     * @param mixed $value
     */
    protected function setDefault(string $name, $value): void
    {
        if (!is_null($value)) {
            $this->defaults[$name] = $value;
        }
    }


    /**
     * Get all settings.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll()
    {
        return $this->settings->getAll($this->names)->map(function ($value, $name) {
            return $value ?? $this->defaults[$name] ?? null;
        });
    }
}
