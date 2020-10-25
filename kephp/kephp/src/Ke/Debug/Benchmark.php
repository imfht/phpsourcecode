<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Debug;

/**
 * 基准测试类
 *
 * 注册多个动作（应该是以不同的方法达成相似的功能、乃至是一样的结果），统一执行，以取得每个动作的执行的时间、内存使用量峰值的统计。
 *
 * @package Ke\Debug
 */
class Benchmark
{

	const INDEX_START = 'start';

	const INDEX_END = 'end';

	const TAG_TIME = 'time';

	const TAG_MEMORY = 'memory';

	const TAG_MEMORY_PEAK = 'memory_peak';

	const TAG_MEMORY_PEAK_KEY = 'memory_peak_key';

	const TAG_MEMORY_LIST = 'memory_list';

	const TAG_TIME_LIST = 'time_list';

	/** @var array 基准测试的执行函数的存储容器，以`'name' => callable`的方式存储，`callable`的格式参考setAction说明 */
	private $actions = [];

	/** @var null 当前执行的action名称，默认应该为null */
	private $action = null;

	/** @var array 执行actions过程，不同阶段的时间戳记录 */
	private $timestamps = [];

	/** @var array 动作的执行所经过的时间、使用的内存的分段统计。 */
	private $usages = [];

	/**
	 * 比较两个时间戳之间的差值，返回结果的单位为微秒（μs，microsecond）
	 *
	 * 如果未指定$end，则会以当前的时间戳作为$end（`microtime()`）
	 *
	 * @param string $start 开始的时间戳，应该是执行`microtime()`的结果，即可"unixsecond microsecond"
	 * @param null|string $end 结束的时间戳，同`$start`，该参数允许为空，为空，则直接取当前的时间戳
	 * @return float 返回两者之间的差值，返回结果的单位微秒（μs，microsecond）
	 */
	public static function diffTimestamp($start, $end = null)
	{
		list($startUS, $startMS) = explode(' ', $start);
		if (empty($end))
			$end = microtime();
		list($endUS, $endMS) = explode(' ', $end);
		return ((float)$endUS + (float)$endMS) - ((float)$startUS + (float)$startMS);
	}

	/**
	 * @param array|null $actions
	 * @return Benchmark
	 */
	public static function factory(array $actions = null)
	{
		return new static($actions);
	}

	/**
	 * 构建函数
	 *
	 * @param array|null $actions
	 */
	public function __construct(array $actions = null)
	{
		if (isset($actions))
			$this->setActions($actions);
	}

