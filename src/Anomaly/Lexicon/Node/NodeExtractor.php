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

        $this->node->addChild($this->childNode);
    }

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
                str_replace(
                    $this->childNode->getExtractionContentOpen(),
                    $this->childNode->getExtractionId('open'),
                    $this->node->getParsedContent()
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
                str_replace(
                    $this->childNode->getExtractionContentClose(),
                    $this->childNode->getExtractionId('close'),
                    $this->node->getParsedContent()
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
            str_replace(
                $this->childNode->getExtractionContent(),
                $this->childNode->getExtractionId(),
                $this->node->getParsedContent()
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
                str_replace(
                    $this->childNode->getExtractionId('open'),
                    $this->childNode->validate() ? $this->node->php($this->childNode->compileOpen()) : null,
                    $this->node->getParsedContent()
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
                str_replace(
                    $this->childNode->getExtractionId('close'),
                    $this->childNode->validate() ? $this->node->php($this->childNode->compileClose()) : null,
                    $this->node->getParsedContent()
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
            str_replace(
                $this->childNode->getExtractionId(),
                $this->childNode->validate() ? $compile : null,
                $this->node->getParsedContent()
            )
        );
    }

}