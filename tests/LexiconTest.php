<?php

use Illuminate\Container\Container;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\FileViewFinder;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/6/14
 * Time: 7:45 PM
 */
class LexiconTest extends TestCase
{

    /**
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var \Anomaly\Lexicon\Lexicon
     */
    protected $lexicon;

    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        $app = require __DIR__ . '/resources/bootstrap/start.php';

        $this->lexicon = $app['anomaly.lexicon'];

        return $app;
    }

    public function testRegisterAndInstantiateOneNodeType()
    {
        $this->assertTrue(class_exists('Anomaly\Lexicon\Node\Variable'));
    }

    public function testGetBlockNodeType()
    {
        $this->assertInstanceOf('Anomaly\Lexicon\Contract\NodeBlockInterface', $this->lexicon->getBlockNodeType());
    }

    public function testAddTemplateAsParsePath()
    {
        $expected = '{{ hello }}';
        $this->lexicon->addParsePath($expected);
        $this->assertArrayHasKey($expected, $this->lexicon->getParsePaths());
        $this->assertTrue($this->lexicon->isParsePath($expected));
    }

    public function testGetViewTemplate()
    {
        $this->assertInternalType('string', $this->lexicon->getViewTemplate());
    }

    public function testGetFullViewClass()
    {
        $this->assertEquals('View_test', $this->lexicon->getViewClass('test'));
    }


}
 