<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Utils;

/**
 * Git常用命令集合
 *
 * 未完善，但需要使用的几率很高
 *
 * @package Ke\Utils
 */
class Git
{

	/**
	 * @var string
	 */
	private $root = null;

	public function getRoot()
	{
		if (!isset($this->root)) {
			exec('git rev-parse --show-cdup', $output);
			$this->root = real_path($output[0]);
		}
		return $this->root;
	}

	public function getLastVersion()
	{
		exec('git log -1', $output);
		if (!empty($output[0])) {
			$version = str_replace('commit ', '', $output[0]);
			if (empty($version))
				$version = 'HEAD';
			return $version;
		}
		return 'HEAD';
	}

	public function getChangeFiles(string $version = null)
	{
		if (empty($version))
			$version = $this->getLastVersion();
		exec("git diff {$version} --name-only", $output);
		return $output;
	}
}