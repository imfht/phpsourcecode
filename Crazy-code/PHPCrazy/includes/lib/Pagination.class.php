<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

/**
* 	分页控制类
*/
class Pagination
{

	var $HTML = array();// 分页模版

	var $php_self;
	var $Urlsuffix = '';

	var $Per;
	var $Start;
	var $Total;

	var $TotalPages;
	var $OnPage;
	var $MaxNumberList = 3;

	/**
	 * 创建分页
	 * 
	 * @权限 公共
	 * @参数 字符串 $Urlsuffix Url附加参数
	 * @参数 整数 $Total 所有记录的统计值
	 * @参数 整数 $Start 数据读取起始值
	 * @参数 整数 $Per 每页显示多少条记录
	 */
	public function __construct($Urlsuffix, $Total, $Start, $Per) {
		
		// 取得当前页面地址
		$this->php_self = htmlspecialchars($_SERVER['PHP_SELF'] );

		// URL参数
		$this->Urlsuffix = $Urlsuffix;

		// 记录总数
		$this->Total = abs(intval($Total));

		// 开始行数
		$this->Start = abs(intval($Start));

		// 显示多少行
		$this->Per = abs(intval($Per));

		// 计算出有多少页
		$this->TotalPages = ceil($Total / $Per);

		// 正在打开的页面
		$this->OnPage = floor($Start / $Per) + 1;

		// 分页模板
		$PaginationTpl = defined('IN_ADMIN') ? ADMIN_ROOT_PATH.DIRECTORY_SEPARATOR.'theme'.DIRECTORY_SEPARATOR.'pagination.tpl.php' : ThemePath(true) . 'pagination.tpl.php';

		// 检查分页模板是否存在
		if (!file_exists($PaginationTpl)) {
			
			throw new Exception(sprintf(L('模版 文件 不存在')));
		}

		$html = fread(fopen($PaginationTpl, 'r'), filesize($PaginationTpl));
		$html = str_replace('\\', '\\\\', $html);
		$html = str_replace('\'', '\\\'', $html);
		$html = str_replace("\n", '', $html);		
		
		preg_match_all('#<!-- BEGIN ([0-9a-zA-Z]*?) -->(.*?)<!-- END ([0-9a-zA-Z]*?) -->#', $html, $HTMLArr);

		$eval_str = '';

		foreach ($HTMLArr[1] as $key => $value) {
			
			$eval_str .= "\n" . '$this->HTML[\'' . $value . '\'] = \'' . $HTMLArr[2][$key] .' \';';

		}

		eval($eval_str);

	}

	/**
	 * 显示分页
	 *
	 * @权限 公共
	 * @返回 分页大于1则返回分页内容，否则返回空
	 */
	public function Box() {

		if ($this->TotalPages <= 0) {

			return '';
		}


		$BoxHtml = str_replace('{FIRST}', $this->First(L('首页')), $this->HTML['Box']);

		$BoxHtml = str_replace('{PREV}', $this->Prev(L('上页')), $BoxHtml);

		$BoxHtml = str_replace('{INFO}', $this->INFO(), $BoxHtml);

		$BoxHtml = str_replace('{SELECT}', $this->Select(), $BoxHtml);

		$BoxHtml = str_replace('{NEXT}', $this->Next(L('下页')), $BoxHtml);

		$BoxHtml = str_replace('{LAST}', $this->Last(L('末页')), $BoxHtml);

		$BoxHtml = str_replace('{NUMBER}', $this->Number(), $BoxHtml);

		return $BoxHtml;

	}

