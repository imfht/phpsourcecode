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
            '_info' => array(
                array('name' => '添加表单',
                    'url' => url('info'),
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
     * 详情
     */
    public function info(){
        $model = model('FieldsetForm');
        $fieldsetId = input('fieldset_id');
        if (input('post.')){
            if ($fieldsetId){
                $status=$model->edit();
            }else{
                $status=$model->add();
            }
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $this->assign('info', $model->getInfo($fieldsetId));
            $this->assign('tplList',model('admin/Config')->tplList());
            return $this->fetch();
        }
    }
    /**
     * 删除
     */
    public function del(){
        $fieldsetId = input('id');
        if (empty($fieldsetId)){
            return ajaxReturn(0,'参数不能为空');
        }
        $validate=validate('FieldsetForm');
        if(!$validate->scene('del')->check(input(''))){
            return ajaxReturn(0,$validate->getError());
        }
        // 删除操作
        $model = model('FieldsetForm');
        if ($model->del($fieldsetId)){
            return ajaxReturn(200,'表单删除成功！');
        }
        else{
            return ajaxReturn(0,'表单删除失败');
        }
    }
}

