<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */


// 全局的常量

/** APP启动引导的时间 */
defined('KE_BOOTSTRAP_TIME') || define('KE_BOOTSTRAP_TIME', microtime());

/** 当前执行脚本的完整路径 */
define('KE_SCRIPT_PATH', realpath($_SERVER['SCRIPT_FILENAME']));
/** 当前执行脚本的目录路径 */
define('KE_SCRIPT_DIR', dirname(KE_SCRIPT_PATH));
/** 当前执行脚本的文件名*/
define('KE_SCRIPT_FILE', basename(KE_SCRIPT_PATH));

/**
 * 类库的版本号
 */
const KE_VER = '1.2.54';

/**
 * 命令行模式
 */
const KE_CLI = 'cli';
/**
 * 网页模式
 */
const KE_WEB = 'web';

/** 当前PHP运行的模式，只有两种，cli和web */
define('KE_APP_MODE', PHP_SAPI === 'cli' ? KE_CLI : KE_WEB);

/** KE_APP_ENV:开发环境 */
const KE_DEV = 'development';
/** KE_APP_ENV:测试环境 */
const KE_TEST = 'test';
/** KE_APP_ENV:开发环境 */
const KE_PRO = 'production';

// PHP变量类型的字面值，即为gettype方法返回的结果
/** null类型 */
const KE_NULL = 'NULL';
/** 布尔类型 */
const KE_BOOL = 'boolean';
/** 整型类型 */
const KE_INT = 'integer';
/** 浮点 */
const KE_FLOAT = 'double';
/** 字符串类型 */
const KE_STR = 'string';
/** 数组类型 */
const KE_ARY = 'array';
/** 对象类型 */
const KE_OBJ = 'object';
/** 资源类型 */
const KE_RES = 'resource';

/** 命名空间的分隔符 */
const KE_DS_CLS = '\\';
/** Windows系统的目录分隔符 */
const KE_DS_WIN = '\\';
/** Unix, Linux系统的目录分隔符 */
const KE_DS_UNIX = '/';

const DS = DIRECTORY_SEPARATOR;


/** 是否WINDOWS环境 */
const KE_IS_WIN = DIRECTORY_SEPARATOR === KE_DS_WIN;

/** 路径名中的噪音值，主要用trim函数中 */
const KE_PATH_NOISE = '/\\ ';

// 以下常量，对应的是import方法专用的参数，用于说明import返回结果的内容
/** import返回原始的数据 */
const KE_IMPORT_RAW = 0b0;
/** import返回成功加载的文件名（并不转为realpath） */
const KE_IMPORT_PATH = 0b10;
/** import返回结果以数组形式返回，如果非数组，将强制转数组，多个文件时，为追加方式，即当出现重复key的时候，后来者不能覆盖前者 */
const KE_IMPORT_ARRAY = 0b100;
const KE_IMPORT_MERGE = 0b101;

const KE_IMPORT_CONTEXT = 0b1000;

// 必须的函数

