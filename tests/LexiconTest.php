<?php

use Illuminate\Container\Container;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\FileViewFinder;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/6/14
 * Time: 7:45 PM
 */
class LexiconTest extends LexiconTestCase
{

    public function testRegisterAndInstantiateOneNodeType()
    {
        $this->assertTrue(class_exists('Anomaly\Lexicon\Node\Variable'));
    }

    public function testGetBlockNodeType()
    {
        $this->assertInstanceOf('Anomaly\Lexicon\Contract\NodeBlockInterface', $this->lexicon->getRootNodeType());
    }

    public function testAddTemplateAsParsePath()
    {
        $expected = '{{ hello }}';
        $this->lexicon->addParsePath($expected);
        $this->assertArrayHasKey($expected, $this->lexicon->getParsePaths());
        $this->assertTrue($this->lexicon->isParsePath($expected));
    }

    public function testGetViewTemplate()
    {
        $this->assertInternalType('string', $this->lexicon->getViewTemplate());
    }

    public function testGetFullViewClass()
    {
        $this->assertEquals(
            'Anomaly\Lexicon\View\LexiconView_test',
            $this->lexicon->getFullViewClass('test')
        );
    }

}
 