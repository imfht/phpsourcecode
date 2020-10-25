<?php
namespace Lib;
use Core\Template,Core\Config;


/**
 * @author shooke
 * 控制器基础类
 * 实现了模板初始化，跳转等基本功能
 */
class Action{

	protected $config = array();
	

	/**
	 * 获取模板对象
	 *
	 * @return unknown
	 */
	public function cpView(){
		static $view = NULL;
		if( empty($view) ){
			$view = new Template( Config::get('APP') );
		}
		return $view;
	}

	/**
	 * 模板赋值
	 *
	 * @param unknown_type $name
	 * @param unknown_type $value
	 * @return unknown
	 */
	protected function assign($name, $value=null){
		return $this->cpView()->assign($name, $value);
	}

	/**
	 * 模板显示
	 *
	 * @param unknown_type $tpl
	 * @param unknown_type $return
	 * @return unknown
	 */
	protected function display($tpl = '' ){
		return $this->cpView()->display($tpl);
	}
	/**
	 * 获取模板内容，不输出
	 *
	 * @param unknown_type $tpl
	 * @param unknown_type $return
	 * @return unknown
	 */
    protected function fetch($tpl=''){
        return $this->cpView()->display($tpl,true);
    }
	/**
	 * 直接跳转
	 *
	 * @param unknown_type $url
	 * @param unknown_type $code
	 */
	protected function redirect( $url, $code=302) {
		header('location:' . $url, true, $code);
		exit;
	}
    /**
     * 
     * $url，基准网址，若为空，将会自动获取，不建议设置为空
     * $total，信息总条数
     * $listRows，每页显示行数
     * $pagebarnum，分页栏每页显示的页数
     * $mode，显示风格，参数可为整数1，2，3，4任意一个
     * 	
	 */
	protected function page($total=0,$listRows=10,$url="",$pageBarNum=10,$mode=1){
		$page=new Page();
		$page->pageSuffix=Config::get('URL_HTML_SUFFIX');		
		$cur_page=$page->getCurPage();//当前页码
		$limit_start=($cur_page-1)*$listRows;
		$limit=$limit_start.','.$listRows;
		$pagestring = $page->show($url,$total,$listRows,$pageBarNum,$mode) ;
		return array($limit,$pagestring);
	}

	/**
     * Ajax方式返回数据到客户端
     * @access public
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
	public function ajaxReturn($data,$type='',$exit=1) {

		if(empty($type)) $type  =   'JSON';
		switch (strtoupper($type)){
			case 'JSON' :
				// 返回JSON数据格式到客户端 包含状态信息
				$data = cp_urlencode($data);
				if($exit){
					header('Content-Type:application/json; charset=utf-8');
					exit(cp_urldecode(json_encode($data)));
				}else{
					return cp_urldecode(json_encode($data));
				}
			case 'XML'  :
				// 返回xml格式数据
				if($exit){
					header('Content-Type:text/xml; charset=utf-8');
					exit(xml_encode($data));
				}else{
					return xml_encode($data);
				}
			default  :
				// 返回可执行的js脚本
				if($exit){
					header('Content-Type:text/html; charset=utf-8');
					exit($data);
				}else{
					return $data;
				}
		}
	}
	/**
     * @desc 操作错误跳转的快捷方法
     * @access public
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param int $wait 等待时间
     * @return void
     */
	public function error($message,$url='',$wait=3) {
	    //ajax返回数据
	    if($ajax){
	        $this->ajaxReturn($message);
	    }
	    //如果手工设置了跳转页面则执行跳转
		$url && header("refresh:$wait;url=$url");
	    //模板处理
	    $errorTpl = Config::get('TPL_ACTION_ERROR');	    
	    if($errorTpl){
			$this->assign('message',$message);
			$this->assign('url',$url);
			$this->assign('wait',$wait);
			$this->display($errorTpl);			
		}else {
		    include CP_ROOT_PATH.'Core/Tpl/Error.php';
		}
		exit;		
	}

	/**
     * 操作成功跳转的快捷方法
     * @access public
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param int $wait 等待时间
     * @return void
     */
	public function success($message,$url='',$wait=1,$exit=FALSE) {
	    //ajax返回数据
	    if($ajax){
	        $this->ajaxReturn($message);
	    }
	    //如果手工设置了跳转页面则执行跳转
	    $url && header("refresh:$wait;url=$url");
	    //模板处理
	    $successTpl = Config::get('TPL_ACTION_SUCCESS');	    
	    if($successTpl){
			$this->assign('message',$message);
			$this->assign('url',$url);
			$this->assign('wait',$wait);
			$this->display($successTpl);			
		}else {
		    include CP_ROOT_PATH.'Core/Tpl/Success.php';
		}
		//如果开启终止则终止程序
		$exit && exit;
	}
	


}