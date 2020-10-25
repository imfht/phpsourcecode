<?php
namespace App\Controller\Admin;

use App\BaseController\AdminBaseController as Base;

/**
 * 系统用户分组相关操作
 * @package App\Controller\Admin
 */
class SysUserGroup extends Base
{
    /**
     * @var \App\Model\SysUserGroup
     */
    private $sysUserGroupModel;

    /**
     * 构造函数
     * @param \Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->addBreadcrumb('系统管理', '/Admin/System/index');
        $this->sysUserGroupModel = model('SysUserGroup');
    }

    /**
     * 用户分组列表
     */
    public function index()
    {
        $this->setSeoTitle('系统用户组管理');
        $this->addBreadcrumb('系统用户组管理', $this->currentUrl);
        //用户组列表
        $groupList     = $this->sysUserGroupModel->getUserGroupList();
        //树结构用户组列表
        $tree          = new \App\Common\Tree('groupId', 'parentId', 'child');
        $tree->nameKey = 'groupName';
        $tree->load($groupList);
        //可嵌套列表
        $addHtml = '<button type="button" class="btn btn-outline btn-danger btn-xs pull-right del"><i class="fa fa-trash-o"></i>删除</button>';
        $addHtml .= '<button type="button" class="btn btn-outline btn-primary btn-xs pull-right edit" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil"></i>编辑</button>';
        $addHtml .= '<button type="button" class="btn btn-outline btn-primary btn-xs pull-right editrule" data-toggle="modal" data-target="#ruleModal"><i class="fa fa-pencil"></i>分组权限</button>';
        $addHtml .= '<button type="button" class="btn btn-outline btn-primary btn-xs pull-right addchild" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil"></i>添加子菜单</button>';
        $nestableHtml = $tree->buildNestableTree($addHtml);
        $this->assign('nestableHtml', $nestableHtml);
        $this->display();
    }
    /**
     * 生成树结构option文本
     * @return string
     */
    public function getTreeOption()
    {
        $secId = isset($_GET['secId']) ? $_GET['secId'] : 0;
        //用户组列表
        $groupList     = $this->sysUserGroupModel->getUserGroupList();
        //树结构用户组列表
        $tree          = new \App\Common\Tree('groupId', 'parentId', 'child');
        $tree->nameKey = 'groupName';
        $tree->load($groupList);
        $secId && $tree->optionSelectId = $secId;
        //用户组选择列表
        $optionHtml = '';
        if (isset($this->request->header['Referer']) && strpos(strtolower($this->request->header['Referer']), '/admin/sysusergroup/index') !== false){
            $optionHtml .= '<option value="0">顶级分组</option>';
        }else{
            $optionHtml .= '<option value="0">请选择</option>';
        }
        $optionHtml .= $tree->buildOptions();
        $this->http->finish($optionHtml);
    }
    /**
     * 保存用户组数据.
     */
    public function save()
    {
        try {
            $postData              = $this->request->post;
            $postData['addUserId'] = $this->user->getUid();
            $sysUserGroup               = new \App\Service\SysUserGroup();
            $rs                    = $sysUserGroup->saveData($postData);
            if ($rs) {
                return $this->showMsg('success', ($postData['groupId'] ? '编辑' : '添加') . '成功', '/Admin/SysUserGroup/index');
            }
            throw new \Exception(($postData['groupId'] ? '编辑' : '添加') . '用户组失败');
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
            $data = $this->sysUserGroupModel->getone(['groupId'=>$id]);
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
     * 保存排序层级数据.
     */
    public function saveSort()
    {
        try {
            $sortData = $this->request->post['sortData'];
            if (empty($sortData)) {
                throw new \Exception('排序数据有误');
            }
            $sysUserGroup = new \App\Service\SysUserGroup();
            $rs      = $sysUserGroup->saveSort($sortData);
            if ($rs) {
                return $this->showMsg('success', '保存排序成功');
            }
            throw new \Exception('保存排序失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 删除用户组
     * @return bool
     */
    public function del()
    {
        try {
            $id   = $this->request->post['id'] ?? 0;
            if (!$id){
                throw new \Exception('请指定要删除的用户组');
            }
            $rs = $this->sysUserGroupModel->set($id, ['isDel'=>1]);
            if ($rs){
                return $this->showMsg('success', '删除成功');
            }
            throw new \Exception('删除失败');
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
                throw new \Exception('请选择要设置权限的用户组');
            }
            $ruleIds              = $this->request->post['ruleIds'] ?? [];
            $rs                    = $this->sysUserGroupModel->set($userId, ['ruleIds' => serialize($ruleIds)]);
            if ($rs) {
                return $this->showMsg('success', '设置用户组权限成功');
            }
            throw new \Exception('设置用户组权限失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
}