if (!function_exists('import')) {
	/**
	 * 加载一个或多个文件。
	 *
	 * ```php
	 * $result = import('a.php'); // a.php存在，返回return 'a'，则此函数就返回'a'
	 *
	 * $result = import('b.php'); // b.php不存在，则该次执行返回false
	 *
	 * $result = import(['a.php', 'b.php']); // ['a', false]
	 * ```
	 *
	 * 传递局部变量
	 * ```php
	 * import('classes.php', ['dir' => '/var/www/myApp']);
	 *
	 * // classes.php
	 * return [
	 *     'App' => $dir . '/App.php',
	 * ];
	 * ```
	 *
	 * 指定不同的模式
	 * ```php
	 * $vars = ['dir' => '/var/www/myApp'];
	 *
	 * // 加载单个文件
	 * import('classes.php', $vars, KE_IMPORT_RAW);   // [ 'App' => '/var/www/myApp/App.php' ]
	 * import('classes.php', $vars, KE_IMPORT_PATH);  // [ 'config.php' ] 已经加载过的路径
	 * import('classes.php', $vars, KE_IMPORT_ARRAY); // [ 'App' => '/var/www/myApp/App.php' ]
	 *
	 * // 加载多个文件
	 * import(['a.php', 'classes.php'], $vars, KE_IMPORT_RAW);   // ['a', [ 'App' => '/var/www/myApp/App.php' ]]
	 * import(['a.php', 'classes.php'], $vars, KE_IMPORT_PATH);  // ['a.php', 'config.php']
	 * import(['a.php', 'classes.php'], $vars, KE_IMPORT_ARRAY); // [ 'App' => '/var/www/myApp/App.php' ] a.php返回的内容不是数组，所以没有
	 * ```
	 *
	 * 假定有classes2.php文件，有如下内容：
	 * ```php
	 * // classes2.php
	 * return [
	 *     'App' => $dir . '/xErp/App.php',
	 *     'Web' => $dir . '/xErp/Web.php',
	 * ];
	 * ```
	 *
	 * ```php
	 * $vars = ['dir' => '/var/www/myApp'];
	 *
	 * import(['classes.php', 'classes2.php'], $vars, KE_IMPORT_ARRAY);
	 * // [ 'App' => '/var/www/myApp/App.php', 'Web' => '/var/www/myApp/xErp/Web.php', ]
	 * // 注意，第二个文件的App无法覆盖已经存在的App。
	 *
	 * import(['classes.php', 'classes2.php'], $vars, KE_IMPORT_MERGE);
	 * // [ 'App' => '/var/www/myApp/xErp/App.php', 'Web' => '/var/www/myApp/xErp/Web.php', ]
	 * // 注意，第二个文件的App已经覆盖掉第一个文件的App。
	 * ```
	 *
	 * 上下文环境模式
	 * ```php
	 * // classes_rebase.php
	 * $dir = '/var/www/newApp';
	 * ```
	 *
	 * ```php
	 * $vars = ['dir' => '/var/www/myApp'];
	 * import(['classes_rebase.php', 'classes.php', 'classes2.php'], $vars, KE_IMPORT_ARRAY | KE_IMPORT_CONTEXT);
	 * // [ 'App' => '/var/www/newApp/App.php', 'Web' => '/var/www/newApp/xErp/Web.php', ]
	 * // classes_rebase.php文件已经修改了dir的变量，之后的文件dir都已经改变。
	 *
	 * echo $vars['dir'];
	 * // 仍然是：'/var/www/myAp'。这个上下文环境只在import函数的流程内有效，并不会改变外部的变量。
	 * ```
	 *
	 * 加载模式的说明
	 *
	 * **KE_IMPORT_RAW** 返回引用源数据
	 * **KE_IMPORT_PATH** 加载成功，则返回该文件的路径（非全路径）
	 * **KE_IMPORT_ARRAY** 加载成功，返回的结果强制转为一个数组，对于`return false`或`return 'abc'`这种，将返回一个空数组，只有返回一个对象或者数组的时候为有效的数据。当加载多个文件时，会将多个文件的结果，合并为一个数组返回，这种合并不是merge方法，当存在相同的key时，后者不能覆盖已经存在的key。
	 * **KE_IMPORT_MERGE** 与`KE_IMPORT_ARRAY`行为类似，但是merge操作，即同key时，后来者会覆盖前者。
	 * **KE_IMPORT_CONTEXT** 在上述四种模式下，可以附加多此模式，字面意思为，保留上下文环境。
	 *
	 * 注意，上述的**KE_IMPORT_ARRAY**和**KE_IMPORT_MERGE**都只是单层合并，不会做深层合并。
	 *
	 * @param string|array  $_path   要加载的路径，可以是字符串表示加载单个文件，也可以是数组表示加载多个文件。
	 * @param array         $_vars   需要在加载时注入的环境变量，这些环境变量在加载的文件中会作为局部变量。
	 * @param int           $_mode   加载文件、返回结果时的模式。
	 * @param array        &$_result 附加的返回数据，只对加载多个文件时有效
	 * @return bool|array|mixed
	 */
	function import($_path, array $_vars = null, int $_mode = KE_IMPORT_RAW, array &$_result = null)
	{
		if (empty($_path))
			return false;
		$_modeArray = ($_mode & KE_IMPORT_ARRAY) === KE_IMPORT_ARRAY;
		$_modeMerge = ($_mode & KE_IMPORT_MERGE) === KE_IMPORT_MERGE;
		if (is_array($_path)) {
			$_result = $_result ?? [];
			if (!empty($_vars)) {
				$_extractMode = EXTR_SKIP;
				if (($_mode & KE_IMPORT_CONTEXT) === KE_IMPORT_CONTEXT)
					$_extractMode = EXTR_SKIP | EXTR_REFS;
				extract($_vars, $_extractMode);
			}
			foreach ($_path as $_index => $_item) {
				$_return = false;
				$_isImport = false;
				if (!empty($_item)) {
					if (is_array($_item)) {
						import($_item, $_vars, $_mode, $_result);
						continue;
					}
					if (is_file($_item) && is_readable($_item)) {
						$_return = require $_item;
						$_isImport = true;
					}
				}
				if ($_isImport) {
					if ($_modeArray) {
						if (is_object($_return))
							$_return = (array)$_return;
						if (!empty($_return) && is_array($_return)) {
							if ($_modeMerge)
								$_result = $_return + $_result;
							else
								$_result += $_return;
						}
						// 其他的字符串、布尔、数值无法转化为等量的array，就放弃不管了。
					} elseif ($_mode === KE_IMPORT_PATH)
						$_result[] = $_item;
				}
				// 多层数组的文件时，这里无法维持原来的索引顺序了
				if ($_mode === KE_IMPORT_RAW)
					$_result[] = $_return;
			}
			return $_result;
		} else {
			$_return = false;
			$_isImport = false;
			if (!empty($_vars)) {
				// 上下文模式，在单个文件里面，是没效果的，只在多个文件中有意义
				extract($_vars, EXTR_SKIP);
			}
			if (is_file($_path) && is_readable($_path)) {
				$_return = require $_path;
				$_isImport = true;
			}
			// 强转数组的格式，必须是成功加载的时候，才进行转换
			if ($_isImport) {
				if ($_modeArray) {
					if (is_array($_return))
						return $_return;
					elseif (is_object($_return))
						return (array)$_return;
					return [];
				} elseif ($_mode === KE_IMPORT_PATH)
					return $_path;
			}
			return $_return;
		}
	}
}

