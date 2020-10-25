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

use Throwable, Ke\Web\Component;

class View extends Renderer
{

	private $view = false;

	private $viewStatus = 0;

	private $layout = false;

	private $layoutStatus = 0;

	private $isInitContent = false;

	private $content = '';

	public function __construct(string $view = null, string $layout = null)
	{
		parent::__construct();
		$this->setView($view)->setLayout($layout);
	}

	public function setView(string $view = null)
	{
		$view = trim($view, KE_PATH_NOISE);
		if (empty($view))
			$view = false;
		$this->view = $view;
		return $this;
	}

	public function setLayout(string $layout = null)
	{
		$layout = trim($layout, KE_PATH_NOISE);
		if (empty($layout))
			$layout = false;
		$this->layout = $layout;
		return $this;
	}

	public function getValidLayout()
	{
		if ($this->layout === false)
			return false;
		// layoutStatus = 0, 表示并没有到加载layout的步骤就出错了
		// layoutStatus = 1, 表示文件存在，但加载layout出错了
		// layoutStatus = 2, 表示文件存在，且加载layout成功了
		if ($this->layoutStatus !== 2)
			return false;
		return $this->layout;
	}

	public function getContent()
	{
		if ($this->isInitContent)
			return $this->content;
		$isDebug = $this->web->isDebug();
		if ($this->view === false) {
			if ($isDebug)
				$this->content .= $this->web->ob->getOutput('webStart');
			return $this->content;
		}
		$this->viewStatus = 0;
		$this->layoutStatus = 0;
		try {
			// 先加载view
			$view = $this->web->getComponentPath($this->view, Component::VIEW);
			if ($view === false)
				throw new \Error("View {$this->view} not found!");
			$this->viewStatus++; // 1 : view文件存在
			$this->content = $this->context->import($view);
			$this->viewStatus++; // 2 : view加载成功

			if ($isDebug)
				$this->content .= $this->web->ob->getOutput('webStart');
			// 加载layout
			if ($this->layout !== false) {
				$layout = $this->web->getComponentPath($this->layout, Component::LAYOUT);
				if ($layout === false)
					throw new \Error("Layout {$this->layout} not found!");
				$this->layoutStatus++; // 1 : layout文件存在
				$this->content = $this->context->import($layout, ['content' => $this->content]);
				$this->layoutStatus++; // 2 : layout文件加载成功
			}
			$this->isInitContent = true;
			return $this->content;
		}
		catch (Throwable $thrown) {
			throw $thrown;
		}
	}

	public function setContent($content)
	{
		$this->content = $content;
		return $this;
	}

	protected function rendering()
	{
		$this->web->sendHeaders();
		print $this->getContent();
	}

}