<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */


namespace Ke\Web\Route;

class Router
{

	const ROOT = '*';

	const MODE_TRADITION = 0;

	const MODE_CONTROLLER = 1;

	const MODE_CLASS = 2;

	/** 基本的正则字符 */
//	const PATTERN_BASE = '[^\/]+'; // 不希望匹配非ascii的字符。
	const PATTERN_BASE = '[a-z0-9\_\-]+'; // 不希望匹配非ascii的字符。
//	const PATTERN_BASE = '[a-z\x7f-\xff][a-z0-9\x7f-\xff\_\-\.]*[a-z\x7f-\xff]';

	/** id的正则字符 */
	const PATTERN_ID = '[\d]+';

	/** name的正则字符 */
	const PATTERN_NAME = '[a-z\x7f-\xff][a-z0-9\x7f-\xff\_\-\.]*';

	/** year的正则字符 */
	const PATTERN_YEAR = '19[\d]{2}|20[\d]{2}';

	/** month的正则字符 */
	const PATTERN_MONTH = '[\d]{1}|0[\d]{1}|1[0-2]';

	/** day的正则字符 */
	const PATTERN_DAY = '[\d]{1}|[0-2][\d]{1}|3[0-1]{1}';

	const PATTERN_FORMAT = '[^\/\.]+';


	// 以下这三个属性，用来预留调整的需要，要改变的时候，需要将以下的三个属性一起修改
	/** @var string 令牌的开始 */
	protected $tokenStart = '{';

	/** @var string 令牌的结束 */
	protected $tokenEnd = '}';

	/** @var string 令牌的检查正则表达式 */
	protected $tokenRegex = '#\{([^\{\}]+)\}#i';


	/** @var array 需要有限替换的特定符号表 */
	public $symbols = [
		'('           => '(?:',
		')'           => ')',
		')?'          => ')?',
		'$action'     => '{action}',
		'$controller' => '{controller}',
		'*'           => '{name}',
	];

	/** @var array 公共的令牌表，如果有需要，可以额外添加 */
	public $tokens = [
		'controller' => self::PATTERN_BASE,
		'action'     => self::PATTERN_BASE,
		'id'         => self::PATTERN_ID,
		'name'       => self::PATTERN_NAME,
		'year'       => self::PATTERN_YEAR,
		'month'      => self::PATTERN_MONTH,
		'day'        => self::PATTERN_DAY,
		'format'     => self::PATTERN_FORMAT,
	];

	/**
	 * controller/action匹配模式的公共mappings
	 * 在没指定节点的mappings，或者节点的mappings未匹配时，会使用这个公共mappings
	 *
	 * @var array
	 */
	public $controllerMappings = [
		'({controller}(/{action})?)?',
	];

	/**
	 * action匹配模式的公共mappings
	 * 这个公共mappings会在指定了mappings[controller]的时候生效
	 *
	 * @var array
	 */
	public $actionMappings = [
		'({action})?',
	];

	/** @var bool 是否自动补完namespace */
//	public $isAutoNamespace = true;

	/**
	 * 路由模式配置的格式：
	 *
	 * 'nodeName' => [
	 *     'namespace' => 'namespace',
	 *     'controller' => 'index',
	 *     'mappings' => [
	 *         $pattern,
	 *         [ $pattern, $params ],
	 *         [ $pattern, $params, $tokens, ]
	 *     ]
	 * ]
	 *
	 * @var array 路由模式配置表
	 */
	public $routes = [
//		'*' => [],
//		'*' => [
//			'controller' => 'index',
//			'mappings'   => [],
//		],
//		'node' => [
//			'namespace'  => 'node',
//			'controller' => null,
//			'mappings'   => [
//              ['path', 'controller#action?key=value']
//          ],
//		],
	];

	/**
	 * @var array 路径的映射
	 */
	private $paths = [
//		'admin' => 'manage'
	];

	private $isPrepare = false;

	private $web = null;

//	public function __construct($routes = null)
//	{
////		$this->web = Web::getWeb(); // todo: Router应该脱离web
//		if (isset($routes))
//			$this->setRoutes($routes);
//	}

	public function loadFile(string $file)
	{
		import($file, ['router' => $this]);
		return $this;
	}

	public function setRoutes(array $routes)
	{
		foreach ($routes as $node => $settings) {
			if (!empty($settings) && is_array($settings))
				$this->setNode($node, $settings);
		}
		return $this;
	}

	public function setNode(string $node, array $settings)
	{
		if (!isset($this->routes[$node]))
			$this->routes[$node] = $settings;
		else
			$this->routes[$node] = array_merge($this->routes[$node], $settings);
		return $this;
	}

