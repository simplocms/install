<?php

namespace Modules\Image\Models;

use App\Helpers\ViewHelper;
use App\Models\Interfaces\ModuleConfigurationInterface;
use App\Models\Media\File;
use App\Models\Module\Module;
use App\Services\MediaLibrary\ImageBuilder;
use App\Structures\Enums\SingletonEnum;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\MediaImageTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Configuration
 * @package Modules\Image\Models
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property int image_id
 * @property bool is_sized
 * @property int width
 * @property int height
 * @property string alt
 * @property string img_class
 */
class Configuration extends Model implements ModuleConfigurationInterface
{
    use AdvancedEloquentTrait;
    use MediaImageTrait {
        makeImageLink as public makeRawImageLink;
    }

    /**
     * @var string Table name of the model
     */
    protected $table = 'module_image_configurations';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = ['image_id', 'is_sized', 'width', 'height', 'alt', 'img_class'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_sized' => 'integer',
        'width' => 'integer',
        'height' => 'integer'
    ];

    /**
     * Render module
     *
     * @param array $renderAttributes
     * @return string
     */
    public function render(array $renderAttributes = []): string
    {
        if (ViewHelper::isViewDemarcated('modules.image', 'theme::modules.image.default')) {
            return view('theme::modules.image.default', [
                'configuration' => $this
            ]);
        }

        $imageUrl = $this->makeImageLink();

        $imgClass = $style = '';
        if ($this->is_sized) {
            $style = "style='max-width:{$this->width}px;max-height:{$this->height}px'";
        } else {
            $style = "style='width:100%'";
        }

        if ($this->img_class) {
            $imgClass = "class='" . htmlentities($this->img_class) . "'";
        }

        return "<img src='{$imageUrl->getUrl()}' alt='{$this->alt}' {$style} {$imgClass}>";
    }


    /**
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function makeImageLink(): ImageBuilder
    {
        $link = $this->makeRawImageLink('image');
        if ($this->is_sized) {
            $link->resize($this->width, $this->height);
        }

        return $link;
    }


    /**
     * Fill model with input values with mutators inside.
     *
     * @param array $inputs
     * @return $this
     */
    public function inputFill(array $inputs)
    {
        if (!intval($inputs['is_sized'])) {
            $inputs['width'] = null;
            $inputs['height'] = null;
        }
        return $this->fill($inputs);
    }


    /**
     * Relation to image file.
     *
     * @return \App\Models\Media\File
     */
    public function getImage(): File
    {
        return $this->hasImage('image') ?
            File::findPrefetched($this->image_id) :
            File::imagePlaceholder();
    }


    /**
     * @return \App\Models\Module\Module
     */
    public function getModule(): Module
    {
        return SingletonEnum::modules()->find('Image');
    }


    /**
     * Relation to image file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }
}