if (!function_exists('parse_class')) {
	/**
	 * 将一个类名解析出namespaceName和className。
	 *
	 * 返回的结果为一个数组，0: namespaceName，1: className。
	 *
	 * 如果class不包含namespace，则0位为null。
	 *
	 * ```php
	 * list($ns, $cls) = parse_class('\MyApp\Hello_World');
	 * // $ns => MyApp
	 * // $cls => Hello_World
	 * list($ns, $cls) = parse_class('Hello_World');
	 * // $ns => null
	 * // $cls => Hello_World
	 * ```
	 *
	 * @param string $class
	 * @return array
	 */
	function parse_class(string $class): array
	{
		$class = trim($class, KE_PATH_NOISE);
		if (empty($class))
			return [null, null];
		if (($pos = strrpos($class, '\\')) !== false) {
			return [substr($class, 0, $pos), substr($class, $pos + 1)];
		}
		return [null, $class];
	}
}

if (!function_exists('error_name')) {
	/**
	 * 根据php的错误类型的整型值，转为字符输出
	 *
	 * ```php
	 * error_name(E_ERROR); // 'Error'
	 * ```
	 *
	 * @param int $code
	 * @return string
	 */
	function error_name(int $code): string
	{
		static $codes;
		if (!isset($codes)) {
			$codes = [
				0                   => 'Unknown',
				E_ERROR             => 'Error',
				E_WARNING           => 'Warn',
				E_PARSE             => 'Parse Error',
				E_NOTICE            => 'Notice',
				E_CORE_ERROR        => 'Error(Core)',
				E_CORE_WARNING      => 'Warn(Core)',
				E_COMPILE_ERROR     => 'Error(Compile)',
				E_COMPILE_WARNING   => 'Warn(Compile)',
				E_USER_ERROR        => 'Error(User)',
				E_USER_WARNING      => 'Warn(User)',
				E_USER_NOTICE       => 'Notice(User)',
				E_STRICT            => 'Strict',
				E_RECOVERABLE_ERROR => 'Error',
			];
		}
		return isset($codes[$code]) ? $codes[$code] : $codes[0];
	}
}