	/**
	 * 添加要执行的动作（action）。
	 *
	 * $action允许以下的几种格式：
	 * - `\Closure`，闭包函数
	 * - `string`，字符串格式，表示为一般的函数，如：`hello_world`，也可以表示静态类的方法，如：`Hello::world`
	 * - `array`，数组格式，表示为类、实例的方法，如：[$obj, 'sayHi']、['Hello', 'World']等。
	 *
	 * 在执行的过程中，会将当前的基准测试的实例作为第一个参数传入，以方便手动标记一些统计的信息。比如：
	 *
	 * ```php
	 * function hello_world(Benchmark $bm) {
	 *     $bm->markMemory('init'); // 记录下初始的内存，这里表示的实际上执行这个方法时候的内存使用情况。
	 *     // 执行一些操作
	 *     $bm->markMemory('end'); // 记录下函数执行到这个阶段的内存使用情况。
	 * }
	 *
	 * $bm = new Benchmark([
	 *     'test1' => 'hello_world',
	 * ]);
	 * $bm->run();
	 * ```
	 *
	 * 当需要对一个类、实例的方法，执行基准测试的时候，因为需要使用到这个类、实例的属性，这个方法才能生效，所以最好的做法，
	 * 是直接在一个闭包内调用这个类、实例的方法，或者在这个类、实例基准上创建相同的基准测试方法入口，来执行他们。如：
	 *
	 * ```php
	 * class Hello {
	 *
	 *     // 假定这是我们需要测试的函数
	 *     public static function world($user)
	 *     {
	 *         echo 'Hello, world, ', $user;
	 *     }
	 *
	 *     public static function benchmark1($bm, $user)
	 *     {
	 *         static::world($user);
	 *     }
	 *
	 *     public static function benchmark2($bm, $user)
	 *     {
	 *         static::world($user);
	 *     }
	 * }
	 *
	 * // 做法1，适用于代码可被修改，或需要传相同的参数，做同样的基准测试的条件下执行
	 * $bm = new Benchmark([
	 *     'test1' => 'Hello::benchmark1',
	 *     'test2' => 'Hello::benchmark2',
	 * ]);
	 * $bm->run('Jack');
	 *
	 * // 做法2，适用于不想改动代码，或者需要传递不同的参数的条件下使用
	 * $bm = new Benchmark([
	 *     'test1' => function($bm) {
	 *         Hello::world('Jack');
	 *     },
	 *     'test2' => function($bm) {
	 *         Hello::world('Tommy');
	 *     },
	 * ]);
	 * $bm->run('Jack');
	 * ```
	 *
	 * @param string $name 该动作的名称，最好以字符串的方式
	 * @param \Closure|array|string|callable $action 该动作的实际执行的函数，必须为可执行的函数`is_callable($action)`
	 * @return $this 返回当前的基准测试的实例
	 */
	public function setAction($name, $action)
	{
		if ($name === 'total')
			return $this;
		if (is_string($action) && strstr($action, '::'))
			$action = explode('::', $action);
		if (!is_callable($action))
			return $this;
		$this->actions[$name] = $action;
		return $this;
	}

	/**
	 * 批量添加要执行的动作
	 *
	 * @param array $actions 以`name => callable`格式，批量添加的动作
	 * @return $this 返回当前的基准测试的实例
	 */
	public function setActions(array $actions = [])
	{
		foreach ($actions as $name => $action) {
			$this->setAction($name, $action);
		}
		return $this;
	}

	/**
	 * 通过动作名称，获取相关的执行动作，如果动作不存在，则返回false
	 *
	 * @param string $name 动作的名称
	 * @return false|\Closure|array|string|callable 如果指定的动作存在，则返回一个可执行的动作，如果不存在，则返回false
	 */
	public function getAction($name)
	{
		if (isset($this->actions[$name]))
			return $this->actions[$name];
		return false;
	}

	/**
	 * 返回所有已经添加的动作。
	 *
	 * @return array 当前已经添加的要执行的动作
	 */
	public function getActions()
	{
		return $this->actions;
	}

	/**
	 * 执行根据动作名称，执行相关的动作，这个函数会强行重置这个动作相关的统计记录。
	 *
	 * @param string $name 动作名称
	 * @param array $args 执行动作所需要传递的参数，必须为数组的格式
	 * @return $this 当前已经添加的要执行的动作
	 */
	public function runAction($name, array $args = [])
	{
		if (!isset($this->actions[$name]))
			return $this;
		if (!isset($args[0]) || $args[0] !== $this)
			array_unshift($args, $this);
		$this->startAction($name);
		call_user_func_array($this->actions[$name], $args);
		$this->endAction($name);
		return $this;
	}

	/**
	 * 执行当前基准测试实例的全部动作。
	 *
	 * 该函数的所有参数，会被作为执行`$this->runAction()`所传递的参数使用。
	 *
	 * @return $this 返回当前的基准测试的实例
	 */
	public function run()
	{
		$args = func_get_args();
		$startTimestamp = microtime();
		foreach ($this->actions as $name => $action) {
			$this->runAction($name, $args);
		}
		$time = $this->diffTimestamp($startTimestamp);
		$memoryPeak = memory_get_peak_usage();
		$peakList = $this->getMemoryPeakList();
		$this->usages['total'] = [
			self::TAG_TIME            => $time,
			self::TAG_MEMORY_PEAK     => $memoryPeak,
			self::TAG_MEMORY_PEAK_KEY => array_search(max($peakList), $peakList),
			self::TAG_MEMORY_LIST     => $peakList,
			self::TAG_TIME_LIST       => false,
		];
		return $this;
	}

