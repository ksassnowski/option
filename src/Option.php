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
     * @param $default The default value to return if the wrapped value is null.
     * @return mixed
     */
    public function getOrElse($default)
    {
        return $this->isDefined() ? $this->value : $default;
    }

    /**
     * Applies a function if the value is defined. Returns itself otherwise.
     * 
     * @param $func The function to apply if the value is defined.
     * @return $this
     */
    public function map($func)
    {
        if (! $this->isDefined())
        {
            return Option::None();
        }
        
        if (! is_callable($func))
        {
            throw new InvalidArgumentException("Argument passed to function map() must be a callable.");
        }
        
        return new Option($func($this->value));
    }

    public function flatMap($func)
    {
        if (! $this->isDefined())
        {
            return Option::None();
        }
        
        return $func($this->value);
    }
}
