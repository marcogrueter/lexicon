<?php
use Anomaly\Lexicon\Node\IgnoreBlock;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/11/14
 * Time: 5:30 AM
 */

class IgnoreBlockTest extends LexiconTestCase
{

    public function testRendersTagWithoutParsingIt()
    {
        $ignoreBlock = new IgnoreBlock($this->lexicon);

        $result = null;

        foreach($ignoreBlock->getMatches('{{ ignore }}{{ tag }}{{ /ignore }}') as $match) {
            $result = $ignoreBlock->make($match)->compile();
            break;
        }

        $this->assertEquals('{{ tag }}', $result);
    }
}
 