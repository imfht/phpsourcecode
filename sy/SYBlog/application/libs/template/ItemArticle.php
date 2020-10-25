<?php
namespace blog\libs\template;
use \sy\base\Router;
class ItemArticle extends \blog\libs\template\ItemBase {
	public function getDate($t) {
		return date($t, $this->sub['modify']);
	}
	public function date($t) {
		echo $this->getDate($t);
	}
	public function excerpt($num, $ext, $removeHtmlTag = TRUE, $return = FALSE) {
		if (!isset($this->sub['body'])) {
			return;
		}
		if ($removeHtmlTag) {
			$b = strip_tags($this->sub['body']);
		} else {
			$b = $this->sub['body'];
		}
		if (mb_strlen($b) > $num) {
			$result = mb_substr($b, 0, $num) . '&nbsp;' . $ext;
		} else {
			$result = $b;
		}
		if ($return) {
			return $result;
		} else {
			echo $result;
		}
	}
	public function url($show = TRUE) {
		if ($show) {
			echo Router::createUrl(['index/article/view', 'id' => $this->sub['id']]);
		} else {
			return Router::createUrl(['index/article/view', 'id' => $this->sub['id']]);
		}
	}
	public function body($option = NULL) {
		$body = $this->sub['body'];
		if (!is_array($option) && $option !== NULL) {
			echo $body;
		} else {
			if (isset($option['imgClass'])) {
				$body = str_replace('<img ', '<img class="' . $option['imgClass'] . '" ', $body);
			}
			if (isset($option['lazyload']) && $option['lazyload']) {
				$body = preg_replace('/<img(.*?)src="(.*?)"/', '<img$1data-' .$option['lazyloadMeta'] . '="$2" src="' . $option['lazyloadImage'] . '"', $body);
			}
			if (isset($option['addNofollow']) && $option['addNofollow']) {
				$body = preg_replace_callback('/<a(.*?)href="(.*?)"/', function($matches) {
					$domain = $_SERVER['HTTP_HOST'];
					$url = $matches[2];
					if (substr($url, 0, 1) === '#' || strpos($url, $domain) !== FALSE || (substr($url, 0, 7) !== 'http://' && substr($url, 0, 8) !== 'https://')) {
						return $matches[0];
					}
					return $matches[0] . ' rel="nofollow"';
				}, $body);
			}
			echo $body;
		}
	}
}