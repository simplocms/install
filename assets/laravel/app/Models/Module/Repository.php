<?php

namespace App\Models\Module;

use Nwidart\Modules\Json;

class Repository extends \Nwidart\Modules\FileRepository
{
    /**
     * Get & scan all modules.
     *
     * @return array
     */
    public function scan()
    {
        $paths = $this->getScanPaths();
        $modules = [];
        foreach ($paths as $key => $path) {
            $manifests = $this->app['files']->glob("{$path}/module.json");
            is_array($manifests) || $manifests = [];
            foreach ($manifests as $manifest) {
                $name = Json::make($manifest)->get('name');
                $modules[$name] = new Module($this->app, $name, dirname($manifest));
            }
        }
        return $modules;
    }


    /**
     * Find installed module.
     *
     * @param string $name
     * @return self|null
     */
    static function findInstalled($name) {
        $installed = InstalledModule::findNamed($name);
        return $installed ? $installed->module : null;
    }


    /**
     * Is module enabled?
     *
     * @param string $name
     * @return boolean
     */
    static function isEnabled($name) {
        $installed = InstalledModule::findNamed($name);
        return $installed ? $installed->enabled : false;
    }

    
    /**
     * {@inheritdoc}
     */
    protected function createModule(...$args)
    {
        return new Module(...$args);
    }


    /**
     * Register the modules.
     */
    public function register()
    {
        /** @var \Nwidart\Modules\Module $module */
        foreach ($this->all() as $module) {
            $module->register();
        }
    }

    /**
     * Boot the modules.
     */
    public function boot()
    {
        /** @var \Nwidart\Modules\Module $module */
        foreach ($this->all() as $module) {
            $module->boot();
        }
    }
}
