<?php
 /**
 +------------------------------------------------------------------------------
 * Framk PHP框架
 +------------------------------------------------------------------------------
 * @package  Framk
 * @author   shawn fon <shawn.fon@gmail.com>
 +------------------------------------------------------------------------------
 */
class Action {
	 
	private $value;
	private $actionDir;
	private $pageNum;
	private $numPerPage;
	private $searchKeyword;
	private $searchValue;

 /* 
 实例化数据操作类并传入数据缓存文件夹与缓存时间参数
 */	
	public function C($cacheDir='',$cacheTime=10) {
		//Framk框架对缓存遵循‘数据更新，缓存更新’原则，但数据更新频率太高的话，用户可以设置缓存时间来延迟更新数据	
		return new Cache($cacheDir,$cacheTime);
	}
	

 /* 
 实例化数据操作类并传入数据缓存文件夹与缓存时间参数
 */	
//	public function db() {	
//		return _instance($GLOBALS['SDB']['DBtype'],$GLOBALS['SDB']['DBpath'],1);	
//		//return new Cache($cacheDir,$cacheTime);
//	}
	
	public function _REQUEST($filed){
		//return isset($_POST[$filed]) ? filter_input(INPUT_POST,$filed,FILTER_SANITIZE_SPECIAL_CHARS) : isset($_GET[$filed]) ? filter_input(INPUT_GET,$filed,FILTER_SANITIZE_SPECIAL_CHARS) : '';
		return @isset($_GET[$filed])?@$_GET[$filed]:@$_POST[$filed];
		//return $_REQUEST[$filed];
	}


	public function L($action,$args =array(),$fileDir=''){
		$actionFile =  ACTION.$this->actionDir.$action.'.class.php';
		//判断路径是否以'/'结尾，若无则加上
		if(strpos($GLOBALS['ActionDir'].'/',$GLOBALS['ActionDir'])===0)$defaultDir=$GLOBALS['ActionDir'].'/';
			$actionFile_default =  ACTION.str_replace ( S,'/', $defaultDir ).$action.'.class.php';//默认目录下的Action类
		if (file_exists($actionFile) || file_exists($actionFile_default)) {
			return _instance("Action/$action",$args, $fileDir='');
		}else if(file_exists(EXTEND.$action.".class.php")) {
			return _instance("Extend/$action",$args, $fileDir='');
		}
	}
	
	
 /* 
 注入变量到显示页面
 */
	//this=>assign方法有如下方式

	//$this->assign('A',$A);
	//$this->assign(array('A'=>$A,'B'=>$B,'E'=>$E,'F'=>$F));
	//$this->assign(array('A'=>$A,'B'=>$B),array('E'=>$E,'F'=>$F));	
	public function assign() {
		$_key = @func_get_arg ( 0 );
		$_value = @func_get_arg ( 1 );
		if (is_array ( $_key )) {		
			foreach ( $_key as $k => $v ) {				
				if(is_array($v)){
					foreach ( $v as $kk => $vv ) {					 
						$this->value [$kk] = $vv;	
					}	
				} 
				    $this->value [$k] = $v;			 				
			}//foreach
		} else {
			$this->value [$_key] = $_value;
		}
	}
	
 /* 
 加载模板文件
 */		 
	 public function show($displayTpl='index') {
		$displayTpl=str_replace('/',S,$displayTpl);	 			
		$tplFile = VIEW.$displayTpl.'.php';		
		@extract ($this->value);
		$mtime = explode(' ',microtime());
		$GLOBALS['EndRunTime'] = (( $mtime[1] + $mtime[0]) - $GLOBALS['StartRunTime']);				
		(file_exists ($tplFile) )? include ($tplFile):_error ( 'fileNotExist', '请检查此目录下显示页面是否存在：'.$tplFile);	
		unset($this->value);
	 }
 /* 
 设置smarty属性并返回已设置smarty对象
 */		 
	public function setSmarty(){
		$smarty=_instance('Extend/smarty/Smarty');
		foreach($GLOBALS['setSmarty'] as $key=>$value){
			$smarty->$key=$value;
		}
		return $smarty;
	}

	//跳转 
	public function location($msg='',$url='',$type=0,$sec='3') {
		$url=ACT.$url;
		$tmp="";
		if(is_array($msg)){
			foreach($msg as $key=>$va_msg){
				$tmp .="<li> ".($key+1).") ".$va_msg."</li>";
			}
		}else{
				$tmp = $msg;
		}
	
		if($type>0){
			$smarty = $this->setSmarty();
			$smarty->assign( array( "message" => $tmp) );
			$smarty->display( 'alert.html' );
			exit;
			
		}else{
			header("Location: $url");
			exit;
		}
	} //end function location 
}
?>
