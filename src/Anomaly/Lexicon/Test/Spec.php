<?php namespace Anomaly\Lexicon\Test;

use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Illuminate\Contracts\View\View;
use PhpSpec\ObjectBehavior;

/**
 * Class ObjectBehavior
 *
 * @package Anomaly\Lexicon\Spec
 */
class Spec extends ObjectBehavior
{

    /**
     * Get matchers
     *
     * @return array
     */
    public function getMatchers()
    {
        return [
            'haveValue'     => function ($subject, $value) {
                return in_array($subject, $value);
            },
            'haveNodes'     => function ($nodeArray) {
                return $this->hasNodes($nodeArray);
            },
            'haveNodeCount' => function ($nodeArray, $expectedCount = 0) {
                return $this->hasNodes($nodeArray) and (count($nodeArray) == $expectedCount);
            },
            'render'        => function (View $view, $expected) {
                $content = $view->render();
                return $this->stringEquals($content, $expected);
            },
            'compile' => function ($subject, $expected) {
                return $this->stringEquals($subject, $expected);
            }
        ];
    }

    /**
     * String equals value? If not output actual
     *
     * @param $subject
     * @param $value
     * @return bool
     */
    public function stringEquals($subject, $value)
    {
        if (!($equals = ($subject === $value))) {
            echo 'Actual: ' . "'{$subject}'" . PHP_EOL;
        }
        return $equals;
    }

    /**
     * Has nodes
     *
     * @param array $nodeArray
     * @return bool
     */
    public function hasNodes($nodeArray)
    {
        $hasNodes = true;
        if (is_array($nodeArray) or $nodeArray instanceof \IteratorAggregate) {
            foreach ($nodeArray as $node) {
                if (!($node instanceof NodeInterface)) {
                    $hasNodes = false;
                }
            }
        }
        return $hasNodes;
    }

} 