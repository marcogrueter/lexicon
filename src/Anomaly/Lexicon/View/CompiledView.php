<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Contract\View\CompiledViewInterface;
use Anomaly\Lexicon\Lexicon;

/**
 * Class CompiledView
 *
 * @package Anomaly\Lexicon\View
 */
class CompiledView implements CompiledViewInterface
{

    /**
     * @var LexiconInterface
     */
    protected $lexicon;

    /**
     * @param LexiconInterface $lexicon
     */
    public function __construct(LexiconInterface $lexicon)
    {
        $this->lexicon = $lexicon;
    }

    /**
     * Render contents
     *
     * @param $_data
     */
    public function render($_data)
    {
    }

    /**
     * @return LexiconInterface
     */
    public function lexicon()
    {
        return $this->lexicon;
    }

    /**
     * Get view factory
     *
     * @return Factory
     */
    public function view()
    {
        return $this->lexicon()->getFoundation()->getFactory();
    }

    /**
     * Compare in conditional expression
     *
     * @param      $left
     * @param      $right
     * @param null $operator
     * @return bool
     */
    public function booleanTest($left, $right, $operator)
    {
        return $this->lexicon()->getFoundation()->getConditionalHandler()->booleanTest($left, $right, $operator);
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
        $parts = explode($this->lexicon()->getScopeGlue(), $key);

        /** @var PluginHandlerInterface $pluginHandler */
        $pluginHandler = $this->lexicon()->getFoundation()->getPluginHandler();

        // Get a plugin
        if ($plugin = $pluginHandler->get($key)) {

            array_shift($parts); // Shift the name
            $method = array_shift($parts); // Shift the method

            // Get the plugin data if found
            $data = $pluginHandler->call($plugin, $method, $attributes, $content);
        }

        // not is null
        if (!is_null($data)) {

            $nextPart = null;

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

                    $data = $data[$nextPart];

                } elseif (is_object($data)) {

                    if (method_exists($data, $nextPart)) {

                        try {

                            $object = call_user_func_array([$data, $nextPart], $attributes);

                            if ($this->lexicon()->isMagicMethodObject($object)) {

                                $data = $data->{$nextPart};

                            } else {

                                $data = $object;

                            }

                        } catch (\Exception $e) {

                            // log exception

                            return $this->expected(null, $expected, $default);

                        }

                    } elseif ($data instanceof \ArrayAccess) {

                        if (!$data->offsetExists($nextPart)) {

                            return $this->expected(null, $expected, $default);

                        }

                        $data = $data->offsetGet($nextPart);

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

        } elseif ($expected == Lexicon::EXPECTED_STRING) {

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