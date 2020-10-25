<?php

namespace app\admin\controller\system;

use app\admin\controller\BaseController;
use think\Db;

/**
 * 菜单规则管理
 * Class RuleController
 * @package app\admin\controller\auth
 */
class ConfigController extends BaseController
{
    public function initialize()
    {
        parent::initialize();
        $this->assign('group', config('param.config_group_list'));
        $this->assign('type', config('param.config_type_list'));
    }

    /**
     * 主页面
     * @return mixed
     */
    public function index()
    {
        return view();
    }

    /**
     * 列表
     * @return mixed
     */
    public function lists()
    {
        $group   = $this->request->post('group', '', 'trim');
        $keyword = $this->request->post('keyword', '', 'trim');
        $map     = [];
        if ($group) {
            $map['group'] = $group;
        }
        if ($keyword) {
            $map['group'] = ['like', '%' . $keyword . '%'];
        }
        $list = Db::name('config')->where($map)->order('sort asc')->select();
        $this->assign('list', $list);
        return view();
    }

    //配置
    public function group()
    {
        if ($this->request->isPost()) {
            $config = $this->request->post('config/a');
            if ($config) {
                foreach ($config as $name => $value) {
                    Db::name('config')->where(['name' => $name])->setField('value', $value);
                }
            }
            cache('sys:cache:config', null);
            $this->success('保存成功！');
        } else {
            //所有配置项
            $group = config('param.config_group_list');
            $list  = [];
            foreach ($group as $key => $val) {
                $temp         = [];
                $temp['id']   = $key;
                $temp['name'] = $val;
                $temp['list'] = Db::name('config')->where(['lock' => 0, 'group' => $key])->select();
                $list[]       = $temp;
            }

            $this->assign('list', $list);
            $this->assign('group', $group);
            $this->assign('type', config('param.config_type_list'));
            return view();
        }

    }

    /**
     * 添加页面和添加操作
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (Db::name('config')->insert($post) !== false) {
                cache('sys:cache:config', null);
                $this->success('操作成功');
            }
            $this->error('添加失败');
        } else {
            return view();
        }
    }

    /**
     * 修改页面和修改操作
     * @return mixed
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (Db::name('config')->update($post) !== false) {
                cache('sys:cache:config', null);
                $this->success('修改成功');
            }
            $this->error('修改失败');
        } else {
            $id = $this->request->get('id', 0, 'intval');
            if (!$id) {
                exit('参数错误');
            }
            $info = Db::name('config')->where('id', $id)->find();
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

        if ($id > 0 && Db::name('config')->where(['id' => $id])->setField('sort', $sort) !== false) {
            $this->success('设置成功');
        }
        $this->error('更新失败');
    }

    /**
     * 快速锁定
     * @return json
     */
    public function lock()
    {
        $id   = $this->request->get('id', 0, 'intval');
        $lock = $this->request->get('lock', 0, 'intval');

        if ($id > 0 && Db::name('config')->where(['id' => $id])->setField('lock', $lock) !== false) {
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
        if (Db::name('config')->delete($id) !== false) {
            cache('sys:cache:config', null);
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

}