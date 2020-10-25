<?php

/**
 * 访问权限验证
 * Class AuthController
 */
class AddonAuthController extends AddonController
{
    public function __construct()
    {
        parent::__construct();
        //设置此页面的过期时间(用格林威治时间表示)，只要是已经过去的日期即可。
        header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");
        //设置此页面的最后更新日期(用格林威治时间表示)为当天，可以强制浏览器获取最新资料
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        //告诉客户端浏览器不使用缓存，HTTP 1.1 协议
        header("Cache-Control: no-cache, must-revalidate");
        //告诉客户端浏览器不使用缓存，兼容HTTP 1.0 协议
        header("Pragma: no-cache");
        //验证后台登录权限
        if (!$this->checkAdminAccess()) {
            $this->error("没有操作权限");
        }
    }

    //后台权限验证
    protected function checkAdminAccess()
    {
        //管理员
        if (IS_SUPER_ADMIN || IS_WEBMASTER) {
            return true;
        }
        //普通用户
        if (!IS_ADMIN) {
            go(__WEB__ . '?m=Admin&c=Login&a=login');
        }
        //普通管理员权限控制
        $nodeModel = M("node");
        $nodeModel->where = array("MODULE" => MODULE, "controller" => CONTROLLER, "action" => ACTION, 'type' => 1);
        $node = $nodeModel->field("nid")->find();
        //node不存在的节点自动通过验证
        if (!$node) {
            return true;
        } else {
            return M('access')->where(array("nid" => $node['nid'], "rid" => $_SESSION['user']['rid']))->find();
        }
    }
}
