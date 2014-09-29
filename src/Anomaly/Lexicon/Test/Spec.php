<?php namespace Anomaly\Lexicon\Test;

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
            'haveValue' => function($subject, $value) {
                return in_array($subject, $value);
            },
        ];
    }

} 