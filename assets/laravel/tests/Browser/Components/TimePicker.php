<?php

namespace Tests\Browser\Components;

use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class TimePicker extends BaseComponent
{
    /**
     * Input field selector.
     *
     * @var string
     */
    protected $inputSelector;

    /**
     * DatePicker constructor.
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
        $browser->assertVisible($this->inputSelector);
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        $list = '+ .picker > .picker__holder > .picker__frame > .picker__wrap > .picker__box > .picker__list';

        return [
            '@item' => "$list > li.picker__list-item"
        ];
    }


    /**
     * Select time in time picker.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param \Carbon\Carbon $time
     */
    public function selectTime(Browser $browser, Carbon $time)
    {
        $this->open($browser);

        $formattedTime = $time->format('G:i');

        // Click twice on the pointer.
        $browser->click("@item[aria-label='$formattedTime']");
    }


    /**
     * Open time picker.
     *
     * @param \Laravel\Dusk\Browser $browser
     */
    public function open(Browser $browser)
    {
        $this->outsideComponent($browser, function (Browser $browser) {
            $browser->click($this->inputSelector);
        });
    }


    /**
     * Clear input with backspace.
     *
     * @param \Laravel\Dusk\Browser $browser
     */
    public function clearPicker(Browser $browser)
    {
        $this->outsideComponent($browser, function (Browser $browser) {
            $browser->click($this->inputSelector)
                ->keys($this->inputSelector, ['{backspace}']);
        });
    }


    /**
     * Run commands outside of the component.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param callable $callback
     */
    public function outsideComponent(Browser $browser, callable $callback)
    {
        $prefix = $browser->resolver->prefix;
        $browser->resolver->prefix = 'body';

        $callback($browser);

        $browser->resolver->prefix = $prefix;
    }
}
