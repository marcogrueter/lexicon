<?php namespace Anomaly\Lexicon\Test\View;

use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class FactoryTest
 *
 * @package Anomaly\Lexicon\Test\View
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

    /**
     *
     */
    public function testFooterContentGetsAppendedWhenExtendingALayout()
    {
        $expected = '<div class="sidebar">This is some sidebar content.</div>
<div class="content">Injecting this content into the yield section.</div>
';

        $this->assertEquals($expected, $this->view->make('test::extends', [])->render());
    }


}
 