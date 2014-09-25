<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\RootInterface;
use Anomaly\Lexicon\Exception\RootNodeTypeNotFoundException;

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
    protected $nodeCollection;

    /**
     * @var NodeExtractor
     */
    protected $nodeExtractor;

    /**
     * Node group
     *
     * @var string
     */
    protected $nodeGroup = self::DEFAULT_NODE_GROUP;

    /**
     * Node types
     *
     * @var array
     */
    protected $nodeTypes = [];

    /**
     * Attribute node types
     *
     * @var array
     */
    protected $attributeNodeTypes = [
        'Anomaly\Lexicon\Attribute\VariableAttribute',
        'Anomaly\Lexicon\Attribute\NamedAttribute',
        'Anomaly\Lexicon\Attribute\OrderedAttribute',
    ];

    /**
     * Node group paths
     *
     * @var array
     */
    protected $nodeGroupPaths = [];

    /**
     * Default node group
     */
    const DEFAULT_NODE_GROUP = 'all';

    /**
     * @param LexiconInterface $lexicon
     */
    public function __construct(LexiconInterface $lexicon, NodeCollection $nodeCollection, NodeExtractor $nodeExtractor)
    {
        $this->lexicon        = $lexicon;
        $this->nodeCollection = $nodeCollection;
        $this->nodeExtractor  = $nodeExtractor;
        $this->bootDefaultNodeGroups();
    }

    /**
     * Make node of type
     *
     * @param               $class
     * @param array         $match
     * @param NodeInterface $parent
     * @param int           $offset
     * @param int           $depth
     * @return NodeInterface
     */
    public function make(
        NodeInterface $nodeType,
        array $match = [],
        NodeInterface $parent = null,
        $offset = 0,
        $depth = 0,
        $id = null
    ) {
        $node = clone $nodeType;

        if ($node->incrementDepth()) {
            $depth++;
        }

        $parentId = $parent ? $parent->getId() : null;

        $node->setId($this->generateId($id));
        $node->setParentId($parentId);
        $node->setMatch($match);
        $node->setOffset($offset);
        $node->setDepth($depth);
        $node->setNodeFinder($this->newNodeFinder($node));
        $node->setup();
        $node->setCurrentContent($node->getContent());
        $node->setItemAlias($node->getItemAliasFromRawAttributes());
        $this->addNode($node);
        $this->extract($node, $parent);

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
    public function createChildNode(NodeInterface $parent, NodeInterface $nodeType, $match, $offset = 0)
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

    public function bootDefaultNodeGroups()
    {
        $container = $this->getLexicon()->getContainer();

        if (!$this->nodeTypes and
            isset($container['config']) and
            $nodeGroups = $container['config']['lexicon::nodeGroups']
        ) {
            $this->nodeTypes = $nodeGroups;
        }
    }

    /**
     * Get node types
     *
     * @param string $nodeGroup
     * @return array
     */
    public function getNodeTypes($nodeGroup = self::DEFAULT_NODE_GROUP)
    {
        $nodeTypes = [];

        if (isset($this->nodeTypes[$nodeGroup])) {
            foreach ($this->nodeTypes[$nodeGroup] as $nodeType) {
                $nodeTypes[] = $this->newNodeType($nodeType);
            }
        }

        return $nodeTypes;
    }

    /**
     * Set attribute node types
     *
     * @param array $attributeNodeTypes
     */
    public function setAttributeNodeTypes(array $attributeNodeTypes)
    {
        $this->attributeNodeTypes = $attributeNodeTypes;
        return $this;
    }

    /**
     * Get node types
     *
     * @param string $nodeGroup
     * @return array
     */
    public function getAttributeNodeTypes()
    {
        $nodeTypes = [];

        foreach ($this->attributeNodeTypes as $nodeType) {
            $nodeTypes[] = $this->newNodeType($nodeType);
        }

        return $nodeTypes;
    }

    /**
     * New node type
     *
     * @param $class
     * @return mixed
     */
    public function newNodeType($class)
    {
        return new $class($this->getLexicon());
    }

    public function newNodeFinder(NodeInterface $node)
    {
        return new NodeFinder($node);
    }

    /**
     * Set node group
     *
     * @param $nodeGroup
     * @return $this
     */
    public function setNodeGroup($nodeGroup)
    {
        $this->nodeGroup = $nodeGroup;
        return $this;
    }

    /**
     * Get node group to use
     *
     * @return string
     */
    public function getNodeGroup()
    {
        return $this->nodeGroup;
    }

    /**
     * Get node extractor
     *
     * @return NodeExtractor
     */
    public function getNodeExtractor()
    {
        return $this->nodeExtractor;
    }

    /**
     * Extract child node from parent
     *
     * @param NodeInterface $child
     * @param NodeInterface $parent
     */
    public function extract(NodeInterface $child, NodeInterface $parent = null)
    {
        if ($parent) {
            $this->getNodeExtractor()->extract($child, $parent);
        }
    }

    /**
     * Inject child node into parent
     *
     * @param NodeInterface $child
     * @param NodeInterface $parent
     */
    public function inject(NodeInterface $child, NodeInterface $parent)
    {
        $this->getNodeExtractor()->inject($child, $parent);
    }

    /**
     * Add node group path
     *
     * @param        $path
     * @param string $nodeGroup
     * @return LexiconInterface
     */
    public function addNodeGroupPath($path, $nodeGroup = self::DEFAULT_NODE_GROUP)
    {
        $this->nodeGroupPaths[$path] = $nodeGroup;
        return $this;
    }

    /**
     * Get node group from path
     *
     * @param $path
     * @return string
     */
    public function getNodeGroupFromPath($path)
    {
        return isset($this->nodeGroupPaths[$path]) ? $this->nodeGroupPaths[$path] : self::DEFAULT_NODE_GROUP;
    }

    /**
     * Get root node type
     *
     * @throws RootNodeTypeNotFoundException
     * @return NodeInterface
     */
    public function getRootNodeType($nodeGroup = self::DEFAULT_NODE_GROUP)
    {
        $block = null;

        foreach ($this->getNodeTypes($nodeGroup) as $nodeType) {
            if ($nodeType instanceof RootInterface) {
                $block = $nodeType;
                break;
            }
        }

        if (!$block) {
            throw new RootNodeTypeNotFoundException;
        }

        return $block;
    }

    /**
     * Get root node
     *
     * @param $content
     * @return NodeInterface
     * @throws RootNodeTypeNotFoundException
     */
    public function getRootNode($content)
    {
        $rootNode = $this->make($this->getRootNodeType())
            ->setName('root')
            ->setContent($content)
            ->setExtractionContent($content)
            ->setCurrentContent($content);

        $this->createChildNodes($rootNode);

        return $rootNode;
    }

    /**
     * Register node groups
     *
     * @param array $nodeGroups
     * @return LexiconInterface
     */
    public function registerNodeGroups(array $nodeGroups = [])
    {
        foreach ($nodeGroups as $nodeGroup => $nodeTypes) {
            $this->registerNodeGroup($nodeTypes, $nodeGroup);
        }
        return $this;
    }

    /**
     * Register node group
     *
     * @param array $nodeTypes
     * @return LexiconInterface
     */
    public function registerNodeGroup(array $nodeTypes, $nodeGroup = self::DEFAULT_NODE_GROUP)
    {
        foreach ($nodeTypes as $nodeType) {
            $this->registerNodeType($nodeType, $nodeGroup);
        }
        return $this;
    }

    /**
     * Register node type
     *
     * @param        $nodeType
     * @param string $nodeGroup
     * @return LexiconInterface
     */
    public function registerNodeType($nodeType, $nodeGroup = self::DEFAULT_NODE_GROUP)
    {
        $this->nodeTypes[$nodeGroup][$nodeType] = $nodeType;
        return $this;
    }

    /**
     * Remove node type from node set
     *
     * @param $nodeType
     * @param $nodeGroup
     * @return LexiconInterface
     */
    public function removeNodeTypeFromNodeGroup($nodeType, $nodeGroup = self::DEFAULT_NODE_GROUP)
    {
        if (isset($this->nodeTypes[$nodeGroup]) and isset($this->nodeTypes[$nodeGroup][$nodeType])) {
            unset($this->nodeTypes[$nodeGroup][$nodeType]);
        }
        return $this;
    }

    /**
     * Generate a random id if none was passed
     *
     * @return string
     */
    public function generateId($id = null)
    {
        if (!$id) {
            $id = str_random(32);
        }

        return $id;
    }
}
