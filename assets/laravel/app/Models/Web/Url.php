<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Url
 * @package App\Models\Web
 * @property string url
 * @property string model
 * @property int model_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder whereLanguage(Language $language)
 */
class Url extends Model
{
    /**
     * @var string Model table
     */
    protected $table = 'urls';

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'url', 'model', 'model_id'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Delete redirect from this url, when new url is created.
        static::saved(function (Url $url): void {
            $redirect = Redirect::findBySourceUrl($url->url);
            if ($redirect) {
                $redirect->delete();
            }
        });
    }


    /**
     * Find URL.
     *
     * @param string $url
     * @return \App\Models\Web\Url|null
     */
    static function findUrl(string $url): ?Url
    {
        return Url::query()->where('url', $url)->first();
    }


    /**
     * Find model.
     *
     * @param string $model
     * @param string|int $id
     * @return mixed
     */
    static function findModel(string $model, $id): ?Url
    {
        return Url::query()->where('model', $model)
            ->where('model_id', $id)->first();
    }


    /**
     * Get instance of model
     *
     * @return \App\Models\Interfaces\UrlInterface
     */
    public function getInstance()
    {
        /** @var \Illuminate\Database\Eloquent\Model $instance */
        $instance = new $this->model;
        return $instance->find($this->model_id);
    }


    /**
     * Filter urls by specified language
     *
     * @param Builder $query
     * @param Language $language
     */
    public function scopeWhereLanguage(Builder $query, Language $language)
    {
        $query->where('url', 'like', $language->language_code . '/%');
    }


    /**
     * Change url. Can update subordinate urls optionally.
     * Automatically created redirect to from previous url.
     *
     * @param string $newUrl
     * @param bool $updateSubordinates
     */
    public function changeUrl(string $newUrl, bool $updateSubordinates): void
    {
        if ($this->url === $newUrl) {
            return;
        }

        $originalUrl = $this->url;
        $this->update(['url' => $newUrl]);
        Redirect::putSystemRedirect($originalUrl, $this->getAttribute('url'));

        if ($updateSubordinates) {
            self::findSubordinates($originalUrl)->each(function (Url $url) use ($originalUrl): void {
                $newUrl = $this->url . substr($url->url, strlen($originalUrl));
                $url->changeUrl($newUrl, false);
            });
        }
    }


    /**
     * Find subordinate urls of specified url.
     *
     * @param string $url
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function findSubordinates(string $url): Collection
    {
        return self::getSubordinatesQuery($url)->get();
    }


    /**
     * Get query with selected subordinate urls of specified url.
     *
     * @param string $url
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getSubordinatesQuery(string $url): Builder
    {
        return self::query()->where('url', 'LIKE', "{$url}/%");
    }
}
