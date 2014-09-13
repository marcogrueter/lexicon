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

        $this->root->setLoopItemName('root');

        $this->parentParent = $this->makeBlockNode('parentParent', 'template', $this->root);

        $this->parentParent->setLoopItemName('parentParent');

        $this->parent = $this->makeBlockNode('parent', 'template', $this->parentParent);

        $this->parent->setLoopItemName('parent');

        $this->variable = new Variable($this->lexicon);
    }

    public function testGetNameRemovesDataPrefix()
    {
        $variable = $this->variable->make(['name' => 'data.title'], $this->root);

        $finder = new NodeFinder($variable);

        $this->assertEquals('title', $finder->getName());
    }

    public function testGetLoopItemSourceIfVariableIsChildOfRootContext()
    {
        $variable = $this->variable->make(['name' => 'book.title'], $this->root);

        $finder = new NodeFinder($variable);

        $this->assertEquals('$__data', $finder->getItemSource());
    }

    public function testGetLoopItemSourceForRootItemName()
    {
        $variable = $this->variable->make(['name' => 'data.title'], $this->root);

        $finder = new NodeFinder($variable);

        $this->assertEquals('$__data', $finder->getItemSource());
    }

    public function testGetLoopItemSourceForCustomItemName()
    {
        $variable = $this->variable->make(['name' => 'parent.title'], $this->parent);

        $finder = new NodeFinder($variable);

        $this->assertEquals('$parentItem', $finder->getItemSource());
    }

    public function testGetLoopItemSourceForNonExistentPrefix()
    {
        $variable = $this->variable->make(['name' => 'nonExistent.title'], $this->parent);

        $finder = new NodeFinder($variable);

        $this->assertEquals('$parentItem', $finder->getItemSource());
    }

    public function testGetLoopItemSourceForParentOfParent()
    {
        $variable = $this->variable->make(['name' => 'parentParent.title'], $this->parent);

        $finder = new NodeFinder($variable);

        $this->assertEquals('$parentParentItem', $finder->getItemSource());
    }

    public function testGetName()
    {
        $variable = $this->variable->make(['name' => 'parent.title'], $this->parent);

        $finder = new NodeFinder($variable);

        $this->assertEquals('title', $finder->getName());
    }

}
 