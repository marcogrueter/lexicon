<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\View\ViewInterface;
use Anomaly\Lexicon\Lexicon;
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
        $container = $this->getFactory()->getContainer();

        /** @var LexiconInterface $lexicon */
        $lexicon = $container['anomaly.lexicon'];
        $lexicon->addNodeSetPath($this->getPath(), $nodeSet);

        return $this;
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

}