<?php
namespace app\home\controller;
class Form extends Site
{
    public function index(){
        $name = urldecode(input('name'));
        $table = len($name,0,20);
        if(empty($table)){
            return $this->error404();
        }
        //获取表单信息
        $where = array();
        $where['table'] = $table;
        $form_info = model('FieldsetForm')->getWhereInfo($where);
        if(empty($form_info)){
            return $this->error404();
        }
        if(!$form_info['show_list']){
            return $this->error404();
        }
        //分页参数
        $size = intval($form_info['list_page']);
        if (empty($size)) {
            $limit = 20;
        } else {
            $limit = $size;
        }
        //设置模型
        $model = model('FieldData');
        $model->setTable(config('database.prefix').'ext_'.$form_info['table']);
        //查询数据
        $where = array();
        if(!empty($form_info['list_where'])){
            $where[] = $form_info['list_where'];
        }
        //查询内容
        $list = $model->loadPageList($where,$limit,$form_info['list_order']);
        //字段列表
        $where = array();
        $where['A.fieldset_id'] = $form_info['fieldset_id'];
        $fieldList = model('FieldForm')->loadList($where);
        //格式化表单内容为基本数据
        $data = array();
        if(!empty($list)){
            foreach ($list as $key => $value) {
                $data[$key]=$value;
                foreach ($fieldList as $v) {
                    $data[$key][$v['field']] = model('FieldData')->revertField($value[$v['field']],$v['type'],$v['config']);
                }
                $data[$key]['furl'] = url('kbcms/Form/info',array('name'=>$name,'id'=>$value['data_id']));
            }
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['name'] = $name;
        //获取分页
        $page = $list->render();
        //位置导航
        $crumb = array(array('name'=>$form_info['name'],'url'=>url('home/Form/index',$pageMaps)));
        //获取顶级栏目信息
        $top_category_info = model('Category')->getInfo($crumb[0]['class_id']);
        //MEDIA信息
        $media = $this->getMedia($form_info['name']);
        $this->assign('crumb',$crumb);
        $this->assign('media', $media);
        $this->assign('pageMaps', $pageMaps);
        $top_category_info['class_id']='form';
        $this->assign('top_category_info', $top_category_info);
        $this->assign('list',$data);
        $this->assign('_page', $page);
        $this->assign('form_info', $form_info);
        return $this->siteFetch($form_info['tpl_list']);
    }

    /**
     * 发布
     */
    public function push(){
        if(empty(input('post.'))){
            return $this->error404();
        }
        $table = input('post.table');
        if(empty($table)){
            return $this->errorBlock();
        }
        //获取表单信息
        $where = array();
        $where['table'] = $table;
        $form_info = model('FieldsetForm')->getWhereInfo($where);
        if(empty($form_info)){
            return $this->errorBlock();
        }
        if(!$form_info['post_status']){
            return $this->errorBlock();
        }
        $data = array();
        foreach (input('post.') as $key => $value) {
            $data['Fieldset_'.$key] = $value;
        }
        $_POST = $data;
        //设置模型
        $model = model('FieldData');
        $model->setTable(config('database.prefix').'ext_'.$form_info['table']);
        //增加信息
        if ($model->saveData('add',$form_info)){
            if(empty($form_info['post_return_url'])){
                $url =  $_SERVER["HTTP_REFERER"];
            }else{
                $url = $form_info['post_return_url'];
            }
            return ajaxReturn(200,$form_info['post_msg'],$url);
        }else{
            $msg = $model->getError();
            if (empty($msg)){
                return ajaxReturn(0,$form_info['name'].'发布失败，请刷新后重新尝试！');
            }else{
                return ajaxReturn(0,$msg);
            }
        }
    }
}
