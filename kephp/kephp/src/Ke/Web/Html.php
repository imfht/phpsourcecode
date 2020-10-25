<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Web;

use Ke\Adm\Model;
use Ke\Adm\Pagination;
use Ke\Uri;

/**
 * 第二次重构Html构造器
 *
 * 1. 使用数组的配置，并不方便于后续继承的类重载属性
 * 2. preTag的方法，容易造成混乱的情况。
 * 3. 不再区分mk的方法，所有构建的方法，都直接返回字符串，不再直接输出
 */
class Html
{
	
	const DATA_ARRAY = 0;
	const DATA_ARRAY_ACCESS = 1;
	const DATA_GENERATOR = 2;
	
	const HORIZONTAL = 0;
	const VERTICAL = 1;
	
	const TABLE_HEAD_FROM_COLUMNS = 'columns';
	const TABLE_HEAD_FROM_DATA = 'data';
	
	const MSG_DEFAULT = 'default'; // 默认的，正常、普通的消息
	const MSG_SUCCESS = 'success'; // 成功，通过，很好
	const MSG_NOTICE = 'notice'; // 提示、通知，可忽略
	const MSG_WARN = 'warning'; // 警告，但不中断，不致命
	const MSG_ERROR = 'error'; // 错误，中断，致命
	
	const PAGE_FIRST = 'pageFirst';
	const PAGE_LAST = 'pageLast';
	const PAGE_PREV = 'pagePrev';
	const PAGE_NEXT = 'pageNext';
	const PAGE_CUR = 'pageCurrent';
	const PAGE_TOTAL = 'pageTotal';
	const PAGE_ITEM = 'pageLink';
	const PAGE_ELLIPSIS = 'pageEllipsis';
	const PAGE_GOTO = 'pageGoto';
	const PAGE_ROW = 'pageRow';
	
	const SUBMIT = 'submit';
	const RESET = 'reset';
	const RETURN = 'return';
	
	const TABLE_EMPTY = 'tableEmpty';
	const TABLE_HEAD_INVALID = 'tableHeadInvalid';
	const TABLE_HEAD_EMPTY = 'tableHeadEmpty';
	const TABLE_HEAD_TAIL = 'tableHeadTail';
	
	const TEXT_NONE = 'none';
	
	/** @var \DOMDocument */
	private $DOM = null;
	
	private $autoId = 0;
	
	private static $instances = [];
	
	protected $xhtmlStyle = true;
	
	/** @var array 属性定义 */
	protected $attributes = [
		'readonly'    => ['type' => 'bool'],
		'disabled'    => ['type' => 'bool'],
		'checked'     => ['type' => 'bool'],
		'selected'    => ['type' => 'bool'],
		'required'    => ['type' => 'bool'],
		'multiple'    => ['type' => 'bool'],
		'src'         => ['type' => 'link'],
		'href'        => ['type' => 'link'],
		'action'      => ['type' => 'link'],
		'data-url'    => ['type' => 'link'],
		'data-href'   => ['type' => 'link'],
		'data-src'    => ['type' => 'link'],
		'data-ref'    => ['type' => 'link'],
		'data-action' => ['type' => 'link'],
	];
	
	/** @var array 闭合标签 */
	protected $closingTags = [
		'meta'     => true,
		'link'     => true,
		'hr'       => true,
		'br'       => true,
		'img'      => true,
		'input'    => true,
		'area'     => true,
		'embed'    => true,
		'keygen'   => true,
		'source'   => true,
		'base'     => true,
		'col'      => true,
		'param'    => true,
		'basefont' => true,
		'frame'    => true,
		'isindex'  => true,
		'wbr'      => true,
		'command'  => true,
		'track'    => true,
	];
	
	protected $texts = [
		self::TABLE_EMPTY        => '没有任何数据！',
		self::TABLE_HEAD_INVALID => '输入了无效的表(Table)数据！',
		self::TABLE_HEAD_EMPTY   => '表头(Table Head)字段为空！',
		self::TABLE_HEAD_TAIL    => '操作',
		self::PAGE_FIRST         => '首页',
		self::PAGE_LAST          => '末页',
		self::PAGE_PREV          => '上一页',
		self::PAGE_NEXT          => '下一页',
		self::PAGE_CUR           => '第 %s 页',
		self::PAGE_TOTAL         => '共 %s 页',
		self::PAGE_ITEM          => '%d',
		self::PAGE_ELLIPSIS      => '...',
		self::PAGE_GOTO          => '跳转',
		self::SUBMIT             => '提交',
		self::RESET              => '重置',
		self::RETURN             => '返回',
		self::TEXT_NONE          => '无',
	];
	
	public $labelColon = '';
	
	// 分页的设置
	public $pageLinks = 6;
	public $pageFirstLast = false;
	public $pagePrevNext = true;
	public $pageGoto = false; // 'input', 'select'
	public $pageRow = '{first}{prev}{links}{next}{last} {current} / {total} {button}';
	
	public $tagPageWrap = 'div';
	public $tagPageLink = 'a';
	public $tagPageSpan = 'span';
	public $tagPageButton = 'button';
	public $tagPageLinks = 'div';
	
	public $classPageWrap = 'page-wrap';
	public $classPageSpan = 'page-span';
	public $classPageLink = 'page-link';
	public $classPageLinks = 'page-links';
	public $classPageButton = 'page-button';
	public $classPageSpanEllipsis = 'page-span page-ellipsis';
	public $classPageSpanInput = 'page-span page-input';
	public $classPageSpanActive = 'page-span page-current';
	
	public $tagFieldSet = 'div';
	public $tagFieldSetLegend = 'div';
	public $tagFieldSetContent = 'div';
	public $tagFieldSetError = 'div';
	public $tagFieldSetTip = 'div';
	public $tagInputWrap = 'div';
	
	public $tagFormStatic = 'div';
	
	public $tagLabelInline = 'label';
	public $classLabelInline = 'label-inline';
	
