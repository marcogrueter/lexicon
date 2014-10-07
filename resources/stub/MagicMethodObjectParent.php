<?php namespace Anomaly\Lexicon\Stub;

/**
 * Class MagicMethodObjectParent
 *
 * @package Anomaly\Lexicon\Stub
 */
class MagicMethodObjectParent extends ArrayAccessObject
{

    public function magic_method_object()
    {
        return new MagicMethodObject();
    }

    public function __get($property)
    {
        if ($property == 'magic_method_object') {
            return $this->magic_method_object()->getResults();
        }

        return null;
    }

} 