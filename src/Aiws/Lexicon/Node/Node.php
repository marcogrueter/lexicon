<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Contract\EnvironmentInterface;
use Aiws\Lexicon\Contract\NodeInterface;
use Aiws\Lexicon\Util\ContextFinder;

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
     * @var bool
     */
    protected $trash = false;

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
        $node = new static;

        $node
            ->setEnvironment($this->lexicon)
            ->getSetup($match);

        $node
            ->setCount($count)
            ->setDepth($depth)
            ->setId($node->getContent() . $node->getName() . $node->getDepth() . $node->getCount())
            ->setItemName(str_replace(' ', '', str_replace($this->lexicon->getScopeGlue(), ' ', $node->getName())) . 'Item')
            ->setContextName($node->getName())
            ->setParsedContent($node->getContent())
            ->setParent($parent)
            ->setAttributes();

        return $node;
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
     * @return $this
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
     * Get child nodes
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
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
     * @return Node
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
     * @return Node
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
        return $this->name;
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
     * @return Node
     */
    public function setParent(Node $parentNode = null)
    {
        $this->parent = $parentNode;
        return $this;
    }

    /**
     * Get root
     *
     * @return Node
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
            $suffix .= '__ ';
        }

        return ' __' . get_called_class() . '__' . $this->getName() . '__' . $this->getId() . '__' . $suffix;
    }

    /**
     * Set item name
     *
     * @param $itemName
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * Set attributes
     *
     * @return Node
     */
    public function setAttributes()
    {
        $this->attributes = $this->lexicon->getRegex()->parseAttributes($this->parsedAttributes);
        return $this;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get attribute
     *
     * @param     $name
     * @param int $default
     * @return null
     */
    public function getAttribute($name, $default = 0)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } elseif (isset($this->attributes[$default])) {
            return $this->attributes[$default];
        }

        return null;
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
     * @return $this
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

    public function setIsTrashable($isTrashable = false)
    {
        $this->trash = $isTrashable;
        return $this;
    }

    /**
     * Is the node trashable?
     *
     * @return bool
     */
    public function isTrashable()
    {
        return $this->trash;
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
        $rootNodeType = $this->lexicon->getRootNodeType();

        if ($rootNodeType instanceof Node) {
            $rootNodeType->setEnvironment($this->lexicon);
            foreach ($rootNodeType->getMatches($this->parsedContent) as $count => $match) {
                $this->createChildNode($rootNodeType, $match, $count);
            }
        }

        foreach ($this->lexicon->getNodeTypes() as $nodeType) {
            if ($nodeType instanceof Node) {
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

        $this->extract($node->createChildNodes());

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
        if (method_exists($node, 'compileParentNode')) {
            $this->setParsedContent($node->compileParentNode($this->getParsedContent()));
        }

        if (method_exists($node, 'getExtractionOpen')) {
            $this->setParsedContent(str_replace(
                $node->getExtractionOpen(),
                $node->getExtractionId('open'),
                $this->getParsedContent()
            ));
        }

        if (method_exists($node, 'getExtractionClose')) {
            $this->setParsedContent(str_replace(
                    $node->getExtractionClose(),
                    $node->getExtractionId('close'),
                    $this->getParsedContent()
                ));
        }

        $this->setParsedContent(str_replace(
            $node->getExtractionContent(),
            $node->getExtractionId(),
            $this->getParsedContent()
        ));

        $this->children[] = $node;

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
            $this->setParsedContent(str_replace(
                $node->getExtractionId('open'),
                $node->compileOpen(),
                $this->getParsedContent()
            ));
        }

        if (method_exists($node, 'compileClose')) {
            $this->setParsedContent(str_replace(
                    $node->getExtractionId('close'),
                    $node->compileClose(),
                    $this->getParsedContent()
                ));
        }

        $this->setParsedContent(str_replace(
            $node->getExtractionId(),
            $node->compile(),
            $this->getParsedContent()
        ));

        return $this;
    }

    /**
     * @return ContextFinder
     */
    public function getContextFinder()
    {
        return new ContextFinder($this);
    }

}