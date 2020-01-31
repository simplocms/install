<?php

namespace App\Models\Photogallery;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\PhotogalleryInterface;
use App\Contracts\PublishableModelInterface;
use App\Contracts\StructuredDataTypeInterface;
use App\Contracts\ViewableModelInterface;
use App\Models\Interfaces\UrlInterface;
use App\Models\User;
use App\Models\Web\Language;
use App\Models\Web\ViewData;
use App\Services\FrontWebTools\ToolbarOptions;
use App\Structures\StructuredData\Types\TypeWebPage;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\HasLanguage;
use App\Traits\OpenGraphTrait;
use App\Traits\PhotogalleryTrait;
use App\Traits\PlannedPublishingTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUrl;
use Illuminate\Support\Collection;

/**
 * Class Photogallery
 * @package App\Models\Photogallery
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property int language_id
 * @property int views
 * @property int user_id
 * @property string title
 * @property string url
 * @property string|null text
 * @property int sort
 * @property string|null seo_title
 * @property string|null seo_description
 * @property bool seo_index
 * @property bool seo_follow
 * @property bool seo_sitemap
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 * @property \Carbon\Carbon deleted_at
 *
 * @property-read \App\Models\User user
 */
class Photogallery extends Model implements
    UrlInterface,
    ViewableModelInterface,
    PhotogalleryInterface,
    PublishableModelInterface,
    ConvertableToStructuredDataInterface
{
    use SoftDeletes,
        AdvancedEloquentTrait,
        HasUrl,
        HasLanguage,
        PlannedPublishingTrait,
        PhotogalleryTrait,
        OpenGraphTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'photogalleries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'url', 'text', 'sort', 'publish_at', 'unpublish_at', 'open_graph',
        'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap'
    ];

    /**
     * The attributes that are set to null when the value is empty
     *
     * @var array
     */
    protected $nullIfEmpty = [
        'text', 'seo_title', 'seo_description',
    ];

    /**
     * @var array
     */
    public $dates = ['deleted_at', 'publish_at', 'unpublish_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sort' => 'int',
        'language_id' => 'int',
        'views' => 'int',
        'user_id' => 'int',
        'seo_index' => 'boolean',
        'seo_follow' => 'boolean',
        'seo_sitemap' => 'boolean',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::saving(function (Photogallery $photogallery) {
            if (is_null($photogallery->sort)) {
                $maxSort = Photogallery::query()->max('sort') ?: 0;
                $photogallery->sort = $maxSort + 1;
            }
        });
    }


    /**
     * User who created article
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }


    /**
     * Photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos(): HasMany
    {
        return $this->hasManyPhotos('photogallery_photos', 'photogallery_id');
    }


    /**
     * Get view data of the model.
     *
     * @param \App\Models\Web\ViewData $data
     * @return \App\Models\Web\ViewData
     */
    public function getViewData(ViewData $data): ViewData
    {
        $data->title = $this->seo_title ?: $this->title;
        $data->description = $this->seo_description;
        $data->index = $this->seo_index;
        $data->follow = $this->seo_follow;

        $data->og_title = $this->open_graph->get('title');
        $data->og_description = $this->open_graph->get('description');
        $data->og_type = $this->open_graph->get('type', 'website');
        $data->og_url = $this->open_graph->get('url');
        $data->og_image = $this->open_graph->hasImage() ? $this->open_graph->makeImageLink()->getUrl() : null;

        $data->custom_meta = [
            'twitter:creator' => optional($this->user)->twitter_account,
        ];

        return $data;
    }


    /**
     * Set options for front-web toolbar.
     *
     * @param \App\Services\FrontWebTools\ToolbarOptions $options
     */
    public function setFrontWebToolbarOptions(ToolbarOptions $options): void
    {
        if ($this->isPublic()) {
            $options->addStatus(
                trans('admin/photogalleries/general.status.published'), ToolbarOptions::STATUS_SUCCESS
            );
        } else {
            $options->addStatus(
                trans('admin/photogalleries/general.status.unpublished'), ToolbarOptions::STATUS_DANGER
            );
        }

        $options->addControl(
            trans('admin/photogalleries/general.frontweb_toolbar.btn_create'),
            route('admin.photogalleries.create'),
            'plus-circle'
        )
            ->addControl(
                trans('admin/photogalleries/general.frontweb_toolbar.btn_edit'),
                route('admin.photogalleries.edit', $this->getKey()),
                'edit'
            );
    }


    /**
     * Get content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return "<div class='_cms-content'>{$this->text}</div>";
    }


    /**
     * Get structured data type.
     *
     * @return \App\Contracts\StructuredDataTypeInterface
     */
    public function toStructuredData(): StructuredDataTypeInterface
    {
        return new TypeWebPage([
            'name' => $this->title,
            'url' => $this->full_url,
            'inLanguage' => $this->language,
            'dateCreated' => $this->created_at,
            'dateModified' => $this->updated_at,
            'description' => $this->seo_description,
            'headline' => $this->seo_title ?? $this->title,
        ]);
    }


    /**
     * Search photogalleries for given term.
     *
     * @param string $term
     * @param \App\Models\Web\Language $language
     * @return \Illuminate\Support\Collection
     */
    public static function search(string $term, Language $language): Collection
    {
        if (!strlen($term)) {
            return collect([]);
        }

        return self::whereLanguage($language)
            ->publishedByDate()
            ->get()
            ->filter(function (Photogallery $photogallery) use ($term) {
                return mb_stripos($photogallery->title, $term) !== false ||
                    mb_stripos($photogallery->seo_title, $term) !== false ||
                    mb_stripos(strip_tags($photogallery->text), $term) !== false;
            });
    }
}
