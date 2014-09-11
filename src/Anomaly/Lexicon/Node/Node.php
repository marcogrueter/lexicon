<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Attribute\AttributeParser;
use Anomaly\Lexicon\ContextFinder;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\ValidatorInterface;

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
    protected $count = 0;

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
     * @var string
     */
    protected $parsedAttributes = '';

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
     * @var ContextFinder
     */
    protected $contextFinder;

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
     * @var bool
     */
    protected $isEmbedded = false;

    /**
     * Should compile as PHP or not?
     *
     * @var bool
     */
    protected $isPhp = true;

    /**
     * Defer compile
     *
     * @var bool
     */
    protected $deferCompile = false;

    /**
     * Is this the root node type
     *
     * @var bool
     */
    protected $root = false;

    /**
     * @param LexiconInterface $lexicon
     */
    public function __construct(LexiconInterface $lexicon)
    {
        $this->lexicon = $lexicon;
    }

    /**
     * @param bool $isEmbedded
     * @return $this
     */
    public function setIsEmbedded($isEmbedded = false)
    {
        $this->isEmbedded = $isEmbedded;
        return $this;
    }

    /**
     * Is embedded
     *
     * @return bool
     */
    public function isEmbedded()
    {
        return $this->isEmbedded;
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
     * Make a new node instance
     *
     * @param array $match
     * @param null  $parentId
     * @param int   $depth
     * @param int   $count
     * @return mixed
     */
    public function make(array $match, $parentId = null, $depth = 0, $count = 0)
    {
        $depth = $this->incrementDepth() ? $depth + 1 : $depth;

        /** @var $node Node */
        $node = new static($this->getLexicon());

        $node
            ->setParentId($parentId)
            ->setCount($count)
            ->setDepth($depth)
            ->setup($match);

        $node
            ->setId($node->getContent() . $node->getName() . $node->getDepth() . $node->getCount())
            ->setItemName(
                studly_case(str_replace($this->getLexicon()->getScopeGlue(), '_', $node->getName())) . 'Item'
            )
            ->setContextName($node->getName())
            ->setParsedContent($node->getContent());

        $parsedAttributes = $node->getParsedAttributes();

        $asSegments = explode('as', $parsedAttributes);

        if (count($asSegments) == 2) {
            $node->setLoopItemName($asSegments[1]);
        }

        return $node;
    }

    /**
     * Return a new AttributeParser
     *
     * @return AttributeParser
     */
    public function newAttributeParser()
    {
        return (new AttributeParser($this))->parse();
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
     * @param array $children
     * @return NodeInterface
     */
    public function addChild(NodeInterface $node)
    {
        $this->getLexicon()->addNode($node);
        $this->children[$node->getId()] = $node->getId();
        return $this;
    }

    /**
     * Get child nodes
     *
     * @param $class mixed
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
     * @return NodeInterface
     */
    public function setId($id)
    {
        $this->id = md5($id);
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
     * Set count
     *
     * @param int $count
     * @return NodeInterface
     */
    public function setCount($count = 0)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * Get count
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
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
        return $this->root;
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
    public function getItemName()
    {
        return $this->itemName;
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
     * @param $parsedAttributes
     * @return NodeInterface
     */
    public function setParsedAttributes($parsedAttributes)
    {
        $this->parsedAttributes = $parsedAttributes;
        return $this;
    }

    /**
     * Get parsed attributes
     *
     * @return string
     */
    public function getParsedAttributes()
    {
        return $this->parsedAttributes;
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
     * Create child nodes
     *
     * @return Node
     */
    public function createChildNodes()
    {
        foreach ($this->lexicon->getNodeTypes() as $nodeType) {
            if ($nodeType instanceof NodeInterface) {
                foreach ($nodeType->getMatches($this->parsedContent) as $count => $match) {
                    $this->createChildNode($nodeType, $match, $count);
                }
            }
        }

        return $this;
    }

    /**
     * Create child node
     *
     * @param Node $nodeType
     * @param      $match
     * @param int  $count
     * @return mixed
     */
    protected function createChildNode(NodeInterface $nodeType, $match, $count = 0)
    {
        $node = $nodeType->make(
            $match,
            $parentId = $this->getId(),
            $this->getDepth(),
            $count
        );

        $node->createChildNodes();

        $this->extract($node);

        return $node;
    }

    /**
     * Extract node content
     *
     * @param Node $node
     * @return Node
     */
    protected function extract(NodeInterface $node)
    {
        if (method_exists($node, 'getExtractionContentOpen')) {
            $this->setParsedContent(
                str_replace(
                    $node->getExtractionContentOpen(),
                    $node->getExtractionId('open'),
                    $this->getParsedContent()
                )
            );
        }

        if (method_exists($node, 'getExtractionContentClose')) {
            $this->setParsedContent(
                str_replace(
                    $node->getExtractionContentClose(),
                    $node->getExtractionId('close'),
                    $this->getParsedContent()
                )
            );
        }

        $this->setParsedContent(
            str_replace(
                $node->getExtractionContent(),
                $node->getExtractionId(),
                $this->getParsedContent()
            )
        );

        $this->addChild($node);

        return $this;
    }

    /**
     * Inject node content
     *
     * @param Node $node
     * @return NodeInterface
     */
    protected function inject(NodeInterface $node)
    {

        if (method_exists($node, 'compileOpen')) {
            $this->setParsedContent(
                str_replace(
                    $node->getExtractionId('open'),
                    $node->validate() ? $this->php($node->compileOpen()) : null,
                    $this->getParsedContent()
                )
            );
        }

        if (method_exists($node, 'compileClose')) {
            $this->setParsedContent(
                str_replace(
                    $node->getExtractionId('close'),
                    $node->validate() ? $this->php($node->compileClose()) : null,
                    $this->getParsedContent()
                )
            );
        }

        if ($node instanceof NodeBlockInterface or !$node->isPhp()) {
            $compile = $node->compile();
        } else {
            $compile = $this->php($node->compile());
        }

        $this->setParsedContent(
            str_replace(
                $node->getExtractionId(),
                $node->validate() ? $compile : null,
                $this->getParsedContent()
            )
        );

        return $this;
    }

    /**
     * Surround node source with PHP tags
     *
     * @param null $segment
     * @return null|string
     */
    public function php($segment = null)
    {
        if (empty($segment)) {
            return null;
        }
        return '<?php ' . $segment . ' ?>';
    }

    /**
     * @return ContextFinder
     */
    public function getContextFinder()
    {
        return new ContextFinder($this);
    }

    /**
     * @param $loopItemName
     * @return Node
     */
    public function setLoopItemName($loopItemName)
    {
        $this->loopItemName = trim($loopItemName);
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
     * @param NodeValidatorInterface $validator
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
     * @return NodeValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Validate the node
     *
     * @param bool $isValid
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
     * Is filter
     *
     * @return mixed
     */
    public function isFilter()
    {
        return $this->getLexicon()->getPluginHandler()->isFilter($this->getName());
    }

    /**
     * Is parse
     *
     * @return mixed
     */
    public function isParse()
    {
        return $this->getLexicon()->getPluginHandler()->isParse($this->getName());
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
     * Get embedded attribute regex
     *
     * @return string
     */
    public function getEmbeddedAttributeRegexMatcher()
    {
        return "/\{\s*?({$this->getVariableRegex()})(\s+.*?)?\s*?(\/)?\}/ms";
    }

    /**
     * Get embedded matches
     *
     * @param $string
     * @return array
     */
    public function getEmbeddedMatches($string)
    {
        return $this->getMatches($string, $this->getEmbeddedAttributeRegexMatcher());
    }

    /**
     * Get match
     *
     * @param $text
     * @param $regex
     * @return array
     */
    public function getMatch($text, $regex)
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
     * Compress
     *
     * @param $string
     * @return mixed
     */
    public function compress($string)
    {
        return preg_replace(['/\s\s+/', '/\n+/'], ' ', trim($string));
    }
}