<?php
namespace Core;
use \Exception;
/**
 * @author shooke
 * 模板解析类
 * 进行模板解析等操作
 */
class Template {
	public $config =array(); //配置
	protected $vars = array();//存放变量信息
	protected $_replace = array();
	protected $cachetime=0;
	
	public function __construct($config = array()) {
	    $this->config = Config::get('TPL');//载入默认配置
	    $this->setConfig($config);//合并新配置		
		$this->_replace = array(
				'str' => array( 'search' => array(),
								'replace' => array()
							),
				'reg' => array( 'search' => array(),
								'replace' => array()					   
							)
		);
	}
	
	//模板赋值
	public function assign($name, $value = '') {
		if( is_array($name) ){
			foreach($name as $k => $v){
				$this->vars[$k] = $v;
			}
		} else {
			$this->vars[$name] = $value;
		}
	}
	//布局
    public function layout($tpl = '', $return = false){
        $tplFile = '';//模板路径
        $tplContent = '';//模板内容
        $layoutContent = '';//布局文件内容
        $match = array();//正则中用到的临时变量
        //模板标记
        $left = $this->config['TPL_TEMPLATE_LEFT'];
        $right = $this->config['TPL_TEMPLATE_RIGHT'];
        //布局变量处理        
        $pregBlock = array();//存放需要替换的内容
        $layoutBlock = array();//存放布局内容用于合并
        $layoutFile = $this->config['TPL_LAYOUT_PATH'];//布局文件路径
        $layout = $left . $this->config['TPL_LAYOUT_BLOCK'] . '_' . '(.*?)' . $right;//{LAYOUT_NAME}
        $layout_end = $left . '\/' . $this->config['TPL_LAYOUT_BLOCK'] . '_' . '(.*?)' . $right;//{/LAYOUT_NAME}
        //如果没有设置模板，则调用当前模块的当前操作模板
        if ( $tpl == '') {
            $tpl = CP_MODULE . "/" . CP_ACTION;
        }
        $tplFile = $this->getTemplateFile($tpl);//取得模板路径
		
		//模板处理
		$tplContent = $this->getTemplateContent($tplFile);		
		//获取内容模板中的内容块
		preg_match_all('/'.$layout.'([\s\S]+?)'.$layout_end.'/',$tplContent,$match);//取得执行前的程序段
		//处理内容块
		if(!empty($match)){
		    foreach ($match[1] as $key=>$val){
		        $layoutBlock[$val] = $match[2][$key];
		    }
		}
		
		//获取指定布局文件
		preg_match('/'.$left.$this->config['TPL_LAYOUT_BLOCK']." +(.*)".$right.'/',$tplContent,$match);		
		isset($match[1]) && $layoutFile = $match[1];//存在指定布局文件时进行赋值
		//获取布局文件内的引用并进行替换		
		$layoutFile = $this->getTemplateFile($layoutFile);		
		$layoutContent = $this->getTemplateContent($layoutFile);
		preg_match_all('/'.$left.'(.*?)'.$right.'/',$layoutContent,$match);
        if(!empty($match[1])){
            foreach ($match[1] as $val){
    		    $layoutContent = preg_replace ( '/'.$left . $val . $right.'/', $layoutBlock[$val], $layoutContent );
    		}
		}
		
		return $layoutContent;
		
		
        
    }
	//执行模板解析输出
	public function display($tpl = '', $return = false ) {
	    $tplContent = '';
	    //如果没有设置模板，则调用当前模块的当前操作模板
	    empty($tpl) && $tpl = CP_MODULE . "/" . CP_ACTION;	
	    
		//启用缓冲
		ob_start();
		
		extract($this->vars, EXTR_OVERWRITE);
		define('TPL_INC', true);
		$tplFile = $this->getTemplateFile($tpl);//取得模板路径
		
		$tplTime = filemtime($tplFile);//模板修改时间
		$compileFile = $this->getCompileFile($tplFile);//取得编译文件路径
		$compileTime = $this->getCompileTime($tplFile);//编译时间		
		
		//不缓存标记
		$block = $this->config['TPL_TEMPLATE_LEFT'].$this->config['TPL_NOCACHE_BLOCK'].$this->config['TPL_TEMPLATE_RIGHT'];
		$block_end = $this->config['TPL_TEMPLATE_LEFT'].'\/'.$this->config['TPL_NOCACHE_BLOCK'].$this->config['TPL_TEMPLATE_RIGHT'];
			
		if ($this->config['TPL_CACHE_TIME']>0){//开启二级缓存
		    //如果没有目录则递归创建
		    if ( !is_dir($this->config['TPL_CACHE_PATH']) ) {
		        @mkdir($this->config['TPL_CACHE_PATH'], 0777, true);
		    }
		    //获取模板内容	    
		    $tplContent = $this->config['TPL_LAYOUT_ON'] ? $this->layout($tpl) : $this->getTemplateContent($tplFile);
		    //取得编译内容
		    $compile_content = $this->compile($tplContent);
		   
		    //开启二级缓存		    	
	        $cacheFile = $this->getCacheFile($tplFile);//取得二级缓存路径	  
	        if (!$this->is_cached($tpl)){//缓存过期或无效时重新生成   	   
                preg_match_all('/'.$block.'([\s\S]+?)'.$block_end.'/',$compile_content,$tplc);//取得执行前的程序段
                ob_start();
                eval('?>' . $compile_content);
                $html = ob_get_contents();
                ob_end_clean();
                preg_match_all('/'.$block.'([\s\S]+?)'.$block_end.'/',$html,$htmlc);//取得执行后的代码
                $cache_content = str_replace($htmlc[0],$tplc[1],$html);//替换非缓存区域            
                file_put_contents($cacheFile, "<?php if (!defined('TPL_INC')) exit;?>" . $cache_content);	
	        }		    
		}else{//未开启二级缓存普通模式
		    //如果没有目录则递归创建
		    if ( !is_dir($this->config['TPL_COMPILE_PATH']) ) {
		        @mkdir($this->config['TPL_COMPILE_PATH'], 0777, true);
		    }
		    if (!file_exists($compileFile) || $tplTime > $compileTime){
		        //获取模板内容	    
		        $tplContent = $this->config['TPL_LAYOUT_ON'] ? $this->layout($tpl) : $this->getTemplateContent($tplFile);
		        //当编译文件不存在或模板文件比便以文件修改时间晚时进行重新编译
		        //执行编译
		        $compile_content = $this->compile($tplContent);
		        $compile_content = preg_replace(array('/'.$block.'/','/'.$block_end.'/'),'',$compile_content);//移除不缓存标记
		        //保存编译文件
		        file_put_contents($compileFile, "<?php if (!defined('TPL_INC')) exit;?>" . $compile_content);
		    }
		}
		$includeFile = $this->config['TPL_CACHE_TIME']>0 ? $cacheFile : $compileFile;
		include( $includeFile );
		//获取内容			
		$content = ob_get_contents();
		ob_end_clean();
		if($return){
		    return $content;
		}else{
		    echo $content;
		}
		
	}	
	
