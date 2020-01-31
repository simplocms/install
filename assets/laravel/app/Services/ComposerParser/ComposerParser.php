<?php

namespace App\Services\ComposerParser;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;

final class ComposerParser
{
    public const ERROR_NOT_FOUND = 'not_fount';
    public const ERROR_INVALID = 'invalid';

    /**
     * @var mixed[]
     */
    private $data;

    /**
     * @var string
     */
    private $error;

    /**
     * @return \App\Services\ComposerParser\ComposerParser
     */
    public static function make(): ComposerParser
    {
        return new static();
    }


    /**
     * Load data from composer.json file.
     */
    private function loadData(): bool
    {
        $this->data = [];

        try {
            $data = \File::get(base_path('composer.json'));
        } catch (FileNotFoundException $e) {
            $this->error = self::ERROR_NOT_FOUND;
            return false;
        }

        $this->data = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error = self::ERROR_INVALID;
            return false;
        }

        return true;
    }


    /**
     * Get an property from an array using “dot” notation.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (is_null($this->data)) {
            $this->loadData();
        }

        return Arr::get($this->data, $key, $default);
    }


    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->get('version', '?');
    }
}
