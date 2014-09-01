<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\EnvironmentInterface;
use Anomaly\Lexicon\Contract\NodeInterface;
use Anomaly\Lexicon\Contract\NodeValidatorInterface;
use Anomaly\Lexicon\AttributeParser;
use Anomaly\Lexicon\ContextFinder;

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
     * @var NodeInterface|null
     */
    protected $parent = null;

    /**
     * @var string
     */
    protected $parsedContent;

    /**
     * @var EnvironmentInterface
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

    protected $isEmbedded = false;

    public function __construct(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
    }



    public function setIsEmbedded($isEmbedded = false)
    {
        $this->isEmbedded = $isEmbedded;
        return $this;
    }

    public function getIsEmbedded()
    {
        return $this->isEmbedded;
    }

    /**
     * Make a new node instance
     *
     * @param array $match
     * @param null  $parent
     * @param int   $depth
     * @param int   $count
     * @return mixed
     */
    public function make(array $match, $parent = null, $depth = 0, $count = 0)
    {
        $depth = ($this->incrementDepth and $depth <= $this->lexicon->getMaxDepth())
            ? $depth + 1
            : $depth;

        /** @var $node Node */
        $node = new static($this->lexicon);

        $node
            ->setEnvironment($this->lexicon)
            ->setParent($parent)
            ->setCount($count)
            ->setDepth($depth)
            ->getSetup($match);

        $node
            ->setId($node->getContent() . $node->getName() . $node->getDepth() . $node->getCount())
            ->setItemName(
                str_replace(' ', '', str_replace($this->lexicon->getScopeGlue(), ' ', $node->getName())) . 'Item'
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
        $this->children[] = $node;
        return $this;
    }

    /**
     * Get child nodes
     *
     * @param $class mixed
     * @return array
     */
    public function getChildren($class = null)
    {
        $children = $this->children;

        if (is_string($class)) {
            foreach ($this->children as $key => $node) {
                if (!is_subclass_of($node, $class)) {
                    unset($children[$key]);
                }
            }
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
     * Set parent node
     *
     * @param Node $parentNode
     * @return NodeInterface
     */
    public function setParent(Node $parentNode = null)
    {
        $this->parent = $parentNode;
        return $this;
    }

    /**
     * Get root
     *
     * @return NodeInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Is root node
     *
     * @return bool
     */
    public function isRoot()
    {
        return !$this->parent;
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

        return "__extraction__" . get_called_class() . '__' . $this->getName() . '__' . $this->getId(
        ) . '__' . $suffix . "__extraction__";
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
     * Set environment
     *
     * @param EnvironmentInterface $lexicon
     * @return NodeInterface
     */
    public function setEnvironment(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    /**
     * Get environment
     *
     * @return EnvironmentInterface
     */
    public function getEnvironment()
    {
        return $this->lexicon;
    }

    /**
     * Array of content to be compiled at the end of a view
     *
     * @return array
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Get open tag matches
     *
     * @param $text
     * @return array
     */
    public function getOpenTagMatches($text)
    {
        return $this->lexicon->getRegex()->getMatches($text, $this->getRegexMatcher());
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
        foreach ($this->getOpenTagMatches($text) as $match) {
            if (!preg_match(
                $this->lexicon->getRegex()->getClosingTagRegexMatcher($this->getName()),
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
                $nodeType->setEnvironment($this->lexicon);
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
            $parent = $this,
            $this->depth,
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
                    $node->validate() ? $this->compiledLine($node->compileOpen()) : null,
                    $this->getParsedContent()
                )
            );
        }

        if (method_exists($node, 'compileClose')) {
            $this->setParsedContent(
                str_replace(
                    $node->getExtractionId('close'),
                    $node->validate() ? $this->compiledLine($node->compileClose()) : null,
                    $this->getParsedContent()
                )
            );
        }

        $this->setParsedContent(
            str_replace(
                $node->getExtractionId(),
                $node->validate() ? $this->compiledLine($node->compile()) : null,
                $this->getParsedContent()
            )
        );

        return $this;
    }

    public function compiledLine($line)
    {
        return "\n__COMPILED__" . $line . "\n";
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
    public function setValidator(NodeValidatorInterface $validator)
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
        return $this->getEnvironment()->isFilter($this->getName());
    }

    /**
     * Is parse
     *
     * @return mixed
     */
    public function isParse()
    {
        return $this->getEnvironment()->isParse($this->getName());
    }

}