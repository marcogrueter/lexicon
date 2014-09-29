<?php namespace spec\Anomaly\Lexicon\Conditional\Test;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class StringTestSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Conditional\Test
 */
class StringTestSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Conditional\Test\StringTest');
    }
    
    function it_can_test_if_it_contains_a_string()
    {
        $this->contains('funkadelic', 'funk')->shouldBe(true);
    }

    function it_can_test_if_it_starts_with_a_string()
    {
        $this->startsWith('apple', 'app')->shouldBe(true);
    }

    function it_can_test_if_it_ends_with_a_string()
    {
        $this->endsWith('watermelon', 'melon')->shouldBe(true);
    }

    function it_can_test_if_the_string_matches_a_regex_pattern()
    {
        $this->is('folder/*', 'folder/file.txt')->shouldBe(true);
    }

}
