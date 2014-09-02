<?php namespace Anomaly\Lexicon\View\Compiler;

use Anomaly\Lexicon\Contract\NodeBlockInterface;

class StreamCompiler
{

    /**
     * Opening string for segment
     */
    const OPEN = '{{@LEXICON@';

    /**
     * Closing string for segment
     */
    const CLOSE = '@LEXICON@}}';

    /**
     * Prefix for segments compiled from nodes
     */
    const COMPILED = '@COMPILED@';

    /**
     * Block node
     *
     * @var NodeBlockInterface
     */
    protected $blockNode;

    /**
     * Stream array of segments
     *
     * @var array
     */
    protected $stream = [];

    /**
     * Set original source
     *
     * @param $source
     * @return $this
     */
    public function __construct(NodeBlockInterface $blockNode)
    {
        $this->blockNode = $blockNode->createChildNodes();
    }

    /**
     * Get block node
     *
     * @return NodeBlockInterface
     */
    public function getBlockNode()
    {
        return $this->blockNode;
    }

    /**
     * Regex
     *
     * @return string
     */
    public function regex()
    {
        $open  = static::OPEN;
        $close = static::CLOSE;
        return "/{$open}(.*?){$close}/";
    }

    /**
     * Source
     *
     * @return string
     */
    public function source()
    {
        return $this->blockNode->compile();
    }

    /**
     * Parse
     *
     * @return array
     */
    public function parse()
    {
        return preg_split($this->regex(), $this->source(), -1, PREG_SPLIT_DELIM_CAPTURE);
    }

    /**
     * Compile
     *
     * @return string
     */
    public function compile()
    {
        $this->stream = $this->parse();

        foreach ($this->stream as &$segment) {
            if (starts_with($segment, static::COMPILED)) {
                $segment = $this->clean($segment);
            }
        }

        return $this->compileFooter(implode($this->getImplodeGlue(), $this->stream));
    }

    /**
     * Compile footer
     *
     * @param $source
     * @return mixed|string
     */
    public function compileFooter($source)
    {
        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.

        $footer = $this->getBlockNode()->getFooter();

        if (count($footer) > 0) {

            foreach ($footer as &$segment) {
                $segment = $this->clean($segment);
            }

            $source = str_replace('{{ parent }}', '', $source);
            $source = ltrim($source, PHP_EOL) . PHP_EOL . implode($this->getImplodeGlue(), array_reverse($footer));
        }

        return $source;
    }

    /**
     * Get implode glue
     *
     * @return null|string
     */
    public function getImplodeGlue()
    {
        return $this->getBlockNode()->getLexicon()->isDebug() ? PHP_EOL : null;
    }

    /**
     * Clean
     *
     * @param $segment
     * @return mixed
     */
    public function clean($segment)
    {
        return str_replace(static::COMPILED, '', $segment);
    }

}