	/**
	 * 开始执行一个动作，会执行重置相关的时间戳、内存记录，并记录下初始时的时间戳和内存。
	 *
	 * @param string $name
	 * @return $this 返回当前的基准测试的实例
	 */
	protected function startAction($name)
	{
		if (!isset($this->actions[$name]))
			return $this;
		$this->action = $name;
		$this->timestamps[$name] = [];
		$this->usages[$name] = [
			self::TAG_TIME            => 0.00,
			self::TAG_MEMORY          => false,
			self::TAG_MEMORY_PEAK     => false,
			self::TAG_MEMORY_PEAK_KEY => false,
			self::TAG_MEMORY_LIST     => false,
			self::TAG_TIME_LIST       => false,
		];
		$this->mark('start');
		return $this;
	}

	/**
	 * 结束一个动作，会统计并清空时间戳、内存的记录
	 *
	 * @param string $name
	 * @return $this 返回当前的基准测试的实例
	 */
	protected function endAction($name)
	{
		if (!isset($this->actions[$name]))
			return $this;
		$this->action = null;
		$this->statTime($name); // 统计时间
		$this->statMemory($name);
		return $this;
	}

	public function mark($index = null)
	{
		$name = $this->action;
		if (!isset($name))
			return $this;
		$this->markTime($index)->markMemory($index);
		return $this;
	}

	/**
	 * 记录一个时间戳
	 *
	 * 这个函数，并不需要指定动作名称，因为每个动作在执行过程中，会自动切换到当前执行的动作名称。
	 *
	 * @param null|string|int $index 时间戳的标记，当这个标记为null，表示的是自动叠加一个记录（array_push），
	 *                               禁止$index为0的标记，0表示为初始的标记，禁止手动修改0的标记
	 * @return $this 返回当前的基准测试的实例
	 */
	public function markTime($index = null)
	{
		$name = $this->action;
		if (!isset($name))
			return $this;
		if (!empty($index) && is_string($index))
			$this->timestamps[$name][$index] = microtime();
		else
			$this->timestamps[$name][] = microtime();
		return $this;
	}

	/**
	 * 记录一个内存使用值
	 *
	 * 这个函数，并不需要指定动作名称，因为每个动作在执行过程中，会自动切换到当前执行的动作名称。
	 *
	 * @param null|string|int $index 时间戳的标记，当这个标记为null，表示的是自动叠加一个记录（array_push），
	 *                               禁止$index为0的标记，0表示为初始的标记，禁止手动修改0的标记
	 * @return $this 返回当前的基准测试的实例
	 */
	public function markMemory($index = null)
	{
		$name = $this->action;
		if (!isset($name))
			return $this;
		if (!empty($index) && is_string($index))
			$this->usages[$name][self::TAG_MEMORY_LIST][$index] = memory_get_usage();
		else
			$this->usages[$name][self::TAG_MEMORY_LIST][] = memory_get_usage();
		return $this;
	}

	public function markEndMemory()
	{
		return $this->markMemory('end');
	}

	/**
	 * 统计指定名称的动作，执行所经过的时间。
	 *
	 * 这里会分两个部分记录：
	 * 1. usages，记录的是每个阶段，距离最开始时间，经过的多少时间
	 * 2. totals，记录的是从开始到结束，总共经过了多少时间
	 *
	 * @param string $name 动作名称
	 * @return $this 返回当前的基准测试的实例
	 */
	protected function statTime($name)
	{
		// 时间戳为空
		if (empty($this->timestamps[$name])) {
			return $this;
		}
		// 最终时间戳
		$end = microtime();
		// 初始的时间
		$start = array_shift($this->timestamps[$name]);
		$list = [];
		foreach ($this->timestamps[$name] as $index => $timestamp) {
			$list[$index] = $this->diffTimestamp($start, $timestamp);
		}
		if (!empty($list)) {
			$this->usages[$name][self::TAG_TIME_LIST] = $list;
		}
		$this->usages[$name][self::TAG_TIME] = $this->diffTimestamp($start, $end);
		return $this;
	}

