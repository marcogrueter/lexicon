<?php namespace Anomaly\Lexicon\Test\Plugin;

use Anomaly\Lexicon\Contract\Plugin\PluginInterface;
use Anomaly\Lexicon\Test\LexiconTestCase;

class PluginTest extends LexiconTestCase
{

    /**
     * @var PluginInterface
     */
    protected $plugin;

    /**
     * Set up plugin
     */
    public function setUpTest()
    {
        $this->plugin = new TestPlugin();
    }

    /**
     * Test get named attribute
     */
    public function testGetNamedAttribute()
    {
        $this->plugin->setAttributes([
                'foo' => 'FOO',
            ]);

        $this->assertEquals('FOO', $this->plugin->getAttribute('foo'));
    }

    /**
     * Test get ordered attribute
     */
    public function testGetOrderedAttribute()
    {
        $this->plugin->setAttributes([
                'FOO',
                'BAR'
            ]);

        $this->assertEquals('BAR', $this->plugin->getAttribute('bar', 1));
    }

    /**
     * Test get ordered attribute
     */
    public function testGetDefaultIfAttributeDoesNotExists()
    {
        $this->assertEquals('DEFAULT', $this->plugin->getAttribute('nonexistent', 0, 'DEFAULT'));
    }

    /**
     * Assert that the md5 plugin method IS a filter and returns true
     */
    public function testPluginMethodIsAFilter()
    {
        $this->assertTrue($this->plugin->isFilter('md5'));
    }

    /**
     * Assert that the lowercase plugin method IS NOT a filter and returns false
     */
    public function testPluginMethodIsNotAFilter()
    {
        $this->assertFalse($this->plugin->isFilter('lowercase'));
    }

    /**
     * Assert that the lowercase IS a parse-able method and returns true
     */
    public function testPluginMethodIsParseAble()
    {
        $this->assertTrue($this->plugin->isParse('lowercase'));
    }

    /**
     * Assert that the lowercase IS NOT a parse-able method / returns false
     */
    public function testPluginMethodIsNotParseAble()
    {
        $this->assertFalse($this->plugin->isParse('md5'));
    }

    /**
     * Assert that the plugin filter returns the expected md5 string
     */
    public function testFilterPluginMethodReturnsExpectedMd5String()
    {
        $this->assertEquals('ed076287532e86365e841e92bfc50d8c', $this->plugin->__call('md5'));
    }

    /**
     * Assert that the plugin filter returns the expected lowercase string
     */
    public function testFilterPluginMethodReturnsExpectedLowercaseString()
    {
        $this->assertEquals('hello world!', $this->plugin->__call('lowercase'));
    }
}
 