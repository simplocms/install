<?php

namespace App\Services\MediaLibrary;

use Illuminate\Http\Response;
use Intervention\Image\Facades\Image;

/**
 * Class SvgFile
 * @package App\Services\MediaLibrary
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
final class SvgFile extends AbstractProcessableFile
{
    private const DEFAULT_BITMAP_SIZE = 1000;

    /**
     * @param string $operation
     * @param array $options
     */
    public function applyOperation(string $operation, array $options = []): void
    {
        // TODO: SVG transformations
    }


    /**
     * @return string
     */
    public function getShortType(): ?string
    {
        return 'svg';
    }


    /**
     * @param array $formats
     * @param array $operations
     * @return \App\Services\MediaLibrary\AbstractProcessableFile
     */
    public function convertToOneFrom(array $formats, array $operations = []): AbstractProcessableFile
    {
        if (in_array('svg', $formats, false)) {
            return $this;
        }

        $size = $this->getBitmapSize($operations);

        $image = Image::make($this->getPath())->resize($size['width'], $size['height']);

        $bitmap = new BitmapImage($this->getPath());
        $bitmap->setImage($image);
        return $bitmap->convertToOneFrom($formats);
    }


    /**
     * @param array $operations
     * @return array
     */
    private function getBitmapSize(array $operations): array
    {
        foreach ($operations as $operation) {
            switch ($operation['operation']) {
                case ImageBuilder::OPERATION_RESIZE:
                case ImageBuilder::OPERATION_FIT:
                case ImageBuilder::OPERATION_CROP:
                case ImageBuilder::OPERATION_FIT_TO_CANVAS:
                    return $operation;
            }
        }

        return ['width' => self::DEFAULT_BITMAP_SIZE, 'height' => self::DEFAULT_BITMAP_SIZE];
    }


    /**
     * @return string
     */
    public function getContent(): string
    {
        return file_get_contents($this->getPath());
    }


    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return 'image/svg+xml';
    }


    /**
     * @return mixed
     */
    public function getResponse(): Response
    {
        return new Response($this->getContent(), 200, [
            'Content-Type' => $this->getMimeType()
        ]);
    }
}
