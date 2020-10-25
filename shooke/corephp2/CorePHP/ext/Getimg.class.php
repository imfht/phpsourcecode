<?php
namespace Ext;
class Getimg{  	

	/**
	 * 绝对地址转换为相对地址
	 *
	 * @param unknown_type $str 要转换的字符串
	 * @param unknown_type $replace 执行文件所在目录名如 admin 不要加/
	 * @return unknown 
	 */
	static function s_relatively($str,$replace=''){
		$root=str_replace(basename(self::ScriptUrl()),'',self::ScriptUrl());		
		$replace = empty($replace) ? '' : $replace.'/';//获得文件所在目录加/
		$root=str_replace($replace,'',$root);//如果执行文件不在根目录，去掉文件所在目录，找到根目录	
		
		$pattern_src = "'<(img|IMG)(.*?)src=[\'|\"]".$root."(.*?(?:[\.gif|\.jpg]))[\'|\"](.*?)[\/]?>'"; //图片正则
		return preg_replace($pattern_src,'<\\1\\2src="\\3" \\4 />',$str);//绝对地址转换为相对地址		
		
	}
	/**
	 * 相对地址转为绝对地址
	 *
	 * @param unknown_type $str 要转换的字符串
	 * @param unknown_type $replace 执行文件所在目录如 admin 不要加/
	 * @return unknown
	 */
	static function s_absolute($str,$replace=''){		
		$root=str_replace(basename(self::ScriptUrl()),'',self::ScriptUrl());
		$replace = empty($replace) ? '' : $replace.'/';//获得文件所在目录加/	
		$root=str_replace($replace,'',$root);//如果执行文件不在根目录，去掉文件所在目录，找到根目录
		
		$pattern_src = "'<(img|IMG)(.*?)src=[\'|\"]data/(.*?(?:[\.gif|\.jpg]))[\'|\"](.*?)[\/]?>'"; //图片正则
		return preg_replace($pattern_src,'<\\1\\2src="'.$root.'data/\\3" \\4 />',$str);//相对地址转为绝对地址
	}
	//取的一张图片的地址
	static function s_getimg($data){
		$pattern_src = '/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/';  
		$num = preg_match($pattern_src, $data, $match_src); 
		if(!$num) return 'images/noimg.jpg';//如果没有图片则返回空字符串
		return $match_src[1];//获得图片数组 
	}
	/*返回图片显示字符串*/
	static function s_getpic($data) {  		
		/*利用正则表达式得到图片链接*/  
		$pattern_src = '/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/';  
		$num = preg_match_all($pattern_src, $data, $match_src); 
		if(!$num) return '';//如果没有图片则返回空字符串
		$arr_src = $match_src[1];//获得图片数组  
		$new_src = self::new_name($arr_src); 
		$return=''; 
		foreach($match_src[0] as $key => $val){
			$return .= str_replace($arr_src[$key],$new_src[$key],$val).' ';
		}
		return $return;
		
	}  
	  
	/*取得缩略图*/  
	static function s_thumb($pic_arr)  
	{  
		foreach($pic_arr as $pic_item){
			if(!self::url($pic_item)) {
				$basename = basename($pic_item) ;
				$pic_item = str_replace($basename, 'thumb_'.$basename, $pic_item); 
			}
			$new_arr[] = $pic_item; 	
		}  
		return $new_arr;
	}  
	/*判断是否为远程图片*/
	static function s_remote($str){
		if (preg_match("/(http:|https:)/i", $str)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 返回当前执行脚本的绝对路径
	 *
	 * <pre>Example:
	 * 请求: http://www.example.net/example/index.php?a=test
	 * 返回: /example/index.php
	 * </pre>
	 * @throws Exception 当获取失败的时候抛出异常
	 */
	private static function ScriptUrl() {
		if (($scriptName = $_SERVER['SCRIPT_FILENAME']) == null) {
			throw new Exception(__CLASS__ . ' determine the entry script URL failed!!!');
		}
		$scriptName = basename($scriptName);
		if (($_scriptName = $_SERVER['SCRIPT_NAME']) != null && basename($_scriptName) === $scriptName) {
			$_returnscriptUrl = $_scriptName;
		} elseif (($_scriptName = $_SERVER['PHP_SELF']) != null && basename($_scriptName) === $scriptName) {
			$_returnscriptUrl = $_scriptName;
		} elseif (($_scriptName = $_SERVER['ORIG_SCRIPT_NAME']) != null && basename($_scriptName) === $scriptName) {
			$_returnscriptUrl = $_scriptName;
		} elseif (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
			$_returnscriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
		} elseif (($_documentRoot = $_SERVER['DOCUMENT_ROOT']) != null && ($_scriptName = $this->getServer(
				'SCRIPT_FILENAME')) != null && strpos($_scriptName, $_documentRoot) === 0) {
				$_returnscriptUrl = str_replace('\\', '/', str_replace($_documentRoot, '', $_scriptName));
		} else{
			throw new Exception(__CLASS__ . ' determine the entry script URL failed!!');
		}
		return $_returnscriptUrl;
	}

} 
