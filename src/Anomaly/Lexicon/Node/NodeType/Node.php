<?php namespace Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Attribute\AttributeNode;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\ValidatorInterface;
use Anomaly\Lexicon\Stub\LexiconStub;

class Node implements NodeInterface
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
    protected $currentContent;

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
     * Node finder
     *
     * @var NodeFinder
     */
    protected $nodeFinder;

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
     * @var AttributeNode
     */
    protected $attributeNode;

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
    public function setMatch(array $match)
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
     * @return int
     */
    public function getItemAliasFromRawAttributes()
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
     * Return attribute node
     *
     * @return AttributeNode
     */
    public function getAttributes()
    {
        return $this->attributeNode;
    }

    /**
     * Compile attributes
     *
     * @return string
     */
    public function compileAttributes()
    {
        $source = '[]';

        if ($attributes = $this->getAttributes() and $result = $attributes->compile()) {
            $source = $result;
        }

        return $source;
    }

    /**
     * Compile a single attribute value
     *
     * @return string
     */
    public function compileAttributeValue($name, $offset = 0, $default = null)
    {
        return $this->getAttributes()->compileAttributeValue($name, $offset, $default);
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
     * Set current  content
     *
     * @param $currentContent
     * @return NodeInterface
     */
    public function setCurrentContent($currentContent)
    {
        $this->currentContent = $currentContent;
        return $this;
    }

    /**
     * Get current  content
     *
     * @return string
     */
    public function getCurrentContent()
    {
        return $this->currentContent;
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

        $nodeFactory = $this->getLexicon()->getFoundation()->getNodeFactory();

        foreach ($this->children as $id) {
            $children[] = $nodeFactory->getById($id);
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
     * @param int $parentId
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
        return $this->getLexicon()->getFoundation()->getNodeFactory()->getById($this->getParentId());
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
        return $this->getLexicon()->getFoundation()->getNodeFinder($this);
    }

    /**
     * @param $loopItemName
     * @return Node
     */
    public function setItemAlias($loopItemName)
    {
        $this->loopItemName = $loopItemName;
        return $this;
    }

    /**
     * Get loop item name
     *
     * @return null|string
     */
    public function getItemAlias()
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
        return $this->getLexicon()->getFoundation()->getPluginHandler()->isFilter($this->getName());
    }

    /**
     * Is a parse able node
     *
     * @return mixed
     */
    public function isParse()
    {
        return $this->getLexicon()->getFoundation()->getPluginHandler()->isParse($this->getName());
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

    /**
     * Set attribute node
     *
     * @param AttributeNode $attributeNode
     * @return $this|NodeInterface
     */
    public function setAttributeNode(AttributeNode $attributeNode)
    {
        $this->attributeNode = $attributeNode;
        return $this;
    }

    /**
     * Get setup from regex match
     *
     * @return mixed
     */
    public function setup()
    {
    }

    /**
     * Get regex string
     *
     * @return string
     */
    public function regex()
    {
        return '';
    }

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return '';
    }

    /**
     * Stub for testing with PHPSpec
     * This stub of the LexiconInterface gets injected to the VariableSpec construct
     *
     * @return \Anomaly\Lexicon\Contract\LexiconInterface
     */
    public static function stub()
    {
        return new static(LexiconStub::stub());
    }

}