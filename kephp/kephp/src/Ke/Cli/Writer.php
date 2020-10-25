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


class Writer
{

	protected $lineCounter = [];

	protected $line = 0;

	public function isOutput()
	{
		// TODO: Implement isOutput() method.
	}

	public function output($output = null, $isBreakLine = false)
	{
		if (!isset($this->lineCounter[$this->line]))
			$this->lineCounter[$this->line] = 0;
		if (!is_array($output))
			$output = [$output];
		$content = '';
		foreach ($output as &$item) {
			if ($item === PHP_EOL) {
				$this->line += 1;
				$this->lineCounter[$this->line] = 0;
				$content .= $item;
			}
			else {
				if ($this->lineCounter[$this->line] > 0)
					$content .= ' ';
				$content .= print_r($item, true);
				$this->lineCounter[$this->line]++;
			}
		}
		if ($isBreakLine) {
			$content .= PHP_EOL;
			$this->line += 1;
		}
		file_put_contents('php://stdout', $content);
	}
}