if (!function_exists('depth_query')) {
	/**
	 * 深度查询一个数据（数组或对象），并取回对应的值。
	 *
	 * ```php
	 * $data = [
	 *     'a' => [
	 *         'a1' => [ 'a1-1', 'a1-2', 'a1-3'],
	 *         'a2' => 'realy a2'
	 *     ],
	 *     'b' => ['b1', 'b2', 'b3],
	 * ];
	 *
	 * depth_query($data, 'a->a1->0');          // 返回'a1-1'
	 * depth_query($data, 'a->a1->4', false);   // 并不存在，你可以指定他的默认值，这里会返回你指定的false。
	 * depth_query($data, 'b.1', false, '.');   // 你还可以指定keys的分隔符
	 * depth_query($data, ['b', 6], false);     // keys也可以是一个数组
	 * ```
	 *
	 * @param array|object $data    数据源
	 * @param string|array $keys    查询的keys，字符串格式为：`'key1->key2->0'`，数组格式：`array('key1', 'key2', 0)`
	 * @param mixed        $default 默认值，当查询的keys的值不存在时，返回该默认值。
	 * @param string       $keysSpr
	 * @return mixed
	 */
	function depth_query($data, $keys, $default = null, string $keysSpr = '->')
	{
		if (empty($keys))
			return $data;
		$keysType = gettype($keys);
		if ($keysType === KE_STR) {
			if (strpos($keys, $keysSpr) !== false) {
				$keys = explode($keysSpr, $keys);
				$keysType = KE_ARY;
			} else {
				// Janpoem 2014.09.21
				// 调整了一些，原来只是检查isset，现在增加empty的判断
				// 需要做更长时间的监控，是否有副作用
				if (is_array($data))
					return isset($data[$keys]) ? $data[$keys] : $default;
				elseif (is_object($data))
					return isset($data->{$keys}) ? $data->{$keys} : $default;
				else
					return $default;
//				if (is_array($data))
//					return !isset($data[$keys]) || ($data[$keys] !== 0 && $data[$keys] !== 0.00 && $data[$keys] !== '0' && empty($data[$keys])) ? $default : $data[$keys];
//
//				elseif (is_object($data))
//					return !isset($data->{$keys}) ||
//					       ($data->{$keys} !== 0 && $data->{$keys} !== 0.00 && $data->{$keys} !== '0' && empty($data->{$keys})) ? $default : $data->{$keys};
//				else
//					return $default;
			}
		}
		if (!empty($data) || (!empty($keys) && $keysType === KE_ARY)) {
			foreach ($keys as $key) {
				if (!is_numeric($key) && empty($key)) continue;
				// 每次循环，一旦没有$key，就退出
				// 不是array的話也到頭了
				// Janpoem 2014.09.21
				// 调整了一些，原来只是检查isset，现在增加empty的判断
				// 需要做更长时间的监控，是否有副作用
				if (is_array($data)) {
					if (!isset($data[$key]))
						return $default;
					else
						$data = $data[$key];
//					if (!isset($data[$key]) || ($data[$keys] !== 0 && $data[$keys] !== 0.00 && $data[$keys] !== '0' && empty($data[$key])))
//						return $default;
//					else
//						$data = $data[$key];
				} elseif (is_object($data)) {
					if (!isset($data->{$key}))
						return $default;
					else
						$data = $data->{$key};
//					if (!isset($data->{$key}) || ($data->{$keys} !== 0 && empty($data->{$key})))
//						return $default;
//					else
//						$data = $data->{$key};
				}
			}
			return $data;
		} else
			return $default;
	}
}

if (!function_exists('equals')) {
	/**
	 * 判断两个值的内容是否相等，对于非数组、对象、资源类型的值，会将其转为字符串进行比较。
	 *
	 * ```php
	 * equals('10', 10);  // true
	 * equals('a', ' a'); // false，后者多了一个空格
	 * ```
	 *
	 * @param mixed $old
	 * @param mixed $new
	 * @return bool 是否相等
	 */
	function equals($old, $new): bool
	{
		if ($old === $new)
			return true;
		$oldType = gettype($old);
		$newType = gettype($new);
		if ($oldType !== KE_ARY && $oldType !== KE_OBJ && $oldType !== KE_RES &&
			$newType !== KE_ARY && $newType !== KE_OBJ && $newType !== KE_RES
		) {
			if ($old === true) $old = 1;
			elseif ($old === false) $old = 0;
			if ($new === true) $new = 1;
			elseif ($new === false) $new = 0;
			return strval($old) === strval($new);
		} else {
			return $old === $new;
		}
	}
}


if (!function_exists('diff_micro')) {
	/**
	 * 比较两个时间戳的差值，返回结果单位为微秒
	 *
	 * @param string      $start
	 * @param null|string $end
	 * @return float
	 */
	function diff_micro(string $start, string $end = null): float
	{
		list($startUS, $startMS) = explode(' ', $start);
		if (empty($end))
			$end = microtime();
		list($endUS, $endMS) = explode(' ', $end);
		return ((float)$endUS + (float)$endMS) - ((float)$startUS + (float)$startMS);
	}
}

if (!function_exists('diff_milli')) {
	/**
	 * 比较两个时间戳的差值，返回结果单位为毫秒
	 *
	 * @param string      $start
	 * @param null|string $end
	 * @return float
	 */
	function diff_milli(string $start, string $end = null): float
	{
		return diff_micro($start, $end) * 1000;
	}
}

