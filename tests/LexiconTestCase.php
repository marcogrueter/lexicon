<?php


use Illuminate\Foundation\Testing\TestCase;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/6/14
 * Time: 7:45 PM
 */
class LexiconTestCase extends TestCase
{

    /**
     * @var \Anomaly\Lexicon\Contract\LexiconInterface
     */
    protected $lexicon;

    /**
     * @var \Illuminate\View\Factory
     */
    protected $view;

    /**
     * @var \Anomaly\Lexicon\CompilerEngineInterface
     */
    protected $engine;

    /**
     * @var \Anomaly\Lexicon\Contract\CompilerInterface
     */
    protected $compiler;

    /**
     * @var \Anomaly\Lexicon\Contract\ConditionalHandlerInterface
     */
    protected $conditionalHandler;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

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

        $this->lexicon            = $app['anomaly.lexicon'];
        $this->engine             = $app['anomaly.lexicon.engine'];
        $this->compiler           = $app['anomaly.lexicon.compiler'];
        $this->conditionalHandler = $app['anomaly.lexicon.conditional.handler'];
        $this->pluginHandler      = $app['anomaly.lexicon.plugin.handler'];
        $this->view               = $app['anomaly.lexicon.factory'];
        $this->files              = $app['files'];

        $this->view->addNamespace('test', __DIR__ . '/resources/views');

        return $app;
    }

}