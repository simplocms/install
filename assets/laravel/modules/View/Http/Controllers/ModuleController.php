<?php

namespace Modules\View\Http\Controllers;

use App\Helpers\ViewHelper;
use App\Http\Controllers\Admin\AdminController;
use Modules\View\Http\Requests\ModuleRequest;
use Modules\View\Models\Configuration;

class ModuleController extends AdminController
{
    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->getFormView(Configuration::getDefault());
    }


    /**
     * Validate configuration and return preview.
     *
     * @param \Modules\View\Http\Requests\ModuleRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function validateAndPreview(ModuleRequest $request)
    {
        $configuration = new Configuration();
        $configuration->inputFill($request->only(['view', 'variables']));

        return response()->json([
            'content' => view('module-view::module_preview', compact('configuration'))->render()
        ]);
    }


    /**
     * Show the form for editing specified resource.
     *
     * @param \Modules\View\Models\Configuration $configuration
     * @return \Illuminate\View\View
     */
    public function edit(Configuration $configuration)
    {
        return $this->getFormView($configuration);
    }


    /**
     * Get variables of specified view.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVariables()
    {
        $view = request('view');
        if (!$view || !ViewHelper::isViewDemarcated('modules.view', $view)) {
            abort(404);
        }

        return \response()->json([
            'fields' => ViewHelper::getViewVariables($view)
        ]);
    }


    /**
     * Get form view.
     *
     * @param \Modules\View\Models\Configuration $configuration
     * @return \Illuminate\View\View
     */
    private function getFormView(Configuration $configuration)
    {
        $views = ViewHelper::getDemarcatedViews('modules.view');
        $variables = $configuration->exists ? $configuration->getInitializedVariables() : null;
        $module = \Module::find('View');
        return view(
            'module-view::configuration.form',
            compact('configuration', 'views', 'variables', 'module')
        );
    }
}
