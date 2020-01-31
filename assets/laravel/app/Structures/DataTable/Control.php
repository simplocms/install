<?php

namespace App\Structures\DataTable;


class Control implements \JsonSerializable
{
    /** @var string */
    protected $text;

    /** @var string */
    protected $url;

    /** @var string */
    protected $icon;

    /** @var bool */
    protected $isDelete;

    /** @var string[] */
    protected $confirmOptions;

    /** @var string */
    protected $target;

    /** @var string */
    protected $isAutomaticPost;

    /** @var string */
    protected $emits;

    /**
     * DataTable control constructor.
     * @param string $text
     * @param string $url
     * @param string|null $icon
     */
    public function __construct(string $text, string $url, ?string $icon = null)
    {
        $this->text = $text;
        $this->url = $url;
        $this->icon = $icon;
        $this->isAutomaticPost = false;
        $this->isDelete = false;
    }


    /**
     * Setup for delete control.
     *
     * @param string[] $confirmOptions
     * @return \App\Structures\DataTable\Control
     */
    public function setDelete(array $confirmOptions): Control
    {
        $this->isDelete = true;
        $this->setConfirmOptions($confirmOptions);
        return $this;
    }


    /**
     * Set confirm options.
     * @param string[] $options
     * @return \App\Structures\DataTable\Control
     */
    public function setConfirmOptions(array $options): Control
    {
        $this->confirmOptions = $options;
        return $this;
    }


    /**
     * Set target of the link.
     *
     * @param string $target
     * @return \App\Structures\DataTable\Control
     */
    public function setTarget(string $target): Control
    {
        $this->target = $target;
        return $this;
    }


    /**
     * Set event emitter.
     *
     * @param string $emits
     * @return \App\Structures\DataTable\Control
     */
    public function setEventEmitter(string $emits): Control
    {
        $this->emits = $emits;
        return $this;
    }


    /**
     * Set link to be automatically sent using POST request after click.
     *
     * @return \App\Structures\DataTable\Control
     */
    public function setAutomaticPost(): Control
    {
        $this->isAutomaticPost = true;
        return $this;
    }


    /**
     * Convert control to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'url' => $this->url,
            'icon' => $this->icon,
            'target' => $this->target,
            'isDelete' => $this->isDelete,
            'confirmOptions' => $this->confirmOptions ?? null,
            'isAutomaticPost' => $this->isAutomaticPost,
            'emits' => $this->emits
        ];
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
