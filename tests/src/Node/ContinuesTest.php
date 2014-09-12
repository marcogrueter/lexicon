<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\Block;
use Anomaly\Lexicon\Node\Continues;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class ContinuesTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class ContinuesTest extends LexiconTestCase
{

    /**
     * Test that Continue only compiles the source if it has a parent node that is not root
     */
    public function testCompilesSourceOnlyIfHasParent()
    {
        $template = '{{ posts }}{{ continue }}{{ /posts }}';

        $root = $this->makeBlockNode($template);

        $parent = $this->parseAndMakeNode(new Block($this->lexicon), $root, $root->getContent());

        $result = $this->compileNode(new Continues($this->lexicon), $parent, $parent->getContent());

        $this->assertEquals('continue;', $result);
    }

    /**
     * Test that Continue only compiles the source if it has a parent node that is not root
     */
    public function testCompilesNullIfParentIsRoot()
    {
        $parent = $this->parseAndMakeNode(new Block($this->lexicon));

        $result = $this->compileNode(new Continues($this->lexicon), $parent, $parent->getContent());

        $this->assertNull($result);
    }

    /**
     * Test that compiles null if it does noy have a parent
     */
    public function testCompilesNullIfDoesNotHaveParent()
    {
        $result = $this->compileNode(new Continues($this->lexicon), $parent = null, '{{ continue }}');

        $this->assertNull($result);
    }
}
 