<?php

namespace Modules\ArticlesList\Http\Controllers;

use App\Http\Controllers\Admin\AdminController;
use Modules\ArticlesList\Models\Configuration;
use Modules\ArticlesList\Models\GridEditorForm;
use Modules\ArticlesList\Http\Requests\ModuleRequest;

class ModuleController extends AdminController
{
    /**
     * Show the form for creating a new resource.
     * @return \Modules\ArticlesList\Models\GridEditorForm
     * @throws \Exception
     */
    public function create()
    {
        return new GridEditorForm(Configuration::getDefault(), $this->getLanguage());
    }


    /**
     * Validate configuration and return preview.
     *
     * @param \Modules\ArticlesList\Http\Requests\ModuleRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function validateAndPreview(ModuleRequest $request)
    {
        $configuration = new Configuration();
        $configuration->inputFill($request->only(['view', 'category_ids', 'tag_ids', 'sort_type', 'limit']));

        return response()->json([
            'content' => view('module-articleslist::module_preview', compact('configuration'))->render()
        ]);
    }


    /**
     * Show the form for editing specified resource.
     *
     * @param \Modules\ArticlesList\Models\Configuration $configuration
     * @return \Modules\ArticlesList\Models\GridEditorForm
     * @throws \Exception
     */
    public function edit(Configuration $configuration)
    {
        return new GridEditorForm($configuration, $this->getLanguage());
    }
}
