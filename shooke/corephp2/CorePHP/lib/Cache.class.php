<?php
namespace Lib;
/**
 * @author shooke
 * 数据缓存类 v1.0
 * 用于缓存数据,如变量,但不能缓存页面
 */
/*
//实例
$cache_set = array(
//缓存路径 , 最后要加"/"
'cacheRoot'=>'./cache/',
//缓存时间
'cacheTime'=>20,
//cache type
'cacheType'=>1,
//扩展名
'cacheExe'=>'.php'
);
$cache = new Cache($cache_set);
$a=array('1','2');
$a="aaa";
$b='';
if($cache->isTrue("d")){
	$c=$cache->read("d");
	echo "c";
	print_r($c);
}else {
$b=$cache->write('d',$a);
}
print_r($b);
//$cache->clear("a");
//echo $cache->read("./cache/d.php");
//echo $d;
*/



class Cache{
	//配置
	public $config = array(
	//缓存路径
	'cacheRoot'=>'./cache/',
	//缓存时间
	'cacheTime'=>1,
	//cache 类型 1串化数据 2变量
	'cacheType'=>2,
	//扩展名
	'cacheExe'=>'.php'
	//转换中间变量
	);

	public $return_name=array();

	function __construct($cache_set = array())
	{
		if(!empty($cache_set)) $this->config=array_merge($this->config,$cache_set);
		$this->config['ClassName'] = __CLASS__;
	}

	/**
	 * 清空缓存
	 * @param string $filename
	 */
	public function clear($filename=''){
		if (file_exists($this->cacheFile($filename))) {
			@unlink($this->cacheFile($filename));
		}elseif (empty($filename)){
			$this->_clearDir($this->config['cacheRoot']);
		}else{
			$this->_clearDir($this->config['cacheRoot'].$filename);
			echo $this->config['cacheRoot'].$filename;
		}
	}
	
	/**
	 * 循环删除路径
	 * @param unknown $dir
	 * @param string $to
	 */
	private function _clearDir($dir,$to = false)
	{
		if ($list = glob($dir.'/*'))
		{
			foreach ($list as $file)
			{
				is_dir($file) ? $this->_clearDir($file) : unlink($file);
			}
		}

		if ($to === false) rmdir($dir);
	}

	
	/**
	 * 写操作，写入缓存文件
	 * @param unknown $filename
	 * @param unknown $writetext
	 * @param string $openmod
	 * @return boolean
	 */
	private function _write($filename, $writetext, $openmod='w'){
		if (!file_exists($filename)) {
			@$this->makeDir( dirname($filename ));
		}
		if(@$fp = fopen($filename, $openmod)) {
			flock($fp, 2);
			fwrite($fp, $writetext);
			fclose($fp);
			return true;
		} else {
			echo "File: $filename write error.";
			return false;
		}
	}

	
	/**
	 * 缓存有效期 有效返回 true
	 * @param unknown $fileName
	 * @return boolean
	 */
	public function isTrue($fileName){
		$fileName=$this->cacheFile($fileName);
		if( file_exists( $fileName ) ) {
			//如果缓存时间为负数则永不过期
			if ($this->config['cacheTime'] < 0) {
				return true;
			}
			//如果缓存时间为0则一直过期
			if ($this->config['cacheTime'] == 0) {
				return false;
			}
			//获取缓存文件的建立时间
			$ctime = intval(filemtime( $fileName ));
			//比较是否大于缓存时间,是则过期 否则不过期
			if (time() - $ctime > $this->config['cacheTime']) {
				return false;
			}else {
				return true;
			}
			//文件不存在视为过期失效
		}else {
			return false;
		}
	}

	/**
	 * 写入缓存
	 * @param unknown $name
	 * @param unknown $data
	 * @return unknown
	 */
	public function write($name,$data){
		$varname=$name;
		$name = $this->cacheFile($name);
		//config['cacheTime']==0也就是不启用缓存是直接返回数据
		if ($this->config['cacheTime'] <> 0) {
			if($this->config['cacheType']==1){
				$write_data = "<?php exit;?>".serialize($data);
				//return $data;
			}else {
				$write_data = "<?php\r\n return ";
				$write_data .= var_export($data,true);
				$write_data .=";\r\n?>";
			}
			$this->_write($name,$write_data);
		}
		return $data;

	}
	
	/**
	 * 获取缓存路径和文件名
	 * @param unknown $filename
	 * @return string
	 */
	private function cacheFile($filename){
		return $this->config['cacheRoot'].$filename.$this->config['cacheExe'];
	}

	
	/**
	 * 读取文件 
	 * @param unknown $file
	 * @return boolean|string|unknown
	 */
	public function read($file){
	    //检查缓存有效性,无效直接返回
	    if(!$this->isTrue($file)) return false;
	    //获取存储路径
		$file=$this->cacheFile($file);
		if (!file_exists($file)) {
			return '';
		}
		if($this->config['cacheType']==1){
			if (function_exists('file_get_contents')){
				$cache_Content= file_get_contents($file);
			}else{
				$fopen = fopen($file,'r');
				$cache_Content = '';
				do {
					$data = fread($fopen,filesize($file));
					if (strlen($data)===0) break;
					$cache_Content .= $data;
				}while(1);
				fclose($fopen);
			}
			$cache_Content = substr($cache_Content,13);/* 去除<?php exit;?> */
			$cache_Content = unserialize($cache_Content);
			return $cache_Content;
		}else{
			return include($file);
		}

	}

	
	/**
	 * 循环创建目录
	 * @param unknown $dir
	 * @param number $mode
	 * @return number|boolean
	 */
	private function makeDir( $dir, $mode = 0777 ) {
		if( ! $dir ) return 0;
		$dir = str_replace( "\\", "/", $dir );
		$mdir = "";
		foreach( explode( "/", $dir ) as $val ) {
			$mdir .= $val."/";
			if( $val == ".." || $val == "." || trim( $val ) == "" ) continue;

			if( ! file_exists( $mdir ) ) {
				if(!@mkdir( $mdir, $mode )){
					return false;
				}
			}
		}
		return true;
	}

}
