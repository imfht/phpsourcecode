<?php
namespace app\kbcms\controller;
use app\admin\controller\Admin;
/**
 * 表单内容管理
 */
class AdminFormData extends Admin {

    /**
     * 当前模块参数
     */
    public function _infoModule()
    {
        $fieldsetId = input('fieldset_id');
        if (empty($fieldsetId)){
            $this->error('参数不能为空！');
        }
        $this->formInfo = model('kbcms/FieldsetForm')->getInfo($fieldsetId);
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
            '_info' => array(
                array('name' => '添加内容',
                    'url' => url('info',array('fieldset_id' => $this->formInfo['fieldset_id'])),
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
    public function index(){
        //筛选条件
        $keyword = input('post.keyword','');
        //字段列表
        $where = array();
        $where['A.fieldset_id'] = $this->formInfo['fieldset_id'];
        $fieldList = model('FieldForm')->loadList($where);
        $tableTh = array();
        $searchWhere = array();
        if(!empty($fieldList)){
            foreach ($fieldList as $key => $value) {
                if($value['show']=='1'){
                    $tableTh[] = $value['name'];
                }
                if($value['search']&&!empty($keyword)){
                    $searchWhere[$value['field']] = $keyword;
                }
            }
        }
        //设置模型
        $model = model('kbcms/FieldData');
        $model->setTable(config('database.prefix').'ext_'.$this->formInfo['table']);
        //查询数据
        $limit=0;
        $list = $model->loadList($searchWhere,$limit,$this->formInfo['list_order']);
        //URL参数
        $pageMaps = array();
        $pageMaps['fieldset_id'] = $this->formInfo['fieldset_id'];
        $pageMaps['keyword'] = $keyword;
        //面包屑
        $breadCrumb = array($this->formInfo['name'].'列表' => url('index',array('fieldset_id' => $this->formInfo['fieldset_id'])));
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('fieldList',  $fieldList);
        $this->assign('list',  $list);
        $this->assign('formInfo', $this->formInfo);
        $this->assign('tableTh', $tableTh);
        $this->assign('keyword',$keyword);
        $this->assign('url', url('index',array('fieldset_id' => $this->formInfo['fieldset_id'])));
        return $this->fetch();
    }
    /**
     * 详细
     */
    public function info(){
        //设置模型
        $model = model('kbcms/FieldData');
        $model->setTable(config('database.prefix').'ext_'.$this->formInfo['table']);
        $fieldsetId=input('fieldset_id');
        $dataId = input('data_id');
        if (input('post.')){
            if ($dataId){
                $status=$model->saveData('edit',$_POST['fieldset_id']);
            }else{
                $status=$model->saveData('add',input('post.fieldset_id'));
            }
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index',array('fieldset_id'=>$fieldsetId)));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $info = $model->getInfo($dataId);
            //字段列表
            $where = array();
            $where['A.fieldset_id'] = $this->formInfo['fieldset_id'];
            $fieldList = model('FieldForm')->loadList($where);
            //获取HTML
            $html='';
            foreach ($fieldList as $value) {
                $html .= model('Field')->htmlFieldFull($value,$info[$value['field']]);
            }
            if(empty($html)){
                return ajaxReturn(0,'请先添加字段！');
            }
            $this->assign('info', $info);
            //$this->assign('tplList',model('admin/Config')->tplList());
            $this->assign('formInfo', $this->formInfo);
            $this->assign('html', $html);
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $dataId = input('id');
        if (empty($dataId))
        {
            $this->error('参数不能为空！');
        }
        //设置模型
        $model = model('kbcms/FieldData');
        $model->setTable(config('database.prefix').'ext_'.$this->formInfo['table']);
        // 删除操作
        if ($model->del($dataId)){
            return ajaxReturn(200,'删除成功！');
        }
        else{
            return ajaxReturn(0,'删除失败');
        }
    }
}

