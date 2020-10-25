<?php
namespace app\kbcms\controller;
use app\admin\controller\Admin;
/**
 * 扩展模型字段管理
 */
class AdminExpandField extends Admin
{

    /**
     * 当前模块参数
     */
    public function _infoModule(){
        $fieldsetId = input('fieldset_id');
        $data = array(
            'info' => array(
                'name' => '扩展模型管理',
                'description' => '管理网站内容所绑定的扩展模型',
            ),
            'menu' => array(
                array('name' => '模型列表',
                    'url' => url('AdminExpand/index'),
                    'icon' => 'list',
                    ),
                array('name' => '字段列表',
                    'url' => url('index', array('fieldset_id' => $fieldsetId)),
                    'icon' => 'list-ul',
                    ),

                ),
            '_info' => array(
                array('name' => '增加字段',
                    'url' => url('info', array('fieldset_id' => $fieldsetId)),
                    ),
                )
            );
        return $data;
    }

    /**
     * 列表
     */
    public function index(){
        $fieldsetId = input('fieldset_id');
        if (empty($fieldsetId))
        {
            $this->error('参数不能为空！');
        }
        $model = model('FieldsetExpand');
        $fieldsetInfo = $model->getInfo($fieldsetId);
        $where = array();
        $where['A.fieldset_id'] = $fieldsetId;
        $list = model('FieldExpand')->loadList($where);
        $breadCrumb = array('模型列表' => url('AdminExpand/index'), '字段列表' => url('index', array('fieldset_id' => $fieldsetId)));
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('list', $list);
        $this->assign('fieldsetInfo', $fieldsetInfo);
        $this->assign('typeField', model('Field')->typeField());
        return $this->fetch();
    }

    /**
     * 详细
     */
    public function info(){

        $fieldId = input('field_id');
        $fieldsetId = input('fieldset_id');
        $model = model('FieldExpand');
        $model_set=model('FieldsetExpand');
        $model_file=model('Field');
        if (input('post.')){
            if ($fieldId){
                $status=$model->edit();
            }else{
                $status=$model->add();
            }
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index',array('fieldset_id'=>$fieldsetId)));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $info = $model->getInfo($fieldId);
            $fieldsetInfo = $model_set->getInfo($fieldsetId);
            $this->assign('info', $info);
            $this->assign('fieldsetInfo', $fieldsetInfo);
            $this->assign('typeField', $model_file->typeField());
            $this->assign('propertyField', $model_file->propertyField());
            $this->assign('typeVerify', $model_file->typeVerify());
            $this->assign('ruleVerify', $model_file->ruleVerify());
            $this->assign('ruleVerifyJs', $model_file->ruleVerifyJs());
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $fieldId = input('id');
        if (empty($fieldId)){
            return ajaxReturn(0,'参数不能为空');
        } 
        // 删除操作
        $model = model('FieldExpand');
        if ($model->del($fieldId)){
            return ajaxReturn(200,'字段删除成功！');
        }else{
            return ajaxReturn(0,'字段删除失败');
        }
    }
}

