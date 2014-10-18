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
        $this->setHash('foo')->getHash()->shouldReturn('foo');
    }

    function it_can_set_and_get_lexicon(LexiconInterface $lexicon)
    {
        $this->setLexicon($lexicon)->getLexicon()->shouldHaveType('Anomaly\Lexicon\Contract\LexiconInterface');
    }

    function it_can_check_if_a_parsed_view_is_not_compiled()
    {
        $this->isNotCompiled('nonexistent')->shouldBe(true);
    }

    function it_can_escape_php()
    {
        $this->escapePhp('<?php echo $foo; ?>')->shouldReturn('&lt;?php echo $foo; ?&gt;');
    }

    function it_can_be_expired()
    {
        $this->getLexicon()->setDebug(false);
        $this->isExpired($this->path('hello.html'))->shouldBeBoolean();
    }

    function it_can_be_expired_when_debug_mode_is_on()
    {
        $this->getLexicon()->setDebug(true);
        $this->isExpired($this->path('hello.html'))->shouldBeBoolean();
    }

    function it_can_be_expired_when_string_template_is_not_parsed()
    {
        $this->getLexicon()->setDebug(false);
        $this->getLexicon()->addStringTemplate('<h1>{{ variable }}</h1>');
        $this->isExpired('<h1>{{ variable }}</h1>')->shouldBe(true);
    }

    function it_can_compile_php()
    {
        $this->compile($this->path('hello.html'))->shouldBeCompiled($this->path('hello.html'));
    }

    function it_can_compile_php_when_path_is_string_template()
    {
        $this->getLexicon()->addStringTemplate('<h1>{{ variable }}</h1>');
        $this->compile('<h1>{{ variable }}</h1>')->shouldBeCompiled('<h1>{{ variable }}</h1>');
    }

    function it_can_compile_php_from_string_template()
    {
        $this->getLexicon()->addStringTemplate('<h1>{{ variable }}</h1>');
        $this->compileFromString('<h1>{{ variable }}</h1>', $this->getCompiledPath('<h1>{{ variable }}</h1>'))->shouldBeCompiled('<h1>{{ variable }}</h1>');
    }

    public function path($path)
    {
        return __DIR__ . '/../../../../resources/views/' . $path;
    }

    public function compiled($path)
    {
        return __DIR__ . '/../../../../resources/storage/views/' . $path;
    }

    public function getMatchers()
    {
        return [
            'beCompiled' => function($subject, $path) {
                return is_file($this->compiled(md5($path)));
            }
        ];
    }

}