if (!function_exists('substitute')) {
	/**
	 * 文本字符替换（函数命名源自Mootools）。
	 *
	 * *原本作为Utils\String包里面的函数，现在将他提取到Common中。*
	 *
	 * ```php
	 * // 基本的使用
	 * $str = '你好，{name}！';
	 * $args = ['name' => 'kephp'];
	 * substitute($str, $args); // '你好，kephp！'
	 *
	 * // 如果变量不存在的话：
	 * $str = '你好，{name}！今天是星期{weekDay}。';
	 * substitute($str, $args); // '你好，kephp！今天是星期。'
	 *
	 * // 他会循环检查
	 * $str = '你好，{name}！{tail}';
	 * $args = [
	 *     'name'    => 'kephp',
	 *     'weekDay' => '六',
	 *     'tail'    => '今天是星期{weekDay}。',
	 * ];
	 *
	 * substitute($str, $args); // '你好，kephp！今天是星期六。'
	 *
	 * // 指定第三个参数，可以取得这次文本替换过程中所匹配到的keywords和对应的变量内容
	 * substitute($str, $args, $matches); // matches将会包含: name, weekDay, tail
	 * ```
	 *
	 * 本函数支持多层数组、对象的深度查询
	 *
	 * ```php
	 * $str = '你好，{name}！{tail}{template}';
	 * $args = [
	 *     'name' => 'kephp',
	 *     'message' => [
	 *         'title' => '认识你很高兴！',
	 *         'content' => '我想了解更多的讯息！',
	 *     ],
	 *     'template' => '<h1>{message->title}</h1><div>{message->content}</div>',
	 *     'weekDay' => '六',
	 *     'tail' => '今天是星期{weekDay}。',
	 * ];
	 * substitute($str, $args);
	 * // 返回：你好，kephp！今天是星期六。<h1>认识你很高兴！</h1><div>我想了解更多的讯息！</div>
	 * ```
	 *
	 * 第四个参数`$regex`，允许设定你自定义的变量的正则匹配表达式。
	 *
	 * @param string $str
	 * @param array  $args
	 * @param string $regex
	 * @param array  $matches
	 * @return string
	 */
	function substitute(string $str, array $args = [], array & $matches = [], $regex = '#\{([^\{\}\r\n]+)\}#'): string
	{
		if (empty($str))
			return '';
		if (empty($args)) // 没有参数，就表示无需替换了
			return $str;
		if (empty($regex))
			$regex = '#\{([^\{\}\r\n]+)\}#';
		if (preg_match($regex, $str)) {
			$str = preg_replace_callback($regex, function ($m) use ($args, & $matches) {
				$key = $m[1];
				$matches[$key] = ''; // give a default empty string
				if (isset($args[$key]) || isset($args->$key)) {
					$matches[$key] = $args[$key];
				} else {
					$matches[$key] = depth_query($args, $key, '');
				}
				return $matches[$key];
			}, $str);
			return substitute($str, $args, $matches, $regex);
		}
		return $str;
	}
}

global $KE;

if (!function_exists('ext')) {
	/**
	 * 对给定的路径名追加后缀文件名，注意，这里会强制将追加的后缀名转为小写格式。
	 *
	 * ```php
	 * ext('/var/www/index.html', 'html'); // 已经有html了，结果还是/var/www/index.html
	 * ext('/var/www/index.html', 'php');  // /var/www/index.html.php
	 * ```
	 *
	 * @param string $path
	 * @param string $ext
	 * @return string
	 */
	function ext(string $path, string $ext): string
	{
		if (!empty($path) && !empty($ext)) {
			if ($ext[0] !== '.')
				$ext = '.' . $ext;
			// 大小写有差异，应确保所有文件后缀应为小写
			if (strcasecmp(strrchr($path, '.'), $ext) !== 0)
				$path = $path . strtolower($ext);
		}
		return $path;
	}
}

if (!function_exists('real_path')) {
	/**
	 * php的realpath，返回路径名的绝对路径名，并判断路径（文件或目录）是否存在。
	 *
	 * _不明白为什么php 7.0以后，realpath的执行效率异常低下。_
	 *
	 * 这个函数会将执行过一次的realpath结果，放入一个全局变量中，第二次再执行，就会从全局变量中去取。而不会再次执行realpath函数。
	 *
	 * @param string $path    路径名
	 * @param bool   $refresh 是否刷新缓存
	 * @return string|false 路径存在，则返回路径名，不存在则返回false
	 */
	function real_path(string $path, $refresh = false)
	{
		global $KE;
		$paths = &$KE['paths'];
		// realpath居然不支持phar，这是bug吗？
		if (!isset($paths[$path]) || !!$refresh) {
			$realPath = realpath($path);
			if ($realPath === false) {
				if (file_exists($path)) {
					$realPath = $path;
				}
			}
			if ($realPath !== false) {
				if (strpos($realPath, KE_DS_WIN) !== false)
					$realPath = str_replace(KE_DS_WIN, KE_DS_UNIX, $realPath);
			}
			$paths[$path] = $realPath;
		}
		return $paths[$path];
	}
}

