<?php namespace spec\Anomaly\Lexicon\View;

use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Stub\ArrayAccessObject;
use Anomaly\Lexicon\Stub\MagicMethodObjectParent;
use Anomaly\Lexicon\Stub\SimpleObject;
use Anomaly\Lexicon\Stub\StringObject;
use Anomaly\Lexicon\Stub\TraversableObject;
use Anomaly\Lexicon\Test\Spec;
use Anomaly\Lexicon\View\Engine;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\ViewFinderInterface;

/**
 * Class FactorySpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\View
 */
class FactorySpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\View\Factory');
    }

    function it_can_make_view()
    {
        $this->make('test::hello')->shouldHaveType('Anomaly\Lexicon\Contract\View\ViewInterface');
    }

    function it_can_parse_and_make_view()
    {
        $this->parse('{{ hello }}')->shouldHaveType('Anomaly\Lexicon\Contract\View\ViewInterface');
    }

    function it_can_make_view_with_alias()
    {
        $this->alias('test::hello', 'foo');
        $this->make('foo')->shouldHaveType('Anomaly\Lexicon\Contract\View\ViewInterface');
    }

    function it_can_get_variable_from_plugin()
    {
        $this->variable([], 'stub.foo')->shouldReturn('FOO, BAR, BAZ!');
    }
    
    function it_returns_null_if_data_is_null()
    {
        $this->variable(null, '')->shouldReturn(null);
    }
    
    function it_can_get_array_size()
    {
        $data = [
            'list' => [
                'one',
                'two',
                'three'
            ]
        ];

        $this->variable($data, 'list.size')->shouldReturn(3);
    }

    function it_can_get_string_size()
    {
        $data = [
            'name' => 'Anomaly'
        ];

        $this->variable($data, 'name.size')->shouldReturn(7);
    }

    function it_returns_null_if_variable_in_array_does_not_exist()
    {
        $this->variable([], 'nonexistent')->shouldReturn(null);
    }
    
    function it_can_get_variable_from_ArrayAccess_object()
    {
        $data = [
            'thing' => new ArrayAccessObject(),
        ];

        $this->variable($data, 'thing.foo')->shouldReturn('value from array access object');
    }
    
    function it_returns_null_if_variable_in_ArrayAccess_object_does_not_exist()
    {
        $data = [
            'thing' => new ArrayAccessObject(),
        ];

        $this->variable($data, 'thing.nonexistent')->shouldReturn(null);
    }
    
    function it_can_get_variable_from_a_simple_object_method()
    {
        $data = [
            'simple' => new SimpleObject()
        ];

        $this->variable($data, 'simple.foo')->shouldReturn('value from method');
    }

    function it_returns_null_if_exception_happens_while_getting_variable_from_a_simple_object_method()
    {
        $data = [
            'simple' => new SimpleObject()
        ];

        $this->variable($data, 'simple.fragile')->shouldReturn(null);
    }

    function it_can_get_variable_from_a_simple_object_property()
    {
        $data = [
            'simple' => new SimpleObject()
        ];

        $this->variable($data, 'simple.bar')->shouldReturn('value from property');
    }

    function it_returns_null_if_simple_object_property_does_not_exist()
    {
        $data = [
            'simple' => new SimpleObject()
        ];

        $this->variable($data, 'simple.nonexistent')->shouldReturn(null);
    }
    
    function it_can_get_any_value()
    {
        $this->expected('whatever', 'any')->shouldReturn('whatever');
    }
    
    function it_can_get_string_that_is_expected_to_be_echoed()
    {
        $this->expected('string', 'string')->shouldReturn('string');
    }

    function it_can_get_float_that_is_expected_to_be_echoed()
    {
        $this->expected(3.50, 'string')->shouldReturn(3.50);
    }

    function it_can_get_number_that_is_expected_to_be_echoed()
    {
        $this->expected(3, 'string')->shouldReturn(3);
    }

    function it_can_get_boolean_that_is_expected_to_be_echoed()
    {
        $this->expected(true, 'string')->shouldBe(true);
    }

    function it_can_get_null_that_is_expected_to_be_echoed()
    {
        $this->expected(null, 'string')->shouldReturn(null);
    }

    function it_can_get_object_that_implements_toString_and_is_expected_to_be_echoed()
    {
        $stringObject = new StringObject();
        $this->expected($stringObject, 'string')->shouldReturn($stringObject);
    }

    function it_can_get_array_that_is_expected_to_be_traversable()
    {
        $this->expected(array(), 'traversable')->shouldReturn(array());
    }

    function it_can_get_object_that_is_expected_to_be_traversable()
    {
        $traversableObject = new TraversableObject();
        $this->expected($traversableObject, 'traversable')->shouldReturn($traversableObject);
    }

    function it_can_run_a_boolean_test_values()
    {
        $this->booleanTest(5, 3, '>')->shouldBe(true);
    }

    function it_can_get_a_magic_method_object_as_property()
    {
        $this->getLexicon()->addMagicMethodClass('Anomaly\Lexicon\Stub\MagicMethodObject');
        $this->variable(['something' => new MagicMethodObjectParent()], 'something.magic_method_object');
    }
    
    /**
     * Rendering views examples
     * The test views are stored in the resources/views folder
     */

    function it_can_render_blocks()
    {

        $data = [
            'books' => [
                [
                    'title' => 'Foo'
                ],
                [
                    'title' => 'Bar'
                ],
            ]
        ];

        $this->make('test::blocks', $data)->render()->shouldReturn('<ul>
    <li>Foo</li>
    <li>Bar</li>
</ul>');

    }

    function it_can_render_comments()
    {
        $this->make('test::comments')->shouldRender('<h1>This content will remain.</h1>
');
    }

    function it_can_render_conditionals()
    {

        $data = [
            'first_name' => 'Lex',
            'last_name' => 'Luthor',
        ];

        $this->make('test::conditionals', $data)->shouldRender('<h2>Lex</h2>

<hr/>
Something.

<hr/>

');
    }

    function it_can_render_variables_from_different_scopes()
    {
        $data = [
            'categories' => [
                [
                    'name'  => 'Art',
                    'posts' => [
                        [
                            'name' => 'Van Goh painting style'
                        ],
                        [
                            'name' => 'What is art? What is not?'
                        ]
                    ]
                ],
                [
                    'name'  => 'PHP',
                    'posts' => [
                        [
                            'name' => 'Getting started with Laravel'
                        ],
                        [
                            'name' => 'Using composer'
                        ]
                    ]
                ]
            ],
        ];

        $this->make('test::scopes', $data)->shouldRender('<ul>
        <li>Art - Van Goh painting style</li>
        <li>Art - What is art? What is not?</li>
            <li>PHP - Getting started with Laravel</li>
        <li>PHP - Using composer</li>
    </ul>');

    }
    
    function it_can_render_different_blocks_with_the_same_variable_tag()
    {
        $data = [
            'messages' => [
                'success' => [
                    [
                        'message' => 'Everything is cool.'
                    ],
                    [
                        'message' => 'Good job.'
                    ]
                ],
                'error' => [
                    [
                        'message' => 'Oh crap.'
                    ],
                    [
                        'message' => 'Something fucked up.'
                    ]
                ]
            ],
        ];

        $this
            ->make('test::different-blocks-with-same-variable', $data)
            ->shouldRender('<ul>
        <li>Everything is cool.</li>
        <li>Good job.</li>
    </ul>
<ul>
        <li>Oh crap.</li>
        <li>Something fucked up.</li>
    </ul>');
    }

    function it_can_render_extends()
    {
        $this
            ->make('test::extends')
            ->shouldRender('<div class="sidebar">This is some sidebar content.</div>
<div class="content">Injecting this content into the yield section.</div>
');
    }
    
    function it_can_render_ignore_block_and_ignore_variable()
    {
        $this
            ->make('test::ignore')
            ->shouldRender('These tags will remain unparsed.
    {{ tag1 }}{{ tag2 }}{{ tag3 }}

{{ single }}');
    }
    
    function it_can_render_include()
    {
        $this
            ->make('test::include')
            ->shouldRender('Original content. This is some sample partial content.');
    }

}
