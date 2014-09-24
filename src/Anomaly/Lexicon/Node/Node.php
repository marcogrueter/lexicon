<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Attribute\AttributeNode;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\ValidatorInterface;
use Anomaly\Lexicon\Lexicon;

abstract class Node implements NodeInterface
{
    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var array
     */
    protected $children = array();

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $depth = 0;

    /**
     * @var string
     */
    protected $extractionContent;

    /**
     * @var array
     */
    protected $footer = array();

    /**
     * @var string
     */
    protected $id;

    /**
     * @var bool
     */
    protected $incrementDepth = true;

    /**
     * @var string
     */
    protected $name = 'root';

    /**
     * Regex match result
     *
     * @var array
     */
    protected $match = [];

    /**
     * @var string
     */
    protected $rawAttributes = '';

    /**
     * @var int|null
     */
    protected $parentId = null;

    /**
     * @var string
     */
    protected $parsedContent;

    /**
     * @var LexiconInterface
     */
    protected $lexicon;

    /**
     * Item name
     *
     * @var string
     */
    protected $itemName;

    /**
     * @var string
     */
    protected $contextItemName;

    /**
     * Context finder
     *
     * @var NodeFinder
     */
    protected $NodeFinder;

    /**
     * @var string|null
     */
    protected $loopItemName;

    /**
     * Is valid for compile
     *
     * @var bool
     */
    protected $isValid = true;

    /**
     * @var
     */
    protected $validator;

    /**
     * Should compile as PHP or not?
     *
     * @var bool
     */
    protected $isPhp = true;

    /**
     * Is extractable
     *
     * @var bool
     */
    protected $extractable = true;

    /**
     * Defer compile
     *
     * @var bool
     */
    protected $deferCompile = false;

    /**
     * @var string
     */
    protected $nodeSet = Lexicon::DEFAULT_NODE_SET;

    /**
     * @param LexiconInterface $lexicon
     */
    public function __construct(LexiconInterface $lexicon)
    {
        $this->lexicon = $lexicon;
    }

    /**
     * Set regex match
     *
     * @param $match
     * @return NodeInterface
     */
    public function setMatch($match)
    {
        $this->match = $match;
        return $this;
    }

    /**
     * Get regex match
     *
     * @param $match
     * @return NodeInterface
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Should this node be compiled as PHP?
     *
     * @return bool
     */
    public function isPhp()
    {
        return $this->isPhp;
    }

    /**
     * Delay compilation after non-deferred nodes
     *
     * @return bool
     */
    public function deferCompile()
    {
        return $this->deferCompile;
    }

    /**
     * Is extractable
     *
     * @return bool
     */
    public function isExtractable()
    {
        return $this->extractable;
    }

    /**
     * Make a new node instance
     *
     * @param array         $match
     * @param NodeInterface $parent
     * @param int           $offset
     * @param int           $depth
     * @return mixed
     */
    public function make(array $match, NodeInterface $parent = null, $offset = 0, $depth = 0)
    {
        if ($this->incrementDepth()) {
            $depth++;
        }

        /** @var $node Node */
        $node = new static($this->getLexicon());

        $parentId = null;

        if ($parent) {
            $parentId = $parent->getId();
        }

        $node
            ->setMatch($match)
            ->setParentId($parentId)
            ->setParsedContent($node->getContent())
            ->setOffset($offset)
            ->setDepth($depth)
            ->setId(str_random(32))
            ->setup();

        $node
            ->setContextName($node->getName())
            ->setLoopItemName($node->getLoopItemInRawAttributes());


        return $this->getLexicon()->addNode($node);
    }

    /**
     * @return int
     */
    public function getLoopItemInRawAttributes()
    {
        $result = null;

        // [0] original string
        // [1] as
        // [2] loop item
        if (preg_match('/\s*(as)\s*(\w+)$/', $this->getRawAttributes(), $match)) {
            $result = $match[2];
        }

        return $result;
    }

