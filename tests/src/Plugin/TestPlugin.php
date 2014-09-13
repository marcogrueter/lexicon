<?php namespace Anomaly\Lexicon\Test\Plugin;

use Anomaly\Lexicon\Plugin\Plugin;


/**
 * Class TestPlugin
 *
 * @package Anomaly\Lexicon\Test\Plugin
 */
class TestPlugin extends Plugin
{
    /**
     * Hello method
     *
     * @return string
     */
    public function hello()
    {
        return 'Hello World!';
    }

    /**
     * Md5 filter method
     *
     * @return string
     */
    public function filterMd5()
    {
        return md5('Hello World!');
    }

    /**
     * Parse able lowercase method
     *
     * @return string
     */
    public function parseLowercase()
    {
        return strtolower('HELLO WORLD!');
    }

}