<?php namespace Aiws\Lexicon\Adapter\Laravel;

use Illuminate\View\Engines\CompilerEngine as BaseCompilerEngine;

class CompilerEngine extends BaseCompilerEngine
{
    /**
     * Refresh
     *
     * @var bool
     */
    protected $refresh = true;

    /**
     * Set refresh property
     *
     * @param bool $refresh
     * @return $this
     */
    public function refresh($refresh = true)
	{
		$this->refresh = $refresh;

		return $this;
	}

	/**
	 * Get the evaluated contents of the view.
	 *
	 * @param  string  $path
	 * @param  array   $data
	 * @return string
	 */
	public function get($path, array $data = array())
	{
		if ($this->refresh)
		{
			$this->compiler->compile($path);
		}

		return parent::get($path, $data);
	}
}