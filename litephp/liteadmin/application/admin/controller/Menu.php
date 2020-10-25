<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/8
 * Time: 15:02
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\model\SystemMenu;
use LiteAdmin\Tree;
use think\db\Where;

/**
 * @title 菜单管理
 * Class Menu
 * @package app\admin\controller
 */
class Menu extends BaseAdmin
{
    /**
     * @title 列表页
     * @return mixed
     */
    public function index()
    {
        $db = SystemMenu::order('sort', 'asc')
            ->order('id', 'asc');
        return $this->_list($db, false);
    }

    protected function _index_list_before(&$list)
    {
        foreach ($list as $key => $value){
            $list[$key] = $value->toArray();
        }
        // 匿名函数 整理子菜单IDs 用于删除时全部子菜单同时删除
        $func = function (&$tree) use (&$func) {
            $aids = [];
            foreach ($tree as &$item) {
                $ids = [];
                $ids[] = $item['id'];
                if (isset($item['_child'])) {
                    $ids = array_merge($ids, $func($item['_child']));
                }
                $aids = array_merge($aids, $ids);
                $item['son_ids'] = implode(',', $ids);
            }
            return $aids;
        };
        $tree = Tree::array2tree($list);
        $func($tree);
        $list =  Tree::tree2list($tree);
    }

    /**
     * @title 添加
     * @return array|mixed
     */
    public function add()
    {
        return $this->_form(new SystemMenu(), 'form');
    }

    /**
     * @title 修改
     * @return array|mixed
     */
    public function edit()
    {
        return $this->_form(new SystemMenu(), 'form');
    }

    protected function _form_before($data)
    {
        if ($this->request->isGet()) {
            $id = $this->request->route('id', false);
            $map = [];
            $id && $map['id'] = ['<>',$id];
            $parents = SystemMenu::where(new Where($map))
                ->order('sort asc,id asc')
                ->select();
            foreach ($parents as $key => $value){
                $parents[$key] = $value->toArray();
            }
            $parents = Tree::array2list($parents, 'id', 'pid', '_child');
            $pid = $this->request->route('pid', false);
            if ($pid){
                $this->assign('pid', $pid);
            }
            $this->assign('parents', $parents);
        }
    }

    /**
     * @title 删除
     * @throws \Exception
     */
    public function del()
    {
        $ids = $this->request->post('ids');
        $res = SystemMenu::whereIn('id', $ids)->delete();
        if ($res) {
            $this->success('删除成功！', '');
        } else {
            $this->error("删除失败");
        }
    }
}