<?php namespace spec\Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Test\Spec;
use Illuminate\Filesystem\Filesystem;

/**
 * Class CompilerSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\View
 */
class CompilerSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\View\Compiler');
    }

    function it_can_set_and_get_path()
    {
        $this->setPath('foo')->getPath()->shouldReturn('foo');
    }

    function it_can_set_and_get_hash()
    {
        $this->setHash(md5('foo'))->getHash()->shouldReturn(md5('foo'));
    }

    function it_can_set_and_get_lexicon(LexiconInterface $lexicon)
    {
        $this->setLexicon($lexicon)->getLexicon()->shouldHaveType('Anomaly\Lexicon\Contract\LexiconInterface');
    }

    function it_can_check_if_a_parsed_view_is_not_compiled()
    {
        $this->isNotParsed('nonexistent')->shouldBe(true);
    }

    function it_can_escape_php()
    {
        $this->escapePhp('<?php echo $foo; ?>')->shouldReturn('&lt;?php echo $foo; ?&gt;');
    }

    function it_can_compile_view_template()
    {
        $this->compile($this->path('hello.html'))->shouldReturn("<?php namespace Anomaly\Lexicon\View; class LexiconView_89161ae7e80908f946fc5b33a0948118 implements \Anomaly\Lexicon\Contract\View\CompiledViewInterface { public function render(\$__data) {
?><h1>Hello <?php echo e(\$__data['__env']->variable(\$__data,'name',[],'',null,'string')); ?>!</h1><?php }} ?>");
    }

    public function path($path)
    {
        return __DIR__ . '/../../../../resources/views/' . $path;
    }

}
