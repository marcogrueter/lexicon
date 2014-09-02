<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\EnvironmentInterface;
use Anomaly\Lexicon\Contract\FactoryInterface;
use Anomaly\Lexicon\Value\Expected;
use Anomaly\Lexicon\Regex;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Factory as BaseFactory;

class Factory extends BaseFactory implements FactoryInterface
{
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View
     */
    public function parse($view, $data = [], $mergeData = [])
    {
        $this->getLexicon()->addParsePath($view);

        /** @var $engine CompilerEngine */
        $engine = $this->container['lexicon.compiler.engine'];

        $data = array_merge($mergeData, $this->parseData($data));

        $this->callCreator($view = new View($this, $engine, md5($view), $view, $data));

        return $view;
    }

    /**
     * @return EnvironmentInterface
     */
    public function getLexicon()
    {
        return $this->container['lexicon'];
    }

    /**
     * Append content to a given section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    protected function extendSection($section, $content)
    {
        $engine = $this->engines->resolve('lexicon');

        $lexicon = $engine->getCompiler()->getLexicon();

        $regex = new Regex($lexicon);

        $content = $regex->compress($content);

        if (isset($this->sections[$section]))
        {
            $content = str_replace('{{ parent }}', $content, $this->sections[$section]);

            $this->sections[$section] = $content;
        }
        else
        {
            $this->sections[$section] = $content;
        }
    }

    public function recursive($template, $data)
    {
        return $this->parse(str_replace('{{ recursive }}', $template, $template), $data);
    }

    /**
     * Compare in conditional expression
     *
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
     * @param  string       $key     Dot-notated key to find
     * @param  array|object $data    Array or object to search
     * @param  mixed        $default Default value to use if not found
     * @return mixed
     */
    public function variable($data, $key, array $attributes = [], $content = '', $default = null, $expected = Expected::ANY)
    {
        $scopes = $pluginScopes = explode($this->getLexicon()->getScopeGlue(), $key);

        $pluginKey = $key;

        $original = $data;

        if ($this->getLexicon()->getPluginHandler()->get($pluginKey)) {

            $plugin = array_shift($scopes);
            $method = array_shift($scopes);

            if (count($scopes) > 2) {
                $pluginKey = $plugin . $this->getLexicon()->getScopeGlue() . $method;
            }

            $data = $this->getLexicon()->getPluginHandler()->call($pluginKey, $attributes, $content);
        }

        $previousScope = null;
        $invalidScope  = null;

        while (count($scopes) > 0) {

            $scope = array_shift($scopes);

            if (is_object($data) and method_exists($data, $scope)) {
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
            } elseif ((is_array($data) or $data instanceof \ArrayAccess) and isset($data[$scope])) {
                $data = $data[$scope];
            } elseif (is_object($data) and isset($data->{$scope})) {
                $data = $data->{$scope};
            } elseif (empty($scopes) and
                $scope == 'count' and
                (!$invalidScope or $previousScope != $invalidScope) and
                (is_array($data) or $data instanceof \Countable or is_string($data))
            ) {
                if (is_string($data)) {
                    $data = strlen($data);
                } else {
                    $data = count($data);
                }
            } elseif (empty($scopes) or $data == $original) {
                $data = $default;
            } else {
                $invalidScope = $scope;
            }

            $previousScope = $scope;
        }

        if ($expected == Expected::ANY) {
            return $data;
        } elseif ($expected == Expected::ECHOABLE and
            (is_string($data) or is_numeric($data) or is_bool($data) or is_null($data) or is_float($data) or
                (is_object($data) and method_exists($data, '__toString')))
        ) {
            return $data;
        } elseif ($expected == Expected::TRAVERSABLE and is_array($data) or $data instanceof \Traversable) {
            return $data;
        }

        return $default;
    }
}
