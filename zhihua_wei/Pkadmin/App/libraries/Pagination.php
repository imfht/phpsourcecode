<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/08 0035
 * Time: 上午 11:53
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: 分页类扩展
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 用于生成分页连接
 */
class CI_Pagination {

	//每次访问的url地址
	protected $base_url = '';
	//给路径添加一个自定义前缀，前缀位于偏移段的前面
	protected $prefix = '';
	//给路径添加一个自定义后缀，后缀位于偏移段的后面。
	protected $suffix = '';
	//这个数字表示你需要做分页的数据的总行数。通常这个数值是你查询数据库得到的数据总量。
	protected $total_rows = 0;
	//放在你当前页码的前面和后面的“数字”链接的数量。
	//比方说值为 2 就会在每一边放置两个数字链接，就像此页顶端的示例链接那样。
	protected $num_links = 2;
	//这个数字表示每个页面中希望展示的数量，在上面的那个例子中，每页显示 10 个项目。
	public $per_page = 10;
	//当前页
	public $cur_page = 0;
	//默认分页的 URL 中显示的是你当前正在从哪条记录开始分页，如果你希望显示实际的页数，将该参数设置为 TRUE 。
	protected $use_page_numbers = FALSE;
	//首页，左边第一个链接显示的文本，如果你不想显示该链接，将其设置为 FALSE 。
	protected $first_link = '&lsaquo; First';
	//下一页，下一页链接显示的文本，如果你不想显示该链接，将其设置为 FALSE 。
	protected $next_link = FALSE;
	//下一页，下一页链接显示的文本，如果你不想显示该链接，将其设置为 FALSE
	protected $prev_link = FALSE;
	//尾页，右边第一个链接显示的文本，如果你不想显示该链接，将其设置为 FALSE 。
	protected $last_link = 'Last &rsaquo;';
	//分页方法自动检测你 URI 的哪一段包含页数，如果你的情况不一样，你可以明确指定它
	protected $uri_segment = 4;
	//起始标签放在所有结果的左侧。
	protected $full_tag_open = '<ul class="pagination pagination-sm">';
	//结束标签放在所有结果的右侧。
	protected $full_tag_close = '</ul>';
	//第一个链接的起始标签。
	protected $first_tag_open = '<li>';
	//第一个链接的结束标签。
	protected $first_tag_close = '</li>';
	//最后一个链接的起始标签。
	protected $last_tag_open = '<li>';
	//最后一个链接的结束标签。
	protected $last_tag_close = '</li>';
	//首页url
	protected $first_url = '';
	//当前页链接的起始标签。
	protected $cur_tag_open = '<li class="active"><a href="javascript:;">';
	//当前页链接的结束标签。
	protected $cur_tag_close = '</a></li>';
	//下一页链接的起始标签。
	protected $next_tag_open = '<li>';
	//下一页链接的结束标签。
	protected $next_tag_close = '</li>';
	//上一页链接的起始标签。
	protected $prev_tag_open = '<li>';
	//上一页链接的结束标签。
	protected $prev_tag_close = '</li>';
	//数字链接的起始标签。
	protected $num_tag_open = '<li>';
	//数字链接的结束标签。
	protected $num_tag_close = '</li>';
	//默认情况下，分页类假设你使用 URI 段 ，并像这样构造你的链接:
	//http://example.com/index.php/test/page/20
	protected $page_query_string = FALSE;
	protected $query_string_segment = 'per_page';
	//如果你不想显示数字链接（例如你只想显示上一页和下一页链接），你可以通过下面的代码来阻止它显示
	protected $display_pages = TRUE;
	//如果你想为分页类生成的每个链接添加额外的属性
	protected $_attributes = '';
	//连接类型
	protected $_link_types = array();
	//默认情况下你的查询字符串参数会被忽略，将这个参数设置为 TRUE ，
	//将会将查询字符串参数添加到 URI 分段的后面以及 URL 后缀的前面
	protected $reuse_query_string = FALSE;
	//当该参数设置为 TRUE 时，会使用 application/config/config.php
	//配置文件中定义的 $config['url_suffix'] 参数 重写 $config['suffix'] 的值
	protected $use_global_url_suffix = FALSE;
	//给数字增加属性
	protected $data_page_attr = 'data-ci-pagination-page';
	//CI Singleton
	protected $CI;

