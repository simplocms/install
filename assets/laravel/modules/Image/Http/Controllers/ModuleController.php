<?php

namespace Modules\Image\Http\Controllers;

use App\Http\Controllers\Admin\AdminController;
use Modules\Image\Http\Requests\ModuleRequest;
use Modules\Image\Models\Configuration;

class ModuleController extends AdminController
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->getFormView(new Configuration(['is_sized' => false]));
    }


    /**
     * Validate configuration and return preview.
     *
     * @param \Modules\Image\Http\Requests\ModuleRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function validateAndPreview(ModuleRequest $request)
    {
        $configuration = new Configuration($request->all());

        return response()->json([
            'content' => view('module-image::module_preview', compact('configuration') )->render()
        ]);
    }


    /**
     * Show the form for editing specified resource.
     *
     * @param \Modules\Image\Models\Configuration $configuration
     * @return \Illuminate\View\View
     */
    public function edit(Configuration $configuration)
    {
        return $this->getFormView($configuration);
    }


    /**
     * Get form view.
     *
     * @param \Modules\Image\Models\Configuration $configuration
     * @return \Illuminate\View\View
     */
    private function getFormView(Configuration $configuration)
    {
        $module = \Module::find('Image');
        return view('module-image::configuration.form', compact('configuration', 'module'));
    }
}
