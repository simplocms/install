<?php

namespace App\Helpers;

use App\Models\Web\Theme;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Support\Str;

abstract class ViewHelper
{
    /**
     * Get all views for specific purpose.
     *
     * @param string $type - e.c. "pages" or "modules.view"
     * @return array
     */
    public static function getDemarcatedViews(string $type): array
    {
        $views = [];
        $theme = SingletonEnum::theme();
        $path = self::getTypePath($type);

        if (!file_exists($path)) {
            return [];
        }

        foreach (\File::files($path) as $file) {
            if (Str::endsWith($file->getFilename(), '.blade.php')) {
                $viewKey = str_replace('.blade.php', '', $file->getFilename());
                $views[$theme->name]["theme::$type.$viewKey"] = self::getViewName($type, $viewKey);
            }
        }

        return $views;
    }


    /**
     * Get keys of demarcated views for given type.
     *
     * @param string $type - e.c. "pages" or "modules.view"
     * @return array
     */
    public static function getDemarcatedViewsKeys(string $type): array
    {
        $views = [];
        $path = self::getTypePath($type);

        if (!file_exists($path)) {
            return [];
        }

        foreach (\File::files($path) as $file) {
            if (Str::endsWith($file->getFilename(), '.blade.php')) {
                $viewKey = str_replace('.blade.php', '', $file->getFilename());
                $views[] = "theme::$type.$viewKey";
            }
        }

        return $views;
    }


    /**
     * Check if given view is demarcated within given type.
     *
     * @param string $type
     * @param string $view
     * @return bool
     */
    public static function isViewDemarcated(string $type, string $view): bool
    {
        return in_array($view, self::getDemarcatedViewsKeys($type));
    }


    /**
     * Get name of the specified view.
     *
     * @param string $type
     * @param string $view
     * @return string
     */
    public static function getViewName(string $type, string $view): string
    {
        $pos = strrpos($view, '.');
        $viewKey = $pos === false ? $view : substr($view, $pos + 1);
        $translateKey = "theme::theme.views.$type.$viewKey";
        $viewName = trans($translateKey);
        $viewName = $viewName === $translateKey ? $viewKey : $viewName;
        return $viewName;
    }


    /**
     * Get view variables.
     * @param string $view
     * @return \App\Structures\FormFields\AbstractFormField[]
     */
    public static function getViewVariables(string $view): array
    {
        // Register theme namespace with context instantiation.
        if (substr($view, 0, 5) === "theme") {
            Theme::getDefault();
        }

        $fields = [];

        ob_start();
        $GET_VARIABLES = true;
        try {
            $fields = include view($view)->getPath();
        } catch (\Exception $e) {
        }
        ob_get_clean();

        if (!is_array($fields)) {
            $fields = [];
        }

        // Backward compatibility: If name is used as a key, move it into the object.
        foreach ($fields as $nameOrIndex => &$field) {
            if (is_string($nameOrIndex) && is_array($field) && !isset($field['name'])) {
                $field['name'] = $nameOrIndex;
            }
        }

        return $fields;
    }


    /**
     * Get path to views of specified type.
     *
     * @param string $type
     * @return string
     */
    protected static function getTypePath(string $type): string
    {
        $typeToPath = str_replace('.', '/', $type);
        return SingletonEnum::theme()->view_path . '/' . $typeToPath;
    }
}
