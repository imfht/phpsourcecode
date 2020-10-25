<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

/**
 * RcAction class
 * $Author: Recho $license: http://www.recho.net/ $
 * $create time: 2012-08-20 09:27
 * $last update time: 2012-08-20 18:50 Recho $
 */
class RcAction{
	public $tkd = NULL;
	
	public function __construct(){
	}
	
	public function display( $file=false){
		$debug_backtrace = debug_backtrace();
		if( preg_match('/RechoPHP/',$debug_backtrace[0]['file'])){
			$template = RC_PATH_LIB.'templates/'.$file.'.htm';
		}else{
			if( $file){
				$file = (preg_match("/\//", $file)) ? $file.'.htm':$debug_backtrace[1]['class'].'/'.$file.'.htm';
			}else{
				$file = $debug_backtrace[1]['class'].'/'.$debug_backtrace[1]['function'].'.htm';
			}
			$template = rc::smarty()->getTemplateDir(0).$file;
		}
		if( file_exists($template)){
			rc::smarty()->assign('thistkd', $this->tkd);
			rc::smarty()->display($template);
		}
		else
			exit("sorry,the smarty file {$debug_backtrace[1]['function']}.htm is not exists!");
	}
	
	public function assign($key, $value=null){
		rc::smarty()->assign($key, $value);
	}
	
	public function error( $message,$ajax=false){
		$this->_dispatch_jump($message,0,$ajax);
	}
	
	/**
	 * 操作成功跳转的快捷方法
	 * @param unknown_type $message	提示信息
	 * @param unknown_type $ajax	是否为Ajax方式
	 */
	public function success( $message,$ajax=false){
		$this->_dispatch_jump($message,1,$ajax);
	}
	
	/**
	 * 默认跳转操作 支持错误导向和正确跳转,调用模板显示 默认为public目录下面的success页面,提示页面为可配置 支持模板标签
	 * @param unknown_type $message	提示信息
	 * @param unknown_type $status	状态
	 * @param unknown_type $ajax	是否为Ajax方式
	 */
	private function _dispatch_jump($message,$status=1,$ajax=false)
	{
		// 判断是否为AJAX返回
		if($ajax || $this->isAjax()) $this->ajaxReturn('',$message,$status);
		// 提示标题
		$this->assign('msgTitle',$status? "操作成功" : "操作失败");
		//如果设置了关闭窗口，则提示完毕后自动关闭窗口
		if($this->get('closeWin'))    $this->assign('jumpUrl','javascript:window.close();');
		$this->assign('status',$status);   // 状态
		$this->assign('message',$message);// 提示信息
		//保证输出不受静态缓存影响
		C('HTML_CACHE_ON',false);
		if($status) { //发送成功信息
			// 成功操作后默认停留1秒
			if(!$this->get('waitSecond'))    $this->assign('waitSecond',"1");
			// 默认操作成功自动返回操作前页面
			if(!$this->get('jumpUrl')) $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
			$this->display(C('TMPL_ACTION_SUCCESS'));
		}else{
			//发生错误时候默认停留3秒
			if(!$this->get('waitSecond'))    $this->assign('waitSecond',"3");
			// 默认发生错误的话自动返回上页
			if(!$this->get('jumpUrl')) $this->assign('jumpUrl',"javascript:history.back(-1);");
			$this->display(C('TMPL_ACTION_ERROR'));
		}
		exit ;
	}
	
	/**
	 * 是否AJAX请求
	 */
	protected function isAjax() {
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
			if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
				return true;
		}
		if(!empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')]))
			// 判断Ajax方式提交
			return true;
		return false;
	}
	
	/**
	 * Ajax方式返回数据到客户端
	 * @param unknown_type $data	要返回的数据
	 * @param unknown_type $info	提示信息
	 * @param unknown_type $status	返回状态
	 * @param unknown_type $type	ajax返回类型 JSON XML
	 */
	protected function ajaxReturn($data,$info='',$status=1,$type='')
	{
		// 保证AJAX返回后也能保存日志
		$result  =  array();
		$result['status']  =  $status;
		$result['info'] =  $info;
		$result['data'] = $data;
		if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
		if(strtoupper($type)=='JSON') {
			// 返回JSON数据格式到客户端 包含状态信息
			header("Content-Type:text/html; charset=utf-8");
			exit(json_encode($result));
		}elseif(strtoupper($type)=='XML'){
			// 返回xml格式数据
			header("Content-Type:text/xml; charset=utf-8");
			exit(xml_encode($result));
		}elseif(strtoupper($type)=='EVAL'){
			// 返回可执行的js脚本
			header("Content-Type:text/html; charset=utf-8");
			exit($data);
		}else{
			// TODO 增加其它格式
		}
	}
	public function get($name){
		return rc::smarty()->getTemplateVars($name);
	}
	
	//-- 大小变小写去! --
	public function urlToupper(){
		if( strtolower( $_SERVER['REQUEST_URI'])!=$_SERVER['REQUEST_URI']){
			M('Debug')->error('301', strtolower( $_SERVER['REQUEST_URI']));
		}
	}
}