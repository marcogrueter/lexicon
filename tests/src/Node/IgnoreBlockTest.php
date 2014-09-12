<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\IgnoreBlock;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class IgnoreBlockTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class IgnoreBlockTest extends LexiconTestCase
{

    public function testRendersTagWithoutParsingIt()
    {
        $template = '{{ ignore }}{{ tag }}{{ /ignore }}';

        $expected = '{{ tag }}';

        $result = $this->compileNode(new IgnoreBlock($this->lexicon), $parent = null, $template);

        $this->assertEquals($expected, $result);
    }
}
 