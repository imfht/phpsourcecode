<?php
namespace app\duxcms\controller;
use app\home\controller\SiteController;
/**
 * 表单列表
 */

class FormController extends SiteController {

	/**
     * 列表
     */
    public function index(){
        $name = urldecode(request('get.name'));
        $table = len($name,0,20);
        if(empty($table)){
            $this->error404();
        }
        //获取表单信息
        $where = array();
        $where['table'] = $table;
        $formInfo = target('duxcms/FieldsetForm')->getWhereInfo($where);
        if(empty($formInfo)){
            $this->error404();
        }
        if(!$formInfo['show_list']){
            $this->error404();
        }
        //分页参数
        $size = intval($formInfo['list_page']); 
        if (empty($size)) {
            $listRows = 20;
        } else {
            $listRows = $size;
        }
        //设置模型
        $model = target('duxcms/FieldData');
        $model->setTable('ext_'.$formInfo['table']);
        //查询数据
        $where = array();
        if(!empty($formInfo['list_where'])){
            $where[] = $formInfo['list_where'];
        }
        //查询内容
        $list = $model->page($listRows)->loadList($where,$limit,$formInfo['list_order']);
        $this->pager = $model->pager;
        //字段列表
        $where = array();
        $where['A.fieldset_id'] = $formInfo['fieldset_id'];
        $fieldList = target('FieldForm')->loadList($where);
        //格式化表单内容为基本数据
        $data = array();
        if(!empty($list)){
            foreach ($list as $key => $value) {
                $data[$key]=$value;
                foreach ($fieldList as $v) {
                    $data[$key][$v['field']] = target('duxcms/FieldData')->revertField($value[$v['field']],$v['type'],$v['config']);
                }
                $data[$key]['furl'] = url('DuxCms/Form/info',array('name'=>$name,'id'=>$value['data_id']));
            }
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['name'] = $name;
        //获取分页
        $page = $this->getPageShow($pageMaps);
        //位置导航
        $crumb = array(array('name'=>$formInfo['name'],'url'=>url('duxcms/Form/index',$pageMaps)));
        //MEDIA信息
        $media = $this->getMedia($formInfo['name']);
        $this->assign('crumb',$crumb);
        $this->assign('media', $media);
        $this->assign('pageList',$data);
        $this->assign('page', $page);
        $this->assign('formInfo', $formInfo);
        $this->siteDisplay($formInfo['tpl_list']);
    }

    /**
     * 表单内容
     */
    public function info(){
        $name = urldecode(request('get.name'));
        $table = len($name,0,20);
        $id = request('get.id');
        if(empty($table)||empty($id)){
            $this->error404();
        }
        //获取表单信息
        $where = array();
        $where['table'] = $table;
        $formInfo = target('duxcms/FieldsetForm')->getWhereInfo($where);
        if(empty($formInfo)){
            $this->error404();
        }
        if(!$formInfo['show_info']){
            $this->error404();
        }
        //设置模型
        $model = target('duxcms/FieldData');
        $model->setTable('ext_'.$formInfo['table']);
        $info = $model->getInfo($id);
        if(empty($info)){
             $this->error404();
        }
        //字段列表
        $where = array();
        $where['A.fieldset_id'] = $formInfo['fieldset_id'];
        $fieldList = target('FieldForm')->loadList($where);
        //格式化表单内容为基本数据
        foreach ($fieldList as $v) {
            $info[$v['field']] = target('duxcms/FieldData')->revertField($info[$v['field']],$v['type'],$v['config']);
        }
        //位置导航
        $crumb = array(
            array('name'=>$formInfo['name'],'url'=>url('duxcms/Form/index',array('name'=>$name))),
            array('name'=>'详情','url'=>url('duxcms/Form/info',array('name'=>$name,'id'=>$id))),
            );
        //MEDIA信息
        $media = $this->getMedia($formInfo['name'] . '- 详情 ');
        $this->assign('crumb',$crumb);
        $this->assign('media', $media);
        $this->assign('formInfo', $formInfo);
        $this->assign('info', $info);
        $this->siteDisplay($formInfo['tpl_info']);
    }

    /**
     * 发布
     */
    public function push(){
        if(!IS_POST){
            $this->error404();
        }
        $table = request('post.table');
        $token = request('post.token');
        $token = trim($token);
        if(empty($table)||empty($token)){
            $this->errorBlock();
        }
        //验证token
        if(!target('FieldsetForm')->validToken($table,$token)){
            $this->error('安全验证失败，请刷新页面后重新提交！');
        }
        //获取表单信息
        $where = array();
        $where['table'] = $table;
        $formInfo = target('duxcms/FieldsetForm')->getWhereInfo($where);
        if(empty($formInfo)){
            $this->errorBlock();
        }
        if(!$formInfo['post_status']){
            $this->errorBlock();
        }
        $data = array();
        foreach (request('post.') as $key => $value) {
            $data['Fieldset_'.$key] = $value;
        }
        $_POST = $data;
        //设置模型
        $model = target('duxcms/FieldData');
        $model->setTable('ext_'.$formInfo['table']);
        //增加信息
        if ($model->saveData('add',$formInfo)){
            if(empty($formInfo['post_return_url'])){
                $url =  $_SERVER["HTTP_REFERER"];
            }else{
                $url = $formInfo['post_return_url'];
            }
            $this->success($formInfo['post_msg'], $url);
        }else{
            $msg = $model->getError();
            if (empty($msg))
            {
                $this->error($formInfo['name'].'发布失败，请刷新后重新尝试！');
            }else{
                $this->error($msg);
            }
        }
    }


}

