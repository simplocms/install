<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class JGrowl extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '#jGrowl > .jGrowl-notification.alert';
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param  Browser $browser
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
            '@title' => '> .jGrowl-header',
            '@message' => '> .jGrowl-message'
        ];
    }


    /**
     * Assert that jGrowl notification has specified title and message.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $title
     * @param string $message
     */
    public function assertSays(Browser $browser, string $title, string $message)
    {
        $browser->assertSeeIn('@title', $title)
            ->assertSeeIn('@message', $message);
    }
}
