<?php

namespace Sassnowski\Option;

use InvalidArgumentException;
use RuntimeException;

/**
 * A class that represents an optional value.
 * 
 * This class is mainly used in places where usually null would be
 * returned.
 * 
 * @package Sassnowski\Option
 */
class Option
{
    /**
     * @var mixed|null
     */
    protected $value;

    /**
     * Option constructor.
     * 
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Named constructor to create an Option with an undefined value (None).
     * 
     * @return static
     */
    public static function None()
    {
        return new static(null);
    }

    /**
     * Unwraps and returns the value. If the value is null an exception 
     * is thrown.
     * 
     * @return mixed
     * @throws RuntimeException If the value is null.
     */
    public function get()
    {
        if (! $this->isDefined())
        {
            throw new RuntimeException("Trying to retrieve null value.");
        }
        
        return $this->value;
    }

    /**
     * Returns false if $value is `null`. Returns true otherwise.
     * 
     * @return bool
     */
    public function isDefined()
    {
        return ! is_null($this->value);
    }

    /**
     * Attempts to return the unwrapped value. If it is null, the $default
     * gets returned instead.
     * 
     * @param $default mixed The default value to return if the wrapped value is null.
     * @return mixed
     */
    public function getOrElse($default)
    {
        return $this->isDefined() ? $this->value : $default;
    }

    /**
     * Applies a function if the value is defined. Returns itself otherwise.
     * 
     * @param $func callable The function to apply if the value is defined.
     * @return $this
     */
    public function map($func)
    {
        if (! $this->isDefined())
        {
            return Option::None();
        }
        
        $this->guardAgainstNonCallable($func, __METHOD__);

        return new Option($func($this->value));
    }

    /**
     * Applies $func to this value if it exists. The difference between
     * map and flatMap is, that the function that is being passed to flatMap
     * also returns an option, i.e. can also fail.
     * 
     * @param $func callable The function to apply to this value.
     * @return Option
     */
    public function flatMap($func)
    {
        if (! $this->isDefined())
        {
            return Option::None();
        }
        
        $this->guardAgainstNonCallable($func, __METHOD__);
        
        return $func($this->value);
    }

    /**
     * If this value is undefined, try the provided alternative function.
     * 
     * @param $alternative callable The alternative function to try if the value is undefined.
     * @return Option
     */
    public function orElse($alternative)
    {
        if (! $this->isDefined())
        {
            $this->guardAgainstNonCallable($alternative, __METHOD__);
            
            return $alternative();
        }
        
        return $this;
    }

    /**
     * Guard against a non callable parameter.
     * 
     * @param $func mixed The parameter to check
     * @param $inMethod string The method the parameter was passed to. Is used in the exception message.
     * @throws InvalidArgumentException If $func is not a callable.
     */
    protected function guardAgainstNonCallable($func, $inMethod)
    {
        if (! is_callable($func)) 
        {
            throw new InvalidArgumentException("Argument passed to function ${inMethod} must be a callable.");
        }
    }
}
