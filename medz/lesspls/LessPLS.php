<?php
/**
 * LessPLS v0.2
 * https://github.com/medz/lesspls
 *
 * LESS css compiler, adapted from http://medz.cn
 *
 * Copyright 2016, Seven Du <lovevipdsw@outlook.com>
 * Licensed under MIT, see LICENSE
 */

/**
 * 轮询查询指定目录编译Less
 *
 * @author Seven Du <lovevipdsw@vip.qq.com> 
 **/
class LessPLS
{
	/**
	 * 系统中的目录分隔符
	 *
	 * @var string
	 **/
	protected static $_ = DIRECTORY_SEPARATOR;

	/**
	 * 系统中的换行符
	 *
	 * @var string
	 **/
	protected static $eol = PHP_EOL;

	/**
	 * 锁和缓存目录名称
	 *
	 * @var string
	 **/
	protected static $lock = '.LessPLS';

	/**
	 * 单例数据
	 *
	 * @var object
	 **/
	private static $_instance;

	/**
	 * Less类单例数据
	 *
	 * @var object
	 **/
	private static $_lessInstance;

	/**
	 * 获取单例实例
	 *
	 * @return object self
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	public static function getInstance()
	{
		if (!self::$_instance instanceof self) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * 获取Less单例
	 *
	 * @return object
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	public static function getInstanceByLess()
	{
		if (!self::$_lessInstance instanceof lessc) {
			self::$_lessInstance = new lessc;
		}
		return self::$_lessInstance;
	}

	/**
	 * 构造方法
	 *
	 * @return void
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	private function __construct()
	{
		ignore_user_abort(true);
		set_time_limit(0);
		echo '==========', date('Y-m-d H:i:s'), '==========', self::$eol, 'PLS Start:', self::$eol;
	}

	/**
	 * 析构方法，删除运行锁文件
	 *
	 * @return void
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	public function __destruct()
	{
		$this->rm($this->output . self::$_ . self::$lock);
	}

	/**
	 * 删除函数，自带递归
	 *
	 * @param string $path 删除的地址
	 * @param bool $recursive 是否递归 [true]
	 * @return bool
	 * @author Medz Seven <lovevipdsw@vip.qq.com>
	 **/
	protected function rm($path, $recursive = true)
	{
		// # 判断是否是文件
		if (is_file($path)) {
			return unlink($path);

		// # 判断是否是目录，判断是否递归，不递归，直接尝试删除
		} elseif (is_dir($path) and !$recursive) {
			return rmdir($path);

		// # 判断是否是目录，如果是目录，则递归删除
		} elseif (is_dir($path) && file_exists($path)) {
			// # 打开目录资源
			$handle = opendir($path);

			// # 单条读取内容
			while(false !== ($file = readdir($handle))) {
				// # 判断内容是否是 .|.. 如果是，跳过
				if ($file == '.' or $file == '..') {
					// # 跳过当前循环
					continue;
				}

				// # 递归执行自己
				$this->rm($path . '/' . $file, $recursive);
			}

			// # 关闭目录资源
			closedir($handle);

			// # 尝试删除当前目录
			return rmdir($path);
		}

		// # 如果以上都不满足，则返回真
		return true;
	}

	/**
	 * 输入要检查的目录
	 *
	 * @var string
	 **/
	protected $input;

	/**
	 * 要输出的目录
	 *
	 * @var string
	 **/
	protected $output;

	/**
	 * 是否递归输入目录下的子目录，并编译到输出目录
	 *
	 * @var string
	 **/
	protected $re = false;

	/**
	 * 编译的less文件名后缀
	 *
	 * @var string
	 **/
	protected $ext = 'less';

	/**
	 * 编译后的拓展名
	 *
	 * @var string
	 **/
	protected $nExt = 'css';