	/**
	 * [首页] 按钮
	 *
	 * @权限 私有
	 * @参数 $Tag 字符串 [首页]的标签
	 * @返回 分页大于1则返回分页内容，否则返回空
	 */
	private function First($Tag) {

		if ($this->TotalPages <= 0) {

			return '';
		}

		if ($this->OnPage == 1) {
			
			$URL = '#';

			$First = str_replace('{URL}', $URL, $this->HTML['First']);

			return str_replace('{TAG}', $Tag, $First);

		} else {

			$URL = $this->php_self . '?start=0&' . $this->Urlsuffix;

			$First = str_replace('{URL}', $URL, $this->HTML['First']);

			return str_replace('{TAG}', $Tag, $First);

		}

	}

	/**
	 * [下页] 按钮
	 *
	 * @权限 私有
	 * @参数 $Tag 字符串 [下页]的标签
	 * @返回 分页大于1则返回分页内容，否则返回空
	 */
	private function Next($Tag) {

		if ($this->TotalPages <= 0) {

			return '';
		}

		
		if ( $this->OnPage == 1 || ($this->OnPage > 1  && $this->OnPage < $this->TotalPages)) {
			
			$URL = $this->php_self . '?start=' . ($this->OnPage * $this->Per) . '&' . $this->Urlsuffix;

			$Next = str_replace('{URL}', $URL, $this->HTML['Next']);

			return str_replace('{TAG}', $Tag, $Next);
		
		} else {

			$URL = '#';

			$Next = str_replace('{URL}', $URL, $this->HTML['Next']);

			return str_replace('{TAG}', $Tag, $Next);

		}

	}

	private function Prev($Tag) {
		
		if ($this->TotalPages <= 0) {

			return '';
		}

		if ($this->OnPage > 1) {

			$URL = $this->php_self . '?start=' . ( ( $this->OnPage - 2 ) * $this->Per ) . '&' . $this->Urlsuffix;

			$Prev = str_replace('{URL}', $URL, $this->HTML['Prev']);

			return str_replace('{TAG}', $Tag, $Prev);
			

		} else {

			$URL = '#';

			$Prev = str_replace('{URL}', $URL, $this->HTML['Prev']);

			return str_replace('{TAG}', $Tag, $Prev);
		}
	}

	private function Last($Tag) {

		if ($this->TotalPages <= 0) {

			return '';
		}

		if ($this->OnPage == $this->TotalPages) {

			$URL = '#';

			$Last = str_replace('{URL}', $URL, $this->HTML['Last']);

			return str_replace('{TAG}', $Tag, $Last);

		} else {

			$URL = $this->php_self . '?start=' . (($this->TotalPages - 1) * $this->Per) . '&' . $this->Urlsuffix;

			$Last = str_replace('{URL}', $URL, $this->HTML['Last']);

			return str_replace('{TAG}', $Tag, $Last);

		}

	}

	private function INFO() {


		$Info = str_replace('{ONPAGE}', $this->OnPage, $this->HTML['Info']);

		return str_replace('{TOTAL}', $this->TotalPages, $Info);

	}

