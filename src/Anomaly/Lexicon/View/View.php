<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\View\ViewInterface;
use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Node\NodeFactory;
use Anomaly\Lexicon\Stub\LexiconStub;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Engines\EngineInterface;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\FileViewFinder;
use Illuminate\View\View as BaseView;

class View extends BaseView implements ViewInterface
{
    /**
     * Using node set
     *
     * @param string $nodeGroup
     * @return View
     */
    public function using($nodeGroup = NodeFactory::DEFAULT_NODE_GROUP)
    {
        $this->getLexicon()->getFoundation()->getNodeFactory()->addNodeGroupPath($this->getPath(), $nodeGroup);
        return $this;
    }

    /**
     * Get Lexicon
     *
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->getFactory()->getContainer()->make('anomaly.lexicon');
    }

    /**
     * Dynamically bind parameters to the view.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return View
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (starts_with($method, 'using')) {
            return $this->using(snake_case(substr($method, 5)));
        }

        return parent::__call($method, $parameters);
    }

    /**
     * View stub for PHPSpec unit test at spec\Anomaly\Lexicon\View\ViewSpec
     *
     * @return Engine
     */
    public static function stub()
    {
        $data = [
            'name' => 'Mr. Anderson'
        ];

        return LexiconStub::factory()->make('test::hello', $data);
    }

    /*public function render()
    {
        $render = parent::render();
        echo $render;
        return $render;
    }*/

}