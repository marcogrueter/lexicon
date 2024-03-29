<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\RootInterface;
use Anomaly\Lexicon\Exception\RootNodeTypeNotFoundException;
use Anomaly\Lexicon\Stub\LexiconStub;

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
        'Anomaly\Lexicon\Attribute\NamedAttribute',
        'Anomaly\Lexicon\Attribute\OrderedAttribute',
        'Anomaly\Lexicon\Attribute\VariableAttribute',
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
    public function __construct(
        LexiconInterface $lexicon,
        NodeCollection $nodeCollection,
        NodeExtractor $nodeExtractor
    ) {
        $this->lexicon        = $lexicon;
        $this->nodeCollection = $nodeCollection;
        $this->nodeExtractor  = $nodeExtractor;
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
        $node->setup();
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
        $this->getCollection()->put($node->getId(), $node);
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
     * Get multiple nodes by id
     *
     * @param array $ids
     * @return array
     */
    public function getByIds(array $ids)
    {
        return $this->getCollection()->getByIds($ids);
    }

    /**
     * Create child nodes
     *
     * @return NodeInterface
     */
    public function createChildNodes(NodeInterface $node)
    {
        /** @var NodeInterface $nodeType */
        foreach ($this->getNodeTypes($this->getNodeGroup()) as $nodeType) {
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
        $this->extract($node, $parent);
        $this->createChildNodes($node);

        return $node;
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

    /**
     * Set node group
     *
     * @param $nodeGroup
     * @return NodeFactory
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
     * @return NodeFactory
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
     * @return RootInterface
     */
    public function getRootNodeType($nodeGroup = self::DEFAULT_NODE_GROUP)
    {
        $this->setNodeGroup($nodeGroup);

        $block = null;

        foreach ($this->getNodeTypes($nodeGroup) as $nodeType) {
            if ($nodeType instanceof RootInterface) {
                $block = $nodeType;
                break;
            }
        }

        if (!$block) {
            throw new RootNodeTypeNotFoundException(
                "The root node type is not registered in the `{$nodeGroup}` node group."
            );
        }

        return $block;
    }

    /**
     * Get root node
     *
     * @param $string
     * @return NodeInterface
     * @throws RootNodeTypeNotFoundException
     */
    public function getRootNode($string, $path = null)
    {
        $rootNode = $this
            ->make($this->getRootNodeType($this->getNodeGroupFromPath($path)))
            ->setName('root')
            ->setContent($string)
            ->setExtractionContent($string)
            ->setCurrentContent($string);

        $this->createChildNodes($rootNode);

        return $rootNode;
    }

    /**
     * Register node groups
     *
     * @param array $nodeGroups
     * @return NodeFactory
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
     * @return NodeFactory
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
     * @return NodeFactory
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
     * @return NodeFactory
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

    /**
     * Stub for testing with PHPSpec
     *
     * @return NodeFactory
     */
    public static function stub()
    {
        return LexiconStub::foundation()->getNodeFactory();
    }

}
