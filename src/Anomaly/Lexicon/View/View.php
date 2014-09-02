<?php namespace Anomaly\Lexicon\View;

class View
{
    /**
     * Data
     *
     * @var array
     */
    protected $data;

    /**
     * View construct
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * View Factory
     *
     * @return Factory
     */
    public function view()
    {
        return $this->data['__env'];
    }

    /**
     * Render
     *
     * @return void
     */
    public function render()
    {
    }
}