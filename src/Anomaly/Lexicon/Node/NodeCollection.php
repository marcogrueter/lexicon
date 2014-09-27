<?php namespace Anomaly\Lexicon\Node;

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
     * Get a node by id
     *
     * @param $id
     * @return mixed|null
     */
    public function getById($id)
    {
        return $this->first(
            function ($offset) use ($id) {
                return ($this->get($offset)->getId() === $id);
            }
        );
    }

    /**
     * Get by multiple ids
     */
    public function getByIds(array $ids)
    {
        return $this->filter(
            function ($node) use ($ids) {
                return in_array($node->getId(), $ids);
            }
        )->toArray();
    }

}