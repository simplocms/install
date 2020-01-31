<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class GridEditor extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '.grideditor';
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param  \Laravel\Dusk\Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector() . ' .grid');
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@add-row' => '.new-row'
        ];
    }


    /**
     * Add new row to the grid editor.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $layout
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function addNewRow(Browser $browser, string $layout = '12')
    {
        $browser->waitFor('.new-row');
        $browser->click('@add-row');
        $prefix = $browser->resolver->prefix;
        $browser->resolver->prefix = 'body';
        $browser
            ->whenAvailable('#row-layouts-modal', function (Browser $modal) use ($layout) {
                $modal->click('._grid-row-layout-' . $layout);
            })
            ->waitUntilMissing('.modal-backdrop');
        $browser->resolver->prefix = $prefix;
    }
}