    /**
     * Return a new AttributeNode
     *
     * @return AttributeNode
     */
    public function newAttributeNode($nodeType = null)
    {
        $attributeNode = new AttributeNode($this->getLexicon());
        return $attributeNode->make([], $this)->createChildNodes($nodeType);
    }

    /**
     * Compile attributes
     *
     * @return string
     */
    public function compileAttributes()
    {
        return $this->newAttributeNode()->compile();
    }

    /**
     * Compile a single attribute value
     *
     * @return string
     */
    public function compileAttributeValue($name, $offset = 0, $default = null)
    {
        return $this->newAttributeNode()->compileAttributeValue($name, $offset, $default);
    }

    /**
     * Increment depth
     *
     * @return bool
     */
    public function incrementDepth()
    {
        return $this->incrementDepth;
    }

    /**
     * Set content
     *
     * @param $content
     * @return NodeInterface
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set extraction content
     *
     * @param $extractionContent
     * @return NodeInterface
     */
    public function setExtractionContent($extractionContent)
    {
        $this->extractionContent = $extractionContent;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtractionContent()
    {
        return $this->extractionContent;
    }

    /**
     * Set parsed content
     *
     * @param $parsedContent
     * @return NodeInterface
     */
    public function setParsedContent($parsedContent)
    {
        $this->parsedContent = $parsedContent;
        return $this;
    }

    /**
     * Get parsed content
     *
     * @return string
     */
    public function getParsedContent()
    {
        return $this->parsedContent;
    }

    /**
     * Set children
     *
     * @param NodeInterface $node
     * @internal param array $children
     * @return NodeInterface
     */
    public function addChild(NodeInterface $node)
    {
        $this->children[$node->getId()] = $node->getId();
        return $this;
    }

    /**
     * Get child nodes
     *
     * @internal param mixed $class
     * @return array
     */
    public function getChildren()
    {
        $children = [];

        foreach ($this->children as $id) {
            $children[] = $this->getLexicon()->getNodeById($id);
        }

        return $children;
    }

    /**
     * Set depth
     *
     * @param $depth
     * @return NodeInterface
     */
    public function setDepth($depth = 0)
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * Get depth
     *
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Set id
     *
     * @param $id
     * @return NodeInterface
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set offset
     *
     * @param int $offset
     * @return NodeInterface
     */
    public function setOffset($offset = 0)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Get offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set name
     *
     * @param $name
     * @return NodeInterface
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return trim($this->name);
    }

    /**
     * Get name matcher
     *
     * @return string
     */
    public function getNameMatcher()
    {
        return $this->getName();
    }

    /**
     * Set parent node id
     *
     * @param Node $parentId
     * @return NodeInterface
     */
    public function setParentId($parentId = null)
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * Get parent id
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Get root
     *
     * @return NodeInterface
     */
    public function getParent()
    {
        return $this->getLexicon()->getNodeById($this->getParentId());
    }

    /**
     * Is root node
     *
     * @return bool
     */
    public function isRoot()
    {
        return !$this->getParent();
    }

    /**
     * Get extraction id
     *
     * @param null $suffix
     * @return string
     */
    public function getExtractionId($suffix = null)
    {
        if ($suffix) {
            $suffix .= '__';
        }

        return get_called_class() . '__' . $this->getName() . '__' . $this->getId() . '__' . $suffix;
    }

    /**
     * Set item name
     *
     * @param $itemName
     * @return NodeInterface
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
        return $this;
    }

    /**
     * Get item variable name
     *
     * @return string
     */
    public function getItemSource()
    {
        return '$' . camel_case(str_replace($this->getLexicon()->getScopeGlue(), '_', $this->getName())) . 'Item';
    }

    /**
     * Get context item name
     *
     * @return string
     */
    public function getContextName()
    {
        return $this->contextItemName;
    }