if (!function_exists('real_dir')) {
	/**
	 * real_path的目录版，判定标准是路径为目录。
	 *
	 * @param string $path 路径名
	 * @param bool   $refresh 是否刷新缓存
	 * @return string|false 路径是一个目录，则返回全路径，不存在则返回false
	 */
	function real_dir(string $path, $refresh = false)
	{
		global $KE;
		$realPath = $KE['paths'][$path] ?? real_path($path, $refresh);
		if ($realPath === false) {
			if (!$refresh)
				return false;
			$realPath = real_path($path, $refresh);
			if ($realPath === false)
				return false;
		}
		if (!isset($KE['stats'][$realPath][0]))
			$KE['stats'][$realPath][0] = $realPath === false ? false : is_dir($realPath);
		return $KE['stats'][$realPath][0] ? $realPath : false;
	}
}

if (!function_exists('real_file')) {
	/**
	 * real_path的文件版，判定标准是路径为文件。
	 *
	 * @param string $path 路径名
	 * @param bool   $refresh 是否刷新缓存
	 * @return string|false 路径是一个文件，则返回全路径，不存在则返回false
	 */
	function real_file(string $path, $refresh = false)
	{
		global $KE;
		$realPath = $KE['paths'][$path] ?? real_path($path, $refresh);
		if ($realPath === false) {
			if (!$refresh)
				return false;
			$realPath = real_path($path, $refresh);
			if ($realPath === false)
				return false;
		}
		if (!isset($KE['stats'][$realPath][1])) {
			$KE['stats'][$realPath][1] = $realPath === false ? false : is_file($realPath);
		}
		return $KE['stats'][$realPath][1] ? $realPath : false;
	}
}

if (!function_exists('parse_path')) {
	/**
	 * 特定的路径解析方法
	 *
	 * 返回的结果以数组装载。
	 *
	 * * 0: 目录名
	 * * 1：文件名
	 * * 2：文件后缀格式
	 *
	 * 如果路径不包含相应部分的数据，那个值会为null，后缀名会强制转为小写
	 *
	 * ```php
	 * parse_path('a', true);      // [null, 'a'], 无目录，a文件名
	 * parse_path('a/b', true);    // ['a', 'b'], a目录，b文件名
	 * parse_path('a/b/c', true);  // ['a/b', 'c'], a/b目录，c文件名
	 *
	 * parse_path('a/', true);     // ['a', null], a目录，无文件
	 * parse_path('a/b/', true);   // ['a/b', null], a/b目录，无文件
	 * parse_path('a/b/c/', true); // ['a/b/c', null], a/b/c目录，无文件
	 *
	 * // 第二个参数表示的是否将后缀文件名拆解出来
	 * parse_path('a/b/c.html', true)；    // ['a/b', 'c', 'html'], true为拆解，默认值
	 * parse_path('a/b/c.html', false);    // ['a/b', 'c.html'], false为不拆解
	 * ```
	 *
	 * @param string $path
	 * @return array 返回数据格式：[目录, 文件名, 后缀名]
	 */
	function parse_path(string $path, bool $parseFormat = true): array
	{
		$return = [null, null];
		if ($path !== '') {
			if (preg_match('#^(?:(.*)[\/\\\\])?([^\/\\\\]+)?$#', $path, $matches)) {
				if (!empty($matches[1])) {
//					$return[0] = preg_replace('#(\/+)$#', '', $matches[1]);
					$return[0] = rtrim($matches[1], KE_PATH_NOISE);
				}
				if (isset($matches[2]) && $matches[2] !== '') {
					if ($parseFormat && ($pos = strrpos($matches[2], '.')) > 0) {
						$return[1] = substr($matches[2], 0, $pos);
						$return[2] = strtolower(substr($matches[2], $pos + 1));
					} else {
						$return[1] = $matches[2];
					}
				}
			}
		}
		return $return;
	}
}

