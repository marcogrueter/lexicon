<?php namespace Anomaly\Lexicon\Plugin;


class CounterPlugin extends Plugin
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
        $id = $this->getAttribute('id', 'default');

        // Use a default of 1 if they haven't specified one and it's the first iteration
        if (!isset($this->ids[$id])) {
            $this->ids[$id] = (int)$this->getAttribute('start', 1, 1);
        } // lets check to see if they're only wanting to show the offset
        elseif ($this->increment) {
            $skip = (int)$this->getAttribute('skip', 1, 2);
            // offset up unless they specify to "subtract"
            $this->ids[$id] = ($this->getAttribute('mode', null, 3) == 'subtract') ?
                $this->ids[$id] - $skip : $this->ids[$id] + $skip;
        }

        // set this back to continue counting again next time
        $this->increment = true;

        return ($this->getAttribute('return', true, 4)) ? $this->ids[$id] : null;
    }

    /**
     * Show the offset
     *
     * @return null
     */
    public function show()
    {
        $this->increment = false;

        $id = $this->getAttribute('id', 'default');

        return isset($this->ids[$id]) ? $this->ids[$id] : null;
    }

}