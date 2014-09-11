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

    public function testGetNodeTypes()
    {
        foreach($this->lexicon->getNodeTypes() as $nodeType) {
            $this->assertInstanceOf('Anomaly\Lexicon\Contract\Node\NodeInterface', $nodeType);
        }
    }

    public function testGetRootNodeType()
    {
        $this->assertInstanceOf('Anomaly\Lexicon\Contract\Node\RootInterface', $this->lexicon->getRootNodeType());
    }

    /**
     * @expectedException \Anomaly\Lexicon\Exception\RootNodeTypeNotFoundException
     */
    public function testRootNodeTypeNotFoundException()
    {
        $this->lexicon->registerNodeTypes([]);
        $this->lexicon->getRootNodeType();
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
        $this->assertInternalType('string', $this->compiler->getViewTemplate());
    }

    public function testGetFullViewClass()
    {
        $hash = '9238d2c5749ab10ada78ccc540985e82';

        $this->assertEquals(
            'Anomaly\Lexicon\View\LexiconView_'.$hash,
            $this->lexicon->getFullViewClass($hash)
        );
    }

}
 