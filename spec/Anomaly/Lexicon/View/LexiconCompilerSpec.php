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
        $this->compile(
            '{{ if theme.actions }}
            {{ theme.actions }}
            <a href="{{ foo.to "{path}" }}" class="btn btn-success">{{ title }}</a>
            {{ /theme.actions }}
            {{ endif }}'
        )->shouldCompile('<?php if($__data[\'__env\']->variable($__data, \'theme.actions\')): ?>
            <?php foreach($__data[\'__env\']->variable($__data,\'theme.actions\',[],\'\',[],\'traversable\') as $i=>$themeActionsItem): ?>
            <a href="<?php echo e($__data[\'__env\']->variable($themeActionsItem,\'foo.to\',[0=>\'\'.$__data[\'__env\']->variable($themeActionsItem,\'path\',[],\'\',null,\'string\').\'\'],\'\',null,\'string\')); ?>" class="btn btn-success"><?php echo e($__data[\'__env\']->variable($themeActionsItem,\'title\',[],\'\',null,\'string\')); ?></a>
            <?php endforeach; ?>
            <?php endif; ?>');
    }

}
