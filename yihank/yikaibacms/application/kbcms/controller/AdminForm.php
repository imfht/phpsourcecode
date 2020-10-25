<?php
namespace app\kbcms\controller;
use app\admin\controller\Admin;
/**
 * 表单管理
 */
class AdminForm extends Admin
{
    /**
     * 当前模块参数
     */
    public function _infoModule()
    {
        $data = array('info' => array('name' => '表单管理',
                'description' => '管理网站自定义表单',
                ),
            'menu' => array(
                array('name' => '表单列表',
                    'url' => url('index'),
                    'icon' => 'list',
                    ),
                ),
            'add' => array(
                array('name' => '添加表单',
                    'url' => url('add'),
                    ),
                ),
                
            );
        return $data;
    }

    /**
     * 列表
     */
    public function index()
    {
        $breadCrumb = array('表单列表' => url());
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('list', model('FieldsetForm')->loadList());
        return $this->fetch();
    }

    /**
     * 增加
     */
    public function add(){
        if (input('post.')){
            $validate=validate('FieldsetForm');
            if(!$validate->scene('add')->check(input('post.'))){
                $this->error($validate->getError());
            }
            $model = model('FieldsetForm');
            if ($model->add()){
                $this->success('表单添加成功！');
            }
            else{
                $this->error('表单添加失败');
            }
        }else{
            $breadCrumb = array('表单列表' => url('index'), '表单添加' => url());
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '添加');
            //$this->assign('tplList',model('admin/Config')->tplList());
            return $this->fetch();
        }
    }

    /**
     * 修改
     */
    public function edit()
    {
        $model = model('FieldsetForm');
        if (input('post.')){
            if ($model->edit()){
                $this->success('表单修改成功！');
            }
            else{
                $this->error('表单修改失败');
            }
        }else{
            $fieldsetId = input('fieldset_id');
            if (empty($fieldsetId)){
                $this->error('参数不能为空！');
            }
            $info = $model->getInfo($fieldsetId);
            $breadCrumb = array('表单列表' => url('index'), '表单修改' => url('edit', array('fieldset_id' => $fieldsetId)));
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '修改');
            $this->assign('info', $info);
            //$this->assign('tplList',model('admin/Config')->tplList());
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $fieldsetId = input('post.id');
        if (empty($fieldsetId)){
            $this->error('参数不能为空！');
        }
        $validate=validate('FieldsetForm');
        if(!$validate->scene('del')->check(input('post.'))){
            $this->error($validate->getError());
        }
        // 删除操作
        $model = model('FieldsetForm');
        if ($model->del($fieldsetId)){
            $this->success('表单删除成功！');
        }
        else{
            $this->error('表单删除失败！');
        }
    }
}

