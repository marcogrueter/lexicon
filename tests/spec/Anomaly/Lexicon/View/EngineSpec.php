<?php namespace spec\Anomaly\Lexicon\View;

use Anomaly\Lexicon\Test\Spec;
use Anomaly\Lexicon\View\Factory;

/**
 * Class EngineSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\View
 */
class EngineSpec extends Spec
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
         * $lexicon->addStringTemplate('<h1>Hello {{ name }}</h1>');
         * from the Engine stub.
         */
        $this->get(
            '<h1>Hello {{ name }}</h1>',
            [
                '__env' => $factory,
                'name'  => 'Mr. Anderson'
            ]
        );
    }

    function it_can_compile_a_file_template(Factory $factory)
    {
        /**
         * This works because we did...
         * $lexicon->addStringTemplate('<h1>Hello {{ name }}</h1>');
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
        $path = $this->path('exception.html');

        $this->getLexicon()->getFoundation()->getNodeFactory()->addNodeGroupPath($path, 'testing');

        /**
         * We purposely compile an undefined variable to cause an exception
         * and test that it can be handled.
         */
        $this->shouldThrow('\ErrorException')->during(
            'get',
            [
                $path,
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
