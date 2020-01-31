<?php

namespace Modules\Link\Models;

use App\Models\Article\Article;
use App\Models\Interfaces\ModuleConfigurationInterface;
use App\Models\Page\Page;
use App\Models\Photogallery\Photogallery;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Configuration
 * @package Modules\Link\Models
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string text
 * @property string|null model
 * @property string|int|null model_id
 * @property string|null url
 * @property string|null view
 * @property array|null tags
 *
 * @property-read string|null model_type
 * @property-read int|null page_id
 * @property-read int|null article_id
 * @property-read int|null photogallery_id
 * @property-read string full_url
 * @property-read array javascript_tags
 */
class Configuration extends Model implements ModuleConfigurationInterface
{
    /**
     * @var string Table name of the model
     */
    protected $table = 'module_link_configurations';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = ['text', 'model', 'model_id', 'url', 'view', 'tags'];


    /**
     * Get model type to class translation table
     *
     * @var array
     */
    static $modelTypeClass = [
        'page' => Page::class,
        'article' => Article::class,
        'photogallery' => Photogallery::class,
    ];


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
     * Get type of model
     *
     * @return string|null
     */
    public function getModelTypeAttribute()
    {
        if (!$this->model) return null;

        $classesToTypes = array_flip(self::$modelTypeClass);
        return isset($classesToTypes[$this->model]) ? $classesToTypes[$this->model] : null;
    }


    /**
     * Get page id
     *
     * @return int|null
     */
    public function getPageIdAttribute()
    {
        return $this->model == Page::class ? $this->model_id : null;
    }


    /**
     * Get article id
     *
     * @return int|null
     */
    public function getArticleIdAttribute()
    {
        return $this->model == Article::class ? $this->model_id : null;
    }


    /**
     * Get photogallery id
     *
     * @return int|null
     */
    public function getPhotogalleryIdAttribute()
    {
        return $this->model == Photogallery::class ? $this->model_id : null;
    }


    /**
     * Get full url
     *
     * @return string
     * @throws \Exception
     */
    public function getFullUrlAttribute(): string
    {
        if (!$this->model) {
            return strlen($this->url) ? $this->url : "#url_not_found";
        }

        return SingletonEnum::urlFactory()->getFullModelUrl($this->model, $this->model_id);
    }


    /**
     * Get attributes string.
     *
     * @deprecated
     * @param array $mergeWith
     * @return string
     */
    public function mergeTagsReturnString(array $mergeWith = [])
    {
        return $this->getHTMLAttributesString($mergeWith);
    }


    /**
     * Get tags
     *
     * @param string|null $value
     * @return array
     */
    public function getTagsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }


    /**
     * Get javascript tags
     *
     * @return array|null
     */
    public function getJavascriptTagsAttribute()
    {
        $tags = [];
        foreach ($this->tags as $tag => $value) {
            $tags[] = [
                'name' => $tag,
                'value' => $value
            ];
        }

        return $tags;
    }


    /**
     * Get html attributes as string.
     *
     * @param array $mergeWith
     * @return string
     */
    public function getHTMLAttributesString(array $mergeWith = []): string
    {
        $stringTags = [];
        foreach ($this->getHTMLAttributes($mergeWith) as $key => $value) {
            $stringTags[] = strlen($value) ? "$key=\"{$value}\"" : $key;
        }

        return join(' ', $stringTags);
    }


    /**
     * Get html attributes. Optionally can be merged with custom tags.
     *
     * @param array $mergeWith
     * @return array
     */
    public function getHTMLAttributes(array $mergeWith = []): array
    {
        $tags = $this->tags;

        // Add outer tags to all tags
        foreach ($mergeWith as $key => $value) {
            // Merge class tag
            if ($key === 'class' && isset($tags[$key])) {
                $mergeWith[$key] .= ' ' . $tags[$key];
            }
            $tags[$key] = $mergeWith[$key];
        }

        return $tags;
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
        $view = $this->view;
        if (is_null($view) || !\View::exists($view)) {
            $view = 'module-link::default';
        }

        return view($view, ['configuration' => $this])->render();
    }


    /**
     * Fill model with input values with mutators inside.
     *
     * @param array $inputs
     * @return $this
     */
    public function inputFill(array $inputs)
    {
        $fill = array_only($inputs, ['view', 'url', 'text']);

        $fill['model'] = null;
        $fill['model_id'] = null;

        // Model
        if (!($inputs['custom_url'] ?? false)) {
            $fill['url'] = null;
            $model = $inputs['model_type'] ?? null;
            $fill['model'] = self::$modelTypeClass[$model];
            $fill['model_id'] = $inputs[$model . '_id'];
        }

        // View
        if (isset($fill['view']) && !strlen($fill['view'])) {
            $fill['view'] = null;
        }

        // Attributes
        $tags = [];
        $tagKeys = $inputs['attribute_key'] ?? [];
        $tagValues = $inputs['attribute_value'] ?? [];
        foreach ($tagKeys as $i => $tagKey) {
            if (!$tagKey) continue;
            $tags[$tagKey] = htmlspecialchars($tagValues[$i]);
        }

        $fill['tags'] = $tags ? json_encode($tags) : null;

        return $this->fill($fill);
    }
}
