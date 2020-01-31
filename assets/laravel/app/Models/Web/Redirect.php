<?php

namespace App\Models\Web;

use App\Models\User;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Redirect
 * @package App\Models\Web
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string from
 * @property string to
 * @property int status_code
 * @property int|null user_id
 *
 * @property-read \App\Models\User|null author
 */
class Redirect extends Model
{
    use AdvancedEloquentTrait;

    /**
     * @var string Model table
     */
    protected $table = 'redirects';

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'from', 'to', 'status_code'
    ];

    /**
     * Author of the redirect.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }


    /**
     * Check if redirect point to URL.
     *
     * @return bool
     */
    public function pointToUrl(): bool
    {
        return !!filter_var($this->to, FILTER_VALIDATE_URL);
    }


    /**
     * Find by source ("from") url.
     *
     * @param array|string $url
     * @return \App\Models\Web\Redirect|\App\Models\Web\Redirect[]|\Illuminate\Database\Eloquent\Collection|null
     */
    public static function findBySourceUrl($url)
    {
        if (is_array($url)) {
            $urls = array_map([self::class, 'normalizeUrl'], $url);
            return self::query()->whereIn('from', $urls)->get();
        }

        return self::query()->where('from', self::normalizeUrl($url))->first();
    }


    /**
     * Put system redirect. If redirect with given source already exist, it updates its target.
     *
     * @param string $from
     * @param string $to
     * @return \App\Models\Web\Redirect|null
     */
    public static function putSystemRedirect(string $from, string $to): ?Redirect
    {
        $targetRedirect = self::findBySourceUrl($to);

        // Makes sure that redirects do not chain.
        if ($targetRedirect) {
            $to = $targetRedirect->to;
        }

        // Prevents redirect loop.
        if ($to === $from) {
            return null;
        }

        $existingRedirect = self::findBySourceUrl($from);

        if ($existingRedirect) {
            $existingRedirect->update([
                'to' => $to,
                'status_code' => 301
            ]);

            return $existingRedirect;
        }

        return $redirect = self::create([
            'from' => $from,
            'to' => $to,
            'status_code' => 301
        ]);
    }


    /**
     * Normalize URL for database.
     *
     * @param string $url
     * @return string
     */
    public static function normalizeUrl(string $url): string
    {
        $url = trim($url, "/ \t\n\r\0\x0B");
        return $url === '' ? '/' : $url;
    }


    /**
     * Set "from" attribute.
     *
     * @param string $value
     */
    public function setFromAttribute(string $value)
    {
        $this->attributes['from'] = self::normalizeUrl($value);
    }


    /**
     * Set "to" attribute.
     *
     * @param string $value
     */
    public function setToAttribute(string $value)
    {
        $this->attributes['to'] = self::normalizeUrl($value);
    }
}