	//自定义添加标签
	public function addTags($tags = array(), $reg = false) {
		$flag = $reg ? 'reg' : 'str';
		foreach($tags as $k => $v) {
			$this->_replace[$flag]['search'][] = $k;
			$this->_replace[$flag]['replace'][] = $v;
		}
	}
	
	//模板编译核心
	protected function compile( $content ) {	
	    //内容为空直接返回	
		if(empty($content)) return '';
		//开始处理
		$cp_template = $content;		
		//如果自定义模板标签解析函数tpl_parse_ext($cp_template)存在，则执行
		if ( function_exists('tpl_parse_ext') ) {
			$cp_template = tpl_parse_ext($cp_template);
		}else{//默认模板标签
		    $cp_template = $this->tag_replace($cp_template);
		}
		
		//追加标签解析
		$cp_template = str_replace($this->_replace['str']['search'], $this->_replace['str']['replace'], $cp_template);
		$cp_template = preg_replace($this->_replace['reg']['search'], $this->_replace['reg']['replace'], $cp_template);
		
		//添加验证token
		if(Config::get('TOKEN_NAME')){
		    $cp_template = preg_replace('/(<\/form>)/i', "<?php echo \Core\Model::Token();?>\\1", $cp_template);
		}
		
		return $cp_template;
	}
	//模板语法解析
	protected function tag_replace($template){
    	$left = $this->config['TPL_TEMPLATE_LEFT'];
    	$right = $this->config['TPL_TEMPLATE_RIGHT'];
    	//php标签
    	/*
    	{php echo phpinfo();}	=>	<?php echo phpinfo(); ?>
    	*/
    	$template = preg_replace ( "/".$left."php\s+(.+)".$right."/", "<?php \\1?>", $template );
    
    	//if 标签
    	/*
    	{if $name==1}		=>	<?php if ($name==1){ ?>
    	{elseif $name==2}	=>	<?php } elseif ($name==2){ ?>
    	{else}				=>	<?php } else { ?>
    	{/if}				=>	<?php } ?>
    	*/
    	$template = preg_replace ( "/".$left."if\s+(.+?)".$right."/", "<?php if(\\1) { ?>", $template );
    	$template = preg_replace ( "/".$left."else".$right."/", "<?php } else { ?>", $template );
    	$template = preg_replace ( "/".$left."elseif\s+(.+?)".$right."/", "<?php } elseif (\\1) { ?>", $template );
    	$template = preg_replace ( "/".$left."\/if".$right."/", "<?php } ?>", $template );
    
    	//for 标签
    	/*
    	{for $i=0;$i<10;$i++}	=>	<?php for($i=0;$i<10;$i++) { ?>
    	{/for}					=>	<?php } ?>
    	*/
    	$template = preg_replace("/".$left."for\s+(.+?)".$right."/","<?php for(\\1) { ?>",$template);
    	$template = preg_replace("/".$left."\/for".$right."/","<?php } ?>",$template);
    
    	//loop 标签
    	/*
    	{loop $arr $vo}			=>	<?php $n=1; if (is_array($arr) foreach($arr as $vo){ ?>
    	{loop $arr $key $vo}	=>	<?php $n=1; if (is_array($array) foreach($arr as $key => $vo){ ?>
    	{/loop}					=>	<?php $n++;}unset($n) ?>
    	*/
    	$template = preg_replace ( "/".$left."loop\s+(\S+)\s+(\S+)".$right."/", "<?php \$n=1;if(is_array(\\1)) foreach(\\1 AS \\2) { ?>", $template );
    	$template = preg_replace ( "/".$left."loop\s+(\S+)\s+(\S+)\s+(\S+)".$right."/", "<?php \$n=1; if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>", $template );
    	$template = preg_replace ( "/".$left."\/loop".$right."/", "<?php \$n++;}unset(\$n); ?>", $template );
    
    	//函数 标签
    	/*
    	{date('Y-m-d H:i:s')}	=>	<?php echo date('Y-m-d H:i:s');?>
    	{$date('Y-m-d H:i:s')}	=>	<?php echo $date('Y-m-d H:i:s');?>
    	*/
    	$template = preg_replace ( "/".$left."([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))".$right."/", "<?php echo \\1;?>", $template );
    	$template = preg_replace ( "/".$left."(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))".$right."/", "<?php echo \\1;?>", $template );
    
    	//替换下划线常量/替换全大写字母常量/替换变量/递归加载模板
    	/*
    	 __APP__        => <?php if(defined('__APP__')){echo __APP__;}else{echo '__APP__';} ?>
    	 {CONSTANCE}	=> <?php echo CONSTANCE;?> 或 {CON_STANCE}	=> <?php echo CON_STANCE;?>
    	 {$var}         => <?php echo $var; ?>
    	 {include head} => <?php \$this->display(\"$1\"); ?>
    	 */
    	$template = preg_replace ( "/__[A-Z]+__/", "<?php if(defined('$0')){echo $0;}else{echo '$0';} ?>", $template );
    	$template = preg_replace ( "/".$left."([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)".$right."/s", "<?php echo \\1;?>", $template );
    	$template = preg_replace ( "/".$left."(\\$[a-zA-Z_]\w*(?:\[[\w\.\"\'\[\]\$]+\])*)".$right."/i", "<?php echo $1; ?>", $template );
    	$template = preg_replace ( "/".$left."include\s*(.*)".$right."/i", "<?php \$this->display(\"$1\"); ?>", $template );
    	
    	//注释 编译时清除
    	/*
    	 {*注释内容*}=> 
    	 {#注释内容#}=>
    	*/
    	$template = preg_replace ( "/".$left."(\*[\s\S]*\*)".$right."/", "", $template );
    	$template = preg_replace ( "/".$left."(#[\s\S]*#)".$right."/", "", $template );
    	    	
    	return $template;
    }
	//判断二级缓存是否有效
	public function is_cached($tpl, $time=0){
		//如果没有设置模板，则调用当前模块的当前操作模板
		if ( $tpl == "" ) {
			$tpl = CP_MODULE . "/" . CP_ACTION;
		}
		$tplFile = $this->getTemplateFile($tpl);//取得模板路径
		$cacheFile = $this->getCacheFile($tplFile);//取得二级缓存路径
		
		$this->cachetime = $time ? $time : $this->config['TPL_CACHE_TIME'];
		
		$tplTime = filemtime($tplFile);//模板修改时间
		$cacheTime = $this->getCacheTime($tplFile);//缓存修改时间
		$nowTime = time();//当前时间
		
		if ($time<0) $this->cachetime = 0;//$time<0本页关闭二级缓存
		if ( !file_exists($cacheFile) || $this->cachetime < $nowTime-$cacheTime || $tplTime > $cacheTime ) {
			return false;
		}else{
			return true;
		}
	}
	/********************************************************************/
	//设置模板引擎参数
	public function setConfig($key, $value = null) {
	    if (is_array($key)) {
	        $this->config = array_merge($this->config , $key);
	    } else {
	        $this->config[$key] = $value;
	    }
	}
	
