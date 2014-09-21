<?php namespace Anomaly\Lexicon\Test\Example;


use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class AttributesTest
 *
 * @package Anomaly\Lexicon\Test\Example
 */
class AttributesTest extends LexiconTestCase
{

    /**
     * It can compile named attributes
     */
    public function test_it_can_compile_named_attributes()
    {
        $expected = '<?php echo $__data[\'__env\']->variable($__data, \'test\', [\'foo\' => \'FOO\', \'bar\' => \'BAR\'], \'\', null, \'echo\'); ?>';

        $result = $this->compiler->compileString('{{ test foo="FOO" bar="BAR" }}');

        $this->assertEquals($expected, $result);
    }

    /**
     * It can compile ordered attributes
     */
    public function test_it_can_compile_ordered_attributes()
    {
        $expected = '<?php echo $__data[\'__env\']->variable($__data, \'test\', [0 => \'FOO\', 1 => \'BAR\'], \'\', null, \'echo\'); ?>';

        $result = $this->compiler->compileString('{{ test "FOO" "BAR" }}');

        $this->assertEquals($expected, $result);
    }

    /**
     * It can compile ordered attributes
     */
    public function test_it_can_compile_variable_attributes()
    {
        $expected = '<?php echo $__data[\'__env\']->variable($__data, \'test\', [0 => \'FOO\', 1 => \'BAR\'], \'\', null, \'echo\'); ?>';

        $result = $this->compiler->compileString('{{ test {foo} {bar} }}');

        $this->assertEquals($expected, $result);
    }

}
 