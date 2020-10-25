<?php

namespace app\admin\controller;

use think\Loader;
use think\Url;
use think\Request;
use think\Session;

/**
 * Description of Menu
 * 后台权限配置控制器
 * @author static7
 */
class Menu extends Admin {
    /**
     * 菜单首页管理
     * @param int $pid 父级ID
     * @author staitc7 <static7@qq.com>
     */

    public function index($pid = 0) {
        $Menu = Loader::model('Menu');
        $data = $Menu->menuList((int) $pid);
        $father = $Menu->father($pid); //查询父级ID
        $value = [
            'father' => $father ?? null,
            'data' => $data['data'] ?? null,
            'pid' => $pid,
            'page' => $data['page']
        ];
        $this->view->metaTitle = '菜单列表';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 菜单详情
     * @param int $id 菜单ID
     * @author staitc7 <static7@qq.com>
     */

    public function edit($id = 0) {
        $Menu = Loader::model('Menu');
        if ((int) $id > 0) {
            $info = $Menu->edit((int) $id);
            $value['info'] = $info;
        }
        $menu_list_all = $Menu->menuListAll(); //获取所有的菜单
        $value['menus'] = $this->menuTree($menu_list_all) ?? null;
        $this->view->metaTitle = '菜单详情';
        return $this->view->assign($value ?? null)->fetch();
    }

    /**
     * 添加菜单
     * @param int $pid 菜单ID
     * @author staitc7 <static7@qq.com>
     */

    public function add($pid = 0) {
        $menu_list_all = Loader::model('Menu')->menuListAll(); //获取所有的菜单
        $value = [
            'menus' => $this->menuTree($menu_list_all) ?? null,
            'pid' => $pid
        ];
        $this->view->metaTitle = '添加菜单';
        return $this->view->assign($value ?? null)->fetch();
    }

    /**
     * 菜单转化成菜单树
     * @param array $list 菜单列表
     * @author staitc7 <static7@qq.com>
     */

    private function menuTree($list = null) {
        if (empty($list)) {
            return null;
        }
        $Menu = Loader::model('Tree', 'logic');
        $menu_list = $Menu->toFormatTree($list);
        return $menu_list;
    }

    /**
     * 用户更新或者添加菜单
     * @author staitc7 <static7@qq.com>
     */

    public function renew() {
        $Menu = Loader::model('Menu');
        $info = $Menu->renew();
        if (is_array($info)) {
            Session::clear('menu');
            return $this->success('操作成功', Url::build('Menu/index', ['pid' => $info['pid'] ?? 0]));
        } else {
            return $this->error($info);
        }
    }

    /**
     * 公用的更新方法
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */

    public function toogle($ids = null, $value = null, $field = 'hide') {
        empty($ids) && $this->error('请选择要操作的数据');
        !is_numeric((int) $value) && $this->error('参数错误');
        $key = ((string) $field == 'hide') ? 'hide' : 'is_dev';
        $info = Loader::model('Menu')->setStatus(['id' => ['in', $ids]], [$key => $value]);
        if ($info !== FALSE) {
            Session::clear('menu');
            return $this->success($value == -1 ? '删除成功' : '更新成功');
        } else {
            return $this->error($value == -1 ? '删除失败' : '更新失败');
        }
    }

    /**
     * 导入菜单
     * @param int $pid 父级导航
     * @author staitc7 <static7@qq.com>
     */

    public function import($pid = 0) {
        $value = [
            'pid' => (int) $pid,
        ];
        return $this->view->assign($value)->fetch();
    }

    /**
     * 菜单导入
     * @param int $pid 父级导航
     * @author staitc7 <static7@qq.com>
     */

    public function importMenu($pid = 0) {
        (int) $pid < 0 && $this->error('参数错误');
        $Request = Request::instance();
        $Request->isAjax() || $this->error('非法请求');
        $tree = $Request->post('tree');
        empty($tree) && $this->error('菜单不能为空');
        $lists = explode(PHP_EOL, $tree);
        $info = Loader::model('Menu')->menuImport($lists, $pid);
        if ($info) {
            Session::clear('menu');
            return $this->success('导入成功', Url::build('Menu/index', ['pid' => $pid]));
        } else {
            return $this->error('导入失败');
        }
    }

    /**
     * 返回后台节点数据
     * @param boolean $tree    是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    final public function returnNodes($tree = true) {
        static $tree_nodes = [];
        if ($tree && !empty($tree_nodes[(int) $tree])) {
            return $tree_nodes[$tree];
        }
        $Menu = Loader::model('menu');
        $model_name = Request::instance()->module(); //当前模块名称
        if ($tree) {
            $list = $Menu->menuField('id,pid,title,url,tip,hide');
            foreach ($list as $key => $value) {
                if (stripos($value['url'], $model_name) !== 0) {
                    $list[$key]['url'] = $model_name . '/' . $value['url'];
                }
            }
            $nodes = list_to_tree($list, 'id', 'pid', 'operator', 0);
            foreach ($nodes as $key => $value) {
                if (!empty($value['operator'])) {
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        } else {
            $nodes = $Menu->menuField('title,url,tip,pid');
            foreach ($nodes as $key => $value) {
                if (stripos($value['url'], $model_name) !== 0) {
                    $nodes[$key]['url'] = $model_name . '/' . $value['url'];
                }
            }
        }
        $tree_nodes[(int) $tree] = $nodes;
        return $nodes;
    }

}
