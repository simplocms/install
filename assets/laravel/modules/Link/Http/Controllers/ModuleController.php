<?php

namespace Modules\Link\Http\Controllers;

use App\Helpers\ViewHelper;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Article\Article;
use App\Models\Page\Page;
use App\Models\Photogallery\Photogallery;
use Modules\Link\Http\Requests\ModuleRequest;
use Modules\Link\Models\Configuration;

class ModuleController extends AdminController
{
    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return $this->createForm(Configuration::getDefault());
    }


    /**
     * Show the form for editing specified resource.
     * @param \Modules\Link\Models\Configuration $configuration
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Configuration $configuration)
    {
        return $this->createForm($configuration);
    }


    /**
     * Validate module and return preview.
     *
     * @param ModuleRequest $request
     * @return mixed
     * @throws \Throwable
     */
    public function validateAndPreview(ModuleRequest $request)
    {
        $configuration = new Configuration();
        $configuration->inputFill($request->all());

        return response()->json([
            'content' => view('module-link::module_preview', compact('configuration'))->render()
        ]);
    }


    /**
     * Return form view
     *
     * @param $configuration
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function createForm($configuration)
    {
        $models = [
            'page' => trans('module-link::admin.grid_editor_form.model_types.page'),
            'article' =>  trans('module-link::admin.grid_editor_form.model_types.article'),
            'photogallery' =>  trans('module-link::admin.grid_editor_form.model_types.photogallery'),
        ];

        $modelLists = [
            'page' => Page::whereLanguage($this->getLanguage())->pluck('name', 'id'),
            'article' => Article::whereLanguage($this->getLanguage())->pluck('title', 'id'),
            'photogallery' => Photogallery::whereLanguage($this->getLanguage())->pluck('title', 'id'),
        ];

        $views = ['' => trans('module-link::admin.grid_editor_form.default_view')]
            + ViewHelper::getDemarcatedViews('modules.link');

        $module = \Module::find('link');

        return view(
            'module-link::configuration.form',
            compact('configuration', 'models', 'modelLists', 'views', 'module')
        );
    }
}
