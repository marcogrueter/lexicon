<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Attribute\Compiler;
use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;

/**
 * Class NodeExtractor
 *
 * @package Anomaly\Lexicon\Node
 */
class NodeExtractor
{
    /**
     * @var NodeInterface
     */
    private $node;

    /**
     * @var NodeInterface
     */
    private $childNode;

    /**
     * preg_replace limit
     */
    const LIMIT = 1;

    /**
     * @param NodeInterface $node
     * @param NodeInterface $childNode
     */
    public function __construct(NodeInterface $node, NodeInterface $childNode)
    {
        $this->node      = $node;
        $this->childNode = $childNode;
    }

    /**
     * Extract node content
     *
     * @param NodeInterface
     * @return Node
     */
    public function extract()
    {
        $this->extractOpen();
        $this->extractClose();
        $this->extractContent();
    }

    /**
     * Inject compiled source
     */
    public function inject()
    {
        $this->injectOpen();
        $this->injectClose();
        $this->injectContent();
    }

    /**
     * Extract open
     */
    public function extractOpen()
    {
        if (method_exists($this->childNode, 'getExtractionContentOpen')) {
            $this->node->setParsedContent(
                preg_replace(
                    $this->search($this->childNode->getExtractionContentOpen()),
                    $this->childNode->getExtractionId('open'),
                    $this->node->getParsedContent(),
                    self::LIMIT
                )
            );
        }
    }

    /**
     * Extract close
     */
    public function extractClose()
    {
        if (method_exists($this->childNode, 'getExtractionContentClose')) {
            $this->node->setParsedContent(
                preg_replace(
                    $this->search($this->childNode->getExtractionContentClose()),
                    $this->childNode->getExtractionId('close'),
                    $this->node->getParsedContent(),
                    self::LIMIT
                )
            );
        }
    }

    /**
     * Extract content
     */
    public function extractContent()
    {
        $this->node->setParsedContent(
            preg_replace(
                $this->search($this->childNode->getExtractionContent()),
                $this->childNode->getExtractionId(),
                $this->node->getParsedContent(),
                self::LIMIT
            )
        );
    }

    /**
     * Inject open
     */
    public function injectOpen()
    {
        if (method_exists($this->childNode, 'compileOpen')) {
            $this->node->setParsedContent(
                preg_replace(
                    $this->search($this->childNode->getExtractionId('open')),
                    $this->childNode->validate() ? $this->node->php($this->childNode->compileOpen()) : null,
                    $this->node->getParsedContent(),
                    self::LIMIT
                )
            );
        }
    }

    /**
     * Inject close
     */
    public function injectClose()
    {
        if (method_exists($this->childNode, 'compileClose')) {
            $this->node->setParsedContent(
                preg_replace(
                    $this->search($this->childNode->getExtractionId('close')),
                    $this->childNode->validate() ? $this->node->php($this->childNode->compileClose()) : null,
                    $this->node->getParsedContent(),
                    self::LIMIT
                )
            );
        }
    }

    /**
     * Inject content
     */
    public function injectContent()
    {
        if ($this->childNode instanceof BlockInterface or !$this->childNode->isPhp()) {
            $compile = $this->childNode->compile();
        } else {
            $compile = $this->node->php($this->childNode->compile());
        }

        $this->node->setParsedContent(
            preg_replace(
                $this->search($this->childNode->getExtractionId()),
                $this->childNode->validate() ? $compile : null,
                $this->node->getParsedContent(),
                self::LIMIT
            )
        );
    }

    /**
     * Prepare regex search
     *
     * @param $string
     * @return string
     */
    public function search($string)
    {
        return '/' . preg_quote($string, '/') . '/';
    }

}