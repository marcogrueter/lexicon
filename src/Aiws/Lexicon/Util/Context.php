<?php namespace Aiws\Lexicon\Util;

use Aiws\Lexicon\Contract\EnvironmentInterface;
use Aiws\Lexicon\Contract\PluginInterface;

class Context
{
    protected $data;

    protected $lexicon;

    protected $scopeGlue;

    public function __construct(EnvironmentInterface $lexicon, $data)
    {
        $this->lexicon   = $lexicon;
        $this->scopeGlue = $lexicon->getScopeGlue();
        $this->data      = $data;
    }

    /**
     * Takes a dot-notated key and finds the value for it in the given
     * array or object.
     *
     * @param  string       $key     Dot-notated key to find
     * @param  array|object $data    Array or object to search
     * @param  mixed        $default Default value to use if not found
     * @return mixed
     */
    public function getVariable($key, array $attributes = [], $content = '', $default = null, $expected = Type::ANY)
    {
        $data       = $this->getData();
        $reflection = $this->newReflection($data);

        $parts = explode($this->scopeGlue, $key);

        $pluginKey = $key;

        if ($this->lexicon->getPlugin($pluginKey)) {

            if (count($parts) > 2) {
                $plugin    = array_shift($parts);
                $method    = array_shift($parts);
                $pluginKey = $plugin . $this->scopeGlue . $method;
            }

            $data = $this->lexicon->call($pluginKey, $attributes, $content);

            $reflection = $this->newReflection($data);
        }

        $count = 0;

        foreach ($parts as $part) {

            if ($reflection->hasMethod($part)) {
                try {
                    $data = call_user_func_array([$data, $part], $attributes);
                } catch(\InvalidArgumentException $e) {
                    echo "There is a problem with the <b>{$key}</b> variable. One of the attributes maybe incorrect.";
                    // @todo - log exception
                    // @todo - fire exception event
                } catch(\ErrorException $e) {
                    echo "There is a problem with the <b>{$key}</b> variable. One of the attributes maybe incorrect.";
                    // @todo - log exception
                    // @todo - fire exception event
                } catch(\Exception $e) {
                    echo "There is a problem with the <b>{$key}</b> variable.";
                    // @todo - log exception
                    // @todo - fire exception event
                }
            } elseif ($reflection->hasArrayKey($part)) {
                $data = $data[$part];
            } elseif ($reflection->hasObjectKey($part)) {
                $data = $data->{$part};
            } elseif (count($parts) == $count or $this->getData() == $data) {
                $data = $default;
            }

            $reflection = $this->newReflection($data);

            $count++;
        }

        if ($expected == Type::ANY) {
            return $data;
        } elseif ($expected == Type::ECHOABLE and $reflection->isEchoable()) {
            return $data;
        } elseif ($expected == Type::ITERATEABLE and $reflection->isIteratable()) {
            return $data;
        }

        return $default;
    }

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function newReflection($data)
    {
        return new Reflection($data);
    }

}