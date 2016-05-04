<?php

namespace spec\Sassnowski\Option;

use Exception;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RuntimeException;
use Sassnowski\Option\Option;

class OptionSpec extends ObjectBehavior
{
    function it_returns_the_value_if_it_exists()
    {
        $this->beConstructedWith(1);

        $this->get()->shouldEqual(1);
    }

    function it_throws_an_exception_if_value_is_null()
    {
        $this->beConstructedWith(null);

        $this->shouldThrow(RuntimeException::class)->during('get');
    }

    function it_should_not_be_defined_if_constructed_with_null()
    {
        $this->beConstructedWith(null);

        $this->shouldNotBeDefined();
    }

    function it_should_be_defined_for_a_non_null_value()
    {
        $this->beConstructedWith("foo");

        $this->shouldBeDefined();
    }

    function it_should_return_the_default_value_if_the_value_is_null()
    {
        $this->beConstructedWith(null);

        $this->getOrElse('default')->shouldEqual('default');
    }

    function it_returns_the_value_if_it_is_defined_and_discards_the_default()
    {
        $this->beConstructedWith([1, 2, 3]);
       
        $this->getOrElse('default')->shouldEqual([1, 2, 3]);
    }
    
    function it_applies_a_function_to_the_value_if_it_is_defined()
    {
        $this->beConstructedWith("abc");
        
        $this->map(function ($value) 
        { 
            return strlen($value);
        })->get()->shouldEqual(3);
    }
    
    function it_does_not_call_the_function_if_the_value_is_not_defined()
    {
        $this->beConstructedWith(null);
        $func = function ($value) 
        {
            throw new Exception;
        };
        
        $this->shouldNotThrow(Exception::class)->during('map', [$func]);
        $this->map($func)->shouldNotBeDefined();
    }
    
    function it_throws_an_exception_if_no_callable_was_passed()
    {
        $this->beConstructedWith(10);
        
        $this->shouldThrow(InvalidArgumentException::class)->during('map', [10]);
    }

    function it_can_be_instantiated_as_a_none()
    {
        $this->beConstructedThrough('None');
        
        $this->shouldNotBeDefined();
    }
    
    function it_applies_the_next_function_to_a_defined_value()
    {
        $this->beConstructedWith(10);
        
        $func = function ($i) {
            if (0 === $i) return Option::None();
            
            return new Option(2 / $i);
        };
        
        $this->flatMap($func)->get()->shouldEqual(0.2);
    }

    function it_aborts_a_chain_of_computations_if_it_encounters_a_none()
    {
        $this->beConstructedWith(10);
        
        $func1 = function () { return new Option(1); };
        $func2 = function () { return Option::None(); };
        $func3 = function () { throw new Exception; };

        $this->flatMap($func1)->flatMap($func2)->flatMap($func3)->shouldNotBeDefined();
    }
    
    function it_should_simply_return_the_value_if_it_exists()
    {
        $this->beConstructedWith(10);
        
        $func = function () { return; };
        
        $this->orElse($func)->get()->shouldEqual(10);
    }
    
    function it_should_return_the_alternative_if_it_is_undefined()
    {
        $this->beConstructedWith(null);
        
        $func = function () { return new Option(20); };
        
        $this->orElse($func)->get()->shouldEqual(20);       
    }
    
    function it_should_return_the_first_alternative_that_is_defined()
    {
        $this->beConstructedWith(null);
        
        $alt1 = function () { return Option::None(); };
        $alt2 = function () { return new Option(10); };
        
        $this->orElse($alt1)->orElse($alt2)->orElse($alt1)->get()->shouldEqual(10);
    }

    function it_should_return_an_undefined_option_if_none_of_the_alternatives_returns_a_defined_value()
    {
        $this->beConstructedWith(null);

        $alt1 = function () { return Option::None(); };

        $this->orElse($alt1)->orElse($alt1)->orElse($alt1)->shouldNotBeDefined();
    }

    function it_should_throw_an_exception_if_a_non_callable_is_passed_during_flatMap()
    {
        $this->beConstructedWith(10);
        
        $this->shouldThrow(InvalidArgumentException::class)->during('flatMap', [10]);
    }
    
    function it_should_throw_an_exception_if_a_non_callable_is_passed_during_orElse()
    {
        $this->beConstructedWith(null);
        
        $this->shouldThrow(InvalidArgumentException::class)->during('orElse', [10]);
    }
}