	public $classFieldSet = 'field';
	public $classFieldSetLegend = 'field-legend';
	public $classFieldSetContent = 'field-content';
	public $classFieldSetError = 'error';
	public $classFieldSetTip = 'field-tip';
	
	public $classForm = 'form';
	
	public $classInput = 'input';
	public $classTextarea = 'input textarea';
	public $classSelect = 'input select';
	public $classButton = 'button';
	public $classButtons = 'buttons';
	
	public $tagButtonLink = 'a';
	public $tagButtons = 'div';
	
	// message
	public $tagMessage = 'div';
	public $classMessage = 'message';
//	public $tagFieldset = 'p';
//	public $classFieldset = 'class12';
	
	public $classTableList = 'table-list';
	
	public $buttonsDelimiter = '';
	
	/**
	 * 取得指定名称的Html构造器实例
	 *
	 * @param string|null $name
	 *
	 * @return Html
	 */
	public static function getInstance(string $name = null)
	{
		if (!isset(self::$instances[$name]))
			self::$instances[$name] = new static();
		return self::$instances[$name];
	}
	
	public function isClosingTag(string $tag): bool
	{
		return !empty($this->closingTags[$tag]);
	}
	
	public function getClosingTags(): array
	{
		return array_keys($this->closingTags);
	}
	
	public function setClosingTag(string $tag, bool $isClosing)
	{
		$this->closingTags[$tag] = $isClosing;
		return $this;
	}
	
	public function setXhtmlStyle(bool $isXhtmlStyle)
	{
		$this->xhtmlStyle = $isXhtmlStyle;
		return $this;
	}
	
	public function isXhtmlStyle(): bool
	{
		return $this->xhtmlStyle;
	}
	
	public function defineAttributes(array $attributes)
	{
		foreach ($attributes as $attr => $setting) {
			$type = gettype($setting);
			if ($type === KE_STR) {
				$type = KE_ARY;
				$setting = ['type' => $setting];
			} else if ($type === KE_OBJ) {
				$type = KE_ARY;
				$setting = (array)$setting;
			}
			if ($type !== KE_ARY)
				continue;
			if (!isset($this->attributes[$attr]))
				$this->attributes[$attr] = $setting;
			else
				$this->attributes[$attr] = array_merge($this->attributes[$attr], $setting);
		}
		return $this;
	}
	
	public function getText($key)
	{
		if (isset($this->texts[$key]))
			return $this->texts[$key];
		if (is_string($key))
			return $key;
		return false;
	}
	
	public function setText($key, string $text)
	{
		$this->texts[$key] = $text;
		return $this;
	}
	
	public function getTexts(): array
	{
		return $this->texts;
	}
	
	public function setTexts(array $texts)
	{
		$this->texts = $texts + $this->texts;
		return $this;
	}
	
	public function autoId(string $prefix): string
	{
		return $prefix . '_auto_id_' . (++$this->autoId);
	}
	
	public function parseAttrByPreg(string $attr): array
	{
		$attr = trim($attr);
		if (empty($attr))
			return [];
		$result = [];
		// backup
		// ([^\+\s\=]+)(?:=["']?(?:.(?!["']?\s+(?:\S+)=|[>"']))+.["']?)?
		// ([^\s\=\+]+)(?:=(([\"\'])([^\3]*(?=\\\3)*.*)\3))?
		// last version
		// ([^\+\s\=]+)(?:\=[\"']([^\"]*)[\"']\s?)?
		$regex = '#([^\+\s\=]+)(?:\=([\"\'])([^\"\']*)\2|\=([^\"\'\s]*))?#';
		// attr-a="ab" attr-b=bb cc
		// ['attr-a' => 'ab', 'attr-b' => 'bb', 'cc' => '']
		// 如果需要将bb cc放入attr-b，需要：attr-a="ab" attr-b="bb cc"
		if (preg_match_all($regex, $attr, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$result[$match[1]] = $match[3] ?? '';
			}
		}
		return $result;
	}
	
	/**
	 * @return \DOMDocument
	 */
	public function getDOM()
	{
		if (!isset($this->DOM))
			$this->DOM = new \DOMDocument();
		return $this->DOM;
	}
	
	public function parseAttrByDOM(string $attr): array
	{
		$attr = trim($attr);
		if (empty($attr))
			return [];
		$data = [];
		$dom = $this->getDOM();
		$dom->loadHTML("<div {$attr}>");
		$els = $dom->getElementsByTagName('div');
		if (isset($els[0])) {
			/** @var \DOMAttr $item */
			foreach ($els[0]->attributes as $item) {
				$data[$item->name] = $item->value;
			}
		}
		return $data;
	}
	
	public function parseAttr(string $attr): array
	{
		$attr = trim($attr);
		if (empty($attr))
			return [];
		else if ($attr[0] === '?') {
			parse_str(substr($attr, 1), $result);
			return $result;
		} else {
			return $this->parseAttrByPreg($attr);
		}
	}
	
	public function specialAttr($name, ...$segments): string
	{
		if (empty($segments))
			return '';
		if ($name === 'name') {
			$result = '';
			foreach ($segments as $segment) {
				$segment = trim($segment);
				if (empty($result)) {
					// 确保先保证name的第一节不能为空, any[]，这样是可以的，但是[any]这样是不行的
					if (!empty($segment))
						$result = $segment;
					continue;
				}
				$result .= "[{$segment}]";
			}
			return $result;
		}
		return implode(($name === 'class' ? '-' : '_'), $segments);
	}
	
	public function id(array $segments)
	{
		return $this->specialAttr('id', ...$segments);
	}
	
	public function name(array $segments)
	{
		return $this->specialAttr('name', ...$segments);
	}
	
