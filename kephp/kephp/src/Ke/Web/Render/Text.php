<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */


namespace Ke\Web\Render;


class Text extends Renderer
{

	private $content = '';

	public function __construct($content)
	{
		parent::__construct();
		$this->content = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setContent($content)
	{
		$this->content = (string)$content;
		return $this;
	}

	protected function rendering()
	{
		$content = $this->getContent();
		$length = strlen($content);
		$this->web->sendHeaders([
			'Content-Length' => $length,
		]);
		print $content;
		exit();
	}
}