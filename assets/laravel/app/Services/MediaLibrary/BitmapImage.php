<?php

namespace App\Services\MediaLibrary;

use Intervention\Image\Constraint;
use Intervention\Image\Image;

/**
 * Class BitmapImage
 * @package App\Services\MediaLibrary
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
final class BitmapImage extends AbstractProcessableFile
{
    private const SHORT_TYPE_PNG = 'png';
    private const SHORT_TYPE_JPEG = 'jpeg';
    private const SHORT_TYPE_GIF = 'gif';
    private const SHORT_TYPE_BMP = 'bmp';
    private const SHORT_TYPE_WEBP = 'webp';
    private const SHORT_TYPE_ICO = 'ico';

    /** @var \Intervention\Image\Image */
    private $imageInstance;

    /**
     * Formats for mime types.
     *
     * @var array
     */
    protected $shortTypes = [
        'image/png' => self::SHORT_TYPE_PNG,
        'image/jpeg' => self::SHORT_TYPE_JPEG,
        'image/gif' => self::SHORT_TYPE_GIF,
        'image/bmp' => self::SHORT_TYPE_BMP,
        'image/webp' => self::SHORT_TYPE_WEBP,
        'image/x-icon' => self::SHORT_TYPE_ICO,
        'image/x-ms-bmp' => self::SHORT_TYPE_BMP
    ];

    /**
     * @return \Intervention\Image\Image
     */
    public function getImage(): Image
    {
        if (!$this->imageInstance) {
            $this->imageInstance = \Image::make($this->getPath());
        }

        return $this->imageInstance;
    }


    /**
     * @param string $operation
     * @param array $options
     */
    public function applyOperation(string $operation, array $options = []): void
    {
        switch ($operation) {
            case ImageBuilder::OPERATION_RESIZE:
                $this->getImage()->resize($options['width'], $options['height'], function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                break;
            case ImageBuilder::OPERATION_FIT:
                $this->getImage()->fit($options['width'], $options['height'], function (Constraint $constraint) {
                    $constraint->upsize();
                });
                break;
            case ImageBuilder::OPERATION_CROP:
                $this->getImage()->crop($options['width'], $options['height']);
                break;
            case ImageBuilder::OPERATION_GREYSCALE:
                $this->getImage()->greyscale();
                break;
            case ImageBuilder::OPERATION_FIT_TO_CANVAS:
                $this->getImage()->resize($options['width'], $options['height'], function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->resizeCanvas(
                    $options['width'], $options['height'], 'center', false, $options['color']
                );
                break;
        }
    }


    /**
     * @return string
     */
    public function getShortType(): ?string
    {
        return $this->shortTypes[$this->getImage()->mime()] ?? null;
    }


    /**
     * @param array $formats
     * @param array $operations
     * @return \App\Services\MediaLibrary\AbstractProcessableFile
     */
    public function convertToOneFrom(array $formats, array $operations = []): AbstractProcessableFile
    {
        $bitmapFormats = self::getBitmapFormatsByPreference();

        foreach ($formats as $format) {
            if (isset($bitmapFormats[$format])) {
                $this->getImage()->encode($format);
                break;
            }
        }

        return $this;
    }


    /**
     * @return array
     */
    public static function getBitmapFormatsByPreference(): array
    {
        return [
            self::SHORT_TYPE_PNG => 1,
            self::SHORT_TYPE_JPEG => 2,
            self::SHORT_TYPE_WEBP => 3,
            self::SHORT_TYPE_GIF => 4,
            self::SHORT_TYPE_BMP => 5,
            self::SHORT_TYPE_ICO => 6,
        ];
    }


    /**
     * @return string
     */
    public function getContent(): string
    {
        if (!$this->getImage()->isEncoded()) {
            $this->getImage()->encode(null, 100);
        }

        return $this->getImage()->getEncoded();
    }


    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->getImage()->mime();
    }


    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->getImage()->response(null, 100);
    }

    /**
     * @param \Intervention\Image\Image $image
     */
    public function setImage(Image $image): void
    {
        $this->imageInstance = $image;
    }
}
