<?php namespace Aiws\Lexicon\Util;

use Aiws\Lexicon\Contract\EnvironmentInterface;

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

        $scopes = $pluginScopes = explode($this->scopeGlue, $key);

        $pluginKey = $key;

        if ($this->lexicon->getPlugin($pluginKey)) {

            $plugin = array_shift($scopes);
            $method = array_shift($scopes);

            if (count($scopes) > 2) {
                $pluginKey = $plugin . $this->scopeGlue . $method;
            }

            $data = $this->lexicon->call($pluginKey, $attributes, $content);

            $reflection->setData($data);
        }

        $previousScope = null;
        $invalidScope = null;

        while (count($scopes) > 0) {

            $scope = array_shift($scopes);

            if ($reflection->hasMethod($scope)) {
                try {
                    $data = call_user_func_array([$data, $scope], $attributes);
                } catch (\InvalidArgumentException $e) {
                    echo "There is a problem with the <b>{$key}</b> variable. One of the attributes maybe incorrect.";
                    // @todo - log exception
                    // @todo - fire exception event
                } catch (\ErrorException $e) {
                    echo "There is a problem with the <b>{$key}</b> variable. One of the attributes maybe incorrect.";
                    // @todo - log exception
                    // @todo - fire exception event
                } catch (\Exception $e) {
                    echo "There is a problem with the <b>{$key}</b> variable.";
                    // @todo - log exception
                    // @todo - fire exception event
                }
            } elseif ($reflection->hasArrayKey($scope)) {
                $data = $data[$scope];
            } elseif ($reflection->hasObjectKey($scope)) {
                $data = $data->{$scope};
            } elseif (empty($scopes) and
                $scope == 'size' and
                (!$invalidScope or $previousScope != $invalidScope) and
                (is_array($data) or $data instanceof \Countable)
            ) {
                $data = count($data);
            } elseif (empty($scopes) and
                $scope == 'length' and
                (!$invalidScope or $previousScope != $invalidScope) and
                is_string($data)) {
                $data = strlen($data);
            } elseif (empty($scopes) or $this->getData() == $data) {
                $data         = $default;
            } else {
                $invalidScope = $scope;
            }

            $previousScope = $scope;

            $reflection->setData($data);
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

    /**
     * New reflection
     *
     * @param $data
     * @return Reflection
     */
    public function newReflection($data)
    {
        return new Reflection($data);
    }

}