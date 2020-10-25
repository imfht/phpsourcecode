<?php
// 逻辑控制器
namespace app\center\Logic;
use app\center\Logic\LogicInterface;
use think\View;
class Controller implements LogicInterface{
    use \traits\controller\Jump;
    protected $app;
    protected $view;
    public function __construct($app)
    {
        $this->app = $app;
        //$this->view = new View();
    }
    public function init(&$opts,$action=null){}
    public function main(){}
    public function ajax(){}
    public function save(){
        $data = $_POST;
        if(empty($data)) $data = $_GET;
        if($data) debugOut($data,true);
        utf8();echo '欢迎访问@CONERO.NET-'.sysdate();
    }
    public function edit($view){}
    // *** 工具控制
    protected function fetch($name=null)
    {
        if(is_object($name)){$this->view = $name;$name = null;}
        elseif(!is_object($this->view)) $this->view = new View();
        $class = $this->getAppName();
        //return $this->app->fetch(APP_PATH.'center/view/'.$class.'/edit.html');
        $name = $name? APP_PATH.'center/view/'.$name.'.html':APP_PATH.'center/view/'.$class.'_edit.html';     
        return $this->view->fetch($name);
    }
    protected function viewInit($view=null){
        if(is_object($view)){$this->view = $view;return;}
        $this->view = new View();
    }
    public function assign($name,$value=null){
        if(!is_object($this->view)) $this->view = new View();
        $this->view->assign($name,$value);
    }
    protected function getAppName(){return strtolower(str_replace('app/center/Logic/','',str_replace('\\','/',get_class($this))));}
    protected function form($view,$name=null){
        $name = $name ? $name:$this->getAppName();
        // $name = 'app/center/view/'.$name.'_edit.html';
        $name = $name.'_edit';
        $view->assign('form',$this->fetch($name));
    }
    // 编辑页面参数
    protected function editPageParam($option=null,$view=null){
        if(empty($option)){
            return [
                'user'=>'','navbar'=>'','navSelf'=>'','navActive'=>'','form'=>''
            ];
        }
        $view = is_object($view)? $view:$this->view;
        if(is_object($view) && is_array($option)) $view->assign($option);
    }
}