	/**
	 * 构造函数
	 * 处理数据
	 */
	public function __construct($params = array()) {
		$this -> CI = &get_instance();
		$this -> CI -> load -> language('pagination');
		foreach (array('first_link', 'next_link', 'prev_link', 'last_link') as $key) {
			if (($val = $this -> CI -> lang -> line('pagination_' . $key)) !== FALSE) {
				$this -> $key = $val;
			}
		}
		$this -> initialize($params);
		log_message('info', 'Pagination Class Initialized');
	}

	/**
	 * 初始化
	 */
	public function initialize(array $params = array()) {
		isset($params['attributes']) OR $params['attributes'] = array();
		if (is_array($params['attributes'])) {
			$this -> _parse_attributes($params['attributes']);
			unset($params['attributes']);
		}

		if (isset($params['anchor_class'])) {
			empty($params['anchor_class']) OR $attributes['class'] = $params['anchor_class'];
			unset($params['anchor_class']);
		}

		foreach ($params as $key => $val) {
			if (property_exists($this, $key)) {
				$this -> $key = $val;
			}
		}

		if ($this -> CI -> config -> item('enable_query_strings') === TRUE) {
			$this -> page_query_string = TRUE;
		}

		if ($this -> use_global_url_suffix === TRUE) {
			$this -> suffix = $this -> CI -> config -> item('url_suffix');
		}

		return $this;
	}

