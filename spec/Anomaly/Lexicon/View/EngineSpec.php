<?php namespace spec\Anomaly\Lexicon\View;

use Anomaly\Lexicon\View\Factory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class EngineSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\View
 */
class EngineSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\View\Engine');
    }

    function it_can_get_compiled_view(Factory $factory)
    {
        $this->get(
            $this->path('hello.html'),
            [
                '__env' => $factory
            ]
        );
    }

    function it_can_compile_a_string_template(Factory $factory)
    {
        /**
         * This works because we did...
         * $lexicon->addParsePath('<h1>Hello {{ name }}</h1>');
         * from the Engine stub.
         */
        $this->get(
            '<h1>Hello {{ name }}</h1>',
            [
                '__env' => $factory,
                'name' => 'Mr. Anderson'
            ]
        );
    }

    function it_can_compile_a_file_template(Factory $factory)
    {
        /**
         * This works because we did...
         * $lexicon->addParsePath('<h1>Hello {{ name }}</h1>');
         * from the Engine stub.
         */
        $this->get(
            $this->path('compile.html'),
            [
                '__env' => $factory,
            ]
        );
    }


    function it_can_handle_a_view_exception(Factory $factory)
    {
        /**
         * We purposely compile an undefined variable to cause an exception
         * and test that it can be handled.
         */
        $this->shouldThrow('\ErrorException')->during(
            'get',
            [
                $this->path('exception.html'),
                [
                    '__env' => $factory,
                ]
            ]
        );
    }

    public function path($path)
    {
        return __DIR__ . '/../../../../resources/views/' . $path;
    }

}
