<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\View\ViewInterface;
use Anomaly\Lexicon\Lexicon;
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
     * @param string $nodeSet
     * @return View
     */
    public function using($nodeSet = Lexicon::DEFAULT_NODE_SET)
    {
        $this->getLexicon()->addNodeSetPath($this->getPath(), $nodeSet);
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

    public static function stub()
    {
        return \Anomaly\Lexicon\Stub\Lexicon::stub()->getFactory()->parse('');
    }

}