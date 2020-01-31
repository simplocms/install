<?php

namespace App\Models\Module;

class Module extends \Nwidart\Modules\Module
{
    /**
     * @var bool
     */
    public static $searchRendering = false;

    /**
     * Get view
     *
     * @param  string $view
     * @param  array $data
     * @param  array $mergeData
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function view($view = null, $data = [], $mergeData = [])
    {
        return view($this->getViewName($view), $data, $mergeData);
    }


    /**
     * Get view name
     *
     * @param $view
     *
     * @return string
     */
    public function getViewName($view)
    {
        return $this->getNamespace() . "::" . $view;
    }


    /**
     * Get module namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return "module-" . strtolower($this->name);
    }


    /**
     * Generate the URL to a named route.
     *
     * @param  string $name
     * @param  array $parameters
     * @param  bool $absolute
     *
     * @return string
     */
    function route($name, $parameters = [], $absolute = true)
    {
        return route(
            'module.' . strtolower($this->name) . '.' . $name,
            $parameters,
            $absolute
        );
    }


    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to
     * set an array of values.
     *
     * @param  array|string $key
     * @param  mixed $default
     *
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $k = $this->getNamespace() . "." . $k;
            }
        } else {
            $key = $this->getNamespace() . "." . $key;
        }

        return config($key, $default);
    }


    /**
     * Translate the given message.
     *
     * @param  string $id
     * @param  string $replace
     * @param  string $locale
     *
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function trans($id = null, $replace = [], $locale = null)
    {
        return trans($this->getNamespace() . "::" . $id, $replace, $locale);
    }


    /**
     * Configuration class.
     * @return string|null Returns null if class does not exist.
     */
    public function getConfigurationClass()
    {
        $class = config('modules.namespace') . "\\" .
            $this->getName() . "\\" .
            config('modules.paths.generator.model') . "\\Configuration";

        return class_exists($class) ? $class : null;
    }


    /**
     * Get instance of specified model.
     *
     * @param string $modelName
     *
     * @return string|null Returns null if class does not exist.
     */
    public function getModel($modelName)
    {
        $class = config('modules.namespace') . "\\" .
            $this->getName() . "\\" .
            config('modules.paths.generator.model') . "\\" . $modelName;

        return class_exists($class) ? new $class : null;
    }


    /**
     * Get module configuration
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getConfiguration($id)
    {
        /** @var \Illuminate\Database\Eloquent\Model $class */
        $class = $this->getConfigurationClass();

        if (class_exists($class)) {
            return $class::findOrFail($id);
        }

        return null;
    }


    /**
     * Get module default configuration
     *
     * @return null
     */
    public function getDefaultConfiguration()
    {
        $class = $this->getConfigurationClass();

        if (class_exists($class) && method_exists($class, 'getDefault')) {
            return $class::getDefault();
        }

        return new $class();
    }


    /**
     * Run migrations for module.
     *
     * @return int
     */
    public function migrate(): int
    {
        return \Artisan::call('module:migrate', [
            'module' => $this->getName(),
            '--force' => true
        ]);
    }


    /**
     * Install module.
     *
     * @return int - artisan exit code
     */
    public function install()
    {
        $this->createPublicLink();

        $exitCode = $this->migrate();

        if ($exitCode === 0) {
            InstalledModule::create([
                'name' => $this->getName(),
                'enabled' => true
            ]);
        }

        return $exitCode;
    }


    /**
     * Uninstall module
     */
    public function uninstall()
    {
        Entity::where('module', '=', $this->getName())->delete();

        \Artisan::call(
            'module:migrate-reset',
            [
                'module' => $this->getName(),
                '--force' => true
            ]
        );

        InstalledModule::where('name', '=', $this->getName())
            ->delete();

        $this->destroyPublicLink();
    }


    /**
     * If is module designed for including to pages.
     *
     * @return boolean
     */
    public function isForGridEditor()
    {
        return $this->config('for-grideditor', true);
    }


    /**
     * Grid editor title.
     * @return string
     */
    public function getGridEditorTitle()
    {
        $geName = $this->config('grideditor_title');
        return $geName ? trans($geName) : $this->name;
    }


    /**
     * Register the service providers from this module.
     */
    public function registerProviders()
    {
        if (strpos($this->getPath(), '/themes/') || strpos($this->getPath(), '\\themes\\')) {
            $autoload = $this->getPath() . '/../../vendor/autoload.php';

            if (!file_exists($autoload)) {
                throw new \Exception(
                    "Theme does not have autoload file. Please run `composer update` command in theme directory."
                );
            }
            require $autoload;
        }

        foreach ($this->get('providers', []) as $provider) {
            $this->app->register($provider);
        }
    }


    /**
     * Get icon of the module.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->config('icon', 'window-restore');
    }


    /**
     * Convert module to array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'title' => $this->getGridEditorTitle(),
            'icon' => $this->getIcon(),
            'url' => $this->route('create')
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function getCachedServicesPath()
    {
        return \Illuminate\Support\Str::replaceLast('services.php', $this->getSnakeName() . '_module.php', $this->app->getCachedServicesPath());
    }

    /**
     * {@inheritdoc}
     */
    public function registerAliases()
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        foreach ($this->get('aliases', []) as $aliasName => $aliasClass) {
            $loader->alias($aliasName, $aliasClass);
        }
    }


    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string $path
     * @return \Illuminate\Support\HtmlString
     *
     * @throws \Exception
     */
    public function mix($path)
    {
        $publicPath = $this->getPublicPath(false);

        try {
            return mix($path, $publicPath);
        } catch (\Exception $e) {
            $this->checkAndFixPublicLink();
            return mix($path, $publicPath);
        }
    }


    /**
     * Get the path to the public folder.
     *
     * @param boolean $absolute - return absolute path?
     *
     * @return string
     */
    public function getPublicPath($absolute = true)
    {
        $path = 'modules/' . $this->getName();
        return $absolute ? public_path($path) : $path;
    }


    /**
     * Check path to public directory (symlink) and if is wrong or missing, fix it.
     *
     * @return void
     */
    public function checkAndFixPublicLink(): void
    {
        $distPath = $this->getPath() . '/Dist';
        $publicPath = $this->getPublicPath(true);

        // Directory is copied or "Dist" dir does not exist
        if (!file_exists($distPath) || file_exists($publicPath) && !is_link($publicPath)) {
            return;
        }

        // Link is missing
        if (!is_link($publicPath)) {
            $this->createPublicLink();
            return;
        }

        // check correct destination of the link
        $linkDestination = readlink($publicPath);
        if ($linkDestination !== $distPath) {
            $this->destroyPublicLink();
            $this->createPublicLink();
        }
    }


    /**
     * Creates public directory.
     */
    private function createPublicLink(): void
    {
        $distPath = $this->getPath() . '/Dist';
        $publicDirectory = public_path('modules');
        $publicPath = $this->getPublicPath(true);

        if (file_exists($distPath) && !file_exists($publicPath)) {
            if (!file_exists($publicDirectory)) {
                mkdir($publicDirectory, 0775);
                chmod($publicDirectory, 0775);
            }

            try {
                symlink($distPath, $publicPath);
            } catch (\Exception $e) {
                \File::copyDirectory($distPath, $publicPath);
            }
        }
    }


    /**
     * Destroy public link / directory of the module.
     */
    private function destroyPublicLink()
    {
        $directory = $this->getPublicPath();

        if (file_exists($directory)) {
            if (is_link($directory)) {
                unlink($directory);
            } else {
                \File::deleteDirectory($directory);
            }
        }
    }
}
