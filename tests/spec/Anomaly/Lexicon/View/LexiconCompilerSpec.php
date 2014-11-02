<?php namespace spec\Anomaly\Lexicon\View;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class LexiconCompilerSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\View
 */
class LexiconCompilerSpec extends Spec
{
    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\View\LexiconCompiler');
    }

    function it_can_compile_context_variables()
    {
        $this
            ->compile(
                '{{ theme.actions }}
                <a href="{{ foo.to "{path}" }}" class="btn btn-success">{{ title }}</a>
                {{ /theme.actions }}'
            )->shouldCompile(
                '<?php foreach($__data[\'__env\']->variable($__data,\'theme.actions\',[],\'\',[],\'traversable\') as $i=>$themeActionsItem): ?>
                <a href="<?php echo e($__data[\'__env\']->variable($themeActionsItem,\'foo.to\',[0=>\'\'.$__data[\'__env\']->variable($themeActionsItem,\'path\',[],\'\',null,\'string\').\'\'],\'\',null,\'string\')); ?>" class="btn btn-success"><?php echo e($__data[\'__env\']->variable($themeActionsItem,\'title\',[],\'\',null,\'string\')); ?></a>
                <?php endforeach; ?>'
            );
    }

    function it_can_simple_conditional()
    {
        $this
            ->compile(
                '{{ if foo }}
                    // do something
                {{ endif }}'
            )->shouldCompile(
                '<?php if($__data[\'__env\']->variable($__data, \'foo\')): ?>
                    // do something
                <?php endif; ?>'
            );
    }

    function it_can_compile_conditional_with_logical_and_comparison_operators()
    {
        $this
            ->compile(
                '{{ if foo and yin == yang }}
                    // do something
                {{ endif }}'
            )->shouldCompile(
                '<?php if($__data[\'__env\']->variable($__data, \'foo\') and $__data[\'__env\']->booleanTest($__data[\'__env\']->variable($__data, \'yin\'),$__data[\'__env\']->variable($__data, \'yang\'),\'==\')): ?>
                    // do something
                <?php endif; ?>'
            );
    }

}
