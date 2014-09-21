<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Contract\View\FactoryInterface;
use Anomaly\Lexicon\Lexicon;
use Illuminate\View\Factory as BaseFactory;

/**
 * Class Factory
 *
 * @package Anomaly\Lexicon\View
 */
class Factory extends BaseFactory implements FactoryInterface
{
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return View
     */
    public function parse($view, $data = [], $mergeData = [])
    {
        $this->getLexicon()->addParsePath($view);

        return $this->newView($this->getLexiconEngine(), md5($view), $view, $data, $mergeData);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return View
     */
    public function make($view, $data = [], $mergeData = [])
    {
        if (isset($this->aliases[$view])) {
            $view = $this->aliases[$view];
        }

        $path = $this->finder->find($view);

        return $this->newView($this->getEngineFromPath($path), $view, $path, $data, $mergeData);
    }

    /**
     * New engine
     *
     * @param $engine
     * @param $view
     * @param $path
     * @param $data
     * @return View
     */
    public function newView($engine, $view, $path, $data = [], $mergeData = [])
    {
        $data = array_merge($mergeData, $this->parseData($data));

        $this->callCreator($view = new View($this, $engine, $view, $path, $data));

        return $view;
    }

    /**
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->container['anomaly.lexicon'];
    }

    /**
     * EngineInterface
     *
     * @return mixed
     */
    public function getLexiconEngine()
    {
        return $this->engines->resolve('anomaly.lexicon');
    }

    /**
     * Compare in conditional expression
     *
     * @codeCoverageIgnore
     * @param      $left
     * @param      $right
     * @param null $operator
     * @return bool
     */
    public function compare($left, $right, $operator = null)
    {
        return $this->getLexicon()->getConditionalHandler()->compare($left, $right, $operator);
    }

    /**
     * Takes a dot-notated key and finds the value for it in the given
     * array or object.
     *
     * @param        $data
     * @param        $key
     * @param array  $attributes
     * @param string $content
     * @param null   $default
     * @param string $expected
     * @return mixed|null
     */
    public function variable(
        $data,
        $key,
        array $attributes = [],
        $content = '',
        $default = null,
        $expected = Lexicon::EXPECTED_ANY
    ) {
        $parts = explode($this->getLexicon()->getScopeGlue(), $key);

        /** @var PluginHandlerInterface $handler */
        $handler = $this->getLexicon()->getPluginHandler();

        // Get a plugin
        if ($plugin = $handler->get($key)) {

            array_shift($parts); // Shit the name
            $method = array_shift($parts); // Shift the method

            // Get the plugin data if found
            $data = $handler->call($plugin, $method, $attributes, $content);
        }

        // not is null
        if (!is_null($data)) {

            while (count($parts) > 0) {

                $nextPart = array_shift($parts);

                // If the last part is .size, return the offset
                if (empty($parts) and $nextPart == 'size' and
                    (is_array($data) or $data instanceof \Countable or is_string($data))
                ) {

                    // Return string length or array offset
                    if (is_string($data)) {

                        return $this->expected(strlen($data), $expected, $default);

                    } elseif (!array_key_exists('size', $data)) {

                        return $this->expected(count($data), $expected, $default);

                    }

                } elseif (is_array($data)) {

                    if (!array_key_exists($nextPart, $data)) {

                        return $this->expected(null, $expected, $default);

                    }

                    $data = $this->expected($data[$nextPart], $expected, $default);

                } elseif (is_object($data)) {

                    if (method_exists($data, $nextPart)) {

                        try {

                            $data = call_user_func_array([$data, $nextPart], $attributes);

                        } catch (\Exception $e) {

                            // log exception

                            return $this->expected(null, $expected, $default);

                        }

                    } elseif ($data instanceof \ArrayAccess) {

                        if (!isset($data[$nextPart])) {

                            return $this->expected(null, $expected, $default);

                        }

                        $data = $data[$nextPart];

                    } else {

                        if (!property_exists($data, $nextPart)) {

                            return $this->expected(null, $expected, $default);

                        }

                        $data = $data->{$nextPart};
                    }
                }

            }

            return $this->expected($data, $expected, $default);

        } else {

            return $this->expected(null, $expected, $default);

        }
    }

    /**
     * Return expected data type as a fallback to wrong data type
     *
     * @param        $data
     * @param string $expected
     * @param null   $finalResult
     * @return array|bool|float|int|null|string|\Traversable
     */
    public function expected($data, $expected = Lexicon::EXPECTED_ANY, $finalResult = null)
    {
        if ($expected == Lexicon::EXPECTED_ANY) {

            $finalResult = $data;

        } elseif ($expected == Lexicon::EXPECTED_ECHO) {

            if (
                is_string($data) or
                is_float($data) or
                is_numeric($data) or
                is_bool($data) or
                is_null($data) or
                (
                    is_object($data) and
                    method_exists($data, '__toString')
                )
            ) {

                $finalResult = $data;

            }

        } elseif ($expected == Lexicon::EXPECTED_TRAVERSABLE) {

            if (is_array($data) or $data instanceof \Traversable) {

                $finalResult = $data;

            };

        }

        return $finalResult;
    }

}
