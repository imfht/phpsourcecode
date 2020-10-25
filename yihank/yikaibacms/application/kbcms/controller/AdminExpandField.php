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
            'add' => array(
                array('name' => '增加字段',
                    'url' => url('add', array('fieldset_id' => $fieldsetId)),
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
     * 增加
     */
    public function add(){
        if (input('post.')){
            $validate=validate('FieldExpand');
            if(!$validate->scene('add')->check(input('post.'))){
                $this->error($validate->getError());
            }
            $model = model('FieldExpand');
            if ($model->add()){
                $this->success('字段添加成功！',url('index', array('fieldset_id' => input('post.fieldset_id'))));
            }
            else{
                $msg = $model->getError();
                if (empty($msg)){
                    $this->error('字段添加失败');
                }
                else{
                    $this->error($msg);
                }
            }
        }else{
            $fieldsetId = input('fieldset_id');
            if (empty($fieldsetId)){
                $this->error('参数不能为空！');
            }
            $model = model('FieldsetExpand');
            $fieldsetInfo = $model->getInfo($fieldsetId);
            if (!$fieldsetInfo){
                $this->error($model->getError());
            }
            $breadCrumb = array('模型列表' => url('AdminExpand/index'), '字段列表' => url('index', array('fieldset_id' => $fieldsetId)), '字段添加' => url());
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
        $model = model('FieldExpand');
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
            $fieldsetInfo = model('FieldsetExpand')->getInfo($info['fieldset_id']);
            $breadCrumb = array('模型列表' => url('AdminExpand/index'), '字段列表' => url('index', array('fieldset_id' => $fieldsetInfo['fieldset_id'])), '字段修改' => url('edit',array('field_id'=>$fieldId,'fieldset_id'=>$fieldsetInfo['fieldset_id'])));

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
        $model = model('FieldExpand');
        if ($model->del($fieldId)){
            $this->success('字段删除成功！');
        }
        else{
            $this->error('字段删除失败！');
        }
    }
}

