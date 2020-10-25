<?php
/**
 * VgotFaster PHP Framework
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

namespace VF\Library;

/*
Exmaple CSS:
.fpage {font-size:12px; padding:3px; margin:3px; text-align:center;}
.fpage a,.fpage b {background-color:#FFF; padding:5px 7px; margin:0 2px; color:#333; border:1px solid #C2D5E3; display:inline-block; vertical-align:middle; text-decoration:none;}
.fpage a:hover {border-color:#336699; text-decoration:none;}
.fpage b {background-color:#E5EDF2;}
.fpage input {border:#1586D6 1px solid; color:#036CB4; vertical-align:middle; text-align:center;}
*/

/**
 * VgotFaster Pagination Library
 *
 * 首次完成于 3:25 2009/11/27
 *
 * @package VgotFaster
 * @author Pader
 */
class Pagination {

	var $set;

	function __construct($set=array())
	{
		$this->set = array(
			//数据总行数
			'totalRows' => 0,
			//当前页数
			'curPage' => 1,
			/*
				页面链接
				页面链接中，请使用 [pageNumReplace] 配置所指定的字符代替分页数字，默认符号为 "*"
				例：list/page/* 便会生成 list/page/1, list/page/2 的页面地址
			*/
			'pageUrl' => '',
			//每页内容列表显示数量
			'perPage' => 20,
			//显示多少个数字链接
			'pageLinks' => 8,
			//链接中用于替换成数字的符号
			'pageNumReplace' => '*',

			//整个分页起始标签设置
			'fullTagOpen' => '<div class="fpage">',
			'fullTagClose' => '</div>',

			//第一页链接及起始标签设置
			'firstLink' => '&laquo;',
			'firstTagOpen' => '',
			'firstTagClose' => '',

			//页面很多时过渡链接
			'transTag' => '...',

			//最后一页链接及起始标签设置
			'lastLink' => '&raquo;',
			'lastTagOpen' => '',
			'lastTagClose' => '',

			//下一页链接及起始标签设置
			'nextLink' => '下一页',
			'nextTagOpen' => '',
			'nextTagClose' => '',

			//上一页链接及起始标签设置
			'prevLink' => '上一页',
			'prevTagOpen' => '',
			'prevTagClose' => '',

			//当前页起始标签设置
			'curTagOpen' => '<b>',
			'curTagClose' => '</b>',

			//常规数字链接起始标签设置
			'numTagOpen' => '',
			'numTagClose' => '',

			//是否显示页数统计
			'showPageCount' => true
		);

		$VF =& getInstance();

		$config = getConfig('pagination', true);
		$config !== null && $this->initialize($config);

		if(count($set) > 0) {
			$this->initialize($set);
		}
	}

	/*
		初始化分页配置
		在分页前修改默认的配置
	*/
	function initialize($config)
	{
		foreach($config as $key => $val) {
			if(isset($this->set[$key])) {
				$this->set[$key] = $val;
			}
		}
		//if($this
	}

	/*
		获取查询开始数
		公开方法，用于获取当前页面查询中开始数
	*/
	function getStart($page='')
	{
		if(empty($page)) {
			$page = $this->set['curPage'];
		}
		$page = intval($page);
		$page = $page > 1 ? $page : 1;
		return ($page - 1) * $this->set['perPage'];
	}

	/*
		创建分页链接 HTML
		处理并返回链接以供输出
	*/
	function makeLinks()
	{
		$total = $this->set['totalRows'];
		$curPage = intval($this->set['curPage']);
		$perPage = $this->set['perPage'];
		$pageLinks = $this->set['pageLinks'];

		$multiPage = '';

		if($total > $perPage) {
			$pagesAll = ceil($total / $perPage);  //算出实际页数
			$curPage = $curPage < 1 ? 1 : ($curPage > $pagesAll ? $pagesAll : $curPage);  //当前页
			$halfLink = $pageLinks / 2;
			$halfDisplay = $halfLink % 2 == 0 ? floor($halfLink) : ceil($halfLink);  //单数时当前焦点在中间，双数时当前焦点在中间偏左

			$start = $curPage - $halfDisplay + 1;  //得出左边开始的偏移数，下面修正
			$start = $pagesAll - $start < $pageLinks ? $start - ($pageLinks - ($pagesAll - $start)) + 1 : $start;  //靠近接尾时修补前面页数
			$start = $start < 1 ? 1 : $start;

			$end = $start + $pageLinks - 1;  //得出到右边结束的页数
			//$end = ($curPage - 1) < $halfDisplay ? 1 + $halfDisplay * 2 : $end;
			$end = $end >= $pagesAll ? $pagesAll : $end;

			//生成每一页的链接
			for($i=$start; $i<=$end; $i++) {
				$multiPage .= ($i == $curPage) ? $this->set['curTagOpen'].$i.$this->set['curTagClose'] : $this->set['numTagOpen'].'<a href="'.$this->pageLink($i).'">'.$i.'</a>'.$this->set['numTagClose'];
			}

			//上一页
			if($curPage > 1) {
				$multiPage = $this->set['prevTagOpen'].'<a href="'.$this->pageLink($curPage - 1).'" class="prev">'.$this->set['prevLink'].'</a>'.$this->set['prevTagClose'].$multiPage;
			}

			//第一页
			if($curPage > 1 && $start > 1) {
				$multiPage = $this->set['firstTagOpen'].'<a href="'.$this->pageLink(1).'" class="first">'.$this->set['firstLink'].'</a>'.$this->set['firstTagClose'].$multiPage;
			}

			//下一页
			if($curPage < $pagesAll) {
				$multiPage .= $this->set['nextTagOpen'].'<a href="'.$this->pageLink($curPage + 1).'" class="next">'.$this->set['nextLink'].'</a>'.$this->set['nextTagClose'];
			}

			//最后一页
			if($curPage < $pagesAll && $end < $pagesAll) {
				$multiPage .=	$this->set['lastTagOpen'].'<a href="'.$this->pageLink($pagesAll).'" class="last">'.$this->set['lastLink'].'</a>'.$this->set['lastTagClose'];
			}

			$multiPage = !empty($multiPage) ? $this->set['fullTagOpen'].($this->set['showPageCount'] ? '<em>&nbsp;('.$pagesAll.')&nbsp;</em>' : '').$multiPage.$this->set['fullTagClose'] : '';
		}

		return $multiPage;
	}

	/*
		传递分页编号生成页面地址
	*/
	function pageLink($page)
	{
		return str_replace($this->set['pageNumReplace'],$page,$this->set['pageUrl']);
	}

}
