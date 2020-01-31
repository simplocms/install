<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class UserNavBarControl extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '.navbar > .navbar-collapse > .nav.navbar-nav > .dropdown-user';
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
            '@toggle' => '> .dropdown-toggle',
            '@image' => '> .dropdown-toggle img',
            '@username' => '> .dropdown-toggle > span',
            '@dropdown' => '> ul.dropdown-menu',
            '@name' => '> ul.dropdown-menu > li.user-header > .name',
            '@settings' => '> ul.dropdown-menu > li:nth-child(3) > a',
            '@logout' => '> ul.dropdown-menu > li:nth-child(4) > a'
        ];
    }
}
