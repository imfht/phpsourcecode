<?php
namespace App\Controller\Admin;
use App\BaseController\AdminBaseController as Base;
use App\Service\WxMenu as WxMenuService;

/**
 * 微信自定义菜单相关操作.
 */
class WxMenu extends Base
{
    /**
     * 菜单模型
     * @var \App\Model\WxMenu
     */
    private $wxMenuModel;
    /**
     * @var \App\Service\WxMenu
     */
    private $wxMenuSer;
    /**
     * 构造函数
     * @param \Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->addBreadcrumb('菜单管理', '/Admin/WxMenu/index');
        $this->wxMenuModel = model('WxMenu');
        $this->wxMenuSer = new WxMenuService();
    }

    /**
     * 菜单首页
     */
    public function index()
    {
        $this->setSeoTitle('菜单管理');
        $this->addBreadcrumb('菜单管理', $this->currentUrl);
        //微信菜单类别
        $this->assign('menuTypeList', $this->wxMenuSer->getMenuTypeList());
        $this->assign('clientPlatformTypeList', $this->wxMenuSer->getClientPlatformTypeList());
        $this->assign('languageList', $this->wxMenuSer->getLanguageList());
        //菜单列表
        $menuList     = $this->wxMenuModel->getMenuList();
        foreach ($menuList as $k => $menuData){
            if ($menuData['isConditional'] == 1){
                $menuData['menuName'] = $menuData['menuName'] . '<font color="red">(个性化)</font>';
            }
            $menuList[$k] = $menuData;
        }
        //树结构菜单列表
        $tree          = new \App\Common\Tree('menuId', 'parentId', 'child');
        $tree->nameKey = 'menuName';
        $tree->load($menuList);
        //可嵌套列表
        $addHtml = '<button type="button" class="btn btn-outline btn-danger btn-xs pull-right del"><i class="fa fa-trash-o"></i>删除</button>';
        $addHtml .= '<button type="button" class="btn btn-outline btn-primary btn-xs pull-right edit" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil"></i>编辑</button>';
        $nestableHtml = $tree->buildNestableTree($addHtml);
        $this->assign('nestableHtml', $nestableHtml);
        $this->display();
    }

    /**
     * 生成树结构option文本
     */
    public function getTreeOption()
    {
        $secId = isset($_GET['secId']) ? $_GET['secId'] : 0;
        //菜单列表
        $menuList     = $this->wxMenuModel->gets([
            'select' => 'menuId,wxMenuId,menuName,isConditional,parentId',
            'where' => "`isDel`=0 AND `parentId`=0",
            'order' => "orderNum ASC,menuId ASC",
        ]);
        //树结构菜单列表
        $tree          = new \App\Common\Tree('menuId', 'parentId', 'child');
        $tree->nameKey = 'menuName';
        $tree->load($menuList);
        $secId && $tree->optionSelectId = $secId;
        //菜单选择列表
        $optionHtml = '<option value="0">顶级菜单</option>';
        $optionHtml .= $tree->buildOptions();
        $this->http->finish($optionHtml);
    }

    /**
     * 同步线上菜单到本地
     * @return bool
     */
    public function syncOnline()
    {
        try {
            $rs = $this->wxMenuSer->syncOnline();
            if ($rs) {
                return $this->showMsg('success', '同步成功', '/Admin/WxMenu/index');
            }
            throw new \Exception('同步失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 推送本地菜单到线上
     * @return bool
     */
    public function pushOnline()
    {
        try {
            $rs = $this->wxMenuSer->pushOnline();
            if ($rs) {
                return $this->showMsg('success', '同步成功', '/Admin/WxMenu/index');
            }
            throw new \Exception('同步失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
    /**
     * 获取菜单数据
     * @return bool
     */
    public function get()
    {
        try {
            $id   = $this->request->get['menuId'] ?? 0;
            $postData = $this->wxMenuModel->getone(['menuId'=>$id]);

            return $this->showMsg('success', '获取成功', '', $postData);
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 保存菜单
     * @return bool
     */
    public function save()
    {
        try {
            $postData              = $this->request->post;
            $postData['addUserId'] = $this->user->getUid();
            $wxMenu               = new \App\Service\WxMenu();
            $rs                    = $wxMenu->saveMenu($postData);
            if ($rs) {
                return $this->showMsg('success', ($postData['menuId'] ? '编辑' : '添加') . '成功', '/Admin/WxMenu/index');
            }
            throw new \Exception(($postData['menuId'] ? '编辑' : '添加') . '菜单失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 保存菜单排序
     * @return bool
     */
    public function saveSort()
    {
        try {
            $sortData = $this->request->post['sortData'];
            if (empty($sortData)) {
                throw new \Exception('排序数据有误');
            }
            $wxMenu = new \App\Service\WxMenu();
            $rs      = $wxMenu->saveSort($sortData);
            if ($rs) {
                return $this->showMsg('success', '保存排序成功');
            }
            throw new \Exception('保存排序失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 删除菜单
     * @return bool
     */
    public function del()
    {
        try {
            $id   = $this->request->post['menuId'] ?? 0;
            if (!$id){
                throw new \Exception('请指定要删除的菜单');
            }
            $rs = $this->wxMenuModel->set($id, ['isDel'=>1]);
            if ($rs){
                return $this->showMsg('success', '删除成功');
            }
            throw new \Exception('删除失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
}