<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class NavBar extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '#app > .page-wrapper > .navbar';
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param  Browser  $browser
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
            '@brand' => '> .navbar-header > .navbar-brand',
            '@menu-toggle' => '> #navbar-mobile > ul.nav:nth-child(1) > li:nth-child(1) > a',
            '@public-link' => '> #navbar-mobile > ul.nav:nth-child(1) > li:nth-child(2) > a',
            '@language-switch' => '> #navbar-mobile > ul.nav:nth-child(2) > li.language-switch',
            '@user-control' => '> #navbar-mobile > ul.nav:nth-child(2) > li.dropdown-user'
        ];
    }
}
