<?php namespace Anomaly\Lexicon\Test\Example;


use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class AttributesTest
 *
 * @package Anomaly\Lexicon\Test\Example
 */
class AttributesTest extends LexiconTestCase
{

    /**
     * It can compile variable attributes with nested ordered attributes
     */
    public function test_it_can_compile_variable_attributes_with_nested_ordered_attributes()
    {
        $expected = '<?php echo $__data[\'__env\']->variable($__data, \'test\', [0 => $__data[\'__env\']->variable($__data, \'foo\', [0 => \'nested\'], \'\', null, \'echo\')], \'\', null, \'echo\'); ?>';

        $result = $this->compiler->compileString('{{ test {foo "nested"} }}');

        $this->assertEquals($expected, $result);
    }

    /**
     * It can compile variable attributes with nested ordered attributes
     */
    public function test_it_can_compile_with_alias()
    {
        $expected = '<?php echo $__data[\'__env\']->variable($__data, \'test\', [], \'\', null, \'echo\'); ?>';

        $result = $this->compiler->compileString('{{ .alias.test }}');

        $this->assertEquals($expected, $result);
    }

    public function test_it_can_compile_named_attribute_from_variable_attribute()
    {
        // {{ include "partial" share="one,two,three" }}

        $expected = '';

        $result = $this->compiler->compileString('{{ include "partial" share="one,two,three" }}');

        $this->assertEquals($expected, $result);
    }


}
 