    /**
     * Set context item name
     *
     * @param $contextItemName
     * @return NodeInterface
     */
    public function setContextName($contextItemName)
    {
        $this->contextItemName = $contextItemName;
        return $this;
    }

    /**
     * Set parsed attributes
     *
     * @param $rawAttributes
     * @return NodeInterface
     */
    public function setRawAttributes($rawAttributes)
    {
        $this->rawAttributes = $rawAttributes;
        return $this;
    }

    /**
     * Get raw attributes
     *
     * @return string
     */
    public function getRawAttributes()
    {
        return $this->rawAttributes;
    }

    /**
     * Get root node
     *
     * @return NodeInterface|Node|null
     */
    public function getRootNode()
    {
        $node = $this;

        while (!$node->isRoot()) {
            $node = $this->getParent();
        }

        return $node;
    }

    /**
     * Get environment
     *
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->lexicon;
    }

    /**
     * Get single tag matches
     *
     * @param $text
     * @return array
     */
    public function getSingleTagMatches($text)
    {
        $matches = array();

        /**
         * $data_matches[0] is the raw data tag
         * $data_matches[1] is the data variable (dot notated)
         */
        foreach ($this->getMatches($text) as $match) {
            if (!preg_match(
                $this->getClosingTagRegex($this->getName()),
                $text,
                $closingTagMatch
            )
            ) {
                $matches[] = $match;
            }
        }

        return $matches;
    }

    /**
     * Set node set
     *
     * @param string $nodeSet
     * @return NodeInterface
     */
    public function setNodeSet($nodeSet = Lexicon::DEFAULT_NODE_SET)
    {
        $this->nodeSet = $nodeSet;
        return $this;
    }

    /**
     * Get node set
     *
     * @param string $nodeSet
     * @return NodeInterface
     */
    public function getNodeSet()
    {
        return $this->nodeSet;
    }

    /**
     * Get node types
     *
     * @return array
     */
    public function getNodeTypes()
    {
        return $this->lexicon->getNodeTypes($this->getNodeSet());
    }

