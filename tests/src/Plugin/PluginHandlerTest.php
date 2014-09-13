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
     * Assert that a non-existent plugin call returns null
     */
    public function testNonExistentPluginReturnsNull()
    {
        $this->assertNull($this->pluginHandler->get('nonexistent.hello'));
    }

    /**
     * Assert that the test.hello plugin method returns `Hello World!`
     */
    public function testPluginMethodCallReturnsHelloWorld()
    {
        $plugin = $this->pluginHandler->get('test.hello');

        $this->assertEquals('Hello World!', $this->pluginHandler->call($plugin, 'hello'));
    }

}
 