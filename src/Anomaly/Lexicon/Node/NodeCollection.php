<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Illuminate\Support\Collection;

/**
 * Class NodeCollection
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package Anomaly\Lexicon\Node
 */
class NodeCollection extends Collection
{

    /**
     * @param mixed $id
     * @return mixed|null
     */
    public function getById($id)
    {
        return $this->offsetExists($id) ? $this->get($id) : null;
    }

    /**
     * Get by multiple ids
     */
    public function getByIds(array $ids)
    {
        $nodes = [];
        foreach($ids as $id) {
            if ($node = $this->getById($id)) {
                $nodes[$id] = $node;
            }
        }
        return new static($nodes);
    }

}