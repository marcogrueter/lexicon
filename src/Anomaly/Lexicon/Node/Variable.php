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
        return $this->getCount();
    }

    /**
     * Compile a named key from an ordered embedded attribute
     *
     * @return string
     */
    public function compileNamedFromOrderedKey()
    {
        if (!$this->isEmbedded()) {

            $node = $this->make(['name' => $this->getName()], $this->getParent());

            $finder = $node->getNodeFinder();

            return $finder->getName();
        }

        return $this->getName();
    }

    public function compileVariable()
    {
        $attributes = $this->newAttributeCompiler()->compile();

        $finder = $this->getNodeFinder();

        $expected = Lexicon::ECHOABLE;

        return "\$__data['__env']->variable({$finder->getItemSource()}, '{$finder->getName(
        )}', {$attributes}, '', null, '{$expected}')";
    }
}