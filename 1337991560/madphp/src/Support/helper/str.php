<?php

use \Madphp\Support\Str;

if ( ! function_exists('e')) {
	/**
	 * Escape HTML entities in a string.
	 *
	 * @param  string  $value
	 * @return string
	 */
	function e($value)
	{
		return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
	}
}

if ( ! function_exists('snake_case')) {
	/**
	 * Convert a string to snake case.
	 *
	 * @param  string  $value
	 * @param  string  $delimiter
	 * @return string
	 */
	function snake_case($value, $delimiter = '_')
	{
		return Str::snake($value, $delimiter);
	}
}

if ( ! function_exists('starts_with')) {
	/**
	 * Determine if a given string starts with a given substring.
	 *
	 * @param  string  $haystack
	 * @param  string|array  $needle
	 * @return bool
	 */
	function starts_with($haystack, $needle)
	{
		return Str::startsWith($haystack, $needle);
	}
}

if ( ! function_exists('str_contains')) {
	/**
	 * Determine if a given string contains a given substring.
	 *
	 * @param  string  $haystack
	 * @param  string|array  $needle
	 * @return bool
	 */
	function str_contains($haystack, $needle)
	{
		return Str::contains($haystack, $needle);
	}
}

if ( ! function_exists('str_finish')) {
	/**
	 * Cap a string with a single instance of a given value.
	 *
	 * @param  string  $value
	 * @param  string  $cap
	 * @return string
	 */
	function str_finish($value, $cap)
	{
		return Str::finish($value, $cap);
	}
}

if ( ! function_exists('str_is')) {
	/**
	 * Determine if a given string matches a given pattern.
	 *
	 * @param  string  $pattern
	 * @param  string  $value
	 * @return bool
	 */
	function str_is($pattern, $value)
	{
		return Str::is($pattern, $value);
	}
}

if ( ! function_exists('str_limit')) {
	/**
	 * Limit the number of characters in a string.
	 *
	 * @param  string $value
	 * @param  int    $limit
	 * @param  string $end
	 * @return string
	 */
	function str_limit($value, $limit = 100, $end = '...')
	{
		return Str::limit($value, $limit, $end);
	}
}

if ( ! function_exists('str_plural')) {
	/**
	 * Get the plural form of an English word.
	 *
	 * @param  string  $value
	 * @param  int  $count
	 * @return string
	 */
	function str_plural($value, $count = 2)
	{
		return Str::plural($value, $count);
	}
}

if ( ! function_exists('str_random')) {
	/**
	 * Generate a more truly "random" alpha-numeric string.
	 *
	 * @param  int     $length
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	function str_random($length = 16)
	{
		return Str::random($length);
	}
}

if ( ! function_exists('str_replace_array')) {
	/**
	 * Replace a given value in the string sequentially with an array.
	 *
	 * @param  string  $search
	 * @param  array   $replace
	 * @param  string  $subject
	 * @return string
	 */
	function str_replace_array($search, array $replace, $subject)
	{
		foreach ($replace as $value) {
			$subject = preg_replace('/'.$search.'/', $value, $subject, 1);
		}

		return $subject;
	}
}

if ( ! function_exists('str_singular')) {
	/**
	 * Get the singular form of an English word.
	 *
	 * @param  string  $value
	 * @return string
	 */
	function str_singular($value)
	{
		return Str::singular($value);
	}
}

if ( ! function_exists('studly_case')) {
	/**
	 * Convert a value to studly caps case.
	 *
	 * @param  string  $value
	 * @return string
	 */
	function studly_case($value)
	{
		return Str::studly($value);
	}
}

