<?php
namespace app\kbcms\controller;
use app\admin\controller\Admin;
/**
 * 扩展模型管理
 */
class AdminExpand extends Admin
{
    /**
     * 当前模块参数
     */
    public function _infoModule()
    {
        $data = array(
            'info' => array(
                    'name' => '扩展模型管理',
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
    public function index(){
        $breadCrumb = array('扩展模型列表' => url());
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('list', model('FieldsetExpand')->loadList());
        return $this->fetch();
    }

    /**
     * 增加
     */
    public function add(){
        if (input('post.')){
            $validate=validate('FieldsetExpand');
            if(!$validate->check(input('post.'))){
                $this->error($validate->getError());
            }
            $model = model('FieldsetExpand');
            if ($model->add()){
                $this->success('扩展模型添加成功！');
            }
            else{
                $this->error('扩展模型添加失败');
            }
        }else{
            $breadCrumb = array('扩展模型列表' => url('index'), '扩展模型添加' => url());
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '添加');
            return $this->fetch();
        }
    }

    /**
     * 修改
     */
    public function edit(){
        $model = model('FieldsetExpand');
        if (input('post.')){
            $validate=validate('FieldsetExpand');
            if(!$validate->check(input('post.'))){
                $this->error($validate->getError());
            }
            if ($model->edit()){
                $this->success('扩展模型修改成功！',url('index'));
            }
            else{
                $this->error('扩展模型修改失败');
            }
        }else{
            $fieldsetId = input('fieldset_id');
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
        $where['A.fieldset_id'] = $fieldsetId;
        $list = model('FieldExpand')->loadList($where);
        if (!empty($list)){
            $this->error('请先删除管理字段!');
        }
        // 删除操作
        $model = model('FieldsetExpand');
        if ($model->del($fieldsetId)){
            $this->success('扩展模型删除成功！');
        }
        else{
            $this->error('扩展模型删除失败！');
        }
    }

    /**
     * 获取扩展模型字段
     * @param int $FieldsetId ID
     * @return bool 删除状态
     */
    public function getField(){
        $classId = input('post.class_id');
        $contentId = input('post.content_id');
        //获取字段集信息
        $fieldsetInfo = model('kbcms/Fieldset')->getInfoClassId($classId);
        if(empty($fieldsetInfo)){
            return;
        }
        //获取字段列表
        $where = array();
        $where['A.fieldset_id'] = $fieldsetInfo['fieldset_id'];
        $fieldList=model('kbcms/FieldExpand')->loadList($where);
        if(empty($fieldList)||!is_array($fieldList)){
            return;
        }
        //获取扩展内容信息
        if(!empty($contentId)){
            $model = model('kbcms/FieldData');
            $model->setTable(config('database.prefix').'ext_'.$fieldsetInfo['table']);
            $contentInfo=$model->getInfo($contentId);
        }
        $html='';
        $fieldModel = model('kbcms/Field');
        foreach ($fieldList as $value) {
            if (!empty($contentInfo)){
                $html .= $fieldModel->htmlFieldFull($value,$contentInfo[$value['field']]);
            }else{
                $html .= $fieldModel->htmlFieldFull($value);
            }
        }
        //var_dump($html);exit;

        return $this->display($html);

    }

    /**
     * 获取字段渲染
     * @param int $FieldsetId ID
     * @return bool 删除状态
     */
    public function getHtmlField($value,$data,$model = 'kbcms/Field'){
        $html = model('kbcms/Field')->htmlFieldFull($value,$data,$model);
        $this->show($html);
    }


    
}

