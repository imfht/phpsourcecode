<?php

namespace App\Controller\Admin;

use App\BaseController\AdminBaseController as Base;

/**
 * 微信用户标签相关操作.
 */
class WxUserTag extends Base
{
    /**
     * @var \App\Model\WxUserTag
     */
    private $wxUserTagModel;

    /**
     * 构造函数
     * @param \Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->addBreadcrumb('微信用户', '/Admin/WxUser/index');
        $this->wxUserTagModel = model('WxUserTag');
    }
    /**
     * 微信用户标签列表
     */
    public function index()
    {
        $this->setSeoTitle('微信用户标签管理');
        $this->addBreadcrumb('用户标签管理', $this->currentUrl);

        $this->display();
    }
    /**
     * 生成树结构option文本
     * @return string
     */
    public function getTreeOption()
    {
        $secId = isset($_GET['secId']) ? $_GET['secId'] : 0;
        //用户标签列表
        $tagList     = $this->wxUserTagModel->getUserTagList();
        //树结构菜单列表
        $tree          = new \App\Common\Tree('tagId', 'parentId', 'child');
        $tree->nameKey = 'tagName';
        $tree->load($tagList);
        $secId && $tree->optionSelectId = $secId;
        //菜单选择列表
        if (isset($this->request->header['Referer']) && strpos(strtolower($this->request->header['Referer']), '/admin/wxusertag/index') !== false){
            $optionHtml = '<option value="0">顶级权限</option>';
        }else{
            $optionHtml = '<option value="">请选择标签</option>';
        }
        $optionHtml .= $tree->buildOptions();
        $this->http->finish($optionHtml);
    }
    /**
     * 返回jsTree格式数据格式
     * @return array
     */
    public function getJsTreeData()
    {
        //权限规则列表
        $isCleanCache = false;
        if (isset($this->request->header['Referer']) && strpos(strtolower($this->request->header['Referer']), '/admin/wxusertag/index') !== false){
            $isCleanCache = true;
        }
        $tagList     = $this->wxUserTagModel->getUserTagList($isCleanCache);
        //树结构用户组列表
        $tree          = new \App\Common\Tree('tagId', 'parentId', 'children');
        $tree->nameKey = 'tagName';
        $tree->load($tagList);
        return $tree->makeJsTreeFormat();
    }

    /**
     * 同步线上标签数据
     * @return bool
     */
    public function syncOnline()
    {
        try {
            $wxUserTagSer = new \App\Service\WxUserTag();
            $rs = $wxUserTagSer->syncOnline();
            if ($rs) {
                return $this->showMsg('success', '同步成功', '/Admin/WxUserTag/index');
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
            $wxUserTagSer               = new \App\Service\WxUserTag();
            $rs                    = $wxUserTagSer->saveData($postData);
            if ($rs) {
                return $this->showMsg('success', ($postData['tagId'] ? '编辑' : '添加') . '用户标签成功', '/Admin/WxUserTag/index');
            }
            throw new \Exception(($postData['menuId'] ? '编辑' : '添加') . '用户标签失败');
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
            $data = $this->wxUserTagModel->getone(['tagId'=>$id]);

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
            $tagId = $this->request->post['id'] ?? 0;
            $parentId = $this->request->post['parent'] ?? 0;
            is_numeric($parentId) || $parentId = 0;
            $orderNum = $this->request->post['position'] ?? 0;
            if (empty($tagId)) {
                throw new \Exception('请选择要移动的对象');
            }
            $wxUserTagSer = new \App\Service\WxUserTag();
            $rs = $wxUserTagSer->setOrderNum($parentId, $tagId, $orderNum);
            if ($rs) {
                return $this->showMsg('success', '移动成功');
            }
            throw new \Exception('移动失败');
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
                throw new \Exception('请指定要删除的用户标签');
            }
            $wxUserTagSer = new \App\Service\WxUserTag();
            if ($wxUserTagSer->del($id)){
                return $this->showMsg('success', '删除成功');
            }
            throw new \Exception('删除失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
}
