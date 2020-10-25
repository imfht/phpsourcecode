<?php

namespace VF\Library;

/**
 * VgotFaster Cache
 *
 * @package VgotFaster
 * @subpackage Library
 * @author pader
 */
class Cache {

	protected $cache = array();
	public $curCatchName;
	protected $cacheDir;
	protected $setCatchEndFlush = FALSE;
	protected $config = array();
	protected $VF;

	public function __construct($config=NULL)
	{
		$this->cacheDir = APPLICATION_PATH.'/data/cache';

		$this->VF =& getInstance();

		$preset = getConfig('cache');

		if ($preset !== null) {
			$this->config = $preset;
		}

		if (is_array($config) && count($config) > 0) {
			$this->config = array_merge($this->config, $config);
		}
	}

	/**
	 * Initialize Cache Config
	 * @param array $config
	**/
	public function initialize($config)
	{
		$this->config = isset($this->config) ? array_merge($this->config['variable'],$config) : $config;
	}

	/**
	 * Get Cache
	 *
	 * @param string $cacheName
	 * @param mixed $set
	 * @return mixed
	**/
	public function get($cacheName, $set=NULL)
	{
		if ($set !== null) {
			$set = $this->exportSet($set);
		} elseif(isset($this->config[$cacheName])) {
			$set = $this->exportSet($this->config[$cacheName]);
		}

		$cacheFile = $this->path($cacheName);
		$cache = array();

		if (file_exists($cacheFile)) {
			if($set !== null && isset($set['lifetime']) && time() - filemtime($cacheFile) > $set['lifetime']) {
				//如果缓存已过期，更新此缓存
				return $this->refresh($cacheName,$set,TRUE);
			} elseif(isset($this->cache[$cacheName])) {
				return $this->cache[$cacheName];
			} else {
				include $cacheFile;
				$this->cache[$cacheName] = $cache[$cacheName];
				return $this->cache[$cacheName];
			}
		} elseif(isset($set['model'])) {
			return $this->refresh($cacheName,$set,TRUE);
		} else {
			showError("No Found Cache <u>$cacheName</u> !",0);
			return NULL;
		}
	}

	/**
	 * 刷新缓存
	 * @param mixed $cacheName
	 * @param mixed $set
	 * @return mixed
	**/
	function refresh($cacheName,$set=NULL,$private=FALSE)
	{
		if(!$private) {
			if(!is_null($set)) {
				$set = $this->exportSet($set);
			} elseif(isset($this->config[$cacheName])) {
				$set = $this->exportSet($this->config[$cacheName]);
			} else {
				showError("Can not refresh cache <b>$cacheName</b>, No found setting!");
			}
		}

		if(!isset($set['model']) or !isset($set['method'])) {
			showError('<b>Refresh Cache Error:</b> Params require model and method!',0);
		}

		$this->VF->load->model($set['model']);
		$pathArr = explode('/',$set['model']);
		$modelName = end($pathArr);

		if (method_exists($this->VF->$modelName, $set['method'])) {
			$newCache = isset($set['params']) ?
				call_user_func_array(array(&$this->VF->$modelName,$set['method']),$set['params'])
				: $this->VF->$modelName->{$set['method']}();

			_systemLog("Refresh Cache [$cacheName]");

			$this->save($cacheName,$newCache);
			return $newCache;

		} else showError("Refresh Cache <b>$cacheName</b> Error: No found method <u>{$set['method']}</u>!",0);
	}

	//存储缓存
	function save($cacheName,$cacheVar)
	{
		$cacheCode = "<?php\r\n//Cache update at ".date('Y-m-d H:i:s')."\r\n"
			.'$cache[\''.$cacheName.'\'] = '
			.$this->varExport($cacheVar)
			.";\r\n";

		$file = $this->path($cacheName);

		$status = (bool)$this->writeFile($file, $cacheCode);
		$status && $this->cleanOpCache($file);

		return $status;
	}