	public function filterAttr(string $name, $value, array $attr)
	{
		$name = trim($name);
		if (empty($name) || is_numeric($name)) // 空字符串，0，数字，都不要
			return false;
		if ($this->xhtmlStyle) // 强制转小写
			$name = strtolower($name);
		$setting = $this->attributes[$name] ?? [];
		if (isset($setting['type'])) {
			if ($setting['type'] === 'bool') {
				if (empty($value))
					return false;
				return [$name, $name];
			} else if ($setting['type'] === 'link') {
				return [$name, $this->filterLink($value)];
			}
		}
		$type = gettype($value);
		if ($type === KE_STR)
			$type = trim(KE_STR);
		// 这两个比较特殊
		if ($name === 'id' || $name === 'name') {
			if (empty($value))
				return false;
			else if ($type === KE_ARY) {
				// todo: 数组的话，自动构建特定的属性
				return [$name, $this->specialAttr($name, ...$value)];
			} else if ($type === KE_STR)
				return [$name, $value];
			else
				return false;
		} else if ($name === 'class') {
			if (empty($value))
				return false;
			else if ($type === KE_ARY) {
				$value = $this->filterClass($value);
				if (!empty($value))
					return [$name, implode(' ', $value)];
				else
					return false;
			} else if ($type === KE_STR)
				return [$name, $value]; // 字符串，不再过滤了，直接输出
			else
				return false;
		} else {
			if (strlen($value) === 0) {
				return [$name, ''];
			} else if ($type === KE_ARY || $type === KE_OBJ)
				return false;
			else if ($type === KE_STR)
				return [$name, htmlentities($value)];
			else
				return [$name, (string)$value];
		}
	}
	
	public function filterLink($link)
	{
		$web = Web::getWeb();
		$type = gettype($link);
		if ($type === KE_ARY) {
			// 数组分两种情况：
			if (isset($link[0])) {
				// ['uri', 'id' => '1', 'name' => 'jan']
				$first = array_shift($link);
				// 这里还得细分，如果传入的是#开头，如：'#hello'，尝试理解为当前的uri转换
				if (is_string($first) && isset($first[0]) && $first[0] === '#') {
					return $web->http->newUri($first, $link);
				}
				return $web->uri($first, $link);
			} else {
				// 如果没有指定0的路径，尝试理解为，基于当前的uri，并且设置查询字符串
				return $web->http->newUri()->setQuery($link);
			}
		} else if ($type === KE_OBJ) {
			// 对象
			if (!($link instanceof Uri))
				$link = new Uri($link);
			return $link;
		} else {
			$link = (string)$link;
			if ($link === '#')
				return $link;
			if (isset($link[0]) && $link[0] === '#') {
				return $web->http->newUri($link);
			}
			return $web->uri($link);
		}
	}
	
	public function filterClass($class, array &$result = []): array
	{
		$type = gettype($class);
		if ($type === KE_STR) {
			$class = trim($class);
			if (strpos($class, ' ') > 0) {
				$class = explode(' ', $class);
				$result = array_merge($result, array_flip(array_filter($class)));
			} else {
				$result[$class] = 1;
			}
		} else if ($type === KE_ARY || $type === KE_OBJ) {
			if ($type === KE_OBJ)
				$class = (array)$class;
			array_walk_recursive($class, function ($item) use (&$result) {
				$item = trim($item);
				if (!empty($item)) {
					$this->filterClass($item, $result);
				}
			});
		} else {
			return [];
		}
		return array_keys($result);
	}
	
	public function joinClass(...$classes): string
	{
		if (!empty($classes))
			return implode(' ', $this->filterClass($classes));
		return '';
	}
	
	public function attrClass(...$classes)
	{
		$classes = $this->filterClass($classes);
		if (!empty($classes))
			return ' class="' . implode(' ', $classes) . '"';
		return '';
	}
	
	public function addClass(array &$attr, $class): array
	{
		if (empty($class))
			return $attr;
		if (isset($attr['class'])) {
			if (is_array($attr['class']))
				$attr['class'][] = $class;
			else
				$attr['class'] = [$attr['class'], $class];
		} else
			$attr['class'] = $class;
		return $attr;
	}
	
	public function unShiftClass(array &$attr, $class): array
	{
		if (empty($class))
			return $attr;
		if (isset($attr['class'])) {
			if (is_array($attr['class']))
				array_unshift($attr['class'], $class);
			else
				$attr['class'] = [$class, $attr['class']];
		} else
			$attr['class'] = $class;
		return $attr;
	}
	
	public function attr2array($attr): array
	{
		$type = gettype($attr);
		if ($type === KE_STR) {
			// 解析字符串，就没有打扁不打扁的问题了
			$attr = ['class' => $attr];
		} else if ($type === KE_OBJ) {
			$attr = get_object_vars($attr);
		} else if ($type !== KE_ARY) {
			$attr = [];
		}
		return $attr;
	}
	
	public function attr($attr, array $merges = null): string
	{
		// 不再支持多层的数组传入，除了特定一些属性以外，但会将整个数组传递过去，不会再循环递进的生成属性名
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		// 去掉原来的mergeAttr方法，尽量节省，不要轻易的去调用方法，而交给更细节的控制。
		if (!empty($merges)) {
			if (isset($merges['class'])) {
				$this->addClass($attr, $merges['class']);
				unset($merges['class']);
			}
			if (!empty($merges))
				$attr = array_merge($attr, $merges);
		}
		$result = '';
		foreach ($attr as $name => $value) {
			if (($segment = $this->filterAttr($name, $value, $attr)) === false)
				continue;
			$result .= ' ' . $segment[0];
			if ($segment[1] === $segment[0]) {
				if ($this->xhtmlStyle)
					$result .= '="' . $segment[0] . '"';
				continue;
			}
			$result .= '="' . $segment[1] . '"';
		}
		return $result;
	}
	
	public function filterContent($content, array &$buffer = null): string
	{
		$type = gettype($content);
		if (is_callable($content)) {
			return call_user_func($content, $this);
		} else if ($type === KE_STR)
			return $content;
		else if ($type === KE_ARY) {
			$buffer = $buffer ?? [];
			foreach ($content as $item) {
				if (is_array($item)) {
					$buffer[] = $this->tag(...$item);
				} else {
					$buffer[] = (string)$item;
				}
			}
			return implode('', $buffer);
		} else
			return (string)$content;
	}
	
