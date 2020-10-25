<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Cli;


abstract class ReflectionCommand extends Command
{

	const REGEX_DOC = '#@(type|require|default|field|shortcut)(?:[\r\n]|[\s\t]+([^\r\n]+))#';

	protected static function loadColumns()
	{
		$columns = parent::loadColumns();
		$ref     = new \ReflectionClass(static::class);
		$props   = $ref->getProperties();
		foreach ($props as $prop) {
			if ($prop->isStatic())
				continue;
			$name = $prop->getName();
			if (isset(self::$columns[$name]))
				continue;
			$doc = $prop->getDocComment();
			if (empty($doc))
				continue;
			$isMatch = preg_match_all(self::REGEX_DOC, $doc, $matches, PREG_SET_ORDER);
			if (!$isMatch)
				continue;
			$rename = preg_replace_callback('#([A-Z])#', function ($m) {
				return '-' . strtolower($m[1]);
			}, $name);
			$column = [];
			foreach ($matches as $match) {
				$field = $match[1];
				$value = isset($match[2]) ? $match[2] : null;
				if ($field === 'require') {
					$value = static::verifyValue('bool', $value);
				}
				$column[$field] = $value;
			}
			if (!isset($column['field']) || strlen($column['field']) <= 0) {
				$column['field'] = $rename;
			}
			$columns[$rename] = $column;
		}
		return $columns;
	}

	public function filterRequire($value)
	{
		if (is_string($value)) {
			$value = strtolower($value);
			if ($value === 'true')
				return true;
			elseif ($value === 'false')
				return false;

		}

	}
}