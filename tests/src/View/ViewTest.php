<?php namespace Anomaly\Lexicon\Test\View;

use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class ViewTest
 *
 * @package Anomaly\Lexicon\Test\View
 */
class ViewTest extends LexiconTestCase
{

    /**
     * Test view `using` magic method
     */
    public function testViewUsingMagicCall()
    {
        $this->view->make('test::view/magic', [])->__call('usingMagic', [])->render();
    }

    /**
     * Test view `with` magic method
     */
    public function testViewWithMagicCall()
    {
        $this->view->make('test::view/magic', [])->__call('withFoo', ['bar'])->render();
    }

}
 