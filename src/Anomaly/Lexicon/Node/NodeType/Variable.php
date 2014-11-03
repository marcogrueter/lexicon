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
        if (preg_match('/\s*?(or)\s*[\'|"](\w+)[\'|"]\s*$/', $this->getRawAttributes(), $match)) {
            $default = $match[2];
            $source = $this->compileVariable($echo, $escaped, $default);
        } else {
            $source = $this->compileVariable($echo, $escaped);
        }

        return $source;
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
    public function compileVariable($echo = true, $escaped = true, $default = 'null')
    {
        $default = "'{$default}'";

        $resolver = new ValueResolver();

        $default = $resolver->resolve($default);

        $finder = $this->getNodeFinder();

        $item       = $finder->getItemSource();
        $name       = $finder->getName();
        $attributes = $this->compileAttributes();

        $expected = Lexicon::EXPECTED_STRING;

        $source = "\$this->variable({$item},'{$name}',{$attributes},'',{$default},'{$expected}')";

        if ($escaped) {
            $source = "e({$source})";
        }

        if ($echo) {
            $source = "echo {$source};";
        }

        return $source;
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