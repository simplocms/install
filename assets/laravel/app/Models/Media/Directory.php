<?php

namespace App\Models\Media;

use App\Services\MediaLibrary\MediaLibrary;
use App\Structures\ClosureTable\HierarchicModel;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\FullTextSearchTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Directory - media library directory
 * @package App\Models
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string name
 * @property string path
 * @property string storage
 * @property int parent_id
 *
 * @property-read \Illuminate\Support\Collection|\App\Models\Media\File[] files
 *
 * @method static \Illuminate\Database\Eloquent\Builder ofRoot()
 */
class Directory extends HierarchicModel
{
    use AdvancedEloquentTrait, FullTextSearchTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'media_directories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'path', 'storage', 'parent_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'parent_id' => 'int',
    ];

    /**
     * Searchable columns for full-text search.
     *
     * @var array
     */
    protected $searchable = ['name'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (Directory $directory) {
            if ($directory->isDirty('name')) {
                $directory->createUniqueNameAndPath();
            }
        });

        static::saved(function (Directory $directory) {
            if ($directory->wasRecentlyCreated && $directory->isDirty('path')) {
                $directory->getMediaLibrary()->createDir($directory->path);
            } else if ($directory->isDirty('path')) {
                $directory->getMediaLibrary()->createDir($directory->path);
                $directory->updateContentsPath();
                $directory->getMediaLibrary()->deleteDir($directory->getOriginal('path'));
            }
        });

        static::deleting(function (Directory $directory) {
            $directory->deleteContent();
        });

        static::deleted(function (Directory $directory) {
            $directory->getMediaLibrary()->deleteDir($directory->path);
        });
    }


    /**
     * Files in directory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'directory_id', 'id');
    }


    /**
     * Convert directory to array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = [
            'id' => $this->getKey(),
            'name' => $this->name,
            'children' => []
        ];

        if ($this->relationLoaded('children')) {
            $data['children'] = $this->children->toArray();
        }

        return $data;
    }


    /**
     * Creates unique name and path.
     */
    protected function createUniqueNameAndPath(): void
    {
        $this->name = $this->getUniqueName($this->name);
        $dirName = $this->getDirectoryName();
        $this->path = ($this->parent ? "{$this->parent->path}/" : '') . $dirName;
    }


    /**
     * Get unique name attribute for the directory.
     *
     * @param string $name
     * @return string
     */
    protected function getUniqueName(string $name): string
    {
        $conflictDirectory = self::query()->where('parent_id', $this->parent_id)
            ->where('name', $name)->first();

        if (!$conflictDirectory) {
            return $name;
        }

        $name = preg_replace('%([_\%]+)%', '\\\\$1', $name);
        $conflictsMap = self::query()->where('parent_id', $this->parent_id)
            ->where('name', 'like', "$name %")
            ->pluck('id', 'name');

        $tryNumber = 1;
        while ($tryNumber <= 1000) {
            $uniqueName = "$name $tryNumber";
            if (!$conflictsMap->has($uniqueName)) {
                return $uniqueName;
            }

            $tryNumber++;
        }

        return str_random(32);
    }


    /**
     * Get media library for initialized for this directory.
     *
     * @return \App\Services\MediaLibrary\MediaLibrary
     */
    protected function getMediaLibrary(): MediaLibrary
    {
        return new MediaLibrary($this->storage);
    }


    /**
     * Select files of root directory.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeOfRoot($query)
    {
        $query->whereNull('parent_id');
    }


    /**
     * Delete content of the directory.
     */
    protected function deleteContent(): void
    {
        // Delete files in directory.
        $this->files->each(function (File $file) {
            $file->delete();
        });

        // Delete sub-directories.
        $this->children->each(function (Directory $directory) {
            $directory->delete();
        });
    }


    /**
     * Get directory name from the name.
     *
     * @return string
     */
    protected function getDirectoryName(): string
    {
        return str_slug($this->name);
    }


    /**
     * Update path of all contents.
     */
    protected function updateContentsPath(): void
    {
        // Move files in directory.
        $this->files->each(function (File $file) {
            $file->update([
                'path' => "{$this->path}/{$file->name}.{$file->extension}"
            ]);
        });

        // Move sub-directories.
        $this->children->each(function (Directory $directory) {
            $directory->update([
                'path' => "{$this->path}/{$directory->getDirectoryName()}"
            ]);
        });
    }
}
