<?php namespace Anomaly\Lexicon\Test\View;

use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class CompilerTest
 *
 * @package Anomaly\Lexicon\Test\View
 */
class CompilerTest extends LexiconTestCase
{

    /**
     * Test a view is not parsed
     */
    public function testAViewIsNotParsed()
    {
        $this->lexicon->setDebug(false);

        $hash = md5('nonexistentview');

        $this->assertTrue($this->compiler->isNotParsed($this->getTestsPath('/resources/storage/views/' . $hash)));
    }

    /**
     *
     */
    public function testFooterGetsCompiled()
    {
        $expected = 'I should have a footer!
<footer>Footer</footer>';

        $rootNode = $this->compiler->getRootNode('I should have a footer!');

        $rootNode->addToFooter('<footer>Footer</footer>');

        $result = $this->compiler->compileRootNode($rootNode);

        $this->assertEquals($expected, $result);
    }

}
 