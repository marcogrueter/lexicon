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

    /**
     * Test get plugin from variable
     */
    public function testGetPluginResultFromVariable()
    {
        $this->assertEquals('Hello World!', $this->view->variable([], 'test.hello'));
    }

    /**
     * Test count array from variable
     */
    public function testSizeOfArrayFromVariable()
    {
        $this->assertEquals(3, $this->view->variable(['array' => $this->arrayStub], 'array.size'));
    }

    /**
     * Test count characters from variable
     */
    public function testGetCountStringCharactersFromVariable()
    {
        $string = 'count_should_be_29_characters';
        $this->assertEquals(29, $this->view->variable(compact('string'), 'string.size'));
    }

    public function testGetCallMethodFromVariable()
    {
        $this->assertEquals('FOO', $this->view->variable(new ObjectStub(), 'foo', [], null, Lexicon::ECHOABLE));
    }

    public function testGetStringObjectFromVariable()
    {
        $data = [
            'stringObject' => new ObjectStub()
        ];

        $this->assertEquals(
            '__toString result',
            $this->view->variable($data, 'stringObject', [], null, Lexicon::ECHOABLE)
        );
    }

    public function testGetPropertyFromArrayAccessObject()
    {
        $this->assertEquals(
            'BAR',
            $this->view->variable(new ObjectStub(), 'bar', [], '', Lexicon::ECHOABLE)
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
                    Lexicon::ECHOABLE
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
                Lexicon::TRAVERSABLE
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
        $this->view->alias('test::hello', 'boom');

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

class ObjectStub implements \ArrayAccess
{
    protected $attributes = [
        'bar' => 'BAR',
        'baz' => 'BAZ',
    ];

    public $yay = 'YAY!';

    /**
     * @return string
     */
    public function foo()
    {
        return 'FOO';
    }

    /**
     * For testing \InvalidArgumentException
     *
     * @param array $array
     * @return null
     */
    public function method(array $array)
    {
        print_r($array);

        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '__toString result';
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     * @return boolean true on success or false on failure.
     *                      </p>
     *                      <p>
     *                      The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $value = null;

        if ($this->offsetExists($offset)) {
            $value = $this->attributes[$offset];
        }

        return $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->attributes[$offset]);
        }
    }
}