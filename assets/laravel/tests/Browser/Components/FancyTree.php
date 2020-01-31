<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class FancyTree extends BaseComponent
{
    /**
     * Input field selector.
     *
     * @var string
     */
    protected $parentSelector;

    /**
     * CKEditor constructor.
     * @param string $parentSelector
     */
    public function __construct(string $parentSelector)
    {
        $this->parentSelector = $parentSelector;
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return $this->parentSelector . ' .fancytree-container';
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
            '@node' => '.fancytree-node'
        ];
    }


    /**
     * Select node with specified text.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $text
     */
    public function clickNode(Browser $browser, string $text)
    {
        // '\\\\\\\\$1' makes from 'LITTLE BUSY BEE,".' => 'LITTLE\\ BUSY\\ BEE\\,\\"\\.'
        $textMod = preg_replace(
            "/([ #;&,.+*~\':\"!^$[\]()=>|\/@])/", '\\\\\\\\$1',
            addslashes($text)
        );
        $pointerClass = 'fancy-tree-dusk-pointer';
        $titleSelector = $this->selector() . ' .fancytree-node > .fancytree-title:contains("' . $textMod . '")';

        $browser->script("$('{$titleSelector}').prev('.fancytree-checkbox').addClass('{$pointerClass}')");
        $browser->click(".{$pointerClass}");
        $browser->script("$('.{$pointerClass}').removeClass('{$pointerClass}')");
    }


    /**
     * Assert node with specified text is selected.
     *
     * @param \Laravel\Dusk\Browser $browser
     * @param string $text
     */
    public function assertNodeSelected(Browser $browser, string $text)
    {
        $text = addslashes($text);
        $pointerClass = 'fancy-tree-dusk-pointer';
        $titleSelector = $this->selector() . ' .fancytree-node > .fancytree-title:contains("' . $text . '")';

        $browser->script("$('{$titleSelector}').parent().addClass('{$pointerClass}')");
        $browser->assertVisible(".{$pointerClass}.fancytree-selected");
        $browser->script("$('.{$pointerClass}').removeClass('{$pointerClass}')");
    }
}
