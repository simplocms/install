<?php

namespace App\Models\Web;

use App\Helpers\Functions;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class Theme
 * @package App\Models\Web
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property-read string id
 * @property-read string name
 * @property-read array menu_locations
 * @property-read array universal_modules
 */
class Theme extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name'];

    /**
     * Disable auto-incrementing identifier.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Configuration of the theme.
     *
     * @var bool
     */
    protected $config;

    /**
     * Theme constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->config = [];
        if ($this->id) {
            $configPath = base_path('resources/themes/' . $this->id) . '/config.php';
            if (file_exists($configPath)) {
                $this->config = include $configPath;
            }

            \View::addNamespace('theme', base_path('resources/themes/' . $this->id . '/view'));
            \Lang::addNamespace('theme', base_path('resources/themes/' . $this->id . '/lang'));
        }
    }


    /**
     * Load configuration as attribute.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        $result = parent::getAttribute($key);

        if (is_null($result)) {
            $result = $this->config[$key] ?? null;
        }

        return $result;
    }


    /**
     * Find theme by its name.
     *
     * @param string $name
     * @return \App\Models\Web\Theme|null
     */
    public static function findNamed($name): ?Theme
    {
        $path = base_path('resources/themes/' . $name);
        if (!$name || !file_exists($path)) {
            return null;
        }

        return new Theme(['id' => $name]);
    }


    /**
     * Get the specified configuration value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function config(string $key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }


    /**
     * Get the CKEditor configuration.
     *
     * @return mixed[]|null
     */
    public function getCKEditorConfig(): ?array
    {
        $config = $this->config('ck_editor');
        if (!$config) {
            return null;
        }

        if (isset($config['heading_options'])) {
            $config['heading_options'] = array_map(function (array $option): array {
                $option['title'] = trans($option['title']);
                return $option;
            }, $config['heading_options']);
        }

        if (isset($config['lists'])) {
            $config['lists'] = array_map(function (array $list): array {
                $list['title'] = trans($list['title']);
                $list['name'] = Functions::sanitizeHtmlClass($list['name'], str_random());
                return $list;
            }, $config['lists']);
        }

        return $config;
    }


	/**
	 * Register system configuration.
	 */
    public function registerSystemConfig(): void
	{
		$config = $this->config('system');
		if (is_array($config) && count($config)) {
			config($config);
		}
	}


    /**
     * Get all themes
     *
     * @param array $columns
     * @return \Illuminate\Support\Collection
     */
    static function all($columns = ['*'])
    {
        $themes = collect([]);

        $themeIterator = new \DirectoryIterator(base_path('resources/themes'));
        foreach ($themeIterator as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                $theme = self::findNamed($fileinfo->getFilename());
                $themes->push($theme);
            }
        }

        return $themes;
    }


    /**
     * Return default template
     *
     * @return self
     */
    static function getDefault()
    {
        $defaultTheme = Theme::findNamed(SingletonEnum::settings()->get('theme'));

        if (!$defaultTheme) {
            $defaultTheme = self::findNamed('default');

            if (!$defaultTheme) {
                $defaultTheme = self::all()->first();
            }

            if ($defaultTheme) {
                $defaultTheme->install();
            }
        }

        return $defaultTheme;
    }


    /**
     * Get theme path
     *
     * @return array
     */
    public function getPathAttribute()
    {
        return base_path('resources/themes/' . $this->id);
    }

    /**
     * Get theme path
     *
     * @return array
     */
    public function getViewPathAttribute()
    {
        return $this->path . '/view';
    }


    /**
     * Assign menu by its ID to location in theme
     *
     * @param $location
     * @param $id
     * @return bool
     */
    public function setMenuLocation($location, $id)
    {
        $menuLocations = $this->config('menu_locations');
        if (!isset($menuLocations[$location])) {
            return false;
        }

        $this->set("menu_$location", $id);
        return true;
    }


    /**
     * Get menu locations with settings
     *
     * @return array
     */
    public function getMenuLocationsAttribute()
    {
        $result = [];
        foreach ($this->config('menu_locations', []) as $menuLocation => $title) {
            $result[$menuLocation] = $this->get("menu_$menuLocation");
        }

        return $result;
    }


    /**
     * Set setting
     *
     * @param $name
     * @param $value
     */
    public function set(string $name, $value)
    {
        if (is_null($value)) {
            SingletonEnum::settings()->forget($this->getSettingsKey($name));
        } else {
            SingletonEnum::settings()->put($this->getSettingsKey($name), $value);
        }
    }


    /**
     * Get settings key.
     *
     * @param string $name
     * @return string
     */
    public function getSettingsKey(string $name): string
    {
        $normalizedId = str_replace('-', '_', snake_case($this->id));
        return str_replace(
            '%lang%',
            SingletonEnum::languagesCollection()->getContentLanguage()->language_code,
            'theme_' . $normalizedId . '_' . $name
        );
    }


    /**
     * Get setting
     *
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return SingletonEnum::settings()->get($this->getSettingsKey($name), $default);
    }


    /**
     * Get all theme settings.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSettings(): Collection
    {
        return SingletonEnum::settings()->getAll($this->getSettingsKeys());
    }


    /**
     * Get settings keys.
     *
     * @return string[]
     */
    public function getSettingsKeys(): array
    {
        $keys = [];
        foreach ($this->config('settings', []) as $nameOrIndex => $typeOrName) {
            $keys[] = $this->getSettingsKey(is_numeric($nameOrIndex) ? $typeOrName : $nameOrIndex);
        }
        return $keys;
    }


    /**
     * Get instance of the theme context
     *
     * @return \App\Models\ContextBase
     */
    public function getContextInstance()
    {
        $class = 'Context';

        $path = base_path('resources/themes/' . $this->id . '/' . $class);
        require_once $path . '.php';

        if (class_exists($class)) {
            return new $class($this);
        }

        return null;
    }


    /**
     * Get path to public directory (symlink).
     *
     * @param string $to
     *
     * @return string
     */
    public function getPublicPath(string $to = null): string
    {
        $to = is_null($to) ? '' : '/' . $to;

        return public_path('theme' . $to);
    }


    /**
     * Get path to theme directory.
     *
     * @param string $to
     *
     * @return string
     */
    public function getThemePath(string $to = null): string
    {
        $to = is_null($to) ? '' : '/' . $to;

        return resource_path('themes/' . $this->id . $to);
    }


    /**
     * Install theme.
     *
     * @return void
     */
    public function install()
    {
        $previousTheme = self::findNamed(SingletonEnum::settings()->get('theme'));
        if ($previousTheme) {
            $previousTheme->uninstall();
        }

        // Make sure that previous link is gone.
        $this->destroyPublicLink();

        SingletonEnum::settings()->put('theme', $this->id);
        $this->createPublicLink();
    }


    /**
     * Uninstall theme.
     *
     * @return void
     */
    public function uninstall()
    {
        $this->destroyPublicLink();
    }


    /**
     * Check path to public directory (symlink) and if is wrong or missing, fix it.
     *
     * @return void
     */
    public function checkAndFixPublicLink(): void
    {
        $themeDistPath = $this->getThemePath('dist');
        $publicPath = $this->getPublicPath();

        // Directory is copied
        if (file_exists($publicPath) && !is_link($publicPath)) {
            return;
        }

        // Link is missing
        if (!is_link($publicPath)) {
            $this->createPublicLink();
            return;
        }

        // check correct destination of the link
        $linkDestination = readlink($publicPath);
        if ($linkDestination !== $themeDistPath) {
            $this->destroyPublicLink();
            $this->createPublicLink();
        }
    }


    /**
     * Create public link / directory of the theme.
     */
    private function createPublicLink(): void
    {
        $sourceDir = $this->getThemePath('dist');
        $linkDestination = $this->getPublicPath();

        if (file_exists($sourceDir)) {
            try {
                symlink($sourceDir, $linkDestination);
            } catch (\Exception $e) {
                \File::copyDirectory($sourceDir, $linkDestination);
            }
        }
    }


    /**
     * Destroy public link / directory of the theme.
     */
    private function destroyPublicLink(): void
    {
        $path = $this->getPublicPath();

        if (file_exists($path)) {
            if (is_link($path)) {
                unlink($path);
            } else {
                \File::deleteDirectory($path);
            }
        }
    }
}

