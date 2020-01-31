<?php

namespace App\Models\Media;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\StructuredDataTypeInterface;
use App\Services\MediaLibrary\ImageBuilder;
use App\Services\MediaLibrary\MediaLibrary;
use App\Structures\Enums\SingletonEnum;
use App\Structures\StructuredData\Types\TypeImageObject;
use App\Traits\AdvancedEloquentTrait;
use App\Traits\FullTextSearchTrait;
use App\Traits\PrefetchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;

/**
 * Class File - media library file
 * @package App\Models\Media
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property string name
 * @property string extension
 * @property string path
 * @property string storage
 * @property int size
 * @property string description
 * @property string image_resolution
 * @property int|null directory_id
 * @property string mime_type
 *
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 *
 * @property-read \App\Models\Media\Directory|null directory
 *
 * @method static \Illuminate\Database\Eloquent\Builder ofRoot()
 * @method static \Illuminate\Database\Eloquent\Builder imagesOnly()
 */
class File extends Model implements ConvertableToStructuredDataInterface
{
    use AdvancedEloquentTrait, FullTextSearchTrait, PrefetchTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'media_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'extension', 'path', 'storage', 'size', 'description',
        'image_resolution', 'directory_id', 'mime_type'
    ];

    /**
     * Searchable columns for full-text search.
     *
     * @var array
     */
    protected $searchable = ['name', 'description'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'size' => 'int',
        'directory_id' => 'int'
    ];

    /**
     * Inner state of the placeholder model.
     *
     * @var true|null
     */
    protected $isPlaceholder;

    /**
     * URL of image to use as a placeholder.
     *
     * @var string|null
     */
    protected $placeholderUrl;

    /** @var bool|null Is file being overrided? */
    private $isOverriding;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (File $file) {
            if ($file->isDirty(['name', 'extension'])) {
                $file->createUniqueNameAndPath();
            }
        });

        static::saved(function (File $file) {
            if (!$file->wasRecentlyCreated && !$file->isOverriding && $file->isDirty('path')) {
                $file->getMediaLibrary()->moveFile($file->getOriginal('path'), $file->path);
            }
            $file->isOverriding = false;
        });

        static::deleted(function (File $file) {
            $file->getMediaLibrary()->deleteFile($file->path);
        });
    }


    /**
     * Directory to which the file belongs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function directory(): BelongsTo
    {
        return $this->belongsTo(Directory::class, 'directory_id');
    }


    /**
     * Convert file to array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'url' => $this->getUrl(),
            'directory_id' => $this->directory_id,
            'extension' => $this->extension,
            'size' => $this->size,
            'description' => $this->description,
            'image_resolution' => $this->image_resolution,
            'supported_image' => $this->isProcessableImage(),
            'selectable_image' => $this->isSelectableImage(),
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp,
        ];
    }


    /**
     * Normalize the name attribute and set up path.
     *
     * @return void
     */
    protected function createUniqueNameAndPath()
    {
        $name = str_slug($this->name);
        $this->name = $this->getUniqueName($name);
        $directoryPath = $this->directory ? "{$this->directory->path}/" : '';
        $this->path = $directoryPath . $this->name . '.' . $this->extension;
    }


    /**
     * Get unique name attribute for the file.
     *
     * @param string $name
     * @return string
     */
    protected function getUniqueName(string $name): string
    {
        $conflictFile = self::query()->where('directory_id', $this->directory_id)
            ->where('extension', $this->extension)
            ->where('name', $name)->first();

        if (!$conflictFile) {
            return $name;
        }

        $conflictsMap = self::query()->where('directory_id', $this->directory_id)
            ->where('extension', $this->extension)
            ->where('name', 'like', "$name-%")
            ->pluck('id', 'name');

        $tryNumber = 1;
        while ($tryNumber <= 1000) {
            $uniqueName = strlen($name) ? "$name-$tryNumber" : $tryNumber;
            if (!$conflictsMap->has($uniqueName)) {
                return $uniqueName;
            }

            $tryNumber++;
        }

        return str_random(32);
    }


    /**
     * Get human-readable file size.
     *
     * @param int $precision
     * @return string
     */
    public function getHumanSize(int $precision = 2): string
    {
        static $units = array('B', 'kB', 'MB', 'GB', 'TB');
        static $step = 1024;
        $i = 0;
        $size = $this->size;

        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }

        return round($size, $precision) . $units[$i];
    }


    /**
     * Get relative path with file name.
     *
     * @return string
     */
    public function getPathWithName(): string
    {
        return $this->path;
    }


    /**
     * Get name with extension.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->name . (is_null($this->extension) ? '' : ".{$this->extension}");
    }


    /**
     * Get preview link for the media library.
     *
     * @return string
     */
    public function getMediaLibraryPreview(): string
    {
        return $this->makeLink()->fitCanvas(140, 100)->getUrl();
    }


    /**
     * Create link builder for this file.
     *
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function makeLink(): ImageBuilder
    {
        if ($this->isPlaceholder) {
            return new ImageBuilder();
        }

        return $this->getMediaLibrary()->makeLink($this);
    }


    /**
     * Check if file is processable image.
     *
     * @return bool
     */
    public function isProcessableImage(): bool
    {
        return $this->getMediaLibrary()->isMimeProcessableFile($this->mime_type);
    }


    /**
     * Check if file is selectable image.
     *
     * @return bool
     */
    public function isSelectableImage(): bool
    {
        return $this->getMediaLibrary()->isMimeSelectableImage($this->mime_type);
    }


    /**
     * Get file data.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getFileData(): string
    {
        return $this->getMediaLibrary()->getFileData($this->getPathWithName());
    }


    /**
     * Get file data.
     *
     * @param string $data
     * @return bool
     */
    public function updateFileData(string $data): bool
    {
        return $this->getMediaLibrary()->updateFileData($this->getPathWithName(), $data);
    }


    /**
     * Override file with uploaded file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool
     */
    public function overrideFile(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();

        $this->fill([
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $mimeType,
            'size' => $file->getSize(),
        ]);

        if (!$this->isSvg() && $this->getMediaLibrary()->isMimeProcessableFile($mimeType)) {
            $this->image_resolution = $this->getMediaLibrary()->getImageResolution($file->getRealPath());
        } else {
            $this->image_resolution = null;
        }

        $originalPath = $this->path;
        $this->isOverriding = true;
        $this->save();
        $result = $this->getMediaLibrary()->saveUploadedFile($file, $this->path);

        if ($result === false) {
            $this->update($this->original);
            return false;
        }

        $this->getMediaLibrary()->clearFileCache($originalPath);
        return true;
    }


    /**
     * Rotate image.
     *
     * @param bool $clockwise
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function rotateImage(bool $clockwise = true): void
    {
        if (!$this->isProcessableImage()) {
            return;
        }

        $image = \Image::make($this->getFileData());
        $image->rotate(90 * ($clockwise ? -1 : 1));
        $this->updateFileData($image->encode(null, 100)->getEncoded());
        $this->image_resolution = "{$image->width()}x{$image->height()}";

        $this->save();
    }


    /**
     * Resize image.
     *
     * @param int $width
     * @param int $height
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function resize(int $width, int $height): void
    {
        if (!$this->isProcessableImage()) {
            return;
        }

        /** @var \Intervention\Image\Image $image */
        $image = \Image::make($this->getFileData());
        $image->resize($width, $height, function (\Intervention\Image\Constraint $constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $fileData = $image->encode(null, 85)->getEncoded();
        $this->updateFileData($fileData);
        $this->image_resolution = "{$image->width()}x{$image->height()}";
        $this->size = strlen($fileData);

        $this->save();
    }


    /**
     * Get media library for initialized for this file.
     *
     * @return \App\Services\MediaLibrary\MediaLibrary
     */
    protected function getMediaLibrary(): MediaLibrary
    {
        return new MediaLibrary($this->storage);
    }


    /**
     * Get file original URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->makeLink()->getUrl();
    }


    /**
     * Select files of root directory.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeOfRoot($query)
    {
        $query->whereNull('directory_id');
    }


    /**
     * Create image placeholder model.
     *
     * @return \App\Models\Media\File
     */
    public static function imagePlaceholder(): File
    {
        $file = new self();
        $file->isPlaceholder = true;

        return $file;
    }


    /**
     * Select files of root directory.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeImagesOnly($query)
    {
        $imageTypes = SingletonEnum::mediaLibrary()->getSelectableImageMimeTypes();
        $query->whereIn('mime_type', $imageTypes);
    }


    /**
     * Get properties of the type.
     *
     * @return \App\Contracts\StructuredDataTypeInterface
     */
    public function toStructuredData(): StructuredDataTypeInterface
    {
        $common = [
            'contentUrl' => $this->getUrl(),
            'uploadDate' => $this->created_at,
            'name' => $this->name,
            'encodingFormat' => $this->mime_type,
            'contentSize' => $this->size
        ];

        if ($this->isSelectableImage()) {
            return new TypeImageObject(array_merge($common, [
                'caption' => $this->description,
                'width' => $this->image_resolution ? $this->getImageWidth() . ' px' : null,
                'height' =>  $this->image_resolution ? $this->getImageHeight() . ' px' : null,
            ]));
        }

        return new TypeImageObject(array_merge($common, [
            'description' => $this->description
        ]));
    }


    /**
     * Get image width.
     *
     * @return int|null
     */
    public function getImageWidth(): ?int
    {
        if ($this->isProcessableImage()) {
            $size = explode('x', $this->image_resolution);
            return intval($size[0]) ?? null;
        }

        return null;
    }


    /**
     * Get image height.
     *
     * @return int|null
     */
    public function getImageHeight(): ?int
    {
        if ($this->isProcessableImage()) {
            $size = explode('x', $this->image_resolution);
            return intval($size[1]) ?? null;
        }

        return null;
    }


    /**
     * @return bool
     */
    public function isSvg(): bool
    {
        return $this->mime_type === 'image/svg+xml';
    }
}
