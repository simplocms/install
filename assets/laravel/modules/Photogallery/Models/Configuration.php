<?php

namespace Modules\Photogallery\Models;

use App\Models\Interfaces\ModuleConfigurationInterface;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model implements ModuleConfigurationInterface
{
    /**
     * @var string Table name of the model
     */
    protected $table = 'module_photogallery_configurations';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = [ 'view', 'photogallery_id' ];


    /**
     * Get default configuration
     *
     * @return Configuration
     */
    static function getDefault(){
        return new self([
            'view' => 'photogallery.index'
        ]);
    }


    /**
     * Photogallery
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function photogallery(){
        return $this->hasOne('App\Models\Photogallery\Photogallery', 'id', 'photogallery_id');
    }


    /**
     * Render module
     *
     * @param array $renderAttributes
     * @return string
     * @throws \Throwable
     */
    public function render(array $renderAttributes = []): string{
        if (!\View::exists($this->view)) {
            return view('module-photogallery::view_not_found')
                ->with('view', $this->view)
                ->render();
        }

        return view($this->view, [
            'photogallery' => $this->photogallery
        ])->render();
    }


    /**
     * Render module
     *
     * @return string
     */
    public function renderPreview(){
        return view($this->view, [
            'photogallery' => $this->photogallery
        ])->render();
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
