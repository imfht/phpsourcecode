<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2018/4/11 0011
 * Time: 下午 22:05
 */

namespace LiteAdmin;

use think\Paginator;

/**
 * layui分页驱动
 * Class Layui
 * @package app\common\lib
 */
class Layui extends Paginator
{
	/**
	 * 上一页按钮
	 * @param string $text
	 * @return string
	 */
	protected function getPreviousButton($text = "&laquo;")
	{

		if ($this->currentPage() <= 1) {
			return $this->getDisabledTextWrapper($text);
		}

		$url = $this->url(
			$this->currentPage() - 1
		);

		return $this->getPageLinkWrapper($url, $text);
	}

	/**
	 * 下一页按钮
	 * @param string $text
	 * @return string
	 */
	protected function getNextButton($text = '&raquo;')
	{
		if (!$this->hasMore) {
			return $this->getDisabledTextWrapper($text);
		}

		$url = $this->url($this->currentPage() + 1);

		return $this->getPageLinkWrapper($url, $text);
	}

	/**
	 * 页码按钮
	 * @return string
	 */
	protected function getLinks()
	{
		if ($this->simple)
			return '';

		$block = [
			'first'  => null,
			'slider' => null,
			'last'   => null
		];

		$side   = 3;
		$window = $side * 2;

		if ($this->lastPage < $window + 6) {
			$block['first'] = $this->getUrlRange(1, $this->lastPage);
		} elseif ($this->currentPage <= $window) {
			$block['first'] = $this->getUrlRange(1, $window + 2);
			$block['last']  = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
		} elseif ($this->currentPage > ($this->lastPage - $window)) {
			$block['first'] = $this->getUrlRange(1, 2);
			$block['last']  = $this->getUrlRange($this->lastPage - ($window + 2), $this->lastPage);
		} else {
			$block['first']  = $this->getUrlRange(1, 2);
			$block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
			$block['last']   = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
		}

		$html = '';

		if (is_array($block['first'])) {
			$html .= $this->getUrlLinks($block['first']);
		}

		if (is_array($block['slider'])) {
			$html .= $this->getDots();
			$html .= $this->getUrlLinks($block['slider']);
		}

		if (is_array($block['last'])) {
			$html .= $this->getDots();
			$html .= $this->getUrlLinks($block['last']);
		}

		return $html;
	}

	/**
	 * 渲染分页html
	 * @return mixed
	 */
	public function render()
	{
		if ($this->hasPages()) {
			if ($this->simple) {
				return sprintf(
					'    <div class="layui-box layui-laypage layui-laypage-default">%s %s</div>',
					$this->getPreviousButton(),
					$this->getNextButton()
				);
			} else {
				return sprintf(
					'<div class="layui-box layui-laypage layui-laypage-default">%s %s %s</div>',
					$this->getPreviousButton(),
					$this->getLinks(),
					$this->getNextButton()
				);
			}
		}
	}

	/**
	 * 生成一个可点击的按钮
	 *
	 * @param  string $url
	 * @param  int    $page
	 * @return string
	 */
	protected function getAvailablePageWrapper($url, $page)
	{
		return '<a href="' . htmlentities($url) . '" >' . $page . '</a>';
	}

	/**
	 * 生成一个禁用的按钮
	 *
	 * @param  string $text
	 * @return string
	 */
	protected function getDisabledTextWrapper($text)
	{
		return '<a href="javascript:;" class="layui-laypage-prev layui-disabled">' . $text . '</a>';
	}

	/**
	 * 生成一个激活的按钮
	 *
	 * @param  string $text
	 * @return string
	 */
	protected function getActivePageWrapper($text)
	{
		return '<span class="layui-laypage-curr">
            <em class="layui-laypage-em"></em>
            <em>'.$text.'</em>
        </span>';
	}

	/**
	 * 生成省略号按钮
	 *
	 * @return string
	 */
	protected function getDots()
	{
		return $this->getDisabledTextWrapper('...');
	}

	/**
	 * 批量生成页码按钮.
	 *
	 * @param  array $urls
	 * @return string
	 */
	protected function getUrlLinks(array $urls)
	{
		$html = '';

		foreach ($urls as $page => $url) {
			$html .= $this->getPageLinkWrapper($url, $page);
		}

		return $html;
	}

	/**
	 * 生成普通页码按钮
	 *
	 * @param  string $url
	 * @param  int    $page
	 * @return string
	 */
	protected function getPageLinkWrapper($url, $page)
	{
		if ($page == $this->currentPage()) {
			return $this->getActivePageWrapper($page);
		}

		return $this->getAvailablePageWrapper($url, $page);
	}
}