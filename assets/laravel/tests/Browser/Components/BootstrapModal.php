<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class BootstrapModal extends BaseComponent
{
    protected $modalSelector;

    public function __construct(string $selector)
    {
        $this->modalSelector = $selector;
    }


    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return $this->modalSelector;
    }


    /**
     * Assert that the browser page contains the component.
     *
     * @param  \Laravel\Dusk\Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->waitFor($this->selector())->assertVisible($this->selector());
    }


    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@content' => '> .modal-dialog > .modal-content',
            '@title' => '> .modal-dialog > .modal-content > .modal-header > .modal-title',
            '@body' => '> .modal-dialog > .modal-content > .modal-body',
            '@footer' => '> .modal-dialog > .modal-content > .modal-footer',
            '@cancel' => '> .modal-dialog > .modal-content > .modal-footer > button[data-dismiss="modal"]',
            '@confirm' => '> .modal-dialog > .modal-content > .modal-footer > button:not([data-dismiss])'
        ];
    }


    /**
     * Confirm modal.
     *
     * @param \Laravel\Dusk\Browser $browser
     */
    public function confirm(Browser $browser)
    {
        $browser->click('@confirm');
    }


    /**
     * Assert modal os going to be closed.
     *
     * @param Browser $browser
     */
    public function assertClose(Browser $browser)
    {
        $browser->waitUntilMissing($this->selector());
    }
}