	/**
	 * 创建分页连接
	 */
	public function create_links() {
		// If our item count or per-page total is zero there is no need to continue.
		// Note: DO NOT change the operator to === here!
		if ($this -> total_rows == 0 OR $this -> per_page == 0) {
			return '';
		}

		// Calculate the total number of pages
		$num_pages = (int) ceil($this -> total_rows / $this -> per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages === 1) {
			return '';
		}

		// Check the user defined number of links.
		$this -> num_links = (int)$this -> num_links;

		if ($this -> num_links < 0) {
			show_error('Your number of links must be a non-negative number.');
		}

		// Keep any existing query string items.
		// Note: Has nothing to do with any other query string option.
		if ($this -> reuse_query_string === TRUE) {
			$get = $this -> CI -> input -> get();

			// Unset the controll, method, old-school routing options
			unset($get['c'], $get['m'], $get[$this -> query_string_segment]);
		} else {
			$get = array();
		}

		// Put together our base and first URLs.
		// Note: DO NOT append to the properties as that would break successive calls
		$base_url = trim($this -> base_url);
		$first_url = $this -> first_url;

		$query_string = '';
		$query_string_sep = (strpos($base_url, '?') === FALSE) ? '?' : '&amp;';

		// Are we using query strings?
		if ($this -> page_query_string === TRUE) {
			// If a custom first_url hasn't been specified, we'll create one from
			// the base_url, but without the page item.
			if ($first_url === '') {
				$first_url = $base_url;

				// If we saved any GET items earlier, make sure they're appended.
				if (!empty($get)) {
					$first_url .= $query_string_sep . http_build_query($get);
				}
			}

			// Add the page segment to the end of the query string, where the
			// page number will be appended.
			$base_url .= $query_string_sep . http_build_query(array_merge($get, array($this -> query_string_segment => '')));
		} else {
			// Standard segment mode.
			// Generate our saved query string to append later after the page number.
			if (!empty($get)) {
				$query_string = $query_string_sep . http_build_query($get);
				$this -> suffix .= $query_string;
			}

			// Does the base_url have the query string in it?
			// If we're supposed to save it, remove it so we can append it later.
			if ($this -> reuse_query_string === TRUE && ($base_query_pos = strpos($base_url, '?')) !== FALSE) {
				$base_url = substr($base_url, 0, $base_query_pos);
			}

			if ($first_url === '') {
				$first_url = $base_url . $query_string;
			}

			$base_url = rtrim($base_url, '/') . '/';
		}

		// Determine the current page number.
		$base_page = ($this -> use_page_numbers) ? 1 : 0;

		// Are we using query strings?
		if ($this -> page_query_string === TRUE) {
			$this -> cur_page = $this -> CI -> input -> get($this -> query_string_segment);
		} elseif (empty($this -> cur_page)) {
			// Default to the last segment number if one hasn't been defined.
			if ($this -> uri_segment === 0) {
				$this -> uri_segment = count($this -> CI -> uri -> segment_array());
			}

			$this -> cur_page = $this -> CI -> uri -> segment($this -> uri_segment);

			// Remove any specified prefix/suffix from the segment.
			if ($this -> prefix !== '' OR $this -> suffix !== '') {
				$this -> cur_page = str_replace(array($this -> prefix, $this -> suffix), '', $this -> cur_page);
			}
		} else {
			$this -> cur_page = (string)$this -> cur_page;
		}

		// If something isn't quite right, back to the default base page.
		if (!ctype_digit($this -> cur_page) OR ($this -> use_page_numbers && (int)$this -> cur_page === 0)) {
			$this -> cur_page = $base_page;
		} else {
			// Make sure we're using integers for comparisons later.
			$this -> cur_page = (int)$this -> cur_page;
		}

		// Is the page number beyond the result range?
		// If so, we show the last page.
		if ($this -> use_page_numbers) {
			if ($this -> cur_page > $num_pages) {
				$this -> cur_page = $num_pages;
			}
		} elseif ($this -> cur_page > $this -> total_rows) {
			$this -> cur_page = ($num_pages - 1) * $this -> per_page;
		}

		$uri_page_number = $this -> cur_page;

		// If we're using offset instead of page numbers, convert it
		// to a page number, so we can generate the surrounding number links.
		if (!$this -> use_page_numbers) {
			$this -> cur_page = (int) floor(($this -> cur_page / $this -> per_page) + 1);
		}

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with.
		$start = (($this -> cur_page - $this -> num_links) > 0) ? $this -> cur_page - ($this -> num_links - 1) : 1;
		$end = (($this -> cur_page + $this -> num_links) < $num_pages) ? $this -> cur_page + $this -> num_links : $num_pages;

		// And here we go...
		$output = '';

		// Render the "First" link.
		// 直接将此处的 $this -> first_link 赋值为 "«"；
		$this -> first_link = "«";
		if ($this -> first_link !== FALSE && $this -> cur_page > ($this -> num_links + 1 + !$this -> num_links)) {
			// Take the general parameters, and squeeze this pagination-page attr in for JS frameworks.
			$attributes = sprintf('%s %s="%d"', $this -> _attributes, $this -> data_page_attr, 1);

			$output .= $this -> first_tag_open . '<a href="' . $first_url . '"' . $attributes . $this -> _attr_rel('start') . '>' . $this -> first_link . '</a>' . $this -> first_tag_close;
		}

		// Render the "Previous" link.
		// 将生成上一页的连接注视掉，无用！！！
		//		if ($this -> prev_link !== FALSE && $this -> cur_page !== 1) {
		//			$i = ($this -> use_page_numbers) ? $uri_page_number - 1 : $uri_page_number - $this -> per_page;
		//
		//			$attributes = sprintf('%s %s="%d"', $this -> _attributes, $this -> data_page_attr, ($this -> cur_page - 1));
		//
		//			if ($i === $base_page) {
		//				// First page
		//				$output .= $this -> prev_tag_open . '<a href="' . $first_url . '"' . $attributes . $this -> _attr_rel('prev') . '>' . $this -> prev_link . '</a>' . $this -> prev_tag_close;
		//			} else {
		//				$append = $this -> prefix . $i . $this -> suffix;
		//				$output .= $this -> prev_tag_open . '<a href="' . $base_url . $append . '"' . $attributes . $this -> _attr_rel('prev') . '>' . $this -> prev_link . '</a>' . $this -> prev_tag_close;
		//			}
		//
		//		}

		// Render the pages
		if ($this -> display_pages !== FALSE) {
			// Write the digit links
			for ($loop = $start - 1; $loop <= $end; $loop++) {
				$i = ($this -> use_page_numbers) ? $loop : ($loop * $this -> per_page) - $this -> per_page;

				$attributes = sprintf('%s %s="%d"', $this -> _attributes, $this -> data_page_attr, $loop);

				if ($i >= $base_page) {
					if ($this -> cur_page === $loop) {
						// Current page
						$output .= $this -> cur_tag_open . $loop . $this -> cur_tag_close;
					} elseif ($i === $base_page) {
						// First page
						$output .= $this -> num_tag_open . '<a href="' . $first_url . '"' . $attributes . $this -> _attr_rel('start') . '>' . $loop . '</a>' . $this -> num_tag_close;
					} else {
						$append = $this -> prefix . $i . $this -> suffix;
						$output .= $this -> num_tag_open . '<a href="' . $base_url . $append . '"' . $attributes . '>' . $loop . '</a>' . $this -> num_tag_close;
					}
				}
			}
		}

		// Render the "next" link
		// 将生成下一页的连接注视掉，无用！！！
		//		if ($this -> next_link !== FALSE && $this -> cur_page < $num_pages) {
		//			$i = ($this -> use_page_numbers) ? $this -> cur_page + 1 : $this -> cur_page * $this -> per_page;
		//
		//			$attributes = sprintf('%s %s="%d"', $this -> _attributes, $this -> data_page_attr, $this -> cur_page + 1);
		//
		//			$output .= $this -> next_tag_open . '<a href="' . $base_url . $this -> prefix . $i . $this -> suffix . '"' . $attributes . $this -> _attr_rel('next') . '>' . $this -> next_link . '</a>' . $this -> next_tag_close;
		//		}

		// Render the "Last" link
		// 直接将此处的 $this -> last_link 赋值为 "»"；
		$this -> last_link = "»";
		if ($this -> last_link !== FALSE && ($this -> cur_page + $this -> num_links + !$this -> num_links) < $num_pages) {
			$i = ($this -> use_page_numbers) ? $num_pages : ($num_pages * $this -> per_page) - $this -> per_page;

			$attributes = sprintf('%s %s="%d"', $this -> _attributes, $this -> data_page_attr, $num_pages);

			$output .= $this -> last_tag_open . '<a href="' . $base_url . $this -> prefix . $i . $this -> suffix . '"' . $attributes . '>' . $this -> last_link . '</a>' . $this -> last_tag_close;
		}

		// Kill double slashes. Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace('#([^:"])//+#', '\\1/', $output);

		// Add the wrapper HTML if exists
		return $this -> full_tag_open . $output . $this -> full_tag_close;
	}

	// --------------------------------------------------------------------

	/**
	 * Parse attributes
	 *
	 * @param	array	$attributes
	 * @return	void
	 */
	protected function _parse_attributes($attributes) {
		isset($attributes['rel']) OR $attributes['rel'] = TRUE;
		$this -> _link_types = ($attributes['rel']) ? array('start' => 'start', 'prev' => 'prev', 'next' => 'next') : array();
		unset($attributes['rel']);

		$this -> _attributes = '';
		foreach ($attributes as $key => $value) {
			$this -> _attributes .= ' ' . $key . '="' . $value . '"';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add "rel" attribute
	 *
	 * @link	http://www.w3.org/TR/html5/links.html#linkTypes
	 * @param	string	$type
	 * @return	string
	 */
	protected function _attr_rel($type) {
		if (isset($this -> _link_types[$type])) {
			unset($this -> _link_types[$type]);
			return ' rel="' . $type . '"';
		}

		return '';
	}

}
