<?php

namespace App\Models;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\StructuredDataTypeInterface;
use App\Models\Entrust\Permission;
use App\Structures\StructuredData\Types\TypePerson;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * Class User
 * @package App\Models
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string firstname
 * @property string lastname
 * @property string|null twitter_account
 * @property string username
 * @property string email
 * @property string password
 * @property string remember_token
 * @property bool enabled
 * @property string position
 * @property string about
 * @property bool protected
 * @property string locale
 */
class User extends Authenticatable implements ConvertableToStructuredDataInterface
{
    use Notifiable, EntrustUserTrait, AdvancedEloquentTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'username', 'email', 'password', 'enabled',
        'position', 'about', 'protected', 'locale', 'twitter_account'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean',
        'protected' => 'boolean'
    ];

    /**
     * Get full name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }


    /**
     * Check if user has a permission by its name.
     *
     * @param string|array $permission Permission string or array of permissions.
     * @param bool $requireAll All permissions in the array are required.
     *
     * @return bool
     */
    public function can($permission, $requireAll = false)
    {
        if ($this->hasRole(['administrator', 'programmer'])) {
            return true;
        }

        if (is_array($permission)) {
            foreach ($permission as $permName) {
                $hasPerm = $this->can($permName);

                if ($hasPerm && !$requireAll) {
                    return true;
                } elseif (!$hasPerm && $requireAll) {
                    return false;
                }
            }

            // If we've made it this far and $requireAll is FALSE, then NONE of the perms were found
            // If we've made it this far and $requireAll is TRUE, then ALL of the perms were found.
            // Return the value of $requireAll;
            return $requireAll;
        } else {
            foreach ($this->cachedRoles() as $role) {
                // Validate against the Permission table
                /** @var Permission $perm */
                foreach ($role->cachedPermissions() as $perm) {
                    if ($perm->includes($permission)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Big block of caching functionality.
     *
     * @return \App\Models\Entrust\Role[]
     */
    public function cachedRoles()
    {
        $userPrimaryKey = $this->primaryKey;
        $cacheKey = 'entrust_roles_for_user_' . $this->$userPrimaryKey;
        if (\Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            return \Cache::tags(\Config::get('entrust.role_user_table'))->remember(
                $cacheKey,
                \Config::get('cache.ttl'),
                function () {
                    return $this->roles;
                }
            );
        }

        return $this->roles;
    }

    /**
     * Many-to-Many relations with Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('entrust.role'), config('entrust.role_user_table'), config('entrust.user_foreign_key'), config('entrust.role_foreign_key'))->enabled();
    }


    /**
     * Get user images directory path
     *
     * @return string
     */
    static function getImagesDirectory()
    {
        return public_path(config('admin.path_upload')) . '/users';
    }


    /**
     * Get user image path
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        $dir = self::getImagesDirectory();
        $imageName = md5($this->id);

        return $dir . '/' . $imageName . '.jpg';
    }


    /**
     * Get user image url
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if ($this->hasCustomImage()) {
            return url(config('admin.path_upload') . '/users/' . md5($this->id) . '.jpg');
        }
        return \Gravatar::get($this->email);
    }


    /**
     * Has user custom image?
     *
     * @return bool
     */
    public function hasCustomImage()
    {
        return \File::exists($this->image_path);
    }


    /**
     * Is user administrator?
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('administrator');
    }


    /**
     * Get properties of the type.
     *
     * @return \App\Contracts\StructuredDataTypeInterface
     */
    public function toStructuredData(): StructuredDataTypeInterface
    {
        return new TypePerson([
            'name' => $this->name,
            'image' => $this->image_url,
            'familyName' => $this->lastname,
            'description' => $this->about,
            'givenName' => $this->firstname,
            'jobTitle' => $this->position
        ]);
    }
}
