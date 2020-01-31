<?php

namespace App\Http\Controllers;

use App\Models\Web\Theme;
use App\Models\Web\ViewData;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class ThemeDevelopmentController extends BaseController
{

    private $themeInstance;

    /**
     * ThemeDevelopmentController constructor.
     */
    public function __construct()
    {
        if (app()->environment('production')) {
            $this->middleware('admin');
        }

        $this->themeInstance = SingletonEnum::theme();
        \View::addNamespace('theme-static', "{$this->themeInstance->path}/_static");

    }


    /**
     * Show list of pages and components.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showList()
    {
        $pages = array_map(function (SplFileInfo $file) {
            $name = str_replace('.blade.php', '', $file->getFilename());
            return [
                'name' => $name,
                'url' => route('theme_development.page', compact('name'))
            ];
        }, $this->getFiles($this->themeInstance, 'pages'));

        $components = array_map(function (SplFileInfo $file) {
            $name = str_replace('.blade.php', '', $file->getFilename());
            return [
                'name' => $name,
                'url' => route('theme_development.component', compact('name'))
            ];
        }, $this->getFiles($this->themeInstance, 'components'));

        return view('theme_development.index', compact('pages', 'components'));
    }


    /**
     * Show page.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $name
     * @return \Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function showPage(Request $request, string $name)
    {
        
        $file = "{$this->themeInstance->path}/_static/pages/$name.blade.php";


        if (!file_exists($file)) {
            abort(404);
        }

        // Generate preview //
        if ($request->has('preview')) {
            $data = new ViewData([
                'language' => SingletonEnum::languagesCollection()->getContentLanguage(),
            ]);
            $context = $this->themeInstance->getContextInstance();
            return \View::file($file)->with(compact('data', 'context'));
        }

        \Config::set('debugbar.inject', false);
        return view('theme_development.page', compact('name'));
    }


    /**
     * Show component.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $name
     * @return string
     */
    public function showComponent(Request $request, string $name)
    {
        $bladeFile = "{$this->themeInstance->path}/_static/components/$name.blade.php";

        if (!file_exists($bladeFile)) {
            abort(404);
        }

        // Generate preview //
        if ($request->has('preview')) {
            \Config::set('debugbar.inject', false);
            $context = $this->themeInstance->getContextInstance();
            $content = \View::file($bladeFile)->with(compact('context'))->render();
            $componentWrapper = "{$this->themeInstance->path}/_static/wrapper.blade.php";

            if (file_exists($componentWrapper)) {
                return \View::file($componentWrapper)->with(compact('context', 'content'))->render();
            }

            return $content;
        }

        $html = \View::file($bladeFile)->render();

       
        try {
            $scss = \File::get("{$this->themeInstance->path}/src/scss/components/$name.scss");
        } catch (\Exception $e) {
            $scss = null;
        }

        return view('theme_development.component', compact('name','html', 'scss'));
    }


    /**
     * Get blade files from specified directory of given template.
     *
     * @param \App\Models\Web\Theme $theme
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    private function getFiles(Theme $theme, string $directory): array
    {
        $pages = [];
        $path = "{$theme->path}/_static/$directory";

        if (!file_exists($path)) {
            return [];
        }

        foreach (\File::files($path) as $file) {
            if (Str::endsWith($file->getFilename(), '.blade.php')) {
                $pages[] = $file;
            }
        }

        return $pages;
    }
}