	private function prepareRoutes()
	{
		if (!isset($this->routes[self::ROOT]))
			$this->routes[self::ROOT] = [];
		foreach ($this->routes as $node => &$route) {
			$node = trim($node, KE_PATH_NOISE);
			$path = isset($route['path']) ? trim($route['path'], KE_PATH_NOISE) : $node;
			$this->paths[$path] = $node;
//			if ($node !== self::ROOT && !isset($route['namespace']))
//				$route['namespace'] = $node;
		}
		return $this;
	}

	public function isPrepare()
	{
		return $this->isPrepare;
	}

	public function getRoutes()
	{
		return $this->routes;
	}


	/**
	 * @param null $input
	 * @param null $routes
	 * @return Result
	 */
	public function routing($input = null): Result
	{
		if (!$this->isPrepare)
			$this->prepareRoutes();

		$rs = Result::factory($input);

		$this->fetchPaths($rs);
		$this->fetchMappings($rs);

		return $rs;
	}

	protected function fetchPaths(Result $rs)
	{
		if (empty($this->paths))
			return $this;

		$pos = null;
		$part = $rs->tail;
		while (!empty($part)) {
			if (isset($this->paths[$part])) {
				$rs->node = $this->paths[$part];
				$rs->head = $part;
				$rs->tail = isset($pos) ? substr($rs->tail, $pos + 1) : '';
				$rs->matchPath = $part;
				break;
			}
			$pos = strrpos($part, '/');
			$part = substr($part, 0, $pos);
		}

		if (empty($rs->node))
			$rs->node = self::ROOT;

		return $this;
	}

	protected function fetchMappings(Result $rs)
	{
		if (!isset($this->routes[$rs->node]))
			return $this;
		$data = &$this->routes[$rs->node];
		$rs->assign($data);

		if (!empty($data['mappings']) && is_array($data['mappings']))
			$this->loopMappings($data['mappings'], $rs, true);

		if (!$rs->matched) {
			if ($rs->mode === self::MODE_CLASS && !empty($rs->action)) {
				$rs->matched = true;
			}
			else {
				if ($rs->mode === self::MODE_TRADITION) {
					$this->loopMappings($this->controllerMappings, $rs);
				}
				else {
					$this->loopMappings($this->actionMappings, $rs);
				}
				if (empty($rs->action) && !empty($data['action'])) {
					$rs->action = $data['action'];
				}
			}
		}
		return $this;
	}


	protected function loopMappings(array & $mappings, Result $rs, bool $isMapping = false)
	{
		foreach ($mappings as & $rule) {
			// 将以下环节拆解出来，即可实现独立的match方法，然而match\get\post这些方法并没有什么实质性的价值
			// 空的规则，不做处理
			if (empty($rule))
				continue;
			if (!isset($rule['_pattern_']))
				$rule = $this->completeRule($rule);
			// 编译一个规则的时候，可能还会碰到无效的设定，所以这里再次做一次检查判断
			if (empty($rule) || empty($rule['_pattern_']))
				continue;
			if (preg_match($rule['_pattern_'], $rs->tail, $rs->matches)) {
				$rs->matched = true;
				$rs->matchedMapping = $isMapping;
				$rs->tail = trim($rs->matches['tail'], KE_PATH_NOISE) ?? '';
				$args = array_intersect_key($rs->matches, $rule['_tokens_']);
				if (!empty($rule[1]))
					$args += $rule[1];
				if (isset($args['controller'])) {
					$rs->controller = $args['controller'];
					unset($args['controller']);
				}
				if (isset($args['action'])) {
					$rs->action = $args['action'];
					unset($args['action']);
				}

//				var_dump($rs->matches);
//				if (!empty($rs->matches[1]))
//					$rs->head .= "/{$rs->matches[1]}";
//				$rs->tail = trim(mb_substr($rs->tail, mb_strlen($rs->matches[0])), KE_PATH_NOISE);
//

				$rs->data = $args;
				break;
			}
		}
	}

