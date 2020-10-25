<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\service\MenuService;
use app\common\controller\Backend;

/**
 * 后台主页-控制器
 * @author 牧羊人
 * @since 2020/7/10
 * Class Index
 * @package app\admin\controller
 */
class Index extends Backend
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/11
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\Admin();
    }

    /**
     * 后台主页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function index()
    {
        // 取消模板布局
        $this->view->engine->layout(false);
        // 获取菜单
        $menuService = new MenuService();
        $result = $menuService->getNavbarMenu($this->permission);
        $this->assign('menuList', $result['data']);
        return $this->fetch();
    }

    /**
     * 系统欢迎页
     * @return mixed
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function main()
    {
        // 取消模板布局
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    /**
     * 个人中心
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @since 2020/7/11
     */
    public function userInfo()
    {
        if (IS_POST) {
            // 参数
            $param = request()->param();
            // 真实姓名
            $realname = trim($param['realname']);
            // 邮箱
            $email = trim($param['email']);
            // 个人简介
            $intro = trim($param['intro']);
            // 街道地址
            $address = trim($param['address']);
            // 联系电话
            $mobile = trim($param['mobile']);
            $data = [
                'id' => $this->adminId,
                'realname' => $realname,
                'email' => $email,
                'intro' => $intro,
                'address' => $address,
                'mobile' => $mobile,
            ];
            $result = $this->model->edit($data);
            if (!$result) {
                return message("信息保存失败", false);
            }
            return message();
        }
        $adminMod = new \app\admin\model\Admin();
        $adminInfo = $adminMod->getInfo($this->adminId);
        $this->assign("adminInfo", $adminInfo);
        return $this->render();
    }

    /**
     * 更新密码
     * @return array
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function updatePwd()
    {
        // 参数
        $param = request()->param();
        // 原密码
        $oldPassword = trim($param['oldPassword']);
        if (!$oldPassword) {
            return message("原密码不能为空", false);
        }
        // 新密码
        $newPassword = trim($param['newPassword']);
        if (!$newPassword) {
            return message("新密码不能为空", false);
        }
        // 确认密码
        $rePassword = trim($param['rePassword']);
        if (!$rePassword) {
            return message("确认密码不能为空", false);
        }
        if ($newPassword != $rePassword) {
            return message("两次输入的密码不一致", false);
        }
        if (get_password($oldPassword . $this->adminInfo['username']) != $this->adminInfo['password']) {
            return message("原始密码不正确", false);
        }
        // 设置新密码
        $data = [
            'id' => $this->adminId,
            'password' => get_password($newPassword . $this->adminInfo['username']),
        ];
        $adminMod = new \app\admin\model\Admin();
        $result = $adminMod->edit($data);
        if (!$result) {
            return message("修改失败", false);
        }
        return message("修改成功");
    }
}
