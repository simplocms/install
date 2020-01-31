<?php

namespace App\Services\FrontWebTools;

final class ToolbarOptions
{
    public const STATUS_DANGER = 'danger';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_DEFAULT = 'default';

    /**
     * @var array
     */
    protected $controls;

    /**
     * @var array
     */
    protected $statuses;

    /**
     * @var \App\Services\FrontWebTools\ToolbarSwitch
     */
    protected $switch;

    /**
     * ToolbarOptions constructor.
     */
    public function __construct()
    {
        $this->controls = [];
        $this->statuses = [];
    }


    /**
     * Add toolbar control.
     *
     * @param string $text
     * @param string $uri
     * @param string $icon
     * @return \App\Services\FrontWebTools\ToolbarOptions
     */
    public function addControl(string $text, string $uri, string $icon): ToolbarOptions
    {
        $this->controls[] = compact('text', 'uri', 'icon');
        return $this;
    }


    /**
     * Set toolbar status.
     *
     * @param string $text
     * @param string $level
     * @return \App\Services\FrontWebTools\ToolbarOptions
     */
    public function addStatus(string $text, $level = self::STATUS_DEFAULT): ToolbarOptions
    {
        $this->statuses[] = compact('text', 'level');
        return $this;
    }


    /**
     * Activate toolbar switch.
     *
     * @param string $name
     * @param array $options
     * @return \App\Services\FrontWebTools\ToolbarSwitch
     */
    public function activateSwitch(string $name, array $options): ToolbarSwitch
    {
        return $this->switch = new ToolbarSwitch($name, $options);
    }


    /**
     * Convert to array.
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'controls' => $this->controls,
            'statuses' => $this->statuses,
            'switch' => $this->switch ? $this->switch->toArray() : null,
        ];
    }
}
