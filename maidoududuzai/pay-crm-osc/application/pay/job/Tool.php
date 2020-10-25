<?php

namespace app\pay\job;

class Tool
{

	public static function show($data = [])
	{
		if(is_array($data) || is_object($data)) {
			$data = JSON($data);
		}
		return '[' . gsdate('Y-m-d H:i:s') . ']' . ' ' . $data . PHP_EOL;
	}

}

