<?php namespace Anomaly\Lexicon\Test\Plugin;

use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class PluginHandlerTest
 *
 * @package Anomaly\Lexicon\Test\Plugin
 */
class PluginHandlerTest extends LexiconTestCase
{

    /**
     * The plugin will only be called if there is a (.) scope
     */
    public function testGetInstanceOfPluginByName()
    {
        $this->assertInstanceOf('Anomaly\Lexicon\Test\Plugin\TestPlugin', $this->pluginHandler->get('test.hello'));
    }

    /**
     * We should get null here because there is no (.) scope
     */
    public function testGetPluginWithoutScopeReturnsNull()
    {
        $this->assertNull($this->pluginHandler->get('test'));
    }

    /**
     * Assert that the test.hello plugin method returns `Hello World!`
     */
    public function testPluginMethodCallReturnsHelloWorld()
    {
        $this->assertEquals('Hello World!', $this->pluginHandler->call('test.hello'));
    }

    /**
     * Assert that a non-existent plugin call returns null
     */
    public function testNonExistentPluginMethodCallReturnsNull()
    {
        $this->assertNull($this->pluginHandler->call('nonexistent.hello'));
    }

    /**
     * Assert that the md5 plugin method IS a filter and returns true
     */
    public function testPluginMethodIsAFilter()
    {
        $this->assertTrue($this->pluginHandler->isFilter('test.md5'));
    }

    /**
     * Assert that the lowercase plugin method IS NOT a filter and returns false
     */
    public function testPluginMethodIsNotAFilter()
    {
        $this->assertFalse($this->pluginHandler->isFilter('test.lowercase'));
    }

    /**
     * Assert that the lowercase IS a parse-able method and returns true
     */
    public function testPluginMethodIsParseAble()
    {
        $this->assertTrue($this->pluginHandler->isParse('test.lowercase'));
    }

    /**
     * Assert that the lowercase IS NOT a parse-able method / returns false
     */
    public function testPluginMethodIsNotParseAble()
    {
        $this->assertFalse($this->pluginHandler->isParse('test.md5'));
    }

    /**
     * Assert that non-existent plugin method is not a filter and returns false
     */
    public function testNonExistentPluginMethodIsNotAFilter()
    {
        $this->assertFalse($this->pluginHandler->isFilter('nonexistent.md5'));
    }

    /**
     * Assert that non-existent plugin method is not parse-able and returns false
     */
    public function testNonExistentPluginMethodIsNotParseAble()
    {
        $this->assertFalse($this->pluginHandler->isParse('nonexistent.md5'));
    }

    /**
     * Assert that the filter returns the expected md5 string
     */
    public function testFilterPluginMethodReturnsExpectedMd5String()
    {
        $this->assertEquals('ed076287532e86365e841e92bfc50d8c', $this->pluginHandler->filter('test.md5'));
    }

    /**
     * Assert that a non existent plugin filter returns null
     */
    public function testNonExistentPluginFilterReturnsNull()
    {
        $this->assertNull($this->pluginHandler->filter('nonexistent.md5'));
    }

}
 