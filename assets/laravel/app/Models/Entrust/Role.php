<?php namespace App\Models\Entrust;

use Zizaco\Entrust\EntrustRole;
use App\Traits\AdvancedEloquentTrait;

class Role extends EntrustRole
{
    use AdvancedEloquentTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'description', 'enabled'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean'
    ];

    /**
     * Automatically set name of role
     */
    public function setAutomaticName()
    {
        $this->name = self::createFriendlyName($this->display_name);
    }


    /**
     * Create friendly name for role.
     *
     * @param string $name
     * @return string
     */
    static function createFriendlyName(string $name): string
    {
        return str_slug($name, '-') . time();
    }


    /**
     * Is editable? Protects roles programmer and administrator
     *
     * @return bool
     */
    public function isEditable()
    {
        return !($this->name == 'programmer' || $this->name == 'administrator');
    }


    /**
     * Enabled roles only
     *
     * @param $query
     */
    public function scopeEnabled($query)
    {
        $query->where('enabled', 1);
    }


    /**
     * Save the inputted permissions.
     *
     * @param array $permissions
     *
     * @return void
     */
    public function saveNamedPermissions(array $permissions)
    {
        $permissionsIds = [];

        foreach ($permissions as $permissionName) {
            $permission = Permission::findNamed($permissionName);
            if (!$permission || !$permission->exists) {
                $permission = Permission::create([
                    'name' => $permissionName
                ]);
            }

            $permissionsIds[] = $permission->id;
        }

        $this->savePermissions($permissionsIds);
    }


    /**
     * Get all permissions groups grouped by names.
     *
     * @return array
     */
    public function getAllPermissionsNames()
    {
        $rolePermissions = $this->perms->pluck(null, 'name');
        $searchedAreas = [];
        $permissionsNames = [];

        // there are saved only permissions with highest weight
        foreach ($rolePermissions as $rolePermissionName => $_) {
            list($area, $permission) = preg_split('~-(?=[^-]*$)~', $rolePermissionName);
            $permissionsNames[$rolePermissionName] = true;

            // if was already searched for lower permissions,
            // it can be sure, that all lower permissions were added to the list
            if (isset($searchedAreas[$area])) continue;
            $searchedAreas[$area] = true;

            // find permissions with lower weight and add them to the list
            foreach (config('permissions.groups') as $groupId => $group) {
                if (!isset($group['permissions'][$permission]) || !in_array($area, $group['areas'])) continue;

                $permissionWeight = $group['permissions'][$permission];
                foreach ($group['permissions'] as $groupPermissionName => $groupPermissionWeight) {
                    if ($permissionWeight > $groupPermissionWeight) {
                        $permissionsNames["$area-$groupPermissionName"] = true;
                    }
                }
            }
        }

        return $permissionsNames;
    }


    /**
     * Get cached permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     */
    public function cachedPermissions()
    {
        $rolePrimaryKey = $this->primaryKey;
        $cacheKey = 'entrust_permissions_for_role_' . $this->$rolePrimaryKey;
        if (\Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            return \Cache::tags(\Config::get('entrust.permission_role_table'))->remember(
                $cacheKey,
                \Config::get('cache.ttl', 60),
                function () {
                    return $this->perms;
                }
            );
        } else return $this->perms;
    }
}
