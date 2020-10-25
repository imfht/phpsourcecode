<?php
namespace app\kbcms\controller;
use app\admin\controller\Admin;
/**
 * 扩展表单字段管理
 */
class AdminFormField extends Admin
{
    /**
     * 当前模块参数
     */
    public function _infoModule(){
        $fieldsetId = input('fieldset_id');
        $data = array(
            'info' => array('name' => '表单管理',
                    'description' => '管理网站自定义表单',
                ),
            'menu' => array(
                array('name' => '表单列表',
                    'url' => url('AdminForm/index'),
                    'icon' => 'list',
                    ),
                array('name' => '字段列表',
                    'url' => url('index', array('fieldset_id' => $fieldsetId)),
                    'icon' => 'list-ul',
                    ),
                ),
            'add' => array(
                array('name' => '增加字段',
                    'url' => url('add', array('fieldset_id' => $fieldsetId)),
                    'icon' => 'plus',
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
        $fieldsetId = input('fieldset_id');
        if (empty($fieldsetId)){
            $this->error('参数不能为空！');
        }
        $model = model('FieldsetForm');
        $fieldsetInfo = $model->getInfo($fieldsetId);
        $where = array();
        $where['A.fieldset_id'] = $fieldsetId;
        $list = model('FieldForm')->loadList($where);
        $breadCrumb = array('表单列表' => url('AdminForm/index'), '字段列表' => url('index', array('fieldset_id' => $fieldsetId)));
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('list', $list);
        $this->assign('fieldsetInfo', $fieldsetInfo);
        $this->assign('typeField', model('Field')->typeField());
        return $this->fetch();
    }

    /**
     * 增加
     */
    public function add(){
        if (input('post.')){
            $validate=validate('FieldExpand');
            if(!$validate->scene('add')->check(input('post.'))){
                $this->error($validate->getError());
            }
            $model = model('FieldForm');
            if ($model->add()){
                $this->success('字段添加成功！');
            }
            else{
                $this->error('字段添加失败');
            }
        }else{
            $fieldsetId = input('fieldset_id');
            if (empty($fieldsetId)){
                $this->error('参数不能为空！');
            }
            $model = model('FieldsetForm');
            $fieldsetInfo = $model->getInfo($fieldsetId);
            if (!$fieldsetInfo){
                $this->error($model->getError());
            }
            $breadCrumb = array('表单列表' => url('AdminForm/index'), '字段列表' => url('index', array('fieldset_id' => $fieldsetId)), '字段添加' => url());
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '添加');
            $this->assign('fieldsetInfo', $fieldsetInfo);
            $this->assign('typeField', model('Field')->typeField());
            $this->assign('propertyField', model('Field')->propertyField());
            $this->assign('typeVerify', model('Field')->typeVerify());
            $this->assign('ruleVerify', model('Field')->ruleVerify());
            $this->assign('ruleVerifyJs', model('Field')->ruleVerifyJs());
            return $this->fetch();
        }
    }

    /**
     * 修改
     */
    public function edit(){
        $model = model('FieldForm');
        if (input('post.')){
            $validate=validate('FieldExpand');
            if(!$validate->scene('edit')->check(input('post.'))){
                $this->error($validate->getError());
            }
            if ($model->edit()){
                $this->success('字段修改成功！');
            }
            else{
                $this->error('字段修改失败');
            }
        }else{
            $fieldId = input('field_id');
            if (empty($fieldId)){
                $this->error('参数不能为空！');
            }
            $info = $model->getInfo($fieldId);
            $fieldsetInfo = model('FieldsetForm')->getInfo($info['fieldset_id']);
            $breadCrumb = array('表单列表' => url('AdminForm/index'), '字段列表' => url('index', array('fieldset_id' => $fieldsetInfo['fieldset_id'])), '字段修改' => url('edit',array('field_id'=>$fieldId,'fieldset_id'=>$fieldsetInfo['fieldset_id'])));
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '修改');
            $this->assign('info', $info);
            $this->assign('fieldsetInfo', $fieldsetInfo);
            $this->assign('typeField', model('Field')->typeField());
            $this->assign('propertyField', model('Field')->propertyField());
            $this->assign('typeVerify', model('Field')->typeVerify());
            $this->assign('ruleVerify', model('Field')->ruleVerify());
            $this->assign('ruleVerifyJs', model('Field')->ruleVerifyJs());
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $fieldId = input('post.id');
        if (empty($fieldId)){
            $this->error('参数不能为空！');
        } 
        // 删除操作
        $model = model('FieldForm');
        if ($model->del($fieldId)){
            $this->success('字段删除成功！');
        }
        else{
            $this->error('字段删除失败！');
        }
    }
}

