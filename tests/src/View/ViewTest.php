<?php
/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/11/14
 * Time: 4:30 AM
 */

class ViewTest extends LexiconTest {


    public function testViewUsingMagicCall()
    {
        $this->view->make('test::magic', [])->usingMagic()->render();
    }

    public function testViewWithMagicCall()
    {
        $this->view->make('test::magic', [])->withFoo('bar')->render();
    }
}
 