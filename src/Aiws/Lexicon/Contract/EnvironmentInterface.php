<?php namespace Aiws\Lexicon\Contract;

use Aiws\Lexicon\Util\Regex;
use Aiws\Lexicon\Util\Type;

interface EnvironmentInterface
{
    /**
     * @return PluginHandlerInterface
     */
    public function getPluginHandler();

    /**
     * @return mixed
     */
    public function getConditionalHandler();

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
     * @return string
     */
    public function getEnvironmentVariable();

    /**
     * @return Regex
     */
    public function getRegex();

    /**
     * @param        $name
     * @return PluginInterface
     */
    public function getPlugin($name);

    /**
     * @return string
     */
    public function getRootContextName();

    /**
     * @param        $name
     * @param array  $attributes
     * @param string $content
     * @return mixed
     */
    public function call($name, $attributes = [], $content = '');

    /**
     * @param        $data
     * @param        $key
     * @param array  $attributes
     * @param string $content
     * @return mixed
     */
    public function get($data, $key, array $attributes = [], $content = '', $default = null, $expected = Type::ANY);

}