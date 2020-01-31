<?php

namespace Tests\Browser\Components;

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class CKEditor extends BaseComponent
{
    /**
     * Button source class.
     */
    const BUTTON_SOURCE = 'cke_button__source';

    /**
     * Input field selector.
     *
     * @var string
     */
    protected $inputSelector;

    /**
     * CKEditor constructor.
     * @param string $inputSelector
     */
    public function __construct(string $inputSelector)
    {
        $this->inputSelector = str_replace("'", '"', $inputSelector);
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return $this->inputSelector;
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param  \Laravel\Dusk\Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->waitFor('@wrapper')->assertVisible('@wrapper');
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@wrapper' => $this->inputSelector . ' + .ck-editor',
        ];
    }


    /**
     * Type into CKEditor input.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $text
     */
    public function typeIn(Browser $browser, string $text)
    {
        $browser->keys(' + .ck-editor > .ck-editor__main > .ck-content', $text);
//        $browser->with('> .ck-editor__main > .ck-content', function (Browser $browser) use ($text) {
//            $browser->sendKeys($text);
//        });
    }
}
