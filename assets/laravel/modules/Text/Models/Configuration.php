<?php

namespace Modules\Text\Models;

use App\Models\Interfaces\ModuleConfigurationInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Configuration
 * @package Modules\Text\Models
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string content
 */
class Configuration extends Model implements ModuleConfigurationInterface
{
    /**
     * @var string Table name of the model
     */
    protected $table = 'module_text_configurations';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = ['content'];


    /**
     * Render module
     *
     * @param array $renderAttributes
     * @return string
     */
    public function render(array $renderAttributes = []): string
    {
        return "<div class='_cms-content'>{$this->content}</div>";
    }

    /**
     * Fill model with input values with mutators inside.
     *
     * @param array $inputs
     * @return $this
     */
    public function inputFill(array $inputs)
    {
        return $this->fill($inputs);
    }
}
