<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

abstract class AdminPage extends BasePage
{
    /**
     * Get the global element for the site by its shortcut.
     *
     * @param string $shortcut
     * @return string
     */
    protected function getSiteElement(string $shortcut)
    {
        return [
            '@pageTitle' => '.page-title'
        ][$shortcut] ?? null;
    }


    /**
     * Create new menu.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $title1
     * @param string $title2
     */
    public function assertHeaderTitle(Browser $browser, string $title1, string $title2 = null)
    {
        $browser->assertSeeIn($this->getSiteElement('@pageTitle'), $title1);

        if (!is_null($title2)) {
            $browser->assertSeeIn($this->getSiteElement('@pageTitle'), $title2);
        }
    }
}