	//创建缓存变量原生态代码
	private function varExport($myVar,$level=0)
	{
		$tabEnd = str_repeat("\t",$level);
		$tab = $tabEnd."\t";
		if(is_array($myVar)) {
			$varCode = "array(\r\n";
			foreach($myVar as $key => $val) {
				$key = is_numeric($key) ? $key : "'$key'";
				if(is_array($val)) {
					$varCode .= $tab.$key.' => '.$this->varExport($val,$level + 1);
				} else {
					$varCode .= $tab.$key.' => \''.addcslashes($val,'\\\'').'\'';
				}
				$varCode .= ",\r\n";
			}
			if($myVar) {
				$varCode = substr_replace($varCode,'',-3,1);
			}
			$varCode .= "$tabEnd)";
		} else {
			$varCode = '\''.addcslashes($myVar,'\\\'').'\'';
		}
		return $varCode;
	}

	/**
	 * Export A Cache Config
	 * @param mixed $set
	 * @return array Cache Config
	**/
	private function exportSet($set)
	{
		$params = array();

		//如果是数组，那么第一个元素之后都是参数
		if(!is_array($set) && is_string($set)) {
			$set = array('model'=>$set);
		}

		if(isset($set['model'])) {
			$s = explode('/',$set['model']);
			$length = count($s);
			if($length < 2) {
				showError('<b>Cache Setting Error:</b> Model length short than 2 !',0);
				return;
			}
			$params = $set;
			$params['method'] = $s[$length-1];
			unset($s[$length-1]);
			$params['model'] = join('/',$s);
			return $params;
		}

		return $set;
	}

	//返回缓存文件地址
	function path($cacheName,$block=FALSE)
	{
		return $block == FALSE ? $this->cacheDir.'/'.$cacheName.'.php' : $this->cacheDir.'/'.$cacheName.'.block';
	}

	function delete($cacheName)
	{
		$file = $this->path($cacheName);

		if (is_file($file)) {
			$this->cleanOpCache($file);
			return unlink($file);
		} else {
			return true;
		}
	}

	/***********************************************************************
	 * VgotFaster Cache Page Buffer Catch Cache
	**/

	function catchStart() { ob_start(); }

	function catchAuto($catchName,$validSeconds)
	{
		$catchName = $this->_catchName($catchName);
		$catchFile = $this->_catchFile($catchName);

		if(time() - @filemtime($catchFile) > $validSeconds) {
			$this->catchStart();
			$this->setCatchEndFlush = TRUE;
			return TRUE;
		} else {
			echo file_get_contents($catchFile);
			return FALSE;
		}
	}

	function catchEndSave($catchName='')
	{
		$buffer = ob_get_contents();

		if($this->setCatchEndFlush) {
			ob_end_flush();
			$this->setCatchEndFlush = FALSE;
		} else {
			ob_end_clean();
		}

		$catchFile = $this->_catchFile($catchName);
		$this->writeFile($catchFile,$buffer);

		return $buffer;
	}

	function _catchName($name)
	{
		$pre = 'catch_';

		if($name) {
			$preLen = strlen($pre);
			if(strlen($name) <=  $preLen OR substr($name,0,$preLen) == $pre) {
				$name = $preLen.$name;
			}
			return $this->curCatchName = $name;
		} elseif($this->curCatchName) {
			return $this->curCatchName;
		} else {
			showError('Cache: Catch Name Is Emtpty!');
		}
	}

	function _catchFile($name) { return $this->cacheDir.'/'.$this->_catchName($name).'.block'; }

	/*****************************Common***************************************/
	//写文件
	function writeFile($fileName, $content) {
		$this->mkdirs(dirname($fileName));
		return file_put_contents($fileName, $content, LOCK_EX);
	}

	private function cleanOpCache($file) {
		if (!function_exists('opcache_is_script_cached')) {
			return;
		}

		if (opcache_is_script_cached($file)) {
			opcache_invalidate($file);
		}
	}

	function mkdirs($dir,$mode=0777) {
		//return is_dir($dir) or (mkdirs(dirname($dir)) and mkdir($dir, $mode));
		if(is_dir($dir)) return TRUE; else {
			if(self::mkdirs(dirname($dir)) and mkdir($dir,$mode)) {
				chmod($dir,0777);
				return TRUE;
			} else return FALSE;
		}
	}

}