	/**
	 * 运行轮询编译Less
	 *
	 * @return void
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	public function run()
	{
		$this->init();
		while (true) {
			try {
				$this->compile();
			} catch (Exception $e) {
				echo "fatal error: ", $e->getMessage(), self::$eol;
			}
			sleep(3);
			if (!file_exists($this->output . self::$_ . self::$lock)) {
				$this->showError('Execution is over.');
			}
		}
	}

	/**
	 * 获取输入目录文件列表
	 *
	 * @return array
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	public function getInputFiles($path, $rootDir = '')
	{
		$files = array();
		$handle = opendir($path);
		while (false != ($file = readdir($handle))) {
			if ($file == '.' || $file == '..' || $file == self::$lock) {
				continue;
			} elseif (is_file($path . self::$_ . $file)) {
				if (pathinfo($path . self::$_ . $file, PATHINFO_EXTENSION) == $this->ext) {
					array_push($files, $rootDir . self::$_ . $file);
				}
			} elseif (is_dir($path . self::$_ . $file) && $this->re) {
				$files = array_merge($files, $this->getInputFiles($path . self::$_ . $file,  $rootDir . self::$_ . $file));
			}
		}
		return $files;
	}

	/**
	 * 遍历目录执行编译
	 *
	 * @return void
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	protected function compile()
	{
		foreach ($this->getInputFiles($this->input) as $file) {
			$filemd5 = md5_file($this->input . self::$_ . $file, false);
			$md5che  = $this->output . self::$_ . self::$lock . self::$_ . md5($file, false);
			$of  = dirname($file) . self::$_ . pathinfo($file, PATHINFO_FILENAME) . '.' . $this->nExt;
			if (file_exists($md5che) && $filemd5 == file_get_contents($md5che) && file_exists($this->output . self::$_ . $of)) {
				continue;
			}
			$out = self::getInstanceByLess()->compile(file_get_contents($this->input . self::$_ . $file));
			$this->rw($this->output . self::$_ . $of, $out);
			file_put_contents($md5che, $filemd5);
			echo date('Y-m-d H:i:s', time()), '    ', $this->input, self::$_ , $file, ' -> ', $this->output, self::$_, $of, self::$eol;
			unset($out, $filemd5, $md5che, $of);
		}
	}

	/**
	 * 递归写入文件数据
	 *
	 * @param string $file 路径文件名
	 * @param string $data 写入的数据
	 * @return void
	 * @author Seve Du <lovevipdsw@vip.qq.com>
	 **/
	public function rw($file, $data)
	{
		$dir = dirname($file);
		if (!is_dir($dir) && !file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
		file_put_contents($file, $data);
		unset($dir, $file, $data);
	}

	/**
	 * 初始化
	 * 用于获取和检查必要的参数等是否正常
	 *
	 * @return self
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	protected function init()
	{
		/* # 获取输入流 */
		$io = $_SERVER['argv'];
		$io && array_shift($io);
		/* # 获取输入的目录 */
		$this->input  = $this->getInputParam('-i', $io);
		/* # 获取输出目录 */
		$this->output = $this->getInputParam('-o', $io, $this->input);
		/* # 获取是否需要递归 */
		$this->re     = $this->getInputParam('-r', $io, $this->re);
		/* # 获取less文件拓展名 */
		$this->ext    = $this->getInputParam('-e', $io, $this->ext);
		/* # 获取编译后保存的拓展名 */
		$this->nExt   = $this->getInputParam('-n', $io, $this->nExt);
		unset($io);
		/* # 检查环境是否满足 */
		return $this->check();
	}

	/**
	 * 获取对应参数
	 *
	 * @param string $name 关键字
	 * @param array $vals 数组合集
	 * @param void $default 默认参数
	 * @return string 得到参数值
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	protected function getInputParam($name, array $vals, $default = null)
	{
		$s = array();
		foreach ($vals as $val) {
			if (substr($val, 0, 2) == $name) {
				$val = substr($val, 2);
				if (!$val && $val !== 0 && $val !== '0') {
					$this->showError('param:' . $name . ' is empty.');
				}
				array_push($s, $val);
			}
		}
		if ($default === null && count($s) <= 0) {
			$this->showError('param:' . $name . ' value was not found.');
		} elseif (count($s) > 1) {
			$this->showError('param:' . $name . ' have two.');
		} elseif (count($s)) {
			$s = array_pop($s);
		} elseif ($default !== null) {
			$s = $default;
		}
		unset($name, $default, $vals);
		return $s;
	}

	/**
	 * 检查目录是否可写
	 *
	 * @param string $dir 要检查的目录
	 * @return boolean
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	protected function checkDirIsW($dir)
	{
		if (!is_dir($dir . self::$_ . self::$lock)) {
			mkdir($dir . self::$_ . self::$lock, 0777, true);
		}
		if (file_exists($dir . self::$_ . self::$lock)) {
			return true;
		}
		return false;
	}

	/**
	 * 检查环境是否满足
	 *
	 * @return void
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	protected function check()
	{
		/* # 检查输入目录是否存在 */
		if (!is_dir($this->input)) {
			$this->showError($this->input . ' Directory does not exist.');

		/* # 检查输出目录是否存在 */
		} elseif (!is_dir($this->output)) {
			$this->showError($this->output . ' Directory does not exist.');

		/* # 检查输出目录是否可写 */
		} elseif (!$this->checkDirIsW($this->output)) {
			$this->showError($this->output . ' Directory is not to write.');
		}
		return $this;
	}

	/**
	 * 显示错误
	 *
	 * @param string $message 显示的消息
	 * @return void
	 * @author Seven Du <lovevipdsw@vip.qq.com>
	 **/
	protected function showError($message)
	{
		echo $message, self::$eol;
		exit;
	}

} // END class LessPLS