<?php

namespace Kernel\Core\Conf;

class ConfigNotFoundException extends \Exception
{
	public function __construct($code, $message = '')
	{
		parent::__construct($message, $code, null);
	}
}