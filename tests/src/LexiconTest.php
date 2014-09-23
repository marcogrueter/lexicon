<?php namespace Anomaly\Lexicon\Test;

use Anomaly\Lexicon\Lexicon;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\FileViewFinder;

/**
 * Class LexiconTest
 *
 * @package Anomaly\Lexicon\Test
 */
class LexiconTest extends LexiconTestCase
{

    /**
     * Test that all node types are registered and instantiated
     */
    public function testGetNodeTypes()
    {
        foreach ($this->lexicon->getNodeTypes() as $nodeType) {
            $this->assertInstanceOf('Anomaly\Lexicon\Contract\Node\NodeInterface', $nodeType);
        }
    }

    /**
     * Test that the root node has been registered and we can get it
     */
    public function testGetRootNodeType()
    {
        $this->assertInstanceOf('Anomaly\Lexicon\Contract\Node\RootInterface', $this->lexicon->getRootNodeType());
    }

    /**
     * Assert that the RootNodeTypeNotFoundException is thrown when the root node type is not registered
     *
     * @expectedException \Anomaly\Lexicon\Exception\RootNodeTypeNotFoundException
     */
    public function testRootNodeTypeNotFoundException()
    {
        $this->lexicon->removeNodeTypeFromNodeSet('Anomaly\Lexicon\Node\Block', Lexicon::DEFAULT_NODE_SET);
        $this->lexicon->getRootNodeType();
    }

    /**
     * Test that we can add a template as a parse path and then test if it is a parse-able path
     */
    public function testAddTemplateAsParsePath()
    {
        $template = '{{ hello }}';
        $this->lexicon->addParsePath($template);
        $this->assertArrayHasKey($template, $this->lexicon->getParsePaths());
        $this->assertTrue($this->lexicon->isParsePath($template));
    }

    /**
     * Assert we can get the view template contents
     */
    public function testGetViewTemplate()
    {
        $expected = '<?php namespace [namespace]; use Anomaly\Lexicon\Contract\View\ViewTemplateInterface; class [class] implements ViewTemplateInterface { public function render($__data) {
?>[source]<?php }} ?>';

        $this->assertEquals($expected, $this->compiler->getViewTemplate());
    }

    /**
     * Assert we get the correct full view class with the view hash
     */
    public function testGetFullViewClass()
    {
        $hash = '9238d2c5749ab10ada78ccc540985e82';

        $this->assertEquals(
            'Anomaly\Lexicon\View\LexiconView_' . $hash,
            $this->lexicon->getCompiledViewFullClass($hash)
        );
    }

}
 