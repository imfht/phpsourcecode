<?php
namespace Component\Orm\Query\Mongodb;
class Tokenizer
{
	public static function tokenize($condition)
	{
		$tokens = static::_scan($condition);
		$tokens = array_filter($tokens, array(__CLASS__, '_filter'));
		$tokens = array_values($tokens);
		if(!isset($tokens[4])) { throw new \InvalidArgumentException('syntax error'); }
		return $tokens;
	}

	private static function _scan($condition)
	{
		$condition = preg_replace('/\s*([=!><]+|\(|\)|\sand\s|\sor\s)\s*/i', ' $1 ', '('.$condition.')');
		$condition = preg_replace('/\sin\s*\(\s*\?\s*\)/i', ' in ?', $condition);
		//$condition = preg_replace('/\s(in|against)\s*\(\s*\?\s*\)/i', ' $1 ?', $condition);
		//$condition = preg_replace('/\smatch\s*\(\s*([a-z0-9_]+)\s*\)/i', ' $1', $condition);
		return explode(' ', $condition);
	}

	private static function _filter($element)
	{
		return $element!=='';
	}
}
