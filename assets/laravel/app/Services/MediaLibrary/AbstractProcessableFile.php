<?php

namespace App\Services\MediaLibrary;

/**
 * Class AbstractProcessableFile
 * @package App\Services\MediaLibrary
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
abstract class AbstractProcessableFile
{
    /**
     * @var string
     */
    private $path;

    /**
     * AbstractProcessableFile constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {

        $this->path = $path;
    }

    /**
     * @param string $operation
     * @param array $options
     */
    abstract public function applyOperation(string $operation, array $options = []): void;

    /**
     * @return string
     */
    abstract public function getShortType(): ?string;

    /**
     * @return string
     */
    abstract public function getMimeType(): string;

    /**
     * @param array $formats
     * @param array $operations
     * @return \App\Services\MediaLibrary\AbstractProcessableFile
     */
    abstract public function convertToOneFrom(array $formats, array $operations = []): AbstractProcessableFile;

    /**
     * @return string
     */
    abstract public function getContent(): string;

    /**
     * @return mixed
     */
    abstract public function getResponse();


    /**
     * @param string $path
     * @return \App\Services\MediaLibrary\AbstractProcessableFile
     */
    public static function makeFromPath(string $path): AbstractProcessableFile
    {
        $mime = mime_content_type($path);

        switch ($mime){
            case 'image/svg+xml':
                return new SvgFile($path);
            default:
                return new BitmapImage($path);
        }
    }


    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

}