	/**
	 * 完成（构建）一个有效的Route规则
	 * 输入的源格式，支持如下的格式：
	 * 1. 字符串格式，表示的是规则的匹配模式，'edit/{id}'
	 * 2. 数组格式，表示的较为完整规则数据，['pattern', 'params', 'tokens', 'onMatch' => 'callback']
	 * 经过构建后的规则，将以数组的格式存放：
	 * <code>
	 * $rule = [
	 *     0 => 'pattern',
	 *     1 => 'params',
	 *     2 => 'tokens',
	 *     'onMatch' => 'callback',
	 *     '_tokens_' => [],
	 *     '_pattern_' => 'pattern'
	 * ]
	 * </code>
	 * 格式：
	 * 0 => 输入的源表达式，如：'image/upload/{hash}'
	 * 1 => 指定的参数，支持数组和字符串两种格式，'Image#upload', ['controller' => 'Image', 'action' => 'upload']
	 * 2 => 当前rule的tokens，如：['hash' => '[a-z][0-9]{32}']
	 * onMatch => callback，支持数组、字符串和匿名函数，如：['class', 'method'], 'function', Closure
	 * _tokens_ => 实际使用了的tokens
	 * _pattern_ => 最终编译完成的正则表达式
	 * 因为将常用的token提取了出来，所以将params放在1位，tokens放在2位
	 *
	 * @param mixed $rule 输入的源格式
	 * @return array|bool 完成后的规则，如果返回false，表示为无效的规则
	 */
	public function completeRule($rule)
	{
		///////////////////////////////////////////////////////////////////
		// 1.0 先对rule进行基本的调整，调整为有效的数组格式
		///////////////////////////////////////////////////////////////////
		// $rule 如果为空，则直接返回false，包括null, false, '', 0
		// 在使用该方法返回的rule时，需要先判断这个empty($rule)
		if (empty($rule))
			return false;
		// 非数组格式，先转为数组格式。经过这里，rule都是数组的格式
		if (!is_array($rule)) {
			$rule = [$rule];
		}
		// 再次检查一次$rule[0]，如果为空继续退出
		if (empty($rule[0]) || !is_string($rule[0]))
			return false;
		// 整理rule的params，转换为有效的key => value的格式
		if (empty($rule[1]))
			$rule[1] = [];
		else {
			$type = gettype($rule[1]);
			if ($type === KE_STR) {
				$rule[1] = $this->parseStr($rule[1]);
			}
			else if ($type === KE_OBJ) {
				$rule[1] = get_object_vars($rule[1]);
			}
			else if ($type !== KE_ARY) {
				$rule[1] = [];
			}
		}
		// 确保这条规则的tokens为一个数组格式，减少后续操作的繁琐度
		if (!isset($rule[2]) || !is_array($rule[2]))
			$rule[2] = [];
		return $this->completePattern($rule);
	}

	/**
	 * 完成（构建）一个规则中的pattern，并将相关的辅助生成的数据记录下来
	 *
	 * @param array $rule
	 * @return array
	 */
	protected function completePattern(array $rule)
	{
		// step1: 初步完善pattern
		$pattern = trim($rule[0], '/'); // 先清空规则两边的/
		$pattern = strtr($pattern, $this->symbols); // 替换符号表
		// step2: 生成tokens表，将私有tokens和公共tokens合并
		$tokens = $rule[2] + $this->tokens;
		// step3: 完成最终pattern
		// 实际使用的令牌表，格式为：symbol => symbolPattern
		// 如：id => (?<id>[\d]+)
		// 令牌表会记录在rule中，1. 用来在路由命中的时候，取出params。2. 用来复检
		$usedTokens = [];
		// 替换表，格式为：{symbol} => symbolPattern
		// 如：{id} => (?<id>[\d]+)
		// 替换表里面的数据，会替换pattern，并生成最终的pattern
		$replacements = []; // 替换表
		// 将pattern中所有的变量的符号找出来，如：{var} => var
		preg_match_all($this->tokenRegex, $pattern, $symbols);
		foreach ($symbols[1] as $symbol) {
			$symbolPattern = isset($tokens[$symbol]) ? "(?<{$symbol}>{$tokens[$symbol]})" : '';
			$usedTokens[$symbol] = $replacements["{$this->tokenStart}{$symbol}{$this->tokenEnd}"] = $symbolPattern;
		}
		$pattern = strtr($pattern, $replacements);
		$pattern = "#^({$pattern})(?<tail>(?:\/(?:[^\/]+))*)\/?$#i";
//		if ($pattern[strlen($pattern) - 1] !== '$') {
//			$pattern = "#^({$pattern}(|\/(?<tail>.*)))$#i";
//		}
//		else {
//			$pattern = "#^({$pattern})$#i";
//		}
//		 已用tokens写入mapping缓存
		// @todo 其实这个数据已经不是必须的了，不需要把pattern记下来，只要将keys记录即可。
		$rule['_tokens_'] = $usedTokens;
		// 不再拼接tail，未匹配的path尾部，自动转化为tail，减少正则容量
		$rule['_pattern_'] = $pattern;
		return $rule;
	}

	public function parseStr(string $value): array
	{
		$result = [];
		$value = trim($value, '/\\ ' . '.');
		if (empty($value))
			return $result;
		if (preg_match('#^([a-z0-9_]+(?:\/[a-z0-9_]+)*)?(?:\#([a-z0-9_]+)?)?$#i', $value, $matches)) {
			if (!empty($matches[1]))
				$result['controller'] = $matches[1];
			if (!empty($matches[2]))
				$result['action'] = $matches[2];
		}
		return $result;
	}
}