<?php
/**
 * ImageBuilder.php created by Patrik Václavek
 */

namespace App\Services\MediaLibrary;

use App\Contracts\ConvertableToStructuredDataInterface;
use App\Contracts\StructuredDataTypeInterface;
use App\Models\Media\File;
use App\Structures\Enums\SingletonEnum;
use App\Structures\StructuredData\Types\TypeImageObject;
use Intervention\Image\Constraint;
use Intervention\Image\Size;

class ImageBuilder implements ConvertableToStructuredDataInterface
{
    const OPERATION_RESIZE = 'size';
    const OPERATION_FIT = 'fit';
    const OPERATION_CROP = 'crop';
    const OPERATION_GREYSCALE = 'bw';
    const OPERATION_FIT_TO_CANVAS = 'cfit';
    const OPERATION_FORMAT = 'format';

    /**
     * @var \App\Models\Media\File
     */
    protected $file;

    /**
     * Operations to be applied to an image.
     *
     * @var array
     */
    protected $operations = [];

    /**
     * Final format of image.
     *
     * @var string
     */
    protected $format;

    /**
     * Url address of the fallback image.
     *
     * @var string|null
     */
    protected $fallbackUrl;

    /**
     * ImageBuilder constructor.
     * @param \App\Models\Media\File|null $file
     */
    public function __construct(?File $file = null)
    {
        $this->file = $file;
    }


    /**
     * Resizes current image based on given width and/or height.
     *
     * @param int $width
     * @param int $height
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function resize(int $width, int $height): ImageBuilder
    {
        $operation = self::OPERATION_RESIZE;
        $this->operations[] = compact('operation', 'width', 'height');

        return $this;
    }


    /**
     * Combine cropping and resizing to format image in a smart way. The method will find the best
     * fitting aspect ratio of your given width and height on the current image automatically,
     * cut it out and resize it to the given dimension.
     *
     * @param int $width
     * @param int $height
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function fit(int $width, int $height): ImageBuilder
    {
        $operation = self::OPERATION_FIT;
        $this->operations[] = compact('operation', 'width', 'height');

        return $this;
    }


    /**
     * Cut out a rectangular part of the current image with given width and height.
     *
     * @param int $width
     * @param int $height
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function crop(int $width, int $height): ImageBuilder
    {
        $operation = self::OPERATION_CROP;
        $this->operations[] = compact('operation', 'width', 'height');

        return $this;
    }


    /**
     * Turns image into a greyscale version.
     *
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function greyscale(): ImageBuilder
    {
        $operation = self::OPERATION_GREYSCALE;
        $this->operations[] = compact('operation');

        return $this;
    }


    /**
     * Resize the boundaries of the current image to given width and height.
     * You can also pass a background color for the emerging area of the image.
     *
     * @param int $width
     * @param int $height
     * @param string|null $color
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function fitCanvas(int $width, int $height, ?string $color = null): ImageBuilder
    {
        $operation = self::OPERATION_FIT_TO_CANVAS;
        $this->operations[] = compact('operation', 'width', 'height', 'color');

        return $this;
    }


    /**
     * Returns image in one of given formats.
     *
     * @param array $formats
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function allowedFormats(array $formats): ImageBuilder
    {
        $this->format = implode(',', $formats);

        return $this;
    }


    /**
     * Returns image in PNG format.
     *
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function formatPNG(): ImageBuilder
    {
        return $this->allowedFormats(['png']);
    }


    /**
     * Build url query.
     *
     * @return string
     */
    protected function buildQuery(): string
    {
        $queryData = [];

        foreach ($this->operations as $operation) {
            $value = null;

            switch ($operation['operation']) {
                case self::OPERATION_RESIZE:
                case self::OPERATION_FIT:
                case self::OPERATION_CROP:
                    $value = "{$operation['width']}x{$operation['height']}";
                    break;
                case self::OPERATION_FIT_TO_CANVAS:
                    $value = "{$operation['width']}x{$operation['height']}";
                    if ($operation['color']) {
                        $value .= "x" . $operation['color'];
                    }
                    break;
            }

            $queryData[] = $operation['operation'] . (is_null($value) ? '' : '=' . $value);
        }

        if ($this->format) {
            $queryData[self::OPERATION_FORMAT] = self::OPERATION_FORMAT . "={$this->format}";
        }

        return join('&', $queryData);
    }


    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        if (!$this->file) {
            return $this->getFallbackUrl();
        }

