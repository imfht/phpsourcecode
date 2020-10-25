<?php
namespace app\duxcms\controller;
use app\admin\controller\AdminController;
/**
 * 表单内容管理
 */
class AdminFormDataController extends AdminController {

    /**
     * 当前模块参数
     */
    public function _infoModule()
    {
        $fieldsetId = request('request.fieldset_id', 0, 'intval');
        if (empty($fieldsetId))
        {
            $this->error('参数不能为空！');
        }
        $this->formInfo = target('duxcms/FieldsetForm')->getInfo($fieldsetId);
        $data = array(
            'info' => array(
                'name' => $this->formInfo['name'].'管理',
                'description' => '管理'.$this->formInfo['name'].'内容',
                ),
            'menu' => array(
                array('name' => '内容列表',
                    'url' => url('index',array('fieldset_id' => $this->formInfo['fieldset_id'])),
                    'icon' => 'list',
                    ),
                ),
            'add' => array(
                array('name' => '添加内容',
                    'url' => url('add',array('fieldset_id' => $this->formInfo['fieldset_id'])),
                    ),
                ),
            'cutNav' => array(
                    'url' => url('index',array('fieldset_id' => $this->formInfo['fieldset_id'])),
                    'complete' =>false,
                )
            );
        return $data;
    }

    /**
     * 列表
     */
    public function index()
    {
        //筛选条件
        $keyword = request('request.keyword','');
        //字段列表
        $where = array();
        $where['A.fieldset_id'] = $this->formInfo['fieldset_id'];
        $fieldList = target('FieldForm')->loadList($where);
        $tableTh = array();
        $searchWhere = array();
        if(!empty($fieldList)){
            foreach ($fieldList as $key => $value) {
                if($value['show']){
                    $tableTh[] = $value['name'];
                }
                if($value['search']&&!empty($keyword)){
                    $searchWhere[$value['field']] = $keyword;
                }
            }
        }
        //设置模型
        $model = target('duxcms/FieldData');
        $model->setTable('ext_'.$this->formInfo['table']);
        //查询数据
        $list = $model->page(20)->loadList($searchWhere,$limit,$this->formInfo['list_order']);
        $this->pager = $model->pager;
        //URL参数
        $pageMaps = array();
        $pageMaps['fieldset_id'] = $this->formInfo['fieldset_id'];
        $pageMaps['keyword'] = $keyword;
        //面包屑
        $breadCrumb = array($this->formInfo['name'].'列表' => url('index',array('fieldset_id' => $this->formInfo['fieldset_id'])));
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('fieldList',  $fieldList);
        $this->assign('list',  $list);
        $this->assign('page',$this->getPageShow($pageMaps));
        $this->assign('formInfo', $this->formInfo);
        $this->assign('tableTh', $tableTh);
        $this->assign('keyword',$keyword);
        $this->assign('url', url('index',array('fieldset_id' => $this->formInfo['fieldset_id'])));
        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add()
    {
        //设置模型
        $model = target('duxcms/FieldData');
        $model->setTable('ext_'.$this->formInfo['table']);
        if (!IS_POST)
        {
            //字段列表
            $where = array();
            $where['A.fieldset_id'] = $this->formInfo['fieldset_id'];
            $fieldList = target('FieldForm')->loadList($where);
            //获取HTML
            $html='';
            foreach ($fieldList as $value) {
                $html .= target('Field')->htmlFieldFull($value);
            }
            if(empty($html)){
                $this->error('请先添加字段！');
            }
            ob_start();
            $this->show($html);
            $html = ob_get_clean();
            //面包屑
            $breadCrumb = array($this->formInfo['name'].'列表' => url('index',array('fieldset_id' => $this->formInfo['fieldset_id'])), '字段列表' => url('index', array('fieldset_id' => $fieldsetId)));
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '添加');
            $this->assign('formInfo', $this->formInfo);
            $this->assign('html', $html);

            $this->adminDisplay('info');
        }else{
            if ($model->saveData('add',$_POST['fieldset_id'])){
                $this->success('表单内容添加成功！',url('index', array('fieldset_id' => request('post.fieldset_id'))));
            }else{
                $msg = $model->getError();
                if (empty($msg))
                {
                    $this->error('表单内容添加失败');
                }else{
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
        //设置模型
        $model = target('duxcms/FieldData');
        $model->setTable('ext_'.$this->formInfo['table']);
        if (!IS_POST)
        {
            $dataId = request('get.data_id', '', 'intval');
            if (empty($dataId))
            {
                $this->error('参数不能为空！');
            }
            $info = $model->getInfo($dataId);
            if (!$info)
            {
                $this->error($model->getError());
            }
            //字段列表
            $where = array();
            $where['A.fieldset_id'] = $this->formInfo['fieldset_id'];
            $fieldList = target('FieldForm')->loadList($where);
            //获取HTML
            $html='';
            foreach ($fieldList as $value) {
                $html .= target('Field')->htmlFieldFull($value,$info[$value['field']]);
            }
            if(empty($html)){
                $this->error('请先添加字段！');
            }
            ob_start();
            $this->show($html);
            $html = ob_get_clean();
            //面包屑
            $breadCrumb = array('表单列表' => url('index'), '表单修改' => url('edit', array('fieldset_id' => $fieldsetId)));
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '修改');
            $this->assign('info', $info);
            $this->assign('tplList',target('admin/Config')->tplList());
            $this->assign('formInfo', $this->formInfo);
            $this->assign('html', $html);
            $this->adminDisplay('info');
        }
        else
        {
            if ($model->saveData('edit',$_POST['fieldset_id']))
            {
                $this->success('表单修改成功！',url('index', array('fieldset_id' => request('post.fieldset_id'))));
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
        $dataId = request('post.data');
        if (empty($dataId))
        {
            $this->error('参数不能为空！');
        }
        //设置模型
        $model = target('duxcms/FieldData');
        $model->setTable('ext_'.$this->formInfo['table']);
        // 删除操作
        if ($model->delData($dataId))
        {
            $this->success('内容删除成功！');
        }
        else
        {
            $msg = $model->getError();
            if (empty($msg))
            {
                $this->error('内容删除失败！');
            }
            else
            {
                $this->error($msg);
            }
        }
    }
}

