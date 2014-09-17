<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Node\NodeFinder;
use Anomaly\Lexicon\Node\Variable;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class NodeFinderTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class NodeFinderTest extends LexiconTestCase
{

    /**
     * @var BlockInterface
     */
    protected $root;

    /**
     * @var Variable
     */
    protected $variable;

    /**
     * @var BlockInterface
     */
    protected $parent;

    /**
     * @var BlockInterface
     */
    protected $parentParent;

    /**
     * Set up test
     */
    public function setUpTest()
    {
        $this->root = $this->makeBlockNode('root');

        $this->root->setLoopItemName('root')->setContextName('root');

        $this->parentParent = $this->makeBlockNode('parentParent', 'template', $this->root);

        $this->parentParent->setLoopItemName('parentParent')->setContextName('parentParent');

        $this->parent = $this->makeBlockNode('parent', 'template', $this->parentParent);

        $this->parent->setLoopItemName('parent')->setContextName('parent');

        $this->variable = new Variable($this->lexicon);
    }

    public function testGetNameRemovesDataPrefix()
    {
        $variable = $this->variable->make([], $this->root)->setName('data.title')->setContextName('data.title');

        $finder = new NodeFinder($variable);

        $this->assertEquals('title', $finder->getName());
    }

    public function testGetLoopItemSourceIfVariableIsChildOfRootContext()
    {
        $variable = $this->variable->make([], $this->root)->setName('book.title');

        $finder = new NodeFinder($variable);

        $this->assertEquals('$__data', $finder->getItemSource());
    }

    public function testGetLoopItemSourceForRootItemName()
    {
        $variable = $this->variable->make([], $this->root)->setName('data.title')->setContextName('data.title');

        $finder = new NodeFinder($variable);

        $this->assertEquals('$__data', $finder->getItemSource());
    }

    public function testGetLoopItemSourceForCustomItemName()
    {
        $variable = $this->variable->make([], $this->parent)->setName('parent.title')->setContextName('parent.title');

        $finder = new NodeFinder($variable);

        $this->assertEquals('$parentItem', $finder->getItemSource());
    }

    public function testGetLoopItemSourceForNonExistentPrefix()
    {
        $variable = $this->variable->make([], $this->parent)->setName('nonExistent.title');

        $finder = new NodeFinder($variable);

        $this->assertEquals('$parentItem', $finder->getItemSource());
    }

    public function testGetLoopItemSourceForParentOfParent()
    {
        $variable = $this->variable->make([], $this->parent)->setName('parentParent.title')->setContextName('parentParent.title');

        $finder = new NodeFinder($variable);

        $this->assertEquals('$parentParentItem', $finder->getItemSource());
    }

    public function testGetName()
    {
        $variable = $this->variable->make([], $this->parent)->setName('parent.title')->setContextName('parent.title');

        $finder = new NodeFinder($variable);

        $this->assertEquals('title', $finder->getName());
    }

}
 