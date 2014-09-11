<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Lexicon;
use Illuminate\View\View as BaseView;

class View extends BaseView
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
     * @param  string  $method
     * @param  array   $parameters
     * @return \Illuminate\View\View
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (starts_with($method, 'using'))
        {
            return $this->using(snake_case(substr($method, 4)), $parameters[0]);
        }

        return parent::__call($method, $parameters);
    }

}