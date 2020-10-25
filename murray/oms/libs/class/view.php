<?php 
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 模板类
*/

defined('INPOP') or exit('Access Denied');

class View extends Base{

	public $data;

	//初始化
	public function __construct(){
		register_shutdown_function(array(&$this, '__destruct'));
	}

	//销毁
	public function __destruct(){}
	
	//设置属性
	public function __set($name, $value){
		$this->data[$name] = $value;
	}
	
	//显示模板
	public function show($classDir_array){
		//获取模板路径
		$viewPath = BASE_PATH.DS.$classDir_array['path'].DS.VIEW_PATH.DS.$classDir_array['className'].DS.$classDir_array['actionName'].VIEW_EXT;
		//获取模板缓存路径
		$viewCachePath = CACHE_VIEW_PATH.DS.$classDir_array['path'].DS.$classDir_array['className'].DS.$classDir_array['actionName'].EXT;
		//编译模板
		$data = file_get_contents($viewPath);
		$this->cache($data, $viewCachePath);
		return true;
	}
	
	//缓存模板
	public function cache($cacheData, $viewCachePath){
		if(constant("PLATFORM") == "sae"){
            $this->saeCache($cacheData, $viewCachePath);
		}else{
            $this->fileCache($cacheData, $viewCachePath);
        }
		return true;
	}

    //SAE缓存
    public function saeCache($cacheData, $viewCachePath){
        //释放变量，用于字段匹配
        if($this->data) extract($this->data);
        $viewCacheDir = dirname($viewCachePath);
        //由于在SAE中，只能用MEMCACHE，需要关闭目录生成
        is_dir($viewCacheDir) or dir_create($viewCacheDir);
        //编译模板
        $data = $this->parse($cacheData);
        file_put_contents($viewCachePath, stripslashes($data));
        @chmod($viewCachePath, 0777);
        include $viewCachePath;
    }

    //文件缓存
    public function fileCache($cacheData, $viewCachePath){
        //释放变量，用于字段匹配
        if($this->data) extract($this->data);
        $viewCacheDir = dirname($viewCachePath);
        is_dir($viewCacheDir) or dir_create($viewCacheDir);
        //编译模板
        $data = $this->parse($cacheData);
        file_put_contents($viewCachePath, stripslashes($data));
        @chmod($viewCachePath, 0777);
        include $viewCachePath;
    }

	//对模板进行标签替换
	public function parse($str){
		$str = preg_replace("/([\n\r]+)\t+/s","\\1",$str);
		$str = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}",$str);
		$str = preg_replace("/\{include\s+(.+)\}/","\n<?php include \\1; ?>\n",$str);
		$str = preg_replace("/\{php\s+(.+)\}/","\n<?php \\1?>\n",$str);
		$str = preg_replace("/\{if\s+(.+?)\}/","<?php if(\\1) { ?>",$str);
		$str = preg_replace("/\{else\}/","<?php } else { ?>",$str);
		$str = preg_replace("/\{elseif\s+(.+?)\}/","<?php } elseif (\\1) { ?>",$str);
		$str = preg_replace("/\{\/if\}/","<?php } ?>",$str);
		$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\}/","<?php if(is_array(\\1)) foreach(\\1 AS \\2) { ?>",$str);
		$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/","\n<?php if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>",$str);
		$str = preg_replace("/\{\/loop\}/","\n<?php } ?>\n",$str);
		$str = preg_replace("/\{weiget\s+(.+)\}/", "\n<?php Weiget::get('\\1'); ?>\n", $str); //匹配组件调用
		$str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\(([^{}]*)\))\}/","<?php echo \\1;?>",$str);
		$str = preg_replace("/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\(([^{}]*)\))\}/","<?php echo \\1;?>",$str);
		$str = preg_replace("/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/","<?php echo \\1;?>",$str);
		$str = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "View::addquote('<?php echo \\1;?>')",$str);
		$str = preg_replace("/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>",$str); //匹配静态变量
		$str = "<?php defined('INPOP') or exit('Access Denied'); ?>".$str;
		return $str;
	}

	//处理反斜杠
	public static function addquote($var){
		return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	}

    //读取模板内容
    public function readTemplate(){

    }

    //回写模板内容
    public function writeTemplate(){

    }
}
