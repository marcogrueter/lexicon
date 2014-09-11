<?php

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/9/14
 * Time: 6:39 PM
 */
class FactoryTest extends LexiconTestCase
{

    /**
     * Test render hello view
     */
    public function testRenderHelloView()
    {
        $data = [
            'variable' => 'Hello World!'
        ];

        $this->assertEquals(
            'Hello World!',
            $this->view->make('test::hello', $data)->render()
        );
    }


    /**
     * Assert that the expected content gets rendered from parsing a string template
     */
    public function testParseStringTemplateView()
    {
        $template = '<ul>{{ posts }}<li>{{ title }}</li>{{ /posts }}</ul>';

        $expected = '<ul><li>Title #1</li><li>Title #2</li></ul>';

        $data = [
            'posts' => [
                [
                    'title' => 'Title #1'
                ],
                [
                    'title' => 'Title #2'
                ],
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->view->parse($template, $data)->render()
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testHandleViewException()
    {
        $this->view->make('test::exception', [])->using('testing')->render();
    }

}
 