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
                if (!($doesHave = ($content == $expected))) {
                    echo 'Result: ' . "'{$content}'".PHP_EOL;
                }
                return $doesHave;
            },
        ];
    }

    /**
     * Has nodes
     *
     * @param array $nodeArray
     * @return bool
     */
    public function hasNodes(array $nodeArray)
    {
        $hasNodes = true;
        if (is_array($nodeArray)) {
            foreach ($nodeArray as $node) {
                if (!($node instanceof NodeInterface)) {
                    $hasNodes = false;
                }
            }
        }
        return $hasNodes;
    }

} 