<?php namespace Aiws\Lexicon\Base;

class Cache
{

    protected $path;

    public function __construct($path = './lexicon/')
    {
        $this->path = $path;
    }

    /**
     * Render the compiled php view
     *
     * @param  string $path
     * @param  array  $data
     * @return string
     */
    public function render($path, $data)
    {
        if (!file_exists($path)) {
            return null;
        }

        ob_start();

        extract($path);

        try {
            include $path;
        } catch (\Exception $e) {
            $this->exception($e);
        }

        return ltrim(ob_get_clean());
    }

    /**
     * Handle the exception.
     *
     * @param  \Exception $e
     * @return void
     * @throws $e
     */
    protected function exception($e)
    {
        ob_get_clean();
        throw $e;
    }

    public function get($content, $data, $namespace)
    {
        return $this->render($this->path($content, $namespace), $data);
    }

    public function put($content, $namespace = null, $php = null)
    {
        if (!is_dir($this->path)) {
            mkdir($this->path, 0777);
        }

        return file_put_contents($this->path($content, $namespace), $php);
    }

    public function flush($namespace = null)
    {
        $path = $this->path . $namespace;

        return array_map('unlink', glob("$path*"));
    }

    public function path($content, $namespace = null)
    {
        return $this->path . $namespace . md5($content);
    }

}