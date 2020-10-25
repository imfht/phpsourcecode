<?php
namespace App\Controller\Admin;

use App\BaseController\AdminBaseController as Base;

/**
 * 微信用户相关操作.
 */
class WxUserGroup extends Base
{
    /**
     * 微信用户分组模型
     * @var \App\Model\WxUserGroup
     */
    private $wxUserGroupModel;
    /**
     * 构造函数
     * @param \Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->addBreadcrumb('微信用户', '/Admin/WxUser/index');
        $this->wxUserGroupModel = model('WxUserGroup');
    }

    /**
     * 微信用户管理首页
     */
    public function index()
    {
        $this->setSeoTitle('微信用户组管理');
        $this->addBreadcrumb('微信用户组管理', $this->currentUrl);
        //用户组列表
        $groupList     = $this->wxUserGroupModel->getUserGroupList();
        if ($groupList){
            foreach ($groupList as $k => $v){
                $v['groupName'] .= '('.$v['userCount'].')';
                $groupList[$k] = $v;
            }
        }
        //树结构用户组列表
        $tree          = new \App\Common\Tree('groupId', 'parentId', 'child');
        $tree->nameKey = 'groupName';
        $tree->load($groupList);
        //可嵌套列表
        $addHtml = '<button type="button" class="btn btn-outline btn-danger btn-xs pull-right del"><i class="fa fa-trash-o"></i>删除</button>';
        $addHtml .= '<button type="button" class="btn btn-outline btn-primary btn-xs pull-right edit" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil"></i>编辑</button>';
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
        $groupList     = $this->wxUserGroupModel->getUserGroupList();
        $primaryKey = 'groupId';
        //用户组选择列表
        $optionHtml = '';
        if (isset($this->request->header['Referer']) && strpos(strtolower($this->request->header['Referer']), '/admin/wxusergroup/index') !== false){
            $optionHtml .= '<option value="0">顶级分组</option>';
        }else{
            //设置主键字段
            $primaryKey = 'wxGroupId';
        }
        //树结构用户组列表
        $tree          = new \App\Common\Tree($primaryKey, 'parentId', 'child');
        $tree->nameKey = 'groupName';
        $tree->load($groupList);
        $secId && $tree->optionSelectId = $secId;

        $optionHtml .= $tree->buildOptions();
        $this->http->finish($optionHtml);
    }
    /**
     * 同步微信线上数据
     */
    public function syncOnline()
    {
        try {
            $wxUserGroupSer = new \App\Service\WxUserGroup();
            $rs = $wxUserGroupSer->syncOnline();
            if ($rs) {
                return $this->showMsg('success', '同步成功', '/Admin/WxUserGroup/index');
            }
            throw new \Exception('同步失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
    /**
     * 保存用户组数据.
     */
    public function save()
    {
        try {
            $postData              = $this->request->post;
            $postData['addUserId'] = $this->user->getUid();
            $wxUserGroup               = new \App\Service\WxUserGroup();
            $rs                    = $wxUserGroup->saveData($postData);
            if ($rs) {
                return $this->showMsg('success', ($postData['groupId'] ? '编辑' : '添加') . '成功', '/Admin/WxUserGroup/index');
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
            $data = $this->wxUserGroupModel->getone(['groupId'=>$id]);

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
            $wxUserGroup = new \App\Service\WxUserGroup();
            $rs      = $wxUserGroup->saveSort($sortData);
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
            $wxUserGroup = new \App\Service\WxUserGroup();
            if ($wxUserGroup->del($id)){
                return $this->showMsg('success', '删除成功');
            }
            throw new \Exception('删除失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
}