	public function tag2field(string $tag, array $attr = null)
	{
		//////////////////////////////////////////////////////////////////
		// 此方法是这次Html重构后的核心所在，所以会总结不同的算法
		//////////////////////////////////////////////////////////////////
		// 算法1：用count去实际计算$segments的长度，10000次，大约耗时110ms
		//////////////////////////////////////////////////////////////////
//		$segments = [$tag];
//		if (strpos($tag, ':') !== false)
//			$segments = explode(':', $tag);
//		if (!empty($segments[0]))
//			$segments[0] = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $segments[0])));
//		if (!empty($attr['type']))
//			$segments[] = $attr['type'];
//		// 算法1，
//		if (count($segments) > 1)
//			return [$segments[0], str_replace(' ', '', ucwords(implode(' ', $segments)))];
//		return [$segments[0], $segments[0]];
		//////////////////////////////////////////////////////////////////
		// 算法2：用断言的方式去预判可能+1，10000次，大约耗时107ms
		//////////////////////////////////////////////////////////////////
//		$segments = [$tag];
//		$count = 1;
//		if (strpos($tag, ':') !== false) {
//			$segments = explode(':', $tag);
//			if (isset($segments[1]))
//				$count += 1;
//		}
//		if (!empty($segments[0]))
//			$segments[0] = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $segments[0])));
//		if (!empty($attr['type'])) {
//			$segments[] = $attr['type'];
//			$count += 1;
//		}
//		if ($count > 1)
//			return [$segments[0], str_replace(' ', '', ucwords(implode(' ', $segments)))];
//		return [$segments[0], $segments[0]];
		//////////////////////////////////////////////////////////////////
		// 算法3：这个更变态，10000次，只要75ms，暂时用这个方案了，比较满意
		//////////////////////////////////////////////////////////////////
		$tag = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $tag)));
		$head = strtok($tag, ':');
		$tail = ucfirst(strtok(':'));
		if (!empty($attr['type']))
			$tail .= ucfirst($attr['type']);
		return [$head, $head . $tail];
	}
	
	// 这个应该尽量省略掉
	public function getBaseClass(string $tag, ...$types)
	{
		$key = str_replace(['_', '-', ':'], ' ', $tag);
		if (!empty($types)) {
			$types = array_filter($types);
			if (!empty($types)) {
				$key .= ' ' . implode(' ', $types);
			}
		}
		$key = str_replace(' ', '', ucwords($key));
		$key = 'class' . $key;
		if (isset($this->{$key}))
			return $this->{$key};
		return '';
	}
	
	// tag:type 转 _ : - => ' ' => ucwords() =>
	public function tag(string $tag = null, $content = null, $attr = null): string
	{
		// 原来的preTag方法，去除
		// 先过滤标签，确保标签是正确的
		$tag = trim($tag);
		// 属性转数组
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		// 过滤和转换标签
		if (!empty($tag)) {
			$field = $this->tag2field($tag, $attr);
			// 两者相同，表示没有指定type
			if ($field[0] === $field[1]) {
				$tagField = 'tag' . $field[0];
				$classField = 'class' . $field[0];
				if (isset($this->{$tagField}))
					$tag = $this->{$tagField};
				if (isset($this->{$classField}))
					$this->addClass($attr, $this->{$classField});
			} else {
				// 以下为试行
				$tagField = 'tag' . $field[0];
				$classField = 'class' . $field[0];
				$detailClassField = 'class' . $field[1];
				if (isset($this->{$tagField}))
					$tag = $this->{$tagField};
				if (isset($this->{$detailClassField}))
					$this->addClass($attr, $this->{$detailClassField});
				else if (isset($this->{$classField}))
					$this->addClass($attr, $this->{$classField});
			}
			// 前置过滤器的方法，暂时保留
//			$method = 'pre' . $field[0];
//			if (is_callable([$this, $method]))
//				$this->{$method}($tag, $content, $attr);
		}
		if (!empty($content) && !is_string($content))
			$content = $this->filterContent($content);
		// 没有标签，直接返回内容
		if (empty($tag))
			return $content;
		$attr = $this->attr($attr);
		if ($this->isClosingTag($tag)) {
			if ($this->xhtmlStyle)
				return "<{$tag}{$attr}/>";
			else
				return "<{$tag}{$attr}>";
		} else {
			return "<{$tag}{$attr}>{$content}</{$tag}>";
		}
	}
	
	public function wrap($content, $before = null, $after = null, $attr = null, string $tag = 'div')
	{
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		$this->addClass($attr, 'inline-wrap');
		if (!is_string($content))
			$content = $this->filterContent($content);
		$content = $this->tag('span', $content, 'main');
		if (!empty($before)) {
			if (!is_string($before))
				$before = $this->filterContent($before);
			$before = $this->tag('span', $before, 'before');
		}
		if (!empty($after)) {
			if (!is_string($after))
				$after .= $this->filterContent($after);
			$after = $this->tag('span', $after, 'after');
		}
		
		return $this->tag($tag, $before . $content . $after, $attr);
	}
	
	public function button(string $text, $type = 'button', $attr = null)
	{
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		if (empty($type))
			$attr['type'] = $type;
		else if ($type !== 'button' && $type !== 'submit' && $type !== 'reset') {
			// 这里其实主要是应对各大css框架，生成一个button like like
			$attr['href'] = $type;
			return $this->tag('button-link', $text, $attr);
		} else
			$attr['type'] = $type;
		return $this->tag('button', $text, $attr);
	}
	
	public function buttons(array $buttons, $attr = null, $delimiter = null)
	{
		$content = [];
		foreach ($buttons as $button) {
			$content[] = $this->button(...(array)$button);
		}
		// tagButtons, classButtons
		return $this->tag('buttons', implode($delimiter ?? $this->buttonsDelimiter, $content), $attr);
	}
	
	public function link(string $text, $href = null, $attr = null)
	{
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		if (empty($href))
			$href = '#';
		$attr['href'] = $href;
		return $this->tag('a', $text, $attr);
	}
	
	public function img($src, $attr = null)
	{
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		$attr['src'] = $src;
		return $this->tag('img', null, $attr);
	}
	
	public function select($options, $selected = null, $attr = null, string $defaultOption = null)
	{
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		if (!empty($attr['multiple']) && !empty($attr['name'])) {
			if (!preg_match('#\[\]$#', $attr['name'])) {
				$attr['name'] .= '[]';
			}
		}
		return $this->tag('select', $this->selectOptions($options, $selected, $defaultOption), $attr);
	}
	
	public function filterOptions($options): array
	{
		$type = gettype($options);
		if ($type === KE_OBJ)
			return (array)$options;
		else if ($type === KE_ARY)
			return $options;
		else
			return [1 => (string)$options];
	}
	
	public function selectOptions(
		$options,
		$selected = null,
		string $defaultOption = null,
		$groupLabel = null,
		array &$buffer = null,
		int $deep = 0
	)
	{
		if (!is_array($options))
			$options = $this->filterOptions($options);
		$result = '';
		$groupLabel = trim($groupLabel);
		if (isset($defaultOption))
			$options = ['' => $defaultOption] + $options;
		foreach ($options as $value => $text) {
			if (is_array($text)) {
				if (empty($text))
					continue;
				$newLabel = $value;
				if (!empty($groupLabel))
					$newLabel = $groupLabel . ' - ' . $newLabel;
				$this->selectOptions($text, $selected, null, $newLabel, $buffer, $deep + 1);
				continue;
			}
			$isSelected = false;
			if (is_array($selected) && !empty($selected) && array_search($value, $selected) !== false)
				$isSelected = true;
			else if (equals($value, $selected))
				$isSelected = true;
			$result .= $this->tag('option', (string)$text, [
				'value'    => $value,
				'selected' => $isSelected,
			]);
		}
		if (!empty($groupLabel))
			$result = $this->tag('optgroup', $result, ['label' => $groupLabel]);
		$buffer[] = $result;
		if (count($buffer) > 1) {
			rsort($buffer);
			return implode('', $buffer);
		}
		return $result;
	}
	
	public function input(string $type, string $value = null, $attr = null)
	{
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		if (empty($type))
			$type = 'text';
		$attr['type'] = $type;
		// value这里比较啰嗦
		if (!isset($attr['value'])) {
			if ($type == 'date' && !empty($value) && is_numeric($value))
				$value = date('Y-m-d', $value);
			$attr['value'] = $value;
		}
		return $this->tag('input', null, $attr);
	}
	
	public function text(string $value = null, $attr = null)
	{
		return $this->input('text', $value, $attr);
	}
	
	public function hidden(string $value = null, $attr = null)
	{
		return $this->input('hidden', $value, $attr);
	}
	
	public function password(string $value = null, $attr = null)
	{
		return $this->input('password', $value, $attr);
	}
	
	public function textarea(string $value = null, $attr = null)
	{
		if (!empty($value))
			$value = htmlentities($value);
		return $this->tag('textarea', $value, $attr);
	}
	
	public function label(string $text, string $for = null, $attr = null)
	{
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		if (!empty($for))
			$attr['for'] = $for;
		return $this->tag('label', $text, $attr);
	}
	
	public function labelInput(string $label, string $type, $attr = null)
	{
		$labelAttr = [];
		if (!empty($attr['id']))
			$labelAttr['for'] = $attr['id'];
		$label = $this->tag('label-inline', $label, $labelAttr);
		$input = $this->input($type, null, $attr);
		if ($type === 'checkbox' || $type === 'radio') {
			return $input . $label;
		} else {
			return $label . $input;
		}
	}
	
	public function groupInputs(string $type, $options, $value = null, $attr = null)
	{
		if (!is_array($options))
			$options = $this->filterOptions($options);
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		$attr['type'] = $type;
		$baseId = empty($attr['id']) ? $this->autoId($type) : $attr['id'];
		$baseName = $attr['name'] ?? '';
		$count = count($options);
		$isMulti = $type !== 'radio' && $count > 1;
		if ($isMulti && !empty($attr['name'])) {
			if (!preg_match('#\[\]$#', $attr['name'])) {
				$attr['name'] .= '[]';
			}
		}
		$result = '';
		if (!empty($baseName)) {
			$hiddenAttr = ['name' => $baseName];
			if (isset($attr['data-field']))
				$hiddenAttr['data-field'] = $attr['data-field'];
			$result .= $this->input('hidden', null, $hiddenAttr);
		}
		foreach ($options as $val => $text) {
			$attr['checked'] = false;
			if (is_array($value))
				$attr['checked'] = array_search($val, $value) !== false;
			else
				$attr['checked'] = equals($val, $value);
			if ($count > 1)
				$attr['id'] = $baseId . '_' . $val;
			else
				$attr['id'] = $baseId;
			$attr['value'] = $val;
			$result .= $this->labelInput($text, $type, $attr);
//			$label = '';
//			$input = $this->input($type, $val, $attr);
//			if (!empty($text))
//				$label = $this->tag('label-inline', $text, ['for' => $attr['id']]); // tagLabelInline, classLabelInline
//			$result .= $input . $label;
		}
		return $result;
	}
	
	public function inputSet(
		array $values,
		string $type = 'hidden',
		array $ignores = [],
		string &$result = null,
		array $prefix = []
	)
	{
		$result = $result ?? '';
		$index = 0;
		foreach ($values as $field => $value) {
			if (isset($ignores[$field]))
				continue;
			if (is_array($value)) {
				$newPrefix = $prefix;
				$newPrefix[] = $field;
				$this->inputSet($value, $type, $ignores, $result, $newPrefix);
				continue;
			}
			$name = $prefix;
			if (is_string($field) || (is_numeric($field) && intval($field) !== $index)) {
				$name[] = $field;
			} else {
				$name[] = '';
			}
			$result .= $this->input($type, (string)$value, ['name' => $name]);
			$index++;
		}
		return $result;
	}
	
	public function fieldSet(string $label, $content = null, $attr = null)
	{
		if (!empty($attr['require']))
			$this->addClass($attr, $this->getBaseClass('fieldSetRequire'));
		$contentAttr = [];
		if (!empty($attr['multiFields']))
			$this->addClass($contentAttr, $this->getBaseClass('fieldSetFields'));
		
		$withOutField = false;
		if (isset($attr['withOutField'])) {
			$withOutField = !empty($attr['withOutField']) ? true : false;
			unset($attr['withOutField']);
		}
		
		if ($withOutField) {
			return $this->tag('div', $content, $contentAttr);
		} else {
			$inner = $this->tag('fieldSetLegend', $label);
			
			$inner .= $this->tag('fieldSetContent', $content, $contentAttr);
			if (!empty($attr['error'])) {
				$this->addClass($contentAttr, $this->classFieldSetError);
				$inner .= $this->tag('fieldSetTip', $attr['error'], $contentAttr);
				$this->addClass($attr, $this->getBaseClass('fieldSetError'));
			} else if (!empty($attr['tip'])) {
				$inner .= $this->tag('fieldSetTip', $attr['tip'], $contentAttr);
			}
			unset($attr['error'], $attr['multiFields'], $attr['require']);
			return $this->tag('fieldSet', $inner, $attr);
		}
	}
	
	public function mkInputAttr(string $field, $value = null, array $column = []): array
	{
		if (!empty($column['placeholder']))
			$placeholder = $column['placeholder'];
		else if (!empty($column['label']))
			$placeholder = $column['label'];
		else if (!empty($column['title']))
			$placeholder = $column['title'];
		else
			$placeholder = $field;
		/////////////////////////////////////////////////////////
		$isRequire = false;
		if (!empty($column['require']))
			$isRequire = true;
		else if (isset($column['empty']) && $column['empty'] !== true)
			$isRequire = true;
		/////////////////////////////////////////////////////////
		$isNumeric = false;
		$isDouble = false;
		// 过滤是否数值类型
		if (!empty($column['numeric'])) {
			$isNumeric = true;
			if ($column['numeric'] >= 3)
				$isDouble = true;
		} else if (!empty($column['int']) || !empty($column['bigint'])) {
			$isNumeric = true;
		} else if (!empty($column['float'])) {
			$isNumeric = $isDouble = true;
		}
		/////////////////////////////////////////////////////////
		$type = 'text';
		if (!empty($column['edit']))
			$type = $column['edit'];
		else if (!empty($column['options']))
			$type = 'select';
		else if (!empty($column['email']))
			$type = 'email';
		else if ($isNumeric)
			$type = 'number'; // 并不强制变为number，如果用户指定为text，则仍使用text
		
		$fields = $column['prefix'] ?? [];
		if (is_string($fields))
			$fields = [$fields];
		else if (is_object($fields))
			$fields = [(string)$fields];
		$fields[] = $field;
		
		$id = $this->id($fields);
		$class = 'field-' . str_replace('_', '-', $id);
		$attr = [
			'type'       => $type,
			'id'         => $id,
			'data-field' => $field,
			'name'       => $this->name($fields),
			'class'      => $class, // 这个是比较特殊的，用于通过css来控制具体每个字段的样式
		];
		
		/////////////////////////////////////////////////////////
		// 属性绑定
		/////////////////////////////////////////////////////////
		if ($isRequire)
			$attr['require'] = true;
		if (!empty($column['disabled']))
			$attr['disabled'] = true;
		if (!empty($column['readonly']))
			$attr['readonly'] = true;
		if ($type !== 'checkbox' && $type !== 'radio') {
			if ($isNumeric && $type === 'number') {
				if (!empty($column['min']) && is_numeric($column['min']))
					$attr['min'] = $column['min'];
				if (!empty($column['max']) && is_numeric($column['max']))
					$attr['max'] = $column['max'];
				if ($isDouble)
					$attr['step'] = 'any'; // html5 input type non-include float
			}
			if ($type === 'text' || $type === 'password' || $type === 'email' || $type === 'url') {
				if (!empty($column['max']))
					$attr['maxlength'] = $column['max'];
			}
			if ($type !== 'select')
				$attr['placeholder'] = $placeholder;
		} else {
		
		}

//		$this->addClass($attr, $type);
		
		return $attr;
	}
	
	public function mkFormColumn(string $field, $value = null, array $column = [], $data = null)
	{
		if (!isset($value) && isset($column['default']))
			$value = $column['default'];
		$inputAttr = $this->mkInputAttr($field, $value, $column);
		$type = $inputAttr['type'];
		$showLabel = true;
		if (isset($column['showLabel']) && $column['showLabel'] === false)
			$showLabel = false;
		$label = $column['label'] ?? $column['title'] ?? $field;
		$isMultiFields = false;
		$typeClass = 'input-edit';
		$withOutField = !empty($column['withOutField']) ? true : false;
		if (strpos($type, 'widget/') === 0) {
			if ($showLabel && !empty($label)) {
				$label .= $this->labelColon;
				$label = $this->label($label, $inputAttr['id']);
			} else {
				$label = '';
			}
			$ctx = $column['context'] ?? [];
			if (!is_array($ctx)) $ctx = [];
			$ctx = [
					'data'   => $data,
					'attr'   => $inputAttr,
					'field'  => $field,
					'value'  => $value,
					'column' => $column,
				] + $ctx;
			$input = Web::getWeb()->getContext()->loadComponent($type, $ctx);
			$typeClass = 'widget';
		} else if ($type === 'html') {
			if (!empty($column['content']))
				$input = $this->tag('div', $column['content'], 'form-html');
			$typeClass = 'html';
		} else if ($type === 'hidden') {
			return $this->input($type, $value, $inputAttr);
		} else {
			if ($showLabel && !empty($label)) {
				$label .= $this->labelColon;
				$label = $this->label($label, $inputAttr['id']);
			} else {
				$label = '';
			}
			if ($type === 'select') {
				$typeClass = 'select';
				$input = $this->select($column['options'] ?? [], $value, $inputAttr,
					$column['defaultOption'] ?? null);
			} else if ($type === 'textarea') {
				$typeClass = 'textarea';
				$input = $this->textarea($value, $inputAttr);
			} else if ($type === 'static') {
				$typeClass = 'static';
				$input = $this->tag('form-static', $value, $inputAttr);
			} else {
				if ($type === 'radio' || $type === 'checkbox')
					$typeClass = $type;
				else
					$typeClass = 'input-edit';
				if (!empty($column['options'])) {
					$isMultiFields = true;
					$input = $this->groupInputs($type, $column['options'], $value, $inputAttr);
				} else {
					$input = $this->input($type, $value, $inputAttr);
				}
			}
			if (isset($column['before']) || isset($column['after'])) {
				$input =
					$this->wrap($input, $column['before'] ?? '', $column['after'] ?? '', $column['wrap'] ?? [], 'input-wrap');
			}
		}
		$fieldSetAttr = [
			'require'      => !empty($column['require']),
			'multiFields'  => $isMultiFields,
			'error'        => $column['error'] ?? null,
			'tip'          => $column['tip'] ?? null,
			'class'        => ($column['class'] ?? '') . ' ' . $typeClass,
			'withOutField' => $withOutField,
		];
		return $this->fieldSet($label, $input ?? '', $fieldSetAttr);
	}
	
	public function message($message, string $type = self::MSG_DEFAULT, $attr = null)
	{
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		return $this->tag("message:{$type}", $message, $attr);
	}
	
	public function success($message, $attr = null)
	{
		return $this->message($message, self::MSG_SUCCESS, $attr);
	}
	
	public function notice($message, $attr = null)
	{
		return $this->message($message, self::MSG_NOTICE, $attr);
	}
	
	public function warning($message, $attr = null)
	{
		return $this->message($message, self::MSG_WARN, $attr);
	}
	
	public function error($message, $attr = null)
	{
		return $this->message($message, self::MSG_ERROR, $attr);
	}
	
	public function mkTableColumn(
		string $field,
		$column,
		$type = self::TABLE_HEAD_FROM_COLUMNS,
		array $mergeColumn = null
	)
	{
		if ($type === self::TABLE_HEAD_FROM_DATA) {
			$column = $mergeColumn ?? ['label' => $field];
		} else {
			if (is_string($column))
				$column = ['label' => $column];
			else if (is_object($column))
				$column = ['label' => (string)$column];
			else if (!is_array($column))
				$column = ['showTable' => !empty($column)];
			// 附加设定
			if (!empty($mergeColumn))
				$column = array_merge($mergeColumn, $column);
		}
		if (!isset($column['label']))
			$column['label'] = $column['title'] ?? $field;
		if (isset($column['attr']))
			$column['attr'] = $this->attr($column['attr']);
		if (!empty($column['hidden']))
			$column = ['showTable' => false];
		return $column;
	}
	
	public function filterColumnValue($row, string $field, $value, array $column = null)
	{
		if (is_string($value)) {
			$length = strlen($value);
			if ($length > 0) {
				if (isset($column['summary']) && is_numeric($column['summary']) && $column['summary'] > 0)
					$value = $this->label(str_summary($value, $column['summary']), null, ['title' => $value]);
				else if (isset($column['strLen']) && is_numeric($column['strLen']) && $column['strLen'] > 0)
					$value = $this->label(str_len_cut($value, $column['strLen']), null, ['title' => $value]);
				else if (isset($column['strWidth']) && is_numeric($column['strWidth']) && $column['strWidth'] > 0)
					$value = $this->label(str_width_cut($value, $column['strWidth']), null, ['title' => $value]);
			}
		} else if (!empty($column['timestamp'])) {
			if (is_numeric($value) && $value > 0)
				$value = date('Y-m-d H:i:s', $value);
			else
				$value = $this->getText(self::TEXT_NONE);
		} else if (isset($column['floatPoint']) && is_numeric($column['floatPoint']) && $column['floatPoint'] > 0) {
			$value = $this->label(float_precision($value, $column['floatPoint']), null, ['title' => $value]);
		}
		
		if (isset($column['options'])) {
			$value = $column['options'][$value] ?? $value;
		}
		
		if (isset($column['before']) || isset($column['after'])) {
			$value = $this->wrap($value, $column['before'], $column['after'], $column['wrap'] ?? [], 'div');
		}
		if ($row instanceof Model) {
			if (isset($column['getter']) && is_callable([$row, $column['getter']]))
				$value = call_user_func([$row, $column['getter']]);
		}
		// todo: 这里会增加一些Model默认属性的处理
		if (isset($column['onShow']) && is_callable($column['onShow'])) {
			$value = call_user_func($column['onShow'], $this, $row, $value, $column);
		}
		return $value;
	}
	
	public function form($data, array $options = null)
	{
		$options['data'] = $data;
		return Web::getWeb()->getContext()->loadComponent('form', $options);
	}
	
	public function tableList($rows, array $options = null)
	{
		$options['rows'] = $rows;
		return Web::getWeb()->getContext()->loadComponent('table_list', $options);
	}
	
	public function paginate(Pagination $pagination = null, $attr = null)
	{
		if (!isset($pagination))
			return '';
		
		$linksCount = intval($this->pageLinks);
		$prevNext = !empty($this->pagePrevNext);
		$firstLast = !empty($this->pageFirstLast);
		$goto = $this->pageGoto;
		
		$field = $pagination->field;
		$pageTotal = $pagination->total;
		$pageCurrent = $pagination->current;
		
		$els = [
			'links'   => '',
			'prev'    => '',
			'next'    => '',
			'first'   => '',
			'last'    => '',
			'current' => '',
			'total'   => '',
			'button'  => '',
		];
		
		if ($pageTotal > $linksCount) {
			$half = (int)($linksCount / 2);
			$start = $pageCurrent - $half;
			if ($start < 1) $start = 1;
			$over = $start + $linksCount;
//				$over = $start + $linksCount - ($firstLast ? ($start == 1 ? 2 : 3) : 1);
			if ($over > $pageTotal) {
				$over = $pageTotal;
				$start = $over - $linksCount;
				if ($start <= 1) $start = 1;
			}
		} else {
			$start = 1;
			$over = $pageTotal;
		}
		
		$uri = Uri::current();
		$ellipsis = $this->paginationLink(self::PAGE_ELLIPSIS, 0);
		
		if ($linksCount > 0) {
			if ($start > 1) {
				if (!$firstLast) {
					$els['links'] .= $this->paginationLink(self::PAGE_ITEM, 1, $uri, $field, false);
					$start += 1;
					if ($start > 2)
						$els['links'] .= $ellipsis;
				} else {
					$els['links'] .= $ellipsis;
				}
			}
			if (!$firstLast && $over < $pageTotal)
				$over -= 1;
			for ($i = $start; $i <= $over; $i++) {
				$els['links'] .= $this->paginationLink(self::PAGE_ITEM, $i, $uri, $field, $pageCurrent);
			}
			if ($over < $pageTotal) {
				if (!$firstLast) {
					if ($over < $pageTotal - 1)
						$els['links'] .= $ellipsis;
					$els['links'] .= $this->paginationLink(self::PAGE_ITEM, $pageTotal, $uri, $field, false);
				} else {
					$els['links'] .= $ellipsis;
				}
			}
		}
		
		if ($firstLast) {
			$els['first'] = $this->paginationLink(self::PAGE_FIRST, 1, $uri, $field, $pageCurrent);
			$els['last'] = $this->paginationLink(self::PAGE_LAST, $pageTotal, $uri, $field, $pageCurrent);
		}
		
		if ($prevNext) {
			$els['prev'] = $this->paginationLink(self::PAGE_PREV, $pageCurrent - 1, $uri, $field, 1 - 1);
			$els['next'] = $this->paginationLink(self::PAGE_NEXT, $pageCurrent + 1, $uri, $field, $pageTotal + 1);
		}
		if (!empty($goto)) {
			if ($goto === 'input') {
				$el = $this->input('number', $pageCurrent, [
					'step' => 1,
					'min'  => 1,
					'max'  => $pageTotal,
					'name' => $field,
				]);
			} else {
				$pages = range(1, $pageTotal);
				$el = $this->select(array_combine($pages, $pages), $pageCurrent, [
					'name' => $field,
				]);
			}
			$els['current'] = $this->tag('page-span:input', sprintf($this->getText(self::PAGE_CUR), $el));
			$els['button'] = $this->tag('page-button', $this->getText(self::PAGE_GOTO));
		} else {
			$els['current'] = sprintf($this->getText(self::PAGE_CUR), $pageCurrent);
		}
		
		$els['total'] = sprintf($this->getText(self::PAGE_TOTAL), $pageTotal);
		
		$row = substitute($this->pageRow, $els);
		
		if (!empty($goto)) {
			$row .= $this->inputSet($uri->getQueryData(), 'hidden', [$field => 1]);
			$row = $this->tag('form', $row, ['action' => $uri, 'method' => 'get']);
		}
		
		return $this->tag('page-wrap', $row, $attr);
	}
	
	public function paginationLink(string $item, int $number, Uri $uri = null, string $field = 'page', $compare = false)
	{
		$attr = [];
		$text = $item;
		if (isset($this->texts[$item])) {
			$text = sprintf($this->texts[$item], $number);
		}
		if ($item === self::PAGE_ELLIPSIS) {
			$tag = 'page-span:ellipsis';
		} else if (isset($uri) && ($compare === false || !equals($compare, $number))) {
			$tag = 'page-link';
			//
			$attr['href'] = $uri->setQuery([$field => $number <= 1 ? null : $number], true);
		} else {
			if ($item === self::PAGE_ITEM)
				$tag = 'page-span:active';
			else
				$tag = 'page-span';
		}
		return $this->tag($tag, $text, $attr);
	}
	
	public $tagIcon = 'i';
	public $classIcon = 'fa';
	
	public function preIcon(string $icon, $attr = null): array
	{
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		$class = "fa-{$icon} fa-fw";
		if (isset($attr['size'])) {
			if ($attr['size'] === 'lg')
				$class .= ' fa-lg';
			else if ($attr['size'] >= 2 && $attr['size'] <= 5)
				$class .= ' fa-' . $attr['size'] . 'x';
			unset($attr['size']);
		}
		if (isset($attr['spin'])) {
			$class .= ' fa-spin';
			unset($attr['spin']);
		}
		if (isset($attr['fw'])) {
			$class .= ' fa-fw';
			unset($attr['fw']);
		}
		if (isset($attr['li'])) {
			$class .= ' fa-li';
			unset($attr['li']);
		}
		if (isset($attr['rotate'])) {
			if ($attr['rotate'] == 90 || $attr['rotate'] === 180 || $attr['rotate'] === 270)
				$class .= ' fa-rotate-' . $attr['rotate'];
			unset($attr['rotate']);
		} else if (isset($attr['flip'])) {
			if ($attr['flip'] === self::HORIZONTAL)
				$class .= ' fa-flip-horizontal';
			else if ($attr['flip'] === self::VERTICAL)
				$class .= ' fa-flip-vertical';
			unset($attr['flip']);
		}
		$this->addClass($attr, $class);
		return $attr;
	}
	
	public function icon(string $icon, $attr = null): string
	{
		return $this->tag('icon', null, $this->preIcon($icon, $attr));
	}
	
	public $tagListItem = 'li';
	
	public function ol($items, $attr = null, $itemAttr = null): string
	{
		if (!is_array($items))
			$items = (array)$items;
		$content = [];
		foreach ($items as $item) {
			if (is_array($item))
				$item = $this->tag(...$item);
			$content[] = $this->tag('list-item', $item, $itemAttr);
		}
		return $this->tag('ol', $content, $attr);
	}
	
	public function ul($items, $attr = null, $itemAttr = null): string
	{
		if (!is_array($items))
			$items = (array)$items;
		$content = [];
		foreach ($items as $item) {
			$content[] = $this->tag('list-item', $item, $itemAttr);
		}
		return $this->tag('ul', $content, $attr);
	}
	
}