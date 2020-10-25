<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Cli\Cmd;

class NewLayout extends NewWidget
{

	protected static $commandName = 'newLayout';

	protected static $commandDescription = '';

	protected $desc = 'layout';

	protected $template = 'Layout.tp';

	public function getPath(bool $checkDir = false)
	{
		$path = $this->dir . DS . 'layout' . DS . $this->name . '.phtml';
		if ($checkDir) {
			$dir = dirname($path);
			if (!is_dir($dir))
				mkdir($dir, 0755, true);
		}
		return $path;
	}

}