	/**
	 *
	 *
	 */
	function Number() {

		$page_str = '';

		// 如果统计页面大于 MaxNumberList
		if ($this->TotalPages > $this->MaxNumberList) {
			
			// 左边
			// 显示 1,...,5,6,7,[8]
			if (($this->OnPage - $this->MaxNumberList) > 1) {
				
				$_str = str_replace('{URL}', $this->php_self . '?start=' . $this->Start(1) . '&' . $this->Urlsuffix, $this->HTML['Number']);
				$_str = str_replace('{TAG}', 1, $_str);
				$page_str .= $_str;

				$_str = str_replace('{TAG}', '...', $this->HTML['Ellipsis']);
				$page_str .= $_str;

				$left = $this->OnPage - $this->MaxNumberList;

				for ($i = $left; $i < $this->OnPage; $i++) { 

					if ($i == $this->OnPage) {

						$_str = str_replace('{TAG}', $i, $this->HTML['SelectedNumber']);
						$page_str .= $_str;

					} else {
						
						$_str = str_replace('{URL}', $this->php_self.'?start='.$this->Start($i).'&'.$this->Urlsuffix, $this->HTML['Number']);
						$_str = str_replace('{TAG}', $i, $_str);
						$page_str .= $_str;
					}
				}

			} else {

				// 显示 1,[2],3
				for ($i = 1; $i < $this->OnPage ; $i++) { 
					
					if ($i == $this->OnPage) {

						$_str = str_replace('{TAG}', $i, $this->HTML['SelectedNumber']);
						$page_str .= $_str;

					} else {

						$_str = str_replace('{URL}', $this->php_self.'?start='.$this->Start($i).'&'.$this->Urlsuffix, $this->HTML['Number']);
						$_str = str_replace('{TAG}', $i, $_str);
						$page_str .= $_str;

					}
				}
			}

			// 右边
			if (($this->OnPage + $this->MaxNumberList) < $this->TotalPages) {
				
				$right = $this->OnPage + $this->MaxNumberList;

				// 显示 [8],9,10,11,...,99
				for ($i = $this->OnPage; $i <= $right; $i++) { 
					
					if ($i == $this->OnPage) {
						
						$_str = str_replace('{TAG}', $i, $this->HTML['SelectedNumber']);
						$page_str .= $_str;

					} else {

						$_str = str_replace('{URL}', $this->php_self.'?start='.$this->Start($i).'&'.$this->Urlsuffix, $this->HTML['Number']);
						$_str = str_replace('{TAG}', $i, $_str);
						$page_str .= $_str;

					}
				}

				$_str = str_replace('{TAG}', '...', $this->HTML['Ellipsis']);
				$page_str .= $_str;

				$_str = str_replace('{URL}', $this->php_self . '?start=' . $this->Start($this->TotalPages) . '&' . $this->Urlsuffix, $this->HTML['Number']);
				$_str = str_replace('{TAG}', $this->TotalPages, $_str);
				$page_str .= $_str;

			} else {

				// 显示 [8],9,10
				for ($i = $this->OnPage; $i <= $this->TotalPages ; $i++) { 
					
					if ($i == $this->OnPage) {
						
						$_str = str_replace('{TAG}', $i, $this->HTML['SelectedNumber']);
						$page_str .= $_str;

					} else {

						$_str = str_replace('{URL}', $this->php_self.'?start='.$this->Start($i).'&'.$this->Urlsuffix, $this->HTML['Number']);
						$_str = str_replace('{TAG}', $i, $_str);
						$page_str .= $_str;

					}
				}
			}


		} else {

			// 如果统计页面小于 MaxNumberList
			// 显示 1,[2],3
			for ($i = 1; $i <= $this->TotalPages ; $i++) { 
				
				if ($i == $this->OnPage) {

					$_str = str_replace('{TAG}', $i, $this->HTML['SelectedNumber']);
					$page_str .= $_str;

				} else {

					$_str = str_replace('{URL}', $this->php_self.'?start='.$this->Start($i).'&'.$this->Urlsuffix, $this->HTML['Number']);
					$_str = str_replace('{TAG}', $i, $_str);
					$page_str .= $_str;
				}

				
			}

		}

		return $page_str;
	}

	/**
	 *	计算当前页数的读取起始值
	 *
	 *	@参数 整数 $pages 某页
	 *
	 *	(某页 - 1) x 读取行数 = 读取起始值
	 *	说明：这对第一页无效
	 */
	private function Start($pages) {

		return ($pages - 1) * $this->Per;

	}

	private function Select() {

		$Select_option = '';

		for ($i = 1; $i <= $this->TotalPages; $i++) { 
				
			$selected = ($this->OnPage == $i) ? ' selected="selected"' : '';

			$Select_option .= '<option value="' . $this->php_self . '?start=' . $this->Start($i) . '&' . $this->Urlsuffix . '"'. $selected.'>' . $i . ' / ' . $this->TotalPages .'</option>';
			
		}

		return str_replace('{OPTION}', $Select_option, $this->HTML['Select']);

	}

}



?>