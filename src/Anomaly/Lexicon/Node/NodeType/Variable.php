<?php namespace Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Stub\LexiconStub;
use Anomaly\Lexicon\Support\ValueResolver;

/**
 * Class Variable
 *
 * @package Anomaly\Lexicon\Node
 */
class Variable extends Single
{

    /**
     * Regex
     *
     * @return string
     */
    public function regex()
    {
        return "/\{\{\s*({$this->getVariableRegex()})(\s+.*?)?\s*(\/)?\}\}/ms";
    }

    /**
     * Compile source
     *
     * @param array $attributes
     * @return string
     */
    public function compile($echo = true, $escaped = true)
    {
        // [0] original string
        // [1] or
        // [2] default value
        if (preg_match($this->createSplitMatcher('or'), $this->getRawAttributes(), $match)) {
            $source = $this->compileVariable($match[2], Lexicon::EXPECTED_BOOLEAN);
        } elseif (preg_match($this->createSplitMatcher('then'), $this->getRawAttributes(), $match)) {
            $source = "{$this->compileVariable('false', Lexicon::EXPECTED_BOOLEAN)} ? '{$match[2]}' : null";
        } else {
            $source = $this->compileVariable();
        }

        if ($escaped) {
            $source = "e({$source})";
        }

        if ($echo) {
            $source = "echo {$source};";
        }

        return $source;
    }

    public function createSplitMatcher($delimiter)
    {
        return "/\s*?({$delimiter})\s*[\'|\"](\w+)[\'|\"]\s*$/";
    }

    /**
     * Compile variable
     *
     * @param      $item
     * @param      $name
     * @param      $attributes
     * @param      $expected
     * @param bool $useEcho
     * @return string
     */
    public function compileVariable($default = 'null', $expected = Lexicon::EXPECTED_STRING)
    {
        if (!in_array($default, ['true', 'false', 'null'])) {
            $default = "'{$default}'";
        }

        $resolver = new ValueResolver();

        $default = $resolver->resolve($default);

        $finder = $this->getNodeFinder();

        $item       = $finder->getItemSource();
        $name       = $finder->getName();
        $attributes = $this->compileAttributes();

        return "\$this->variable({$item},'{$name}',{$attributes},'',{$default},'{$expected}')";
    }

    /**
     * Compile key
     *
     * @return int
     */
    public function compileKey()
    {
        return $this->getOffset();
    }

    /**
     * Stub for testing with PHPSpec
     * This stub of the LexiconInterface gets injected to the VariableSpec construct
     *
     * @return \Anomaly\Lexicon\Contract\LexiconInterface
     */
    public static function stub()
    {
        return new static(LexiconStub::get());
    }

}