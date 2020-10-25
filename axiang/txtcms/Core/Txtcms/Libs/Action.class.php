<?php
/**
 * TXTCMS 框架控制器类
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
abstract class Action {
	protected $view=null;
	private $name     =  '';
	protected $config   =   array();
	protected $template;
	/**
     * 架构函数 取得模板对象实例
     * @access public
     */
	public function __construct() {
        //实例化视图类
        $this->view=new View;
		$this->template=$this->view->template;
        //控制器初始化
        if(method_exists($this,'_init')) $this->_init();
    }
	public function tplConf($item='',$val=null,$template=false){
		if($val===null){
			if($item!=''){
				if($template) return $this->template->$item;
				return $this->view->options[$item];
			}else{
				if($template) return $this->template;
				return $this->view->options;
			}
		}else{
			if($template){
				$this->template->$item=$val;
			}else{
				$this->view->options[$item]=$val;
			}
		}
	}
	/**
     * 获取当前Action名称
     * @access protected
     */
    protected function getActionName() {
        if(empty($this->name)) {
            // 获取Action名称
            $this->name     =   substr(get_class($this),0,-6);
        }
        return $this->name;
    }
	/**
     * 模板显示
     * @access protected
     */
	protected function display($templateFile='',$cacheid='') {
        $this->view->display($templateFile,$cacheid);
    }
	/**
     * 模板变量赋值
     * @access protected
     */
	protected function assign($name,$value='') {
        $this->view->assign($name,$value);
        return $this;
    }
	public function __set($name,$value) {
        $this->assign($name,$value);
    }
	/**
     * 取得模板显示变量的值
     * @access protected
     */
	public function get($name='') {
        return $this->view->get($name);      
    }

    public function __get($name) {
        return $this->get($name);
    }
	public function __isset($name) {
        return $this->get($name);
    }
	protected function error($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,0,$jumpUrl,$ajax);
    }
	protected function success($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,1,$jumpUrl,$ajax);
    }
	protected function ajaxReturn($data,$type='JSON') {
        if(func_num_args()>2) {
            $args           =   func_get_args();
            array_shift($args);
            $info           =   array();
            $info['data']   =   $data;
            $info['info']   =   array_shift($args);
            $info['status'] =   array_shift($args);
            $data           =   $info;
            $type           =   $args?array_shift($args):'';
        }
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
                exit($handler.'('.json_encode($data).');');  
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);
        }
    }
	public function __call($method,$args) {
        if( 0 === strcasecmp($method,ACTION_NAME.config('ACTION_SUFFIX'))) {
            if(method_exists($this,'_empty')) {
                // 如果定义了_empty操作 则调用
                $this->_empty($method,$args);
            }elseif(is_file($this->view->getTemplate())){
                // 检查是否存在默认模版 如果有直接输出模版
                $this->display();
            }else{
                _404('非法操作:'.ACTION_NAME);
            }
        }else{
            switch(strtolower($method)) {
                // 判断提交方式
                case 'ispost'   :
                case 'isget'    :
                case 'ishead'   :
                case 'isdelete' :
                case 'isput'    :
                    return strtolower($_SERVER['REQUEST_METHOD']) == strtolower(substr($method,2));
                // 获取变量 支持过滤和默认值 调用方式 $this->_post($key,$filter,$default);
                case '_get'     :   $input =& $_GET;break;
                case '_post'    :   $input =& $_POST;break;
                case '_put'     :   parse_str(file_get_contents('php://input'), $input);break;
                case '_param'   :  
                    switch($_SERVER['REQUEST_METHOD']) {
                        case 'POST':
                            $input  =  $_POST;
                            break;
                        case 'PUT':
                            parse_str(file_get_contents('php://input'), $input);
                            break;
                        default:
                            $input  =  $_GET;
                    }
                    break;
                case '_request' :   $input =& $_REQUEST;   break;
                case '_session' :   $input =& $_SESSION;   break;
                case '_cookie'  :   $input =& $_COOKIE;    break;
                case '_server'  :   $input =& $_SERVER;    break;
                case '_globals' :   $input =& $GLOBALS;    break;
                default:
                    throw_exception(__CLASS__.':'.$method.'请求的方法不存在！');
            }
            if(!isset($args[0])) { // 获取全局变量
                $data       =   $input; // 由VAR_FILTERS配置进行过滤
            }elseif(isset($input[$args[0]])) { // 取值操作
                $data       =	$input[$args[0]];
                $filters    =   isset($args[1])?$args[1]:config('DEFAULT_FILTER');
                if($filters) {
                    $filters    =   explode(',',$filters);
                    foreach($filters as $filter){
                        if(function_exists($filter)) {
                            $data   =   is_array($data)?array_map($filter,$data):$filter($data); // 参数过滤
                        }
                    }
                }
            }else{ // 变量默认值
                $data       =	 isset($args[2])?$args[2]:NULL;
            }
            return $data;
        }
    }
	/**
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     * @param string $message 提示信息
     * @param Boolean $status 状态
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @access private
     * @return void
     */
	private function dispatchJump($message,$status=1,$jumpUrl='',$ajax=false) {
		$this->tplConf('compile_check',true);
		$this->tplConf('caching',false);
        if(true === $ajax || IS_AJAX) {// AJAX提交
            $data           =   is_array($ajax)?$ajax:array();
            $data['info']   =   $message;
            $data['status'] =   $status;
            $data['url']    =   $jumpUrl;
            $this->ajaxReturn($data);
        }
        if(is_int($ajax)){
			$waitSecond=$ajax;
			$this->assign('waitSecond',$ajax);
		}
        if(!empty($jumpUrl)) $this->assign('jumpUrl',$jumpUrl);
        // 提示标题
        if($this->view->get('msgTitle')===null) $this->assign('msgTitle',$status? '操作成功！' : '操作失败！');
        $this->assign('status',$status);   // 状态
		
        if($status) { //发送成功信息
            $this->assign('message',$message);// 提示信息
            // 成功操作后默认停留1秒
            if(!isset($this->waitSecond)) $this->assign('waitSecond','1');
            // 默认操作成功自动返回操作前页面
            if(!isset($this->jumpUrl)) $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
            $this->display(config('TMPL_ACTION_SUCCESS'));
        }else{
            $this->assign('error',$message);// 提示信息
            //发生错误时候默认停留3秒
            if(!isset($this->waitSecond))$this->assign('waitSecond','3');
            // 默认发生错误的话自动返回上页
            if(!isset($this->jumpUrl)) $this->assign('jumpUrl',"javascript:history.back(-1);");
            $this->display(config('TMPL_ACTION_ERROR'));
            // 中止执行  避免出错后继续执行
            exit ;
        }
    }
}