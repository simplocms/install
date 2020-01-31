<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class TableActions extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return 'ul.icons-list';
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
            '@toggle' => '> .dropdown > .dropdown-toggle',
            '@dropdown' => '> .dropdown > .dropdown-menu',
            '@item' => '> .dropdown > .dropdown-menu > li',
            '@link' => '> .dropdown > .dropdown-menu > li > a'
        ];
    }


    /**
     * Click nth child of table actions dropdown.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param int $nth
     */
    public function clickNthChild(Browser $browser, int $nth)
    {
        $toggleSelector = $browser->resolver->format('@toggle');
        $browser->script("$('{$toggleSelector}').click()");
        $browser->waitFor('@dropdown')->click("@item:nth-child({$nth})");
    }
}
