<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Lexicon;

class Variable extends Single
{
    /**
     * Is embedded
     *
     * @var bool
     */
    protected $isEmbedded = false;

    /**
     * Set is embedded
     *
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

    public function regex()
    {
        return "/\{\{\s*({$this->getVariableRegex()})(\s+.*?)?\s*(\/)?\}\}/ms";
    }

    public function compile(array $attributes = [])
    {
        $echo = $end = null;

        if (!$this->isEmbedded()) {
            $echo = 'echo ';
            $end  = ';';
        }

        return "{$echo}{$this->compileVariable()}{$end}";
    }

    public function compileKey()
    {
        return $this->getOffset();
    }

    /**
     * Compile a named key from an ordered embedded attribute
     *
     * @return string
     */
    public function compileNamedFromOrderedKey()
    {
        if (!$this->isEmbedded()) {

            $node = $this->make([], $this->getParent())->setName($this->getName());

            $finder = $node->getNodeFinder();

            return $this->getNodeFinder()->getName();
        }

        return $this->getName();
    }

    /**
     * Compile variable
     *
     * @return string
     */
    public function compileVariable()
    {
        $attributes = $this->compileAttributes();

        $finder = $this->getNodeFinder();

        $name = $finder->getName();

        $item = $finder->getItemSource();

        $expected = Lexicon::EXPECTED_ECHO;

        return "\$__data['__env']->variable({$item}, '{$name}', {$attributes}, '', null, '{$expected}')";
    }
}