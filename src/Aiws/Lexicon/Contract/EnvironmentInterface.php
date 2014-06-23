<?php namespace Aiws\Lexicon\Contract;

use Aiws\Lexicon\Util\Regex;

interface EnvironmentInterface
{
    /**
     * @return PluginHandlerInterface
     */
    public function getPluginHandler();

    /**
     * @return mixed
     */
    public function getConditionHandler();

    /**
     * @return string
     */
    public function getScopeGlue();

    /**
     * @return int
     */
    public function getMaxDepth();

    /**
     * @return array
     */
    public function getNodeTypes();

    /**
     * @return array
     */
    public function getIgnoredMatchers();

    /**
     * @return Regex
     */
    public function getRegex();

    /**
     * @param        $name
     * @param array  $attributes
     * @param string $content
     * @return mixed
     */
    public function call($name, $attributes = [], $content = '');
}