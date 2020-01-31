<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class SweetAlertModal extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '> .swal-overlay > .swal-modal';
    }


    /**
     * Assert that the browser page contains the component.
     *
     * @param  \Laravel\Dusk\Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector());
    }


    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@cancel' => '> .swal-footer > .swal-button-container > .swal-button--cancel',
            '@confirm' => '> .swal-footer > .swal-button-container > .swal-button--confirm'
        ];
    }


    /**
     * Assert is delete alert modal.
     *
     * @param \Laravel\Dusk\Browser $browser
     */
    public function assertIsDelete(Browser $browser, string $title)
    {
        $browser->assertVisible('> .swal-icon--warning')
            ->assertSeeIn('> .swal-title', $title)
            ->assertVisible('@confirm.swal-button--danger');
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
}
