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
                '<?php foreach($this->variable($__data,\'theme.actions\',[],\'\',[],\'traversable\') as $i=>$themeActionsItem): ?>
                <a href="<?php echo e($this->variable($themeActionsItem,\'foo.to\',[0=>\'\'.$this->variable($themeActionsItem,\'path\',[],\'\',null,\'string\').\'\'],\'\',null,\'string\')); ?>" class="btn btn-success"><?php echo e($this->variable($themeActionsItem,\'title\',[],\'\',null,\'string\')); ?></a>
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
                '<?php if($this->variable($__data, \'foo\')): ?>
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
                '<?php if($this->variable($__data, \'foo\') and $this->booleanTest($this->variable($__data, \'yin\'),$this->variable($__data, \'yang\'),\'==\')): ?>
                    // do something
                <?php endif; ?>'
            );
    }

}
