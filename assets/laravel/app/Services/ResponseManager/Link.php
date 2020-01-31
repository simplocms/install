<?php

namespace App\Services\ResponseManager;

/**
 * Class Link
 * @package App\Services\ResponseManager
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
final class Link
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $rel;

    /**
     * @var string[]
     */
    private $attributes;

    /**
     * @param string|null $url
     * @param string $rel
     * @param string[] $attributes
     */
    public function __construct(string $url, string $rel, array $attributes = [])
    {
        $this->url = $url;
        $this->rel = $rel;
        $this->attributes = $attributes;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }


    /**
     * @return string
     */
    public function getHeaderString(): string
    {
        $attributes = $this->getPairedAttributes();
        return "<{$this->url}>; " . join('; ', $attributes);
    }


    /**
     * @return string
     */
    public function getMetaLink(): string
    {
        $attributes = $this->getPairedAttributes();
        return "<link href=\"{$this->url}\" " . join(' ', $attributes) . " />";
    }


    /**
     * @return string[]
     */
    private function getPairedAttributes(): array
    {
        $attributes = ['rel="' . $this->rel . '"'];
        foreach ($this->attributes as $key => $value) {
            $attributes[] = $key . '="' . $value . '"';
        }

        return $attributes;
    }
}
