<?php

namespace App\Contracts;

use App\Models\Web\ViewData;
use App\Services\FrontWebTools\ToolbarOptions;

/**
 * Interface ViewableModelInterface
 * @package App\Contracts
 * @mixin \Illuminate\Database\Eloquent\Model
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
interface ViewableModelInterface
{
    /**
     * Get view data of the model.
     *
     * @param \App\Models\Web\ViewData $data
     * @return \App\Models\Web\ViewData
     */
    public function getViewData(ViewData $data): ViewData;

    /**
     * Set options for front-web toolbar.
     *
     * @param \App\Services\FrontWebTools\ToolbarOptions $options
     */
    public function setFrontWebToolbarOptions(ToolbarOptions $options): void;
}