    /**
     * Create child nodes
     *
     * @return NodeInterface
     */
    public function createChildNodes()
    {
        /** @var NodeInterface $nodeType */
        foreach ($this->getNodeTypes() as $nodeType) {
            foreach ($nodeType->getMatches($this->getParsedContent()) as $offset => $match) {
                $this->createChildNode($nodeType, $match, $offset);
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
    protected function createChildNode(NodeInterface $nodeType, $match, $offset = 0)
    {
        $node = $nodeType->make(
            $match,
            $parent = $this,
            $offset,
            $this->getDepth()
        );

        $node->setNodeSet($this->getNodeSet())->createChildNodes();

        $this->addChild($node);
        $this->extract($node);

        return $node;
    }

    /**
     * Extract node content
     *
     * @param NodeInterface $node
     * @return NodeInterface
     */
    protected function extract(NodeInterface $childNode)
    {
        if ($this->isExtractable()) {
            $this->newExtractor($this, $childNode)->extract();
        }

        return $this;
    }

    /**
     * Inject node content
     *
     * @param NodeInterface $node
     * @return NodeInterface
     */
    protected function inject(NodeInterface $childNode)
    {
        $this->newExtractor($this, $childNode)->inject();
        return $this;
    }

    /**
     * New node extractor
     *
     * @param NodeInterface $node
     * @param NodeInterface $childNode
     * @return NodeExtractor
     */
    public function newExtractor(NodeInterface $node, NodeInterface $childNode)
    {
        return new NodeExtractor($node, $childNode);
    }

    /**
     * Surround node source with PHP tags
     *
     * @param null $segment
     * @return null|string
     */
    public function php($segment = null)
    {
        if ($segment) {
            $segment = '<?php ' . $segment . ' ?>';
        }
        return $segment;
    }

    /**
     * Wrap source in escape method
     *
     * @param $source
     * @return string
     */
    public function escape($source)
    {
        return 'e(' . $source . ')';
    }

    /**
     * @return NodeFinder
     */
    public function getNodeFinder()
    {
        return new NodeFinder($this);
    }

    /**
     * @param $loopItemName
     * @return Node
     */
    public function setLoopItemName($loopItemName)
    {
        $this->loopItemName = $loopItemName;
        return $this;
    }

    /**
     * Get loop item name
     *
     * @return null|string
     */
    public function getLoopItemName()
    {
        return $this->loopItemName;
    }

    /**
     * Set node validator
     *
     * @param ValidatorInterface $validator
     * @return NodeInterface
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * Get node validator
     *
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Validate the node
     *
     * @internal param bool $isValid
     * @return NodeInterface
     */
    public function validate()
    {
        if ($validator = $this->getValidator()) {
            return $validator->isValid();
        }

        return $this->isValid();
    }

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * Is filter node
     *
     * @return mixed
     */
    public function isFilter()
    {
        $isFilter = false;

        $lexicon = $this->getLexicon();

        if ($plugin = $lexicon->getPluginHandler()->get($this->getName())) {
            $parts    = explode($lexicon->getScopeGlue(), $this->getName());
            $isFilter = $plugin->isFilter($parts[1]);
        }

        return $isFilter;
    }

    /**
     * Is a parse able node
     *
     * @return mixed
     */
    public function isParse()
    {
        $isParse = false;

        $lexicon = $this->getLexicon();

        if ($plugin = $lexicon->getPluginHandler()->get($this->getName())) {
            $parts   = explode($lexicon->getScopeGlue(), $this->getName());
            $isParse = $plugin->isParse($parts[1]);
        }

        return $isParse;
    }

    /**
     * Get variable regex
     *
     * @return string
     */
    public function getVariableRegex()
    {
        $glue = preg_quote($this->getLexicon()->getScopeGlue(), '/');

        return $glue === '\\.' ? '[a-zA-Z0-9_' . $glue . ']+' : '[a-zA-Z0-9_\.' . $glue . ']+';
    }

    /**
     * Get closing tag regex
     *
     * @param $name
     * @return string
     */
    public function getClosingTagRegex($name)
    {
        return '/\{\{\s*(\/' . $name . ')\s*\}\}/m';
    }

    /**
     * Get match
     *
     * @param $text
     * @param $regex
     * @return array
     */
    public function getSingleMatch($text, $regex)
    {
        $match = [];
        preg_match($regex, $text, $match);
        return $match;
    }

    /**
     * Get matches
     *
     * @param $string
     * @param $regex
     * @return array
     */
    public function getMatches($string, $regex = null)
    {
        if (!$regex) {
            $regex = $this->regex();
        }
        $matches = [];
        preg_match_all($regex, $string, $matches, PREG_SET_ORDER);
        return $matches;
    }

    /**
     * Node has a parent block
     *
     * @return bool
     */
    public function hasParentBlock()
    {
        return $this->getParent() instanceof BlockInterface;
    }

    /**
     * Node has parent root
     *
     * @return bool
     */
    public function hasParentRoot()
    {
        return $parent = $this->getParent() and $parent->isRoot();
    }

    /**
     * Node has parent block which is not the root
     *
     * @return bool
     */
    public function hasParentBlockNotRoot()
    {
        return $this->hasParentBlock() and !$this->hasParentRoot();
    }

    /**
     * Compress
     *
     * @param $string
     * @return mixed
     */
    public function compress($string)
    {
        return preg_replace(['/\s\s+/', '/\n+/'], ' ', trim($string));
    }

    /**
     * Get value from match array with an offset
     *
     * @param $offset
     * @return string|null
     */
    public function match($offset)
    {
        return $this->get($this->getMatch(), $offset);
    }

    /**
     * Get value from array or default
     *
     * @param array $array
     * @param       $key
     * @param null  $value
     */
    public function get(array $array, $key, $value = null)
    {
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
        }

        return $value;
    }

}