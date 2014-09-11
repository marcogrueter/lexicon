<?php

class TestPlugin extends Anomaly\Lexicon\Plugin\Plugin
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
        return strtolower('Hello World!');
    }
}