if (!function_exists('compare_path')) {
	/**
	 * 比较两个路径，返回相同的部分
	 *
	 * 必须确保两个传入的路径都是被净化处理过的路径名，不包含类如/../，并且请确保传入的路径都有一致的目录分隔符。
	 * 本函数不会自动调用purge的函数，请调用前自己执行
	 *
	 * ```php
	 * compare_path('/aa/bb/cc', '/aa/bb/dd'); // => aa/bb
	 *
	 * // 这个函数还可以用于挑出两个字符串相同的部分
	 * compare_path('ab-cd-ef-gh-ij', 'ab-cd-ef-gh-abc', '-'); // => ab-cd-ef-gh
	 * ```
	 *
	 * @param string      $source
	 * @param string      $target
	 * @param string      $delimiter
	 * @param string|null $prefix
	 * @return string
	 */
	function compare_path(string $source = null, string $target = null, string $delimiter = KE_DS_UNIX, string $prefix = null): string
	{
		if (empty($source) || empty($target))
			return false;
		if (empty($delimiter))
			$delimiter = KE_DS_UNIX;
		$source = trim($source, KE_PATH_NOISE);
		$target = trim($target, KE_PATH_NOISE);
		$result = [];
		$splitSource = explode($delimiter, $source);
		$splitTarget = explode($delimiter, $target);
		if (!empty($splitSource) && !empty($splitTarget)) {
			foreach ($splitSource as $index => $str) {
				if (!isset($splitSource[$index]) ||
					!isset($splitTarget[$index]) ||
					strcasecmp($splitSource[$index], $splitTarget[$index]) !== 0
				) {
					break;
				}
				$result[] = $str;
			}
		}
		if (!empty($result))
			return $prefix . implode($delimiter, $result);
		return false;
	}
}


/** 点（./../）删除处理 */
const KE_PATH_DOT_REMOVE = 0b00;
/** 点（./../）转为正确的路径的处理 */
const KE_PATH_DOT_NORMALIZE = 0b01;
/** 保持点（./../），不做任何处理 */
const KE_PATH_DOT_KEEP = 0b10;
/** 最开头的路径分隔符强制清除 */
const KE_PATH_LEFT_TRIM = 0b0000;
/** 最开头的路径分隔符强制保留（如果没有会自动补充） */
const KE_PATH_LEFT_REMAIN = 0b0100;
/** 最开头的路径分隔符维持原样 */
const KE_PATH_LEFT_NATIVE = 0b1000;

if (!function_exists('purge_path')) {
	/**
	 * 净化一个路径名
	 *
	 * @param string $path
	 * @param int    $mode
	 * @param string $spr
	 * @param null   $noise
	 * @return string
	 */
	function purge_path(string $path, int $mode = 0, string $spr = DS, $noise = null): string
	{
		// 路径左边的处理模式
		$left = ($mode >> 2) << 2;
		// 路径的.处理模式
		$dot = $mode ^ $left;
		// 过滤$spr，基于spr来确定noise
		if (empty($spr))
			$spr = DS;
		elseif ($spr !== DS) {
			$len = mb_strlen($spr); // 这里要用mb来判断，因为可能输出的非ascii字符
			if ($len <= 0)
				$spr = DS;
			elseif ($len > 1)
				$spr = mb_substr($spr, 0, 1);
		}
		// 这里只能针对特定的情况下，补充noise
		if (empty($noise)) {
			if ($spr === KE_DS_WIN)
				$noise = KE_DS_UNIX;
			elseif ($spr === KE_DS_UNIX)
				$noise = KE_DS_WIN;
		}
		// 噪音不为空，则先将路径值中的噪音去掉
		if (!empty($noise))
			$path = str_replace($noise, $spr, $path);

		$isWinPath = false;
		$isAbsPath = false;
		$head = null;
		if ($isWinPath = preg_match('#^([a-z]\:)[\/\\\\]#i', $path, $matches)) {
			$size = strlen($matches[0]);
			$head = substr($path, 0, $size);
			$path = substr($path, $size);
			$path = trim($path, KE_PATH_NOISE);
			$isAbsPath = true; // 符合windows风格的路径名，必然是绝对路径
		} else {
			if (isset($path[0]) && $path[0] === $spr)
				$isAbsPath = true;
			$path = trim($path, KE_PATH_NOISE);
		}

		if (!empty($path) && $path !== $spr) {
			$path = urldecode($path);
			$sprQuote = preg_quote($spr);
			if ($dot === KE_PATH_DOT_NORMALIZE) {
				$split = explode($spr, $path);
				$temp = [];
				foreach ($split as $index => $part) {
					if ($part === '.' || $part === $spr || empty($part))
						continue;
					elseif ($part === '..') {
						array_pop($temp);
						continue;
					} else {
						$temp[] = $part;
					}
				}
				$path = implode($spr, $temp);
			} elseif ($dot === KE_PATH_DOT_REMOVE) {
				if (preg_match('#(\.{1,}[' . $sprQuote . ']{1,})#', $path))
					$path = preg_replace('#(\.{1,}[' . $sprQuote . ']{1,})#', $spr, $path);
				$path = preg_replace('#[' . $sprQuote . ']+#', $spr, $path);
			} else {
				$path = preg_replace('#[' . $sprQuote . ']+#', $spr, $path);
			}
		}

		if ($isWinPath) {
			// windows的路径风格，就忽略$left的设置了
			$path = $head . $path;
		} else {
			if ($left === KE_PATH_LEFT_NATIVE) {
				if ($isAbsPath && $path[0] !== $spr)
					$path = $spr . $path;
			} elseif ($left === KE_PATH_LEFT_TRIM) {
				if (!empty($path) && $path[0] === $spr)
					$path = ltrim($path, $spr);
			} else {
				if (empty($path))
					$path = $spr;
				elseif ($path[0] !== $spr)
					$path = $spr . $path;
			}
		}

		return $path;
	}
}

