<?php namespace Aiws\Lexicon\Data;

class Source
{
    protected $source = '';

    public function __construct($source = '')
    {
        $this->source = $source;
    }

    /**
     * @param $source
     * @return $this
     */
    public function write($source)
    {
        $this->source .= $source;
        return $this;
    }

    /**
     * @return $this
     */
    public function line()
    {
        $this->source .= "\n";
        return $this;
    }

    /**
     * @return $this
     */
    protected function open()
    {
        $this->source = '<?php ' . $this->source;
        return $this;
    }

    /**
     * @return $this
     */
    protected function close()
    {
        $this->source .= '; ?>';
        return $this;
    }

    /**
     * @return $this
     */
    public function tags()
    {
        $this->open()->close();
        return $this;
    }

    /**
     * @return $this
     */
    public function asEcho()
    {
        trim($this->source);

        if (!empty($this->source)) {
            $this->source = 'echo ' . $this->source;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function tagsEcho()
    {
        $this->asEcho()->tags();
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}