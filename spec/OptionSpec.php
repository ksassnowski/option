<?php

namespace spec\Sassnowski\Option;

use Exception;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RuntimeException;

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
        $this->beConstructedWith(10);
        
        $this->map(function ($value) 
        { 
            return $value + 10;
        })->get()->shouldEqual(20);
    }
    
    function it_does_not_call_the_function_if_the_value_is_not_defined()
    {
        $this->beConstructedWith(null);
        $fn = function () { throw new Exception; };
        
        $this->shouldNotThrow(Exception::class)->during('map', [$fn]);
    }
    
    function it_throws_an_exception_if_no_callable_was_passed()
    {
        $this->beConstructedWith(10);
        
        $this->shouldThrow(InvalidArgumentException::class)->during('map', [10]);
    }
}
