<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;

/**
 * Class NodeFactory
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package Anomaly\Lexicon\Node
 */
class NodeFactory
{

    /**
     * @var LexiconInterface
     */
    protected $lexicon;

    /**
     * @var array
     */
    protected $nodes = [];

    /**
     * @var NodeCollection
     */
    private $nodeCollection;

    /**
     * @var NodeExtractor
     */
    private $nodeExtractor;

    /**
     * @param LexiconInterface $lexicon
     */
    public function __construct(LexiconInterface $lexicon, NodeCollection $nodeCollection, NodeExtractor $nodeExtractor)
    {
        $this->lexicon = $lexicon;
        $this->nodeCollection = $nodeCollection;
        $this->nodeExtractor = $nodeExtractor;
    }

    /**
     * Make node of type
     *
     * @param               $class
     * @param array         $match
     * @param NodeInterface $parent
     * @param int           $offset
     * @param int           $depth
     * @return LexiconInterface
     */
    public function make(
        NodeInterface $nodeType,
        array $match = [],
        NodeInterface $parent = null,
        $offset = 0,
        $depth = 0
    ) {
        $node = clone $nodeType;

        if ($node->incrementDepth()) {
            $depth++;
        }

        $parentId = $parent ? $parent->getId() : null;

        $node->setParentId($parentId);
        $node->setMatch($match);
        $node->setCurrentContent($node->getContent());
        $node->setOffset($offset);
        $node->setDepth($depth);
        $node->setId(str_random(32));
        $node->setNodeFinder(new NodeFinder($node));
        $node->setup();

        $node->setLoopItemName($node->getLoopItemInRawAttributes());
        $this->addNode($node);
        $this->extractParsing($node, $parent);

        return $node;
    }

    /**
     * Get lexicon
     *
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->lexicon;
    }

    /**
     * @return NodeCollection
     */
    public function getCollection()
    {
        return $this->nodeCollection;
    }

    /**
     * Add node
     *
     * @param NodeInterface $node
     * @return $this
     */
    public function addNode(NodeInterface $node)
    {
        $this->getCollection()->push($node);
        return $this;
    }

    /**
     * Get node by id
     *
     * @param int $id
     * @return NodeInterface|null
     */
    public function getById($id)
    {
        return $this->getCollection()->getById($id);
    }

    /**
     * Create child nodes
     *
     * @return NodeInterface
     */
    public function createChildNodes(NodeInterface $node)
    {
        /** @var NodeInterface $nodeType */
        foreach ($this->getNodeTypes() as $nodeType) {
            foreach ($nodeType->getMatches($node->getCurrentContent()) as $offset => $match) {
                $this->createChildNode($node, $nodeType, $match, $offset);
            }
        }

        return $this;
    }

    /**
     * Create child node
     *
     * @param NodeInterface|Node $nodeType
     * @param                    $match
     * @param int                $offset
     * @return mixed
     */
    protected function createChildNode(NodeInterface $parent, NodeInterface $nodeType, $match, $offset = 0)
    {
        $node = $this->make(
            $nodeType,
            $match,
            $parent,
            $offset,
            $parent->getDepth()
        );

        $parent->addChild($node);

        $this->createChildNodes($node);

        $this->extract($node, $parent);

        return $node;
    }

    private function getNodeTypes()
    {
        return $this->getLexicon()->getNodeTypes();
    }

    public function setNodeGroup($argument1)
    {
        // TODO: write logic here
    }

    public function getNodeGroup()
    {
        // TODO: write logic here
    }

    public function getNodeExtractor()
    {
        return $this->nodeExtractor;
    }

    public function extract(NodeInterface $child, NodeInterface $parent)
    {
        return $this->getNodeExtractor()->extract($child, $parent);
    }

    public function inject($argument1, $argument2)
    {
        // TODO: write logic here
    }
}
