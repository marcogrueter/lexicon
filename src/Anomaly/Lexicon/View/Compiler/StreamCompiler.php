<?php namespace Anomaly\Lexicon\View\Compiler;

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
     * Original source
     *
     * @var string
     */
    protected $source = '';

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
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
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
     * Parse
     *
     * @return array
     */
    public function parse()
    {
        return preg_split($this->regex(), $this->source, -1, PREG_SPLIT_DELIM_CAPTURE);
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