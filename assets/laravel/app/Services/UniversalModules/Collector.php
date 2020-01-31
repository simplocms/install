<?php
/**
 * Collector.php created by Patrik Václavek
 */

namespace App\Services\UniversalModules;


use App\Structures\Enums\SingletonEnum;

class Collector
{
    /**
     * @var \App\Services\UniversalModules\UniversalModule[]
     */
    protected $registeredModules;

    /**
     * Collector constructor.
     */
    public function __construct()
    {
        $this->registeredModules = [];
        $this->collect();
    }


    /**
     * Collect universal modules from configuration.
     */
    protected function collect()
    {
        $modules = $this->getFromConfiguration();

        foreach ($modules as $module) {
            $this->registeredModules[$module->getKey()] = $module;
        }
    }


    /**
     * Get configuration of universal modules.
     *
     * @return \App\Services\UniversalModules\UniversalModule[]
     */
    protected function getFromConfiguration(): array
    {
        $theme = SingletonEnum::theme();
        if (!$theme) {
            return [];
        }

        // TODO: this should not be necessary
        $theme->getContextInstance();

        return $theme->config('universal_modules', []);
    }


    /**
     * Is universal module registered?
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->registeredModules[$key]);
    }


    /**
     * Return universal module if exists, otherwise throw 404.
     *
     * @param string $key
     * @return \App\Services\UniversalModules\UniversalModule
     */
    public function findOrFail(string $key): UniversalModule
    {
        return $this->registeredModules[$key] ?? abort(404);
    }


    /**
     * Return universal module if exists.
     *
     * @param string $key
     * @return \App\Services\UniversalModules\UniversalModule|null
     */
    public function find(string $key): ?UniversalModule
    {
        return $this->registeredModules[$key] ?? null;
    }


    /**
     * Get all universal modules.
     *
     * @return \App\Services\UniversalModules\UniversalModule[]
     */
    public function all(): array
    {
        return $this->registeredModules;
    }
}
