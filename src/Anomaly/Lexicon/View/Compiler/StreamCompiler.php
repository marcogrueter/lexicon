<?php namespace Anomaly\Lexicon\View\Compiler;

use Anomaly\Lexicon\Contract\NodeBlockInterface;

class StreamCompiler
{

    /**
     * Opening string for segment
     */
    const OPEN = '__LEXICON';

    /**
     * Closing string for segment
     */
    const CLOSE = 'LEXICON__';

    /**
     * Prefix for segments compiled from nodes
     */
    const COMPILED = '*COMPILED*';

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

        $source = '';

        foreach ($this->stream as $segment) {
            if (starts_with($segment, static::COMPILED)) {
                $source .= $this->spaces(8) . $this->clean($segment) . "\n";
            } elseif (!empty($segment)) {
                $source .= $this->spaces(8) . $this->string($segment) . "\n";
            }
        }

        return $this->compileFooter($source);
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
                $segment = $this->spaces(8) . $this->clean($segment) . "\n";
            }
            $source = str_replace('{{ parent }}', '', $source);
            $source = ltrim($source, PHP_EOL) . PHP_EOL . implode(PHP_EOL, array_reverse($footer));
        }

        return $source;
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

    /**
     * String
     *
     * @param $segment
     * @return string
     */
    public function string($segment)
    {
        if (str_contains($segment = $this->clean($segment), '\'')) {
            $segment = addslashes($segment);
            return "echo stripslashes('{$segment}');";
        }

        return "echo '{$segment}';";
    }

    /**
     * Spaces
     *
     * @param int $number
     * @return string
     */
    public function spaces($number = 1)
    {
        return str_repeat("\x20", $number);
    }

}