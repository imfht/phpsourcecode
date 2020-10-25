<?php
namespace app\duxcms\controller;
use app\admin\controller\AdminController;
/**
 * 扩展模型管理
 */
class AdminExpandController extends AdminController
{
    /**
     * 当前模块参数
     */
    public function _infoModule()
    {
        $data = array('info' => array('name' => '扩展模型管理',
                'description' => '管理网站内容所绑定的扩展模型',
                ),
            'menu' => array(
                array('name' => '模型列表',
                    'url' => url('index'),
                    'icon' => 'list',
                    ),
                
                ),
            'add' => array(
                array('name' => '添加模型',
                    'url' => url('add'),
                    ),
                )
            );
        return $data;
    }

    /**
     * 列表
     */
    public function index()
    {
        $breadCrumb = array('扩展模型列表' => url());
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('list', target('FieldsetExpand')->loadList());
        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add()
    {
        if (!IS_POST)
        {
            $breadCrumb = array('扩展模型列表' => url('index'), '扩展模型添加' => url());
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '添加');
            $this->adminDisplay('info');
        }
        else
        {
            $model = target('FieldsetExpand');
            if ($model->saveData('add'))
            {
                $this->success('扩展模型添加成功！',url('index'));
            }
            else
            {
                $msg = $model->getError();
                if (empty($msg))
                {
                    $this->error('扩展模型添加失败');
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
        $model = target('FieldsetExpand');
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
            $breadCrumb = array('扩展模型列表' => url('index'), '扩展模型修改' => url('edit', array('fieldset_id' => $fieldsetId)));
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '修改');
            $this->assign('info', $info);
            $this->adminDisplay('info');
        }
        else
        {
            if ($model->saveData('edit'))
            {
                $this->success('扩展模型修改成功！',url('index'));
            }
            else
            {
                $msg = $model->getError();
                if (empty($msg))
                {
                    $this->error('扩展模型修改失败');
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
        $model = target('FieldsetExpand');
        if ($model->delData($fieldsetId))
        {
            $this->success('扩展模型删除成功！');
        }
        else
        {
            $msg = $model->getError();
            if (empty($msg))
            {
                $this->error('扩展模型删除失败！');
            }
            else
            {
                $this->error($msg);
            }
        }
    }

    /**
     * 获取扩展模型字段
     * @param int $FieldsetId ID
     * @return bool 删除状态
     */
    public function getField()
    {
        $classId = request('post.class_id',0,'intval');
        $contentId = request('post.content_id',0,'intval');

        //获取字段集信息
        $fieldsetInfo = target('duxcms/Fieldset')->getInfoClassId($classId);
        if(empty($fieldsetInfo)){
            return;
        }
        //获取字段列表
        $where = array();
        $where['A.fieldset_id'] = $fieldsetInfo['fieldset_id'];
        $fieldList=target('duxcms/FieldExpand')->loadList($where);
        if(empty($fieldList)||!is_array($fieldList)){
            return;
        }
        //获取扩展内容信息
        if(!empty($contentId)){
            $model = target('duxcms/FieldData');
            $model->setTable('ext_'.$fieldsetInfo['table']);
            $contentInfo=$model->getInfo($contentId);
        }
        $html='';
        $fieldModel = target('duxcms/Field');
        foreach ($fieldList as $value) {
            $html .= $fieldModel->htmlFieldFull($value,$contentInfo[$value['field']]);
        }
        $this->show($html);

    }

    /**
     * 获取字段渲染
     * @param int $FieldsetId ID
     * @return bool 删除状态
     */
    public function getHtmlField($value,$data,$model = 'duxcms/Field')
    {
        $html = target('duxcms/Field')->htmlFieldFull($value,$data,$model);
        $this->show($html);
    }


    
}

