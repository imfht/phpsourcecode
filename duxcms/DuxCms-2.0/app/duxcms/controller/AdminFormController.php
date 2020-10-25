<?php
namespace app\duxcms\controller;
use app\admin\controller\AdminController;
/**
 * 表单管理
 */
class AdminFormController extends AdminController
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
        $this->assign('list', target('FieldsetForm')->loadList());
        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add()
    {
        if (!IS_POST)
        {
            $breadCrumb = array('表单列表' => url('index'), '表单添加' => url());
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '添加');
            $this->assign('tplList',target('admin/Config')->tplList());
            $this->adminDisplay('info');
        }
        else
        {
            $model = target('FieldsetForm');
            if ($model->saveData('add'))
            {
                $this->success('表单添加成功！',url('index'));
            }
            else
            {
                $msg = $model->getError();
                if (empty($msg))
                {
                    $this->error('表单添加失败');
                }
                else
                {
                    $this->error($msg);
                }
            }
        }
    }

    /**
     * 修改
     */
    public function edit()
    {
        $model = target('FieldsetForm');
        if (!IS_POST)
        {
            $fieldsetId = request('get.fieldset_id', '', 'intval');
            if (empty($fieldsetId))
            {
                $this->error('参数不能为空！');
            }
            $info = $model->getInfo($fieldsetId);
            if (!$info)
            {
                $this->error($model->getError());
            }
            $breadCrumb = array('表单列表' => url('index'), '表单修改' => url('edit', array('fieldset_id' => $fieldsetId)));
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '修改');
            $this->assign('info', $info);
            $this->assign('tplList',target('admin/Config')->tplList());
            $this->adminDisplay('info');
        }
        else
        {
            if ($model->saveData('edit'))
            {
                $this->success('表单修改成功！',url('index'));
            }
            else
            {
                $msg = $model->getError();
                if (empty($msg))
                {
                    $this->error('表单修改失败');
                }
                else
                {
                    $this->error($msg);
                }
            }
        }
    }

    /**
     * 删除
     */
    public function del()
    {
        $fieldsetId = request('post.data');
        if (empty($fieldsetId))
        {
            $this->error('参数不能为空！');
        } 
        // 删除操作
        $model = target('FieldsetForm');
        if ($model->delData($fieldsetId))
        {
            $this->success('表单删除成功！');
        }
        else
        {
            $msg = $model->getError();
            if (empty($msg))
            {
                $this->error('表单删除失败！');
            }
            else
            {
                $this->error($msg);
            }
        }
    }
}

