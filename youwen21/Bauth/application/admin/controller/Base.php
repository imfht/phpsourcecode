<?php
namespace app\admin\controller;

use app\lib\Bauth;
use app\lib\Menu;
use think\Controller;

class Base extends Controller {
    public $uid = 0;

    public function _empty() {
        return $this->error('方法不存在');
    }

    public function _initialize() {
        parent::_initialize();
        if (!is_login()) {
            // exit(url('adminpublic/login'));
            $this->redirect(url('AdminPublic/login'));
        }
        $this->uid = \think\Session::get('userInfo.id');

        $module = $this->request->module();
        $controller = $this->request->controller();
        $action = $this->request->action();

        $auth = new Bauth($this->uid, $module);
        // $auth->authInit();
        if(isset($_SESSION['allowList'])){
            $ret = $auth->setAllowList($_SESSION['allowList']);
        }else{
            $allowList = $auth->authInit();
            $_SESSION['allowList'] = $allowList;
        }

        if (!$auth->check($controller, $action)) {
            $this->error('权限不足');
        }

        $menu = new Menu($auth->getPower(), $auth->getAllowId());
        if(isset($_SESSION['reqId'.$module.$controller.$action])){
            $reqId = $_SESSION['reqId'.$module.$controller.$action];
        }else{
            $reqId = $_SESSION['reqId'.$module.$controller.$action] = $menu->getId($module, $controller, $action);
        }
        if(isset($_SESSION['reqRootId'.$reqId])){
            $reqRootId = $_SESSION['reqRootId'.$reqId];
        }else{
            $reqRootId = $_SESSION['reqRootId'.$reqId] = $menu->getRootId($reqId);
        }
        if(isset($_SESSION['topMenu'])){
            $topMenu = $_SESSION['topMenu'];
        }else{
            $topMenu = $_SESSION['topMenu'] = $menu->getTopMenu('admin');
            $topIds = array_column($topMenu, 'id');
            if(!in_array($reqRootId, $topIds)){
                $reqRootId = $topIds[0];
            }
        }
        if(isset($_SESSION['sideTree'.$reqRootId])){
            $sideTree = $_SESSION['sideTree'.$reqRootId];
        }else{
            $sideTree = $_SESSION['sideTree'.$reqRootId] = $menu->getSideTree($reqRootId);
        }

        $this->assign('_reqRootId', $reqRootId);
        $this->assign('_sideList', $sideTree);
        $this->assign('_topMenu', $topMenu);

        //后台系统用户行为记录
        \app\lib\BehaviorRecording::log($this->uid, $controller, $action);

        // 特殊环境 限制部分操作
        // 本地的代码或者正式环境可以去此处代码
        if(\think\Env::get('scene') == 'demo'){
            $access = 'PHP_'.'ACCESSDENIED_'.strtoupper($controller.'_'.$action);
            if(getenv($access)){
                $this->error('本环境不允许此操作');
            }
        }
    }

    /**
     * 通用编辑功能
     * @param  $model      模型
     * @param  $where      条件
     * @param  $data       更新的数据
     * @param  $msgReplace 返回的消息
     * @author baiyouwen
     */
    protected function editRows($model, $where, $data, $msgReplace = []) {
        $msg = array_merge(['success' => '操作成功！', 'error' => '操作失败！', 'url' => '', 'ajax' => $this->request->isAjax()], $msgReplace);
        if (false !== db($model)->where($where)->update($data)) {
            return $this->success($msg['success'], $msg['url']);
        } else {
            $this->error($msg['error'], $msg['url']);
        }
    }
    /**
     * 获取参数 ， 参数不存在则输出错误信息
     * @param  $name 参数名
     * @param  $msg  错误提示信息
     * @param  $type 获取参数的类型
     * @author baiyouwen
     */
    protected function inputOrError($name, $msg = null, $type = 'param') {
        $ret = $this->request->$type($name, null);
        if (is_null($msg)) {
            $msg = 'Missing parameter :' . $name;
        }
        if (is_null($ret)) {
            $this->error($msg, ''); //Argument
        }
        return $ret;
    }

    /**
     * 状态编辑  启用  禁用  删除
     * @author baiyouwen
     */
    public function changeStatus() {
        $id = $this->request->param('ids', 0);
        $ids = $this->request->param('ids/a', $id);
        if (is_array($ids)) {
            $ids = implode(',', $ids);
        }
        $method = $this->inputOrError('method'); // forbidRow deleteRow resumeRow
        $table = $this->inputOrError('target');
        $table = $this->_safeTarget($table);
        $pk = $this->request->param('pk', 'id');
        $field = $this->request->param('targetField', 'status');
        $setValue = 0;
        switch ($method) {
        case 'forbidRow':
            $setValue = 0;
            break;
        case 'deleteRow':
            $setValue = -1;
            break;
        case 'resumeRow':
            $setValue = 1;
            break;
        default:
            $this->error('操作类型不存在');
            break;
        }
        $ret = db($table)->where([$pk => array('in', $ids)])->update([$field => $setValue]);
        if (false !== $ret) {
            return $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 对表查行安全转换，避免表名暴露在外
     * @author baiyouwen
     */
    private function _safeTarget($target='')
    {
        if(isset($this->_safeTargets[$target])){
            return $this->_safeTargets[$target];
        }
        return $target;
    }
    private $_safeTargets = 
    [
        'user' => 'administrator',
    ];

    /**
     * 公众方法 列表 分页输出
     * @param  string $table 表名
     * @param  array $map   查询条件
     * @return array        查询出的结果集
     */
    protected function lists($table='', $map=[], $order='')
    {
        $pageConf = [];
        $pageConf['page'] = $this->rememberPage();

        if(empty($order)){
            $pageObj = db($table)->where($map)->paginate(10, false, $pageConf);
        }else{
            $pageObj = db($table)->where($map)->order($order)->paginate(10, false, $pageConf);
        }
        // $page = $pageObj->render();
        // $retArray = $pageObj->toArray();
        // $this->assign('_page', $page);
        // return $retArray;
        $this->assign('_list', $pageObj);
        $this->relocatePage = false;
        return 1;
    }

    /**
     * 记录分页 当再次进入列表页面自动定位到指定分页
     * @author baiyouwen
     */
    protected function rememberPage()
    {
        $controller = $this->request->controller();
        $actioin = $this->request->action();
        $pageTag = 'page_'.$controller.'_'.$actioin;
        if($this->request->has('page', 'get')){ //是否带有页面参数
            $pageNum = $this->request->param('page');
        }else{ // 无页面参数表示第一次进，取cookie存的页面赋值给页面参数
            $pageNum = $this->request->cookie($pageTag, 1);
        }
        setcookie($pageTag, $pageNum, time()+86400);
        return $pageNum;
    }

    public function tsuccess()
    {
        return $this->fetch('base/success');
    }
}
