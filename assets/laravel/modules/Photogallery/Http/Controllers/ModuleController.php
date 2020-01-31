<?php

namespace Modules\Photogallery\Http\Controllers;

use App\Helpers\ViewHelper;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Photogallery\Photogallery;
use Illuminate\Http\Response;
use Modules\Photogallery\Http\Requests\ModuleRequest;
use Modules\Photogallery\Models\Configuration;

class ModuleController extends AdminController
{
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $configuration = Configuration::getDefault();

        $views = ViewHelper::getDemarcatedViews('modules.photogallery');
        $photogalleries = Photogallery::whereLanguage($this->getLanguage())->pluck('title', 'id');
        return view('module-photogallery::configuration.form', compact('configuration', 'views', 'photogalleries'));
    }


    /**
     * Validate module and return preview.
     *
     * @param ModuleRequest $request
     * @return mixed
     */
    public function validateAndPreview(ModuleRequest $request)
    {
        $configuration = new Configuration($request->only('photogallery_id', 'view'));

        return response()->json([
            'content' => view('module-photogallery::module_preview', compact('configuration') )->render()
        ]);
    }


    /**
     * Show the form for editing specified resource.
     * @return Response
     */
    public function edit(Configuration $configuration)
    {
        $views = ViewHelper::getDemarcatedViews('modules.photogallery');
        $photogalleries = Photogallery::whereLanguage($this->getLanguage())->pluck('title', 'id');
        return view('module-photogallery::configuration.form', compact('configuration', 'views', 'photogalleries'));
    }
}