	//获取模板文件路径
    public function getTemplateFile($tpl=''){
	    $tplFile = $this->config['TPL_TEMPLATE_PATH'] . $tpl . $this->config['TPL_TEMPLATE_SUFFIX'];
		if ( !file_exists($tplFile) ) {
		    throw new Exception($tplFile . "模板文件不存在");
		}
		return $tplFile;
	}
	//获取模板文件内容
	public function getTemplateContent($tplFile=''){
		return file_get_contents($tplFile);
	}
	//获取编译文件路径
	public function getCompileFile($tplFile=''){
	    $tplFile = $this->config['TPL_COMPILE_PATH'].$tplFile;//加入路径,防止编译和缓存路径是同一目录是相互覆盖的问题
	    return $this->config['TPL_COMPILE_PATH'] . md5($tplFile) . $this->config['TPL_COMPILE_SUFFIX'];
	}
	//获取编译时间
	public function getCompileTime($tplFile=''){
	    $cplFile = $this->getCompileFile($tplFile);//根据模板获取编译地址
	    if (file_exists($cplFile)){
	        return filemtime($cplFile);//返回编译时间
	    }else{
	        return 0;//如果没有文件返回0
	    }
	    
	}
	//获取缓存文件路径
	public function getCacheFile($tplFile=''){
	    $tplFile = $this->config['TPL_CACHE_PATH'].$tplFile;//加入路径,防止编译和缓存路径是同一目录是相互覆盖的问题
	    return $this->config['TPL_CACHE_PATH'] . md5($tplFile) . $this->config['TPL_CACHE_SUFFIX'];	    
	}
	//获取缓存时间
	public function getCacheTime($tplFile=''){	    
	    $cacheFile = $this->getCacheFile($tplFile);//根据模板获取缓存地址
	    if (file_exists($cacheFile)){
	       return filemtime($cacheFile);//返回缓存时间
	    }else{
	        return 0;//如果没有文件返回0
	    }
	}
	
}