        $query = $this->buildQuery();
        return route('media', $this->file->getPathWithName()) . ($query ? "?$query" : '');
    }


    /**
     * Get url.
     *
     * @return int[]|null[]
     */
    public function getSize(): array
    {
        if (!$this->file) {
            return [1000, 1000]; // placeholder size
        }

        $imageWidth = $this->file->getImageWidth();
        $imageHeight = $this->file->getImageHeight();

        foreach ($this->operations as $operation) {

            switch ($operation['operation']) {
                case self::OPERATION_RESIZE:
                    $size = new Size($imageWidth ?? $operation['width'], $imageHeight ?? $operation['height']);

                    $size = $size->resize($operation['width'], $operation['height'], static function (Constraint $constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    return [
                        $size->getWidth(),
                        $size->getHeight()
                    ];
                case self::OPERATION_FIT:
                    return [
                        $imageWidth ? min($operation['width'], $imageWidth) : $operation['width'],
                        $imageHeight ? min($operation['height'], $imageHeight) : $operation['height'],
                    ];
                case self::OPERATION_CROP:
                    return [$operation['width'], $operation['height']];
                case self::OPERATION_FIT_TO_CANVAS:
                    return [$operation['width'], $operation['height']];
            }
        }

        return [$this->file->getImageWidth(), $this->file->getImageHeight()];
    }

    /**
     * Get fallback image url.
     *
     * @return string
     */
    public function getFallbackUrl(): string
    {
        if (!$this->fallbackUrl) {
            return url('media/images/media-placeholder.png');
        }

        return $this->fallbackUrl;
    }


    /**
     * Set fallback image url.
     *
     * @param string $imageUrl
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function setFallbackUrl(string $imageUrl): ImageBuilder
    {
        $this->fallbackUrl = $imageUrl;
        return $this;
    }


    /**
     * Get image.
     *
     * @param \App\Services\MediaLibrary\AbstractProcessableFile $file
     * @return \App\Services\MediaLibrary\AbstractProcessableFile
     */
    public function applyOnFile(AbstractProcessableFile $file): AbstractProcessableFile
    {
        if ($this->format) {
            $allowedFormats = explode(',', $this->format);

            if (!in_array($file->getShortType(), $allowedFormats, false)) {
                $file = $file->convertToOneFrom($allowedFormats, $this->operations);
            }
        }

        foreach ($this->operations as $operation) {
            $file->applyOperation($operation['operation'], $operation);
        }

        return $file;
    }


    /**
     * Make builder from url parameters.
     *
     * @param array $params
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public static function fromParameters(array $params): ImageBuilder
    {
        $builder = new ImageBuilder();

        foreach ($params as $operation => $value) {
            switch ($operation) {
                case self::OPERATION_RESIZE:
                    $size = explode('x', $value);
                    $builder->resize(intval($size[0]), intval($size[1] ?? $size[0]));
                    break;
                case self::OPERATION_FIT:
                    $size = explode('x', $value);
                    $builder->fit(intval($size[0]), intval($size[1] ?? $size[0]));
                    break;
                case self::OPERATION_CROP:
                    $size = explode('x', $value);
                    $builder->crop(intval($size[0]), intval($size[1] ?? $size[0]));
                    break;
                case self::OPERATION_GREYSCALE:
                    $builder->greyscale();
                    break;
                case self::OPERATION_FIT_TO_CANVAS:
                    $values = explode('x', $value);

                    $hexPattern = '/^#?([a-f0-9]{1,2})([a-f0-9]{1,2})([a-f0-9]{1,2})$/i';
                    $color = null;
                    if (isset($values[2]) && preg_match($hexPattern, $values[2])) {
                        $color = $values[2];
                    }

                    $builder->fitCanvas(
                        intval($values[0]), intval($values[1] ?? $values[0]), $color
                    );
                    break;
                case self::OPERATION_FORMAT:
                    $formats = explode(',', $value);
                    if ($formats) {
                        $builder->allowedFormats($formats);
                    }
                    break;
            }
        }

        return $builder;
    }


    /**
     * Get cache key for given file.
     *
     * @return string
     */
    protected function getCacheKey(): string
    {
        return $this->buildQuery();
    }


    /**
     * Get image.
     *
     * @param string $path
     * @return \App\Services\MediaLibrary\AbstractProcessableFile
     */
    public function getFile(string $path): AbstractProcessableFile
    {
        $fileCache = new FileCache($path);

        $cacheKey = $this->getCacheKey();
        $file = $fileCache->getCachedFile($cacheKey);

        if (!$file) {
            $file = SingletonEnum::mediaLibrary()->getProcessableFile($path);

            if ($this->canApplyOnFile($file)) {
                $file = $fileCache->cacheFile($cacheKey, $this->applyOnFile($file));
            }
        }

        return $file;
    }


    /**
     * Get url.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getUrl();
    }


    /**
     * Get properties of the type.
     *
     * @return \App\Contracts\StructuredDataTypeInterface
     */
    public function toStructuredData(): StructuredDataTypeInterface
    {
        return new TypeImageObject([
            'url' => $this->getUrl(),
            'uploadDate' => $this->file->created_at ?? null,
            'name' => $this->file->name ?? null,
            'encodingFormat' => $this->file->mime_type ?? null,
            'contentSize' => $this->file->size ?? null,
            'caption' => $this->file->description ?? null,
        ]);
    }

    /**
     * Check if builder can apply operations on specific file.
     * Basically exception for SVGs not te be modified without reformatting.
     *
     * @param \App\Services\MediaLibrary\AbstractProcessableFile $file
     * @return bool
     */
    public function canApplyOnFile(AbstractProcessableFile $file): bool
    {
        return !($file instanceof SvgFile && !$this->format);
    }
}
