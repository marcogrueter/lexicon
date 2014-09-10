<?php
/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/9/14
 * Time: 6:39 PM
 */

class FactoryTest extends LexiconTestCase {


    public function testMakeAndRenderHelloView()
    {
        $this->assertEquals('Hello World!', $this->view->make('test::hello', [
               'variable' => 'Hello World!'
            ])->render());
    }
}
 