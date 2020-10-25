<?php

namespace App\Controller\Admin;

use App\BaseController\AdminBaseController as Base;

/**
 * 系统用户相关操作.
 */
class SysAuthRule extends Base
{
    /**
     * @var \App\Model\SysAuthRule
     */
    private $sysAuthRuleModel;

    /**
     * 构造函数
     * @param \Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->addBreadcrumb('系统管理', '/Admin/System/index');
        $this->sysAuthRuleModel = model('SysAuthRule');
    }
    /**
     * 权限列表
     */
    public function index()
    {
        $this->setSeoTitle('权限管理');
        $this->addBreadcrumb('权限管理', $this->currentUrl);

        $this->display();
    }
    /**
     * 生成树结构option文本
     * @return string
     */
    public function getTreeOption()
    {
        $secId = isset($_GET['secId']) ? $_GET['secId'] : 0;
        //菜单列表
        $authList     = $this->sysAuthRuleModel->getAuthRuleList();
        //树结构菜单列表
        $tree          = new \App\Common\Tree('ruleId', 'parentId', 'child');
        $tree->nameKey = 'ruleName';
        $tree->load($authList);
        $secId && $tree->optionSelectId = $secId;
        //菜单选择列表
        $optionHtml = '<option value="0">顶级权限</option>';
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
        if (isset($this->request->header['Referer']) && strpos(strtolower($this->request->header['Referer']), '/admin/sysauthrule/index') !== false){
            $ruleList     = $this->sysAuthRuleModel->getAuthRuleList();
            if ($ruleList){
                foreach ($ruleList as $k => $v){
                    if (isset($v['isPublic']) && $v['isPublic'] == 1){
                        $v['ruleName'] .= '    <font color="#006400">(公共)</font>';
                    }
                    $ruleList[$k] = $v;
                }
            }
        }else{
            $ruleList     = $this->sysAuthRuleModel->getAuthRuleListByChoice();
        }
        //树结构用户组列表
        $tree          = new \App\Common\Tree('ruleId', 'parentId', 'children');
        $tree->nameKey = 'ruleName';
        $tree->load($ruleList);
        return $tree->makeJsTreeFormat();
    }
    /**
     * 保存用户组数据.
     */
    public function save()
    {
        try {
            $postData              = $this->request->post;
            $postData['addUserId'] = $this->user->getUid();
            $sysAuthRule               = new \App\Service\SysAuthRule();
            $rs                    = $sysAuthRule->saveData($postData);
            if ($rs) {
                return $this->showMsg('success', ($postData['ruleId'] ? '编辑' : '添加') . '成功', '/Admin/SysAuthRule/index');
            }
            throw new \Exception(($postData['menuId'] ? '编辑' : '添加') . '权限规则失败');
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
            $data = $this->sysAuthRuleModel->getone(['ruleId'=>$id]);

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
            $ruleId = $this->request->post['id'] ?? 0;
            $parentId = $this->request->post['parent'] ?? 0;
            is_numeric($parentId) || $parentId = 0;
            $orderNum = $this->request->post['position'] ?? 0;
            if (empty($ruleId)) {
                throw new \Exception('请选择要移动的对象');
            }
            $sysAuthRuleSer = new \App\Service\SysAuthRule();
            $rs = $sysAuthRuleSer->setOrderNum($parentId, $ruleId, $orderNum);
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
            $ids   = $this->request->post['ids'] ?? 0;
            if (!$ids){
                throw new \Exception('请指定要删除的规则');
            }
            $rs = $this->sysAuthRuleModel->sets(['isDel'=>1], [
                'in' => ['ruleId', $ids]
            ]);
            if ($rs){
                return $this->showMsg('success', '删除成功');
            }
            throw new \Exception('删除失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
}
