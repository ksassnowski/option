# Option Type for PHP

[![Build Status](https://travis-ci.org/ksassnowski/option.svg?branch=master)](https://travis-ci.org/ksassnowski/option)
[![Coverage Status](https://coveralls.io/repos/github/ksassnowski/option/badge.svg?branch=develop)](https://coveralls.io/github/ksassnowski/option?branch=develop)
[![Code Climate](https://codeclimate.com/github/ksassnowski/option/badges/gpa.svg)](https://codeclimate.com/github/ksassnowski/option)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3f99aec5-edcf-4d02-a5ae-466b83680dd7/mini.png)](https://insight.sensiolabs.com/projects/3f99aec5-edcf-4d02-a5ae-466b83680dd7)

A PHP implementation of the `Option` data type from Scala (or `Maybe` from Haskell if you want).

## Installation

Install the package through composer:

```bash
$ composer require sassnowski/option
```

That’s it! Now you can use it in your code.

```php
function divide($a, $b)
{
	if (0 === $b)
	{
		return Option::None();
	}
	
	return new Option($a / $b);
}

$result = divide(10, 5);
$result->get(); // 2

$result2 = divide(10, 0);
$result->isDefined(); // false
```


## Summary

An `Option` represents an optional value, or in other words a value that may not exist. It is sometimes described as a List that contains a maximum of one item.

An `Option` is used in places where otherwise `null` might be used, e.g., the result of a Database Query. A more general way to put it is: A computation might return an `Option` if it is not defined for some inputs.

## Option::map($func)

Using `Option`s means that a lot of code needs to be aware of this data type. In order to still be able to reuse functions that operate on unwrapped values, this class provides a `map` function.

The purpose of the `map` function is to *lift* a function that normally operators on regular values to now work on `Option` values. Formally it turns a function of type

```
a -> b
```

into a function of type

```
Option a -> Option b
```

### Example

```php
// Note: This example uses PHP 7 type hinting. This is in no 
// way necessary for this package to work and is simply there 
// to illustrate the types that these functions are supposed to
// operate on.

function length(string $a): int
{
	return strlen($a);
}

$length1 = (new Option("abc"))->map('length');
$length1->isDefined(); // true
$length1->get(); // 3

// The length function never gets executed, since we're 
// dealing with an undefined value.
$length2 = Option::None->map('length');
$length2->isDefined(); // false
$length2->get(); // RuntimeException
```

The above example lifted the function `length` of type `string -> int` into a function of type `Option string -> Option int`. This means that we can still write and use functions that were written without optional values in mind and simply lift them to a function that can handle `Option`s.

An important characteristic of the `map` method is, that the function that is being mapped over the option will never get executed if we’re dealing with an undefined value.

## Option::flatMap($func)

*Todo*

## Option::getOrElse($default)

*Todo*

## Option::orElse($alternative)

*Todo*

## Option::isDefined()

This function simply returns `true` if the value is anything other than `null` in which case it returns `false`.

## License

MIT
