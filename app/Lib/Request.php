<?php
namespace Acd\Lib;

/**
 * Typed access to request parameters ($_GET, $_POST, $_REQUEST or any nested array of them).
 * A parameter can always arrive with an unexpected type (s[]=x turns "s" into an array); since
 * PHP 8 that ends in a TypeError in string functions, so it is treated as if it were missing.
 */
class Request
{
	/** $source[$name] when it is a string; null when it is missing or is not a string */
	public static function string($source, $name): ?string
	{
		return is_array($source) && isset($source[$name]) && is_string($source[$name]) ? $source[$name] : null;
	}

	/** $source[$name] when it is an array; [] when it is missing or is not an array */
	public static function array($source, $name): array
	{
		return is_array($source) && isset($source[$name]) && is_array($source[$name]) ? $source[$name] : [];
	}

	/** Like array(), keeping only the string values */
	public static function strings($source, $name): array
	{
		return array_filter(self::array($source, $name), 'is_string');
	}
}
