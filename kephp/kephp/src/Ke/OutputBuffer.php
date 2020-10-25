<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015-2016 KePHP Authors All Rights Reserved
 * @link      http://kephp.com
 * @link      https://git.oschina.net/kephp/kephp-core
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke;

/**
 * 输出缓冲控制器
 *
 * 目前主要常用于Web部分，Cli部分没有使用到。
 *
 * @package Ke
 */
class OutputBuffer
{

	const NODE_ROOT = 'root';

	const NODE_STARTUP = 'startup';

	/** 执行冲刷 */
	const DO_FLUSH = 0;

	/** 执行清理，并获取 */
	const DO_CLEAN = 1;

	private static $instance = null;

	private $root = 0;

	/**
	 * @var null|string 当前节点缓冲层的节点名称
	 */
	private $node = null;

	/**
	 * @var int 当前的缓冲层的深度
	 */
	private $level = -1;

	/**
	 * @var array 当前未回收的缓冲层节点，存储格式为`'node' => level`
	 */
	private $nodes = [];

	/**
	 * @var array 已经回收了的缓冲内容，存储格式为`'node' => 'output'`
	 */
	private $outputs = [];

	/**
	 * @var int 自动生成节点名称的索引值
	 */
	private $autoIndex = 0;

	/**
	 * 获取全局单例实例
	 *
	 * @return OutputBuffer
	 */
	public static function getInstance(): OutputBuffer
	{
		if (!isset(self::$instance)) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * 构造函数
	 */
	final private function __construct()
	{
		$level = ob_get_level();
		$this->root = $level;
		$this->setNode(self::NODE_ROOT, $level);
		if ($level <= 0) {
			$this->start(self::NODE_STARTUP);
		}
		else {
			$this->start(self::NODE_STARTUP);
//			$this->setNode(self::NODE_STARTUP, $level);
		}
	}

	/**
	 * 生成自动的节点名称
	 *
	 * @return string
	 */
	protected function mkAutoNode(): string
	{
		return 'ob_' . (++$this->autoIndex);
	}

	/**
	 * 设定当前的节点层级
	 *
	 * 该方法具有强制扭转缓冲控制的能力，所以作为私有方法。
	 *
	 * @param string $node 节点的名称
	 * @param int    $level 节点的层级
	 * @return $this
	 */
	private function setNode($node, $level)
	{
		$this->nodes[$node] = $level;
		$this->outputs[$node] = null;
		$this->node = $node;
		$this->level = $level;
		return $this;
	}

	/**
	 * 添加缓冲节点
	 *
	 * @param string|null $node 节点名称，如果为空会使用自动生成节点
	 * @param bool        $isStartOb 是否堆叠新一个层缓冲
	 * @return $this
	 */
	protected function addNode($node, $isStartOb = false)
	{
		if (empty($node))
			$node = $this->mkAutoNode();
		if (isset($this->outputs[$node]))
			return $this;
		if ($isStartOb)
			ob_start();
		$level = ob_get_level();
		if ($level > $this->level) {
			$this->setNode($node, $level);
		}
		return $this;
	}

	/**
	 * 启用或堆叠新一个层缓冲
	 *
	 * @param string|null $node 节点名称，如果为空会使用自动生成节点
	 * @return $this
	 */
	public function start($node = null)
	{
		return $this->addNode($node, true);
	}

	/**
	 * 操作输出缓冲回滚
	 *
	 * @param null|string|int $target 回滚的层级、节点，为__空__时表示回滚当前节点，为__int__时，表示回滚的层级，为__string__时，表示为回滚的目标节点。
	 * @param int             $action 回滚执行的操作，`OutputBuffer::DO_CLEAN`表示清空缓冲，并捕获缓冲的内容，`OutputBuffer::DO_FLUSH`则直接冲刷（输出）相应节点的缓存内容
	 * @return $this
	 */
	public function rolling($target = null, $action = self::DO_CLEAN)
	{
		$to = 0;
		if (isset($this->nodes[$target])) {
			// 如果存在这个节点，表示回滚到这个节点
			$to = $this->nodes[$target];
		}
		elseif (is_numeric($target)) {
			if ($target < 0) {
				// 当目标小于0的时候，则表示回滚多少层数
				$to = $this->level + 1 + $target;
			}
			elseif ($target >= 0) {
				// 大于0，表示回滚到具体的某个层数
				$to = $target;
			}
			if ($to > $this->level)
				$to = $this->level;
			elseif ($to < 0)
				$to = 0;
		}
		elseif (!isset($this->nodes[$target])) {
			return $this;
		}
		if ($to < $this->root)
			$to = $this->root;
		$levels = array_flip($this->nodes);
		$level = ob_get_level();
		$node = $this->node;
		while ($level >= $to) {
			if (isset($levels[$this->level])) {
				$node = $levels[$this->level];
				unset($levels[$this->level], $this->nodes[$node]);
			}
			if ($action === self::DO_CLEAN) {
				$content = ob_get_contents();
				if (!empty($content)) {
					if (!isset($this->outputs[$node]))
						$this->outputs[$node] = $content;
					else
						$this->outputs[$node] = $content . $this->outputs[$node];
				}
				ob_end_clean();
			}
			elseif ($action === self::DO_FLUSH) {
				ob_end_flush();
			}
			$level--;
			$this->level = $level;
			$this->node = isset($levels[$this->level]) ? $levels[$this->level] : null;
		}
		return $this;
	}

	/**
	 * 捕获并清空（到）指定节点的缓冲
	 *
	 * @param null|string|int $target 回滚的层级、节点，为__空__时表示回滚当前节点，为__int__时，表示回滚的层级，为__string__时，表示为回滚的目标节点。
	 * @return $this
	 */
	public function clean($target)
	{
		return $this->rolling($target, self::DO_CLEAN);
	}

	/**
	 * 冲刷（输出）出（到）指定节点的缓冲
	 *
	 * @param null|string|int $target 回滚的层级、节点，为__空__时表示回滚当前节点，为__int__时，表示回滚的层级，为__string__时，表示为回滚的目标节点。
	 * @return $this
	 */
	public function flush($target)
	{
		return $this->rolling($target, self::DO_FLUSH);
	}

	/**
	 * 执行指定的函数，将执行过程中输出内存捕获到到缓存层并返回。
	 *
	 * 该函数并不捕获执行过程中抛出的异常，请自行添加`try {} catch {}`
	 *
	 * ```php
	 * $buffer = $web->ob->getFunctionBuffer(null, function() {
	 *     echo 'hello world!';
	 * }); // return 'hello world!'
	 * ```
	 *
	 * @param null|string $node 节点名称，为空时，会自动构建节点名称。
	 * @param callable    $fn 可被执行的函数
	 * @return bool|string 返回执行过程中捕获的缓存内容
	 */
	public function getFunctionBuffer($node, $fn)
	{
		if (empty($node))
			$node = $this->mkAutoNode();
		if (!isset($this->outputs[$node]) && is_callable($fn)) {
			$this->start($node);
			call_user_func($fn);
			$this->clean($node);
			return $this->getOutput($node, true);
		}
		return false;
	}

	/**
	 * 加载指定的文件（单个或多个），并将加载文件时的输出捕获到缓冲层返回。
	 *
	 * ```php
	 * $buffer = $web->ob->getImportBuffer(['path/file1.php', 'path/file2.php'], ['id' => 1]);
	 * ```
	 *
	 * @param string|array $file 要加载文件，可以用数组来加载多个文件
	 * @param array|null   $context 加载文件时要传入的局部变量
	 * @return bool|string 返回加载文件中捕获的缓存内容
	 */
	public function getImportBuffer($file, array & $context = null)
	{
		$node = $this->mkAutoNode();
		if (!isset($this->outputs[$node])) {
			$this->start($node);
			import($file, $context, KE_IMPORT_RAW | KE_IMPORT_CONTEXT);
			$this->clean($node);
			return $this->getOutput($node, true);
		}
		return false;
	}

	/**
	 * 取得当前已捕获的输出缓冲内容的节点列表。
	 *
	 * @return array 返回内容为以多个 key 为数组的列表（非Map）
	 */
	public function getOutputKeys(): array
	{
		return array_keys(array_filter($this->outputs));
	}

	/**
	 * 取得当前已经捕获的输出缓冲内容
	 *
	 * 该函数允许传入指定的keys列表，来获取指定的节点缓冲内容。
	 *
	 * ```php
	 * $buffers = $web->ob->getOutputs('a', 'c');
	 * // 假定节点 a, c都存在，则返回 ['a' => '...', 'c' => '...']
	 * ```
	 *
	 * @return array 返回内容以 `'key' => 'value'` 的键值对存储
	 */
	public function getOutputs(): array
	{
		$keys = func_get_args();
		if (empty($keys)) {
			$outputs = array_filter($this->outputs);
		}
		else {
			$outputs = array_intersect_key($this->outputs, array_flip($keys));
		}
		return $outputs;
	}

	/**
	 * 取出指定节点的输出缓冲内容
	 *
	 * @param string $node 节点名称
	 * @param bool   $isRemove 当存在相应节点时，是否删除该节点的内容
	 * @return string 节点不存在时，将返回空字符，存在时则返回的是对应节点的内容
	 */
	public function getOutput($node, $isRemove = false)
	{
		if (isset($this->outputs[$node])) {
			$output = $this->outputs[$node];
			if ($isRemove)
				unset($this->outputs[$node]);
			return $output;
		}
		return '';
	}
}