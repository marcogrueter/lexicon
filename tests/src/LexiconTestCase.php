<?php namespace Anomaly\Lexicon\Test;

use Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Contract\View\CompilerInterface;
use Anomaly\Lexicon\Contract\View\EngineInterface;
use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Node\Block;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\View\Factory;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class LexiconTestCase
 *
 * @package Anomaly\Lexicon\Test
 */
class LexiconTestCase extends TestCase
{

    /**
     * @var LexiconInterface
     */
    protected $lexicon;

    /**
     * @var Factory
     */
    protected $view;

    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @var CompilerInterface
     */
    protected $compiler;

    /**
     * @var ConditionalHandlerInterface
     */
    protected $conditionalHandler;

    /**
     * @var PluginHandlerInterface
     */
    protected $pluginHandler;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var Block
     */
    protected $blockNode;

    /**
     * @var \Anomaly\Lexicon\Node\Comment
     */
    protected $commentNode;

    /**
     * Creates the application.
     *
     * @return HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        $app = require $this->getTestsPath('resources/bootstrap/start.php');

        $this->lexicon            = $app['anomaly.lexicon'];
        $this->engine             = $app['anomaly.lexicon.engine'];
        $this->compiler           = $app['anomaly.lexicon.compiler'];
        $this->conditionalHandler = $app['anomaly.lexicon.conditional.handler'];
        $this->pluginHandler      = $app['anomaly.lexicon.plugin.handler'];
        $this->view               = $app['anomaly.lexicon.factory'];
        $this->files              = $app['files'];

        $testingNodeSet = $this->lexicon->getNodeSet(Lexicon::DEFAULT_NODE_SET);

        array_unshift($testingNodeSet, 'Anomaly\Lexicon\Test\Node\Undefined');

        $this->lexicon->registerNodeSet($testingNodeSet, 'testing');

        $this->view->addNamespace('test', $this->getTestsPath('resources/views'));

        // Register the test plugin
        $this->pluginHandler->register('test', 'Anomaly\Lexicon\Test\Plugin\TestPlugin');

        return $app;
    }

    public function setUpNodeTypes()
    {

    }

    public function parseAndMakeNode(NodeInterface $node, NodeInterface $parent = null, $template = '')
    {
        $matches = $node->getMatches($template);

        if (!empty($matches)) {
            $node = $node->make($matches[0], $parent);
        }

        return $node;
    }

    public function compileNode(NodeInterface $node, NodeInterface $parent = null, $template = '', $default = null)
    {
        $node = $this->parseAndMakeNode($node, $parent, $template);
        return $node->isValid() ? $node->compile() : $default;
    }

    public function makeBlockNode($template = '', NodeInterface $parent = null)
    {
        return (new Block($this->lexicon))->make(
            [
                'name'    => 'root',
                'content' => $template,
            ],
            $parent
        );
    }

    public function getTestsPath($path)
    {
        return __DIR__ . '/../' . $path;
    }

}