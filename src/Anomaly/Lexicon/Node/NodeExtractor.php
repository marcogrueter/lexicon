<?php namespace Anomaly\Lexicon\Node;

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
     * preg_replace limit
     */
    const LIMIT = 1;

    /**
     * Opening tag constant
     */
    const OPENING_TAG = 'opening';

    /**
     * Closing tag constant
     */
    const CLOSING_TAG = 'closing';

    /**
     * Extract parent content
     *
     * @param NodeInterface $child
     * @param NodeInterface $parent
     */
    public function extract(NodeInterface $child, NodeInterface $parent)
    {
        $this->extractOpening($child, $parent);
        $this->extractClosing($child, $parent);
        $this->extractContent($child, $parent);
    }

    /**
     * Inject compiled source
     *
     * @param NodeInterface $child
     * @param NodeInterface $parent
     */
    public function inject(NodeInterface $child, NodeInterface $parent)
    {
        $this->injectOpening($child, $parent);
        $this->injectClosing($child, $parent);
        $this->injectContent($child, $parent);
    }

    /**
     * Extract open
     */
    public function extractOpening(NodeInterface $child, NodeInterface $parent)
    {
        if ($child instanceof BlockInterface and $openingTag = $child->getOpeningTag()) {

            $content = preg_replace(
                $this->search($openingTag),
                $child->getExtractionId(static::OPENING_TAG),
                $parent->getCurrentContent(),
                self::LIMIT
            );

            $parent->setCurrentContent($content);
        }
    }

    /**
     * Extract close
     */
    public function extractClosing(NodeInterface $child, NodeInterface $parent)
    {
        if ($child instanceof BlockInterface and $closingTag = $child->getClosingTag()) {

            $content = preg_replace(
                $this->search($closingTag),
                $child->getExtractionId(static::CLOSING_TAG),
                $parent->getCurrentContent(),
                self::LIMIT
            );

            $parent->setCurrentContent($content);
        }
    }

    /**
     * Extract content
     */
    public function extractContent(NodeInterface $child, NodeInterface $parent)
    {
        $content = preg_replace(
            $this->search($child->getExtractionContent()),
            $child->getExtractionId(),
            $parent->getCurrentContent(),
            self::LIMIT
        );
        //dd($content);
        $parent->setCurrentContent($content);
    }

    /**
     * Inject open
     */
    public function injectOpening(NodeInterface $child, NodeInterface $parent)
    {
        if ($child instanceof BlockInterface and $source = $child->compileOpeningTag()) {

            $content = preg_replace(
                $this->search($child->getExtractionId(static::OPENING_TAG)),
                $child->validate() ? $this->php($source) : null,
                $parent->getCurrentContent(),
                self::LIMIT
            );

            $parent->setCurrentContent($content);
        }
    }

    /**
     * Inject close
     */
    public function injectClosing(NodeInterface $child, NodeInterface $parent)
    {
        if ($child instanceof BlockInterface and $source = $child->compileClosingTag()) {

            $content = preg_replace(
                $this->search($child->getExtractionId(static::CLOSING_TAG)),
                $child->validate() ? $this->php($source) : null,
                $parent->getCurrentContent(),
                self::LIMIT
            );

            $parent->setCurrentContent($content);
        }
    }

    /**
     * Inject content
     */
    public function injectContent(NodeInterface $child, NodeInterface $parent)
    {
        $source = $child->isPhp() ? $this->php($child->compile()) : $child->compile();

        $content = preg_replace(
            $this->search($child->getExtractionId()),
            $child->validate() ? $source : null,
            $parent->getCurrentContent(),
            self::LIMIT
        );

        $parent->setCurrentContent($content);
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

    /**
     * Surround node source with PHP tags
     *
     * @param null $source
     * @return null|string
     */
    public function php($source = null)
    {
        if ($source) {
            $source = '<?php ' . $source . ' ?>';
        }
        return $source;
    }

}
