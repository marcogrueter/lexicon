<?php namespace Anomaly\Lexicon\Test\View;

use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Test\LexiconTestCase;
use Illuminate\Support\Collection;

/**
 * Class FactoryTest
 *
 * @package Anomaly\Lexicon\Test\View
 */
class FactoryTest extends LexiconTestCase
{

    protected $arrayStub = [
        [
            'name' => 'one'
        ],
        [
            'name' => 'two'
        ],
        [
            'name' => 'three'
        ],
    ];

    /**
     * Test render hello view
     */
    public function testRenderHelloView()
    {
        $data = [
            'variable' => 'Hello World!'
        ];

        $this->assertEquals(
            '<h1>Hello World!</h1>',
            $this->view->make('test::view/hello', $data)->render()
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
        $this->view->make('test::view/exception', [])->using('testing')->render();
    }

    /**
     *
     */
    public function testFooterContentGetsAppendedWhenExtendingALayout()
    {
        $expected = '<div class="sidebar">This is some sidebar content.</div>
<div class="content">Injecting this content into the yield section.</div>
';

        $this->assertEquals($expected, $this->view->make('test::view/extends', [])->render());
    }

    /**
     * Test get plugin from variable
     */
    public function testGetPluginResultFromVariable()
    {
        $this->assertEquals('Hello World!', $this->view->variable([], 'test.hello'));
    }

    /**
     * Test offset array from variable
     */
    public function testSizeOfArrayFromVariable()
    {
        $this->assertEquals(3, $this->view->variable(['array' => $this->arrayStub], 'array.size'));
    }

    /**
     * Test offset characters from variable
     */
    public function testGetCountStringCharactersFromVariable()
    {
        $string = 'count_should_be_29_characters';
        $this->assertEquals(29, $this->view->variable(compact('string'), 'string.size'));
    }

    public function testGetCallMethodFromVariable()
    {
        $this->assertEquals('FOO', $this->view->variable(new ObjectStub(), 'foo', [], null, Lexicon::EXPECTED_ECHO));
    }

    public function testGetStringObjectFromVariable()
    {
        $data = [
            'stringObject' => new ObjectStub()
        ];

        $this->assertEquals(
            '__toString result',
            $this->view->variable($data, 'stringObject', [], null, Lexicon::EXPECTED_ECHO)
        );
    }

    public function testGetPropertyFromArrayAccessObject()
    {
        $this->assertEquals(
            'BAR',
            $this->view->variable(new ObjectStub(), 'bar', [], '', Lexicon::EXPECTED_ECHO)
        );
    }

    public function testGetPublicPropertyObject()
    {
        $object = new \stdClass();

        $object->yay = 'YAY!';

        $this->assertEquals(
            'YAY!',
            $this->view->variable($object, 'yay')
        );
    }

    public function testGetEchoAbleFromVariable()
    {
        $types = [
            'string'  => 'hello',
            'float'   => 1.23456789,
            'integer' => 5,
            'boolean' => true,
            'null'    => null,
            'object'  => new ObjectStub()
        ];

        foreach ($types as $type => $value) {
            $this->assertInternalType(
                $type,
                $this->view->variable(
                    $types,
                    $type,
                    $attributes = [],
                    $content = '',
                    $default = null,
                    Lexicon::EXPECTED_ECHO
                )
            );
        }

    }

    public function testGetTraversableFromVariable()
    {
        $data = [
            'collection' => new Collection($this->arrayStub)
        ];

        $this->assertInstanceOf(
            'Traversable',
            $this->view->variable(
                $data,
                'collection',
                $attributes = [],
                $content = '',
                $default = null,
                Lexicon::EXPECTED_TRAVERSABLE
            )
        );
    }

    public function testPreviousScopeInVariable()
    {
        $data = [
            'level1' => [
                'level2' => 'value'
            ]
        ];

        $this->assertEquals('value', $this->view->variable($data, 'level1.level2'));
    }

    public function testViewAlias()
    {
        $this->view->alias('test::view/hello', 'boom');

        $data = [
            'variable' => 'This works!'
        ];

        $this->assertEquals(
            '<h1>This works!</h1>',
            $this->view->make('boom', $data)->render()
        );
    }

    public function testNonExistentArrayKeyReturnsNull()
    {
        $data = [
            'foo' => [

            ]
        ];

        $this->assertNull($this->view->variable($data, 'foo.nonexistent'));
    }

    /**
     * Test catch exception and return null
     */
    public function testCatchExceptionAndReturnNull()
    {
        $this->assertNull($this->view->variable(new ObjectStub(), 'method', ['string']));
    }

    /**
     * Test non-existent \ArrayAccess object property returns null
     */
    public function testNonExistentArrayAccessObjectPropertyReturnsNull()
    {
        $this->assertNull($this->view->variable(new ObjectStub(), 'nonexistent'));
    }

    /**
     * Test non-existent object property returns null
     */
    public function testNonExistentObjectPropertyReturnsNull()
    {
        $this->assertNull($this->view->variable(new \stdClass(), 'nonexistent'));
    }

    /**
     * Test null data returns null
     */
    public function testNullDataReturnsNull()
    {
        $this->assertNull($this->view->variable(null, 'regardless'));
    }

}