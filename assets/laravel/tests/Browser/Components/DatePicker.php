<?php

namespace Tests\Browser\Components;

use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class DatePicker extends BaseComponent
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
        $box = '+ .picker > .picker__holder > .picker__frame > .picker__wrap > .picker__box';

        return [
            '@box' => $box,
            '@monthPrev' => "$box > .picker__header > .picker__nav--prev",
            '@monthNext' => "$box > .picker__header > .picker__nav--next",
            '@days' => "$box > .picker__table > tbody > tr > td > .picker__day.picker__day--infocus",
            '@close' => "$box > .picker__footer > .picker__button--close"
        ];
    }


    /**
     * Select date in date picker.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param \Carbon\Carbon $date
     */
    public function selectDate(Browser $browser, Carbon $date)
    {
        $this->open($browser);

        $months = $this->diffInMonthsFromNow($date);

        for ($i = 0; $i < abs($months); $i++) {
            if ($months > 0) {
                $browser->click('@monthNext');
            } else {
                $browser->click('@monthPrev');
            }
        }

        $daysSelector = $this->inputSelector . $this->elements()['@days'];

        // Locate day with jQuery and click it.
        $browser->script("$('" . $daysSelector . ":contains({$date->day})').first().click()");
    }


    /**
     * Open date picker.
     *
     * @param \Laravel\Dusk\Browser $browser
     */
    public function open(Browser $browser)
    {
        $this->outsideComponent($browser, function (Browser $browser) {
            $browser->click($this->inputSelector);
        });
        $browser->waitFor('@box');
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


    /**
     * Get difference in months between dates.
     *
     * @param \Carbon\Carbon $date
     *
     * @return int
     */
    private function diffInMonthsFromNow(Carbon $date)
    {
        $now = Carbon::now();
        if ($now->year === $date->year) {
            return $date->month - $now->month;
        }

        if ($date->isFuture()) {
            return (12 - $now->month) + $date->month + (($date->year - $now->year - 1) * 12);
        }

        return -($now->month + (12 - $date->month) + (($now->year - $date->year - 1) * 12));
    }
}
