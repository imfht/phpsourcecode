<?php

namespace app\admin\controller\auth;

use app\admin\controller\BaseController;
use app\admin\model\AuthRule;
use app\common\library\Tree;
use think\Db;

/**
 * 菜单规则管理
 * Class RuleController
 * @package app\admin\controller\auth
 */
class RuleController extends BaseController
{
    /**
     * 主页面
     * @return mixed
     */
    public function index()
    {
        //获取一级菜单
        $list = AuthRule::all(['pid' => 0]);
        $this->assign('list', $list);
        return view();
    }

    /**
     * 列表
     * @return mixed
     */
    public function lists()
    {
        $pid  = $this->request->post('pid', 0, 'intval');
        $list = $this->getList();
        if ($pid > 0) {
            $cids = Tree::getChildsId($list, $pid);
            $list = Db::name('auth_rule')->where('id', 'in', $cids)->order('pid asc,sort asc')->select();
        }
        $list = Tree::unlimitForLevel($list, '├─', $pid);
        $this->assign('list', $list);
        return view();
    }

    /**
     * 添加页面和添加操作
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post     = $this->request->post();
            $validate = new \app\admin\validate\AuthRule();
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }
            if ((new AuthRule())->save($post) !== false) {
                $this->success('操作成功');
            }
            $this->error('操作失败');
        } else {
            $list = Tree::unlimitForLevel($this->getList());
            return $this->fetch('edit', ['list' => $list]);
        }
    }

    /**
     * 修改页面和修改操作
     * @return mixed
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $post            = $this->request->post();
            $post['is_menu'] = empty($post['is_menu']) ? 0 : 1;
            $validate        = new \app\admin\validate\AuthRule();
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }
            if ((new AuthRule())->isUpdate(true)->save($post) !== false) {
                $this->success('操作成功');
            }
            $this->error('操作失败');
        } else {
            $id = $this->request->get('id', 0, 'intval');
            if (!$id) {
                $this->error('参数错误');
            }
            $info = AuthRule::get($id);
            $list = Tree::unlimitForLevel($this->getList());
            $this->assign('list', $list);
            $this->assign('info', $info);
            return view();
        }
    }

    /**
     * 快速排序
     * @return json
     */
    public function sort()
    {
        $id   = $this->request->get('id', 0, 'intval');
        $sort = $this->request->post('sort', 0, 'intval');

        $rule       = AuthRule::get($id);
        $rule->sort = $sort;
        if ($id > 0 && $rule->save() !== false) {
            $this->success('设置成功');
        }
        $this->error('参数错误');
    }

    /**
     * 设置菜单
     * @return json
     */
    public function menu()
    {
        $id      = $this->request->get('id', 0, 'intval');
        $is_menu = $this->request->get('is_menu', 0, 'intval');

        if ($id > 0 && Db::name('auth_rule')->where(['id' => $id])->setField('is_menu', $is_menu) !== false) {
            $this->success('设置成功');
        }
        $this->error('更新失败');
    }

    /**
     * 删除操作
     * @return json
     */
    public function delete()
    {
        $id = $this->request->get('id', 0, 'intval');
        if ($id <= 0) {
            $this->error('参数错误');
        }
        if (AuthRule::destroy($id) !== false) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    /**
     * 获取所有规则
     * @return []
     */
    private function getList()
    {
        return Db::name('auth_rule')->order('pid asc,sort asc')->select();
    }
}
