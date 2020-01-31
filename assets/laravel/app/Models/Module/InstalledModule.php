<?php

namespace App\Models\Module;

use App\Structures\Enums\SingletonEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InstalledModule
 * @property Module $module
 * @property string name
 *
 * @method static \Illuminate\Database\Eloquent\Builder enabled()
 */
class InstalledModule extends Model
{
    /**
     * Model table
     *
     * @var string
     */
    protected $table = 'installed_modules';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = [ 'name', 'enabled' ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean'
    ];


    /**
     * Find named module
     *
     * @param $name
     * @return \App\Models\Module\InstalledModule|null
     */
    static function findNamed($name) 
    {
        return self::where('name', $name)->first();
    }


    /**
     * Get module instance
     *
     * @return Module|null
     */
    public function getModuleAttribute()
    {
        return SingletonEnum::modules()->find($this->name);
    }


    /**
     * Check if module exists.
     *
     * @return bool
     */
    public function checkModuleExists(): bool
    {
        return $this->module !== null;
    }


    /**
     * Only enabled modules
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeEnabled($query) 
    {
        $query->where('enabled', '=',1);
    }
}