if (!function_exists('predir')) {
	/**
	 * 预创建指定路径的目录
	 *
	 * 主要用于减少写入文件前，判断文件的目录是否存在的代码
	 *
	 * ```php
	 * file_put_contents(predir('/var/www/myapp/log/abc.log'), 'anything...');
	 * ```
	 *
	 * @param string $path
	 * @param int    $mode
	 * @return string
	 */
	function predir(string $path, $mode = 0755)
	{
		$dir = dirname($path);
		if (!empty($dir) && $dir !== '.' && $dir !== '/' && $dir !== '\\' && !is_dir($dir))
			mkdir($dir, $mode, true);
		return $path;
	}
}

if (!function_exists('convert_path_slash')) {
	function convert_path_slash(string $path = null, $replace = KE_DS_UNIX): string
	{
		if (empty($path))
			return '';
		if ($replace !== KE_DS_UNIX && $replace !== KE_DS_WIN)
			$replace = KE_DS_UNIX;
		$search = $replace === KE_DS_UNIX ? KE_DS_WIN : KE_DS_UNIX;
		if (strpos($path, $search) !== false)
			$path = str_replace($search, $replace, $path);
		return $path;
	}
}

if (!function_exists('split_phar')) {
	function split_phar(string $path)
	{
		$phar = false;
		if (preg_match('#^phar://(.*)[\/\\\\]([^\/\\\\]+\.phar)#i', $path, $matches)) {
			$path = $matches[1];
			$phar = $matches[2];
		} elseif (preg_match('#^phar://(.*)#', $path, $matches)) {
			$path = $matches[1];
		}
		return [$path, $phar];
	}
}

if (!function_exists('relative_path')) {
	function relative_path(string $target, string $source, string $plus = null, $slash = KE_DS_UNIX)
	{
		if ($slash !== KE_DS_UNIX && $slash !== KE_DS_WIN)
			$slash = KE_DS_UNIX;
		$source = convert_path_slash($source, $slash);
		$target = convert_path_slash($target, $slash);
		if (isset($plus))
			$plus = ltrim($plus, KE_PATH_NOISE);
		list($source, $phar) = split_phar($source);

		$relative = $source;

		if (strlen($source) > 0) {
			$samePart = compare_path($source, $target, $slash);
			if (!empty($samePart)) {
				$samePart = preg_quote($samePart);
				$quoteSlash = preg_quote($slash);
				$regex = "#^({$quoteSlash}?{$samePart}{$quoteSlash}?)#i";
				$sourceTail = preg_replace($regex, '', $source);
				$sourceTail = trim($sourceTail, KE_PATH_NOISE);
				$targetTail = preg_replace($regex, '', $target);
				$targetTail = trim($targetTail, KE_PATH_NOISE);
				$targetDepth = 0;
				if (strlen($targetTail) > 0) {
					$targetSplit = explode($slash, $targetTail);
					$targetDepth = count($targetSplit);
				}
				$relative = str_repeat($slash . '..', $targetDepth);
				if (strlen($sourceTail) > 0)
					$relative .= $slash . $sourceTail;
			}
		}

		if ($phar !== false) {
			$relative = 'phar://' . $relative;
			$relative .= $slash . $phar;
		}

		if (strlen($plus) > 0) {
			$relative .= $slash . convert_path_slash($plus);
		}

		return $relative;
	}
}