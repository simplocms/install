<?php

namespace App\Services\FrontWebTools;

final class ToolbarSwitch
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $options;

    /**
     * @var mixed[]|null
     */
    private $action;

    /**
     * @var mixed
     */
    private $active;

    /**
     * ToolbarOptions constructor.
     * @param string $name
     * @param string[] $options
     */
    public function __construct(string $name, array $options)
    {
        $this->name = $name;
        $this->options = $options;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setActionPost(string $url): self
    {
        $this->action = [
            'type' => 'post',
            'url' => $url
        ];

        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setActive($value): self
    {
        $this->active = $value;
        return $this;
    }

    /**
     * Convert to array.
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'options' => $this->options,
            'active' => $this->active,
            'action' => $this->action
        ];
    }
}
