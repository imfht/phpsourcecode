<?php

namespace App\Controller\Admin;

use App\BaseController\AdminBaseController as Base;

/**
 * 系统用户相关操作.
 */
class SysUser extends Base
{
    /**
     * @var \App\Model\SysUser
     */
    private $sysUserModel;

    /**
     * 构造函数.
     *
     * @param \Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->addBreadcrumb('系统管理', '/Admin/System/index');
        $this->sysUserModel = model('SysUser');
    }

    /**
     * 用户列表.
     */
    public function index()
    {
        $this->setSeoTitle('系统用户管理');
        $this->addBreadcrumb('系统用户管理', $this->currentUrl);

        $this->display();
    }

    /**
     * 返回用户列表数据
     * @return array
     */
    public function getPageList()
    {
        //绘制计数器。
        $draw = (int) ($this->request->request['draw'] ?? 0);
        $where = [
            'select' => 'id,userName,account,email,loginTime,loginIp,groupName,sys_user.isDel',
        ];
        //开始位置
        $start = (int) ($this->request->request['start'] ?? 0);
        //长度
        $length = (int) ($this->request->request['length'] ?? 10);
        $where['limit'] = $start . ',' . $length;
        //搜索关键字
        $keyword = $this->request->request['search']['value'] ?? '';
        if ($keyword){
            $where['where'] = (isset($where['where']) && $where['where'] ? ' AND ' : '') . "`userName` like '$keyword%' OR `account` like '$keyword%' OR `email` like '$keyword%'";
        }
        //排序字段
        $order = $this->request->request['order'] ?? [];
        if ($order){
            switch ($order[0]['column']){
                case 1:
                    $where['order'] = 'sys_user.account '.$order[0]['dir'];
                    break;
                default:
                    $where['order'] = 'sys_user.addTime desc';
            }
        }

        $data  = [
            'draw' => $draw,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ];
        $data['recordsTotal'] = $this->sysUserModel->count($where);
        $list = $this->sysUserModel->getUserList($where);
        if ($list){
            foreach ($list as $k => $v){
                $v['DT_RowId'] = $v['id'];
                $list[$k] = $v;
            }
        }
        $data['data'] = $list;
        $data['recordsFiltered'] = count($list);

        return $data;
    }
    /**
     * 保存用户组数据.
     */
    public function save()
    {
        try {
            $postData              = $this->request->post;
            $postData['addUserId'] = $this->user->getUid();
            $sysUserSer               = new \App\Service\SysUser();
            $rs                    = $sysUserSer->saveData($postData);
            if ($rs) {
                return $this->showMsg('success', ($postData['id'] ? '编辑' : '添加') . '成功', '/Admin/SysUser/index');
            }
            throw new \Exception(($postData['menuId'] ? '编辑' : '添加') . '用户失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 获取用户组数据.
     *
     * @return bool
     */
    public function get()
    {
        try {
            $id   = $this->request->get['id'] ?? 0;
            $data = $this->sysUserModel->getone([
                'select' => "id,groupId,userName,account,email,ruleIds",
                'where'=>"`id`=$id",
            ]);
            if ($data['ruleIds']){
                $data['ruleIds'] = unserialize($data['ruleIds']);
            }else{
                $data['ruleIds'] = [];
            }
            return $this->showMsg('success', '获取成功', '', $data);
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
    /**
     * 设置用户状态
     * @return bool
     */
    public function setStatus()
    {
        try {
            $id   = $this->request->post['id'] ?? 0;
            $status = (int) $this->request->post['status'] ?? 0;
            $actName = $status == 1 ? '禁用' : '开启';
            if (!$id){
                throw new \Exception('请指定要删除的用户');
            }
            $rs = $this->sysUserModel->set($id, ['isDel'=>$status]);
            if ($rs){
                return $this->showMsg('success', $actName . '成功');
            }
            throw new \Exception($actName . '失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 保存权限
     * @return bool
     */
    public function saveRule()
    {
        try {
            $userId = (int) $this->request->post['id'];
            if (!$userId){
                throw new \Exception('请选择要设置权限的用户');
            }
            $ruleIds              = $this->request->post['ruleIds'] ?? [];
            $rs                    = $this->sysUserModel->set($userId, ['ruleIds' => serialize($ruleIds)]);
            if ($rs) {
                return $this->showMsg('success', '设置用户权限成功');
            }
            throw new \Exception('设置用户权限失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
}
