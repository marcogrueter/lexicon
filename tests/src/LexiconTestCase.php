<?php


use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Node\Single;
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
     * @var \Anomaly\Lexicon\Contract\View\EngineInterface
     */
    protected $engine;

    /**
     * @var \Anomaly\Lexicon\Contract\View\CompilerInterface
     */
    protected $compiler;

    /**
     * @var \Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface
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

        $app = require __DIR__ . '/../resources/bootstrap/start.php';

        $this->lexicon            = $app['anomaly.lexicon'];
        $this->engine             = $app['anomaly.lexicon.engine'];
        $this->compiler           = $app['anomaly.lexicon.compiler'];
        $this->conditionalHandler = $app['anomaly.lexicon.conditional.handler'];
        $this->pluginHandler      = $app['anomaly.lexicon.plugin.handler'];
        $this->view               = $app['anomaly.lexicon.factory'];
        $this->files              = $app['files'];

        $testingNodeSet = $this->lexicon->getNodeSet(Lexicon::DEFAULT_NODE_SET);

        array_unshift($testingNodeSet, 'AnomalyLexiconUndefinedNode');

        $this->lexicon->registerNodeSet($testingNodeSet, 'testing');

        $this->view->addNamespace('test', __DIR__ . '/../resources/views');

        return $app;
    }

    public function getTestsPath($path)
    {
        return __DIR__ . '/../' . $path;
    }

}

class AnomalyLexiconUndefinedNode extends Single
{
    protected $name = 'undefined';

    public function compile()
    {
        return 'echo $testingUndefinedVariableToCauseViewException;';
    }
}