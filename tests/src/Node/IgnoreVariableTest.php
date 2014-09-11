<?php
use Anomaly\Lexicon\Node\IgnoreVariable;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/11/14
 * Time: 5:51 AM
 */

class SingleRemainTest extends LexiconTestCase
{

    public function testRendersTagWithoutParsingIt()
    {
        $ignoreVariable = new IgnoreVariable($this->lexicon);

        $result = null;

        foreach($ignoreVariable->getMatches('@{{ unprocessed }}') as $match) {
            $result = $ignoreVariable->make($match)->compile();
            break;
        }

        $this->assertEquals('{{ unprocessed }}', $result);
    }
}
 