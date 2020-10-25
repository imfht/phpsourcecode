<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 * 本类只用在图片管理器中
 */
namespace oscshop;
class Pagination {
	public $total = 0;
	public $page = 1;
	public $limit = 20;
	public $num_links = 8;
	public $url = '';
	public $text_first = '|&lt;';
	public $text_last = '&gt;|';
	public $text_next = '&gt;';
	public $text_prev = '&lt;';

	public function render() {
		$total = $this->total;

		if ($this->page < 1) {
			$page = 1;
		} else {
			$page = $this->page;
		}

		if (!(int)$this->limit) {
			$limit = 10;
		} else {
			$limit = $this->limit;
		}

		$num_links = $this->num_links;
		$num_pages = ceil($total / $limit);

		$output = '<ul class="pagination">';

		if ($page > 1) {
			$output .= '<li><a href="' .$this->url .'/page/1'. '">' . $this->text_first . '</a></li>';
			if($page - 1 === 1){
				$output .= '<li><a href="' .$this->url .'/page/1'. '">' . $this->text_prev . '</a></li>';
			} else {
				$output .= '<li><a href="'.$this->url .'/page/'.($page - 1) . '">' . $this->text_prev . '</a></li>';
			}
		}

		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);

				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}

				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}

			for ($i = $start; $i <= $end; $i++) {
				if ($page == $i) {
					$output .= '<li class="active"><span>' . $i . '</span></li>';
				} else {if($i === 1){
					$output .= '<li><a href="' . $this->url .'/page/'.$i. '">' . $i . '</a></li>';
				} else {
					$output .= '<li><a href="' . $this->url  .'/page/'.$i.  '">' . $i . '</a></li>';
				}
				}
			}
		}

		if ($page < $num_pages) {
			$output .= '<li><a href="' . $this->url .'/page/'.($page + 1). '">' . $this->text_next . '</a></li>';
			$output .= '<li><a href="' . $this->url.'/page/'.$num_pages . '">' . $this->text_last . '</a></li>';
		}

		$output .= '</ul>';

		if ($num_pages > 1) {
			return $output;
		} else {
			return '';
		}
	}
}
