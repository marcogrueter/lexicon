<?php namespace Aiws\Lexicon\Plugin;


class Counter extends Plugin
{
    /**
     * Increment
     *
     * @var bool
     */
    protected $increment = true;

    /**
     * Ids
     *
     * @var array
     */
    protected $ids = [];

    /**
     * Plugin name
     *
     * @var string
     */
    public $name = 'counter';

    /**
     * Count
     *
     * @return null
     */
    public function count()
    {
        $id = $this->getAttribute('id', 0, 'default');

        // Use a default of 1 if they haven't specified one and it's the first iteration
        if (!isset($this->ids[$id])) {
            $this->ids[$id] = (int)$this->getAttribute('start', 1, 1);
        } // lets check to see if they're only wanting to show the count
        elseif ($this->increment) {
            $skip = (int)$this->getAttribute('skip', 2, 1);
            // count up unless they specify to "subtract"
            $this->ids[$id] = ($this->getAttribute('mode', 3) == 'subtract') ?
                $this->ids[$id] - $skip : $this->ids[$id] + $skip;
        }

        // set this back to continue counting again next time
        $this->increment = true;

        return ($this->getAttribute('return', 4, 'true') == 'true') ? $this->ids[$id] : null;
    }

    /**
     * Show the count
     *
     * @return null
     */
    public function show()
    {
        $this->increment = false;

        $id = $this->getAttribute('id', 0, 'default');

        return isset($this->ids[$id]) ? $this->ids[$id] : null;
    }

}