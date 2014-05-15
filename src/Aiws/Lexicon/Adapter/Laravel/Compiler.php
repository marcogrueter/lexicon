<?php namespace Aiws\Lexicon\Adapter\Laravel;

use Closure;
use Illuminate\View\Compilers\BladeCompiler;
use Aiws\Lexicon\Lexicon;

class Compiler extends BladeCompiler
{
    protected $view;

    protected $parserCachePath;

    /**
     * All of the available compiler functions.
     *
     * @var array
     */
    protected $compilers = array(
        'Extensions',
        'Extends',
        //'Comments',
        //'Echos',
        //'Openings',
        //'Closings',
        //'Else',
        //'Unless',
        //'EndUnless',
        //'Includes',
        //'Each',
        //'Yields',
        //'Shows',
        //'Language',
        //'SectionStart',
        //'SectionStop',
        //'SectionAppend',
        //'SectionOverwrite',
    );

	/**
	 * All of the plugins that have been registered.
	 *
	 * @var array
	 */
	static public $plugins = array();

    public function setView($view)
    {
        $this->view = $view;
    }

    public function getData()
    {
        return $this->view->getData();
    }

    public function boot($parserCachePath)
    {
		$this->extend(function($content) use ($parserCachePath) {

            $lexicon = new Lexicon($parserCachePath, function($name, $parameters, $content, $node) {
                $callbackHandler = new $node->callbackHandlerClass;
                return $callbackHandler->call($name, $parameters, $content);
            });

            return $lexicon->compile($content, $this->getData());

		});

	    return $this;
    }

	/**
	 * Register a template plugin.
	 *
	 * @param  string   $name
	 * @param  Closure  $plugin
	 * @return void
	 */
	public static function plugin($name, Closure $plugin)
	{
		static::$plugins[$name] = $plugin;
	}

	/**
	 * Parse the content for template tags.
	 *
	 * @return string
	 */
	public static function parse($content)
	{

		//print_r(static::$viewDataParser->getData()); exit;

		if(count(static::$plugins) == 0)
		{
			// The regular expression will match all Blade tags if there are
			// no plugins. To prevent this from happening, parsing will be
			// forced to end here.
			return $content;
		}

		$names = array();

		foreach(static::$plugins as $name => $plugin)
		{
			$names[] = preg_quote($name, '/');
		}

		$regexp = '/\{\{('.implode('|', $names).')(.*?)\}\}/u';

		return  preg_replace_callback($regexp, function($match)
		{
			list(, $name, $params) = $match;

			if( ! empty($params))
			{
				// The tag's parameters need to be converted into a PHP array.
				// Single quotes will need to be backslashed to prevent them
				// from accidentally escaping out.
				$params = addcslashes($params, '\'');
				$params = preg_replace('/ (.*?)="(.*?)"/', '\'$1\'=>\'$2\',', $params);
				$params = substr($params, 0, -1);
			}

			return '<?php echo '.get_called_class().'::call(\''.$name.'\', array('.$params.')); ?>';
		}, $content);
	}



	/**
	 * Call a template plugin.
	 *
	 * @param  string  $name
	 * @param  array   $params
	 * @return mixed
	 */
	public static function call($name, $params = array())
	{
		$plugin = static::$plugins[$name];

		return $plugin($params);
	}

}