	/**
	 * 统计一个动作的内存使用情况
	 *
	 * 内存的使用统计，必须在动作执行的末尾手动调用`markMemory`，才能生成一个有效的内存使用情况，离开执行的动作，
	 * 就无法拿到这个执行动作实际使用的内存。
	 *
	 * 所以，内存的统计，只有当满足以下条件，才会去统计内存的使用，这和时间不同：
	 * 1. 超过2个以上的内存标记
	 * 2. 并且有0和end两个标记的内存标记，才会去执行具体的内存统计。
	 *
	 *
	 * 这里会分两个部分记录：
	 * 1. usages，记录的是每个阶段，距离最开始时间，经过的多少时间
	 * 2. totals，记录的是从开始到结束，总共经过了多少时间
	 *
	 * @param string $name 动作名称
	 * @return $this 返回当前的基准测试的实例
	 */
	protected function statMemory($name)
	{
		if (!isset($this->usages[$name]) || empty($this->usages[$name][self::TAG_MEMORY_LIST]))
			return $this;
		if (count($this->usages[$name][self::TAG_MEMORY_LIST]) <= 1)
			return $this;

		$max = max($this->usages[$name][self::TAG_MEMORY_LIST]);
		$key = array_search($max, $this->usages[$name][self::TAG_MEMORY_LIST]);
		if ($key !== false) {
			$this->usages[$name][self::TAG_MEMORY_PEAK] = $max;
			$this->usages[$name][self::TAG_MEMORY_PEAK_KEY] = $key;
		}
		if (isset($this->usages[$name][self::TAG_MEMORY_LIST]['end'])) {
			$this->usages[$name][self::TAG_MEMORY] =
				$this->usages[$name][self::TAG_MEMORY_LIST]['end'] -
				$this->usages[$name][self::TAG_MEMORY_LIST]['start'];
		}
		return $this;
	}

	/**
	 * 获得全部基准测试分段统计结果
	 *
	 * @return array
	 */
	public function getUsages()
	{
		return $this->usages;
	}

	/**
	 * 获得指定名称的基准测试结果
	 *
	 * @param string $name
	 * @return bool|array
	 */
	public function getUsage($name)
	{
		return isset($this->usages[$name]) ? $this->usages[$name] : false;
	}

	public function getUsageTime($name)
	{
		$usage = $this->getUsage($name);
		if ($usage === false || !isset($usage[self::TAG_TIME]))
			return false;
		return $usage[self::TAG_TIME];
	}

	public function getUsageMemoryPeak($name)
	{
		$usage = $this->getUsage($name);
		if ($usage === false || !isset($usage[self::TAG_MEMORY_PEAK]))
			return false;
		return $usage[self::TAG_MEMORY_PEAK];
	}

	public function getUsageMemoryPeakKey($name)
	{
		$usage = $this->getUsage($name);
		if ($usage === false || !isset($usage[self::TAG_MEMORY_PEAK_KEY]))
			return false;
		return $usage[self::TAG_MEMORY_PEAK_KEY];
	}

	public function getUsageTimeList($name)
	{
		$usage = $this->getUsage($name);
		if ($usage === false || !isset($usage[self::TAG_TIME_LIST]))
			return false;
		return $usage[self::TAG_TIME_LIST];
	}

	public function getUsageMemoryList($name)
	{
		$usage = $this->getUsage($name);
		if ($usage === false || !isset($usage[self::TAG_MEMORY_LIST]))
			return false;
		return $usage[self::TAG_MEMORY_LIST];
	}

	public function getMemoryPeakList()
	{
		$list = [];
		foreach ($this->usages as $name => $usage) {
			if ($name === 'total')
				continue;
			$list[$name] = isset($usage[self::TAG_MEMORY_PEAK]) ? $usage[self::TAG_MEMORY_PEAK] : false;
		}
		return $list;
	}
}