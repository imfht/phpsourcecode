<?php
class Dispatcher {
	private $actionDir='';
 /* 
构造函数，进行路径分析
 */	
	public function __construct() {		
		$scriptName		= $_SERVER ['SCRIPT_NAME'];
		$requestURI		= $_SERVER ['REQUEST_URI'];
/*		if($GLOBALS['ReWrite']){
			foreach($GLOBALS['Router'] as $row){
				$rule=$row['rule'];
				$durl=$row['durl'];
				if(preg_match('#'.$rule.'#isU', $requestURI,$rtn_arr)){
					unset($rtn_arr[0]);
					$requestURI= str_replace(array('{$1}','{$2}','{$3}','{$4}'),$rtn_arr,$durl);
				}
			}
		}*/
		//过滤入口文件
		$find_file_arr	= array('/index.php','/crm.php');
		$appName 		= str_replace ($find_file_arr, '', $scriptName );
		//定义项目相http地址
		define ( 'APP_HTTP', ( str_replace (APP_NAME, '', $appName ) ) );
		//定义项目名	
		define ( 'APP', ( strlen(APP_NAME)===0 ? $appName : $appName.'/'.APP_NAME) );
		
		//定义访问路径前缀,判断是否开启了
		//define ( 'ACT', ( $GLOBALS['ReWrite'] ? $appName : $scriptName) );
		define ( 'ACT', $scriptName );

		if($requestURI ==$appName.'/'){
			$urlStr='';
		}else{
			$urlStr=str_replace (ACT, '', $requestURI );//取得当前index.php之后的字符串	
			//$urlStr=str_replace (ACT, '', str_replace ($find_file_arr, '', $requestURI ));//取得当前index.php之后的字符串	
		}
		$this->getAction ( $this->filterParams($urlStr) );//
	}
 /*
 过滤访问地址中一些符号，确定Action是否有子级目录
 */			
	private function filterParams($urlStr) {
	
		$urlStr=substr($urlStr,1);//过滤第一个'?'或'/'
		if(!empty($_SERVER['QUERY_STRING'])){
			$urlStr =str_replace(array('&','='),'/',$urlStr);//如果为查询字符串则过滤字符'&','='
		}
		$urlArr=explode ( '/', $urlStr );
		//如果开启重写并且静态后缀不为空则过滤伪静态后缀
		if($GLOBALS['ReWrite']&&!empty($GLOBALS['htmlExt'])){
			array_splice($urlArr,-1,1,array(str_replace($GLOBALS['htmlExt'],'',$urlArr[count($urlArr)-1]) ));
		}	

		foreach($urlArr as $key=>$value){//取得Action所处目录
			$firstLetter = substr($value,0,1);//截取路径参数中第一个字母				
			if($firstLetter== strtolower ($firstLetter)){//判断第一个字母是否小写，如果小写则为目录名
				$this->actionDir .=$value.S ;//获取Action的目录	
				unset($urlArr[$key]);//将目录从路径参数中剔除
			}else{
				break;//直到有参数首字母为大写时则停止循环
			}			
		}
		return array_merge($urlArr,array());//重组参数数组并返回，此时的参数已去掉了目录
	}	
	
 /* 
 取得传递的参数，确定Action类的完整路径
 */		
	private function getAction($urlArray) {
		//设置默认的类名和方法
		empty ( $urlArray[0]) ? $action  = 'Index' : $action = $urlArray[0];//确定Action类
		empty ( $urlArray[1] ) ? $method = 'main' : $method = $urlArray[1];//确定调用的Action类方法
		define('ACTION_NAME',$action);//定义Action名称
		define('METHOD_NAME',$method);//定义方法名称	
		$paramsNum = count ( $urlArray );		
		if ($paramsNum > 2){//如果参数数目大于2则说明有参数需要传入到Action类方法中去	
			for ($i=2;$i<$paramsNum;$i++) {//从2开始，剔掉ACTION类和方法
				if($GLOBALS['URLMode']==0 && $i<$paramsNum){	//如果为访问路径设为完整模式则参数必然成对，就可以通过将处在奇数位置的参数设为键，偶数位置的参数设为值	而组成数组							
					$_GET[ $urlArray[$i] ] = @$urlArray[++$i];
				}else{					
					$_GET[$i-2]= $urlArray [$i];//减2，这样可以从$_GET[0]开始取值，否则第一个参数值为$_GET[2]
				}
			}			 							
		}

		$actionFile =  ACTION.$this->actionDir.$action.'.class.php';
		//exit;
		
		if(strpos($GLOBALS['ActionDir'].'/',$GLOBALS['ActionDir'])===0)$defaultDir=$GLOBALS['ActionDir'].'/';//判断路径是否以'/'结尾，若无则加上
		$actionFile_default =  ACTION.str_replace ( '/', S, $defaultDir ).$action.'.class.php';//默认目录下的Action类		
		if (file_exists($actionFile)) {			
			$this->doAction($actionFile ,$action,$method);		
		} else if (file_exists($actionFile_default)) {			
			$this->doAction($actionFile_default ,$action,$method);						
		} else {
			echo $actionFile;
			_error('Error','Dispatcher->getAction:第'.__LINE__.'行， 请检查此文件是否存在:'.$this->actionDir.$action.'.class.php');	 		
		}				
	}
 /* 
 包含Action类文件，实例化类，调用方法
 */		
	private function doAction($file ,$Action,$method){
		require_once($file);	
		if(class_exists($Action)){
			$action = new $Action();
			if (method_exists ( $action, $method )) {
				$action->$method ();					 		
			} else {
				_error('methodNotExist', 'Dispatcher->doAction:第'.__LINE__.'行，'.$Action.'类不存在该方法:'.$method);
			}			
		}else{
			_error ( 'classError', 'Dispatcher->doAction:第'.__LINE__.'行，文件名应与类名一致并且文件后缀应以.class.php结尾：'.$Action.' 类不存在', true);
		}
		
	}
 /*  +------------------------------------------------------------------------------ */
}
?>