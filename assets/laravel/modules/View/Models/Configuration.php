<?php

namespace Modules\View\Models;

use App\Helpers\ViewHelper;
use App\Models\Interfaces\ModuleConfigurationInterface;
use App\Models\Media\File;
use App\Structures\FormFields\Image;
use App\Structures\FormFields\MediaFile;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;

/**
 * Class Configuration
 * @package Modules\View\Models
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string view
 * @property array variables
 */
class Configuration extends Model implements ModuleConfigurationInterface
{
    use AdvancedEloquentTrait;

    /**
     * @var string Table name of the model
     */
    protected $table = 'module_view_configurations';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'variables' => 'array',
    ];

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = ['view', 'variables'];

    /**
     * Get default configuration
     *
     * @return Configuration
     */
    static function getDefault()
    {
        return new self([
            'view' => ''
        ]);
    }


    /**
     * Render module
     *
     * @param array $renderAttributes
     * @return string
     * @throws \Throwable
     */
    public function render(array $renderAttributes = []): string
    {
        if (!View::exists($this->view)) {
            return view('module-view::missing_view', ['name' => $this->view])->render();
        }

        $variables = $this->getInitializedVariables();
        return view($this->view, $variables)->render();
    }


    /**
     * Fill model with input values with mutators inside.
     *
     * @param array $inputs
     * @return $this
     */
    public function inputFill(array $inputs)
    {
        if (isset($inputs['variable']) && $inputs['variable']) {
            $this->variables = (array)$inputs['variable'];
        } else {
            $this->variables = null;
        }

        return $this->fill($inputs);
    }

    /**
     * Get initialized variables.
     */
    public function getInitializedVariables()
    {
        $fields = $this->getFields();
        $variables = $this->variables ?? [];
        $initializedVariables = [];

        foreach ($fields as $index => $field) {
            $value = $variables[$field->getName()] ?? null;

            if ($value !== null &&
                ($field instanceof MediaFile || $field instanceof Image)
            ) {
                $value = File::findPrefetched($value);
            }

            $initializedVariables[$field->getName()] = $value;
        }

        return $initializedVariables;
    }

    /**
     * @return \App\Structures\FormFields\AbstractFormField[]
     */
    public function getFields(): array
    {
        return ViewHelper::getViewVariables($this->view);
    }
}
