<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use \think\Db;
// 应用公共文件

/***************标签方法开始****************/
/**
 * 获取导航列表
 * @param $tag 搜索条件
 * @return
 */
function get_nav_menu($tag){
    $tag=param2array($tag);
    $limit = !empty($tag['limit']) ? $tag['limit'] : '';
    $order = !empty($tag['order']) ? $tag['order'] : 'sort ASC';
    //根据参数生成查询条件
    $where['nm.status'] = array('eq',1);
    $where['n.lang_id'] = array('eq',get_lang_id());
    if (!empty($tag['nav_id'])) {
        $where['nm.nav_id'] = array('eq',$tag['nav_id']);
    }
    if (!empty($tag['parent_id'])) {
        $where['nm.parent_id'] = array('eq',$tag['parent_id']);
    }else{
        $where['nm.parent_id']=array('eq',0);
    }
    if (!empty($tag['where'])) {
        $where[] = $tag['where'];
    }
    $list=Db::name('nav_menu')
        ->alias('nm')
        ->field('nm.*')
        ->join('nav n','nm.nav_id=n.nav_id','left')
        ->where($where)
        ->order($order)
        ->limit($limit)
        ->select();
    if ($list){
        $i = 0;
        foreach ($list as $key=>$val){
            $list[$key]['i'] = $i++;
        }
    }
    return $list;
}
/**
 * 获取分类列表（导航）
 * @param $tag 搜索条件
 * @return
 */
function get_cat($tag){
    $tag=param2array($tag);
    $limit = !empty($tag['limit']) ? $tag['limit'] : '';
    $order = !empty($tag['order']) ? $tag['order'] : 'sequence ASC';
    //根据参数生成查询条件
    $where['show'] = array('eq',1);
    $where['lang_id'] = array('eq',get_lang_id());
    if (!empty($tag['class_id'])) {
        $where['class_id'] = array('eq',$tag['class_id']);
    }
    if (!empty($tag['class_ids'])) {
        $where['class_id'] = array('in',$tag['class_ids']);
    }
    if (!empty($tag['parent_id'])) {
        $where['parent_id'] = array('eq',$tag['parent_id']);
    }else{
        $where['parent_id']=array('eq',0);
    }
    if (!empty($tag['where'])) {
        $where[] = $tag['where'];
    }
    $list=Db::name('category')->where($where)->order($order)->limit($limit)->select();
    if ($list){
        $i = 0;
        foreach ($list as $key=>$val){
            $list[$key]['child_num']=Db::name('category')->where(array('parent_id'=>$val['class_id']))->count();
            $list[$key]['app'] = strtolower($val['app']);
            $list[$key]['curl'] = model('Category')->getUrl($val);
            $list[$key]['i'] = $i++;
        }
    }
    return $list;
}

/**
 * 获取内容列表
 * @param $tag
 * @return mixed
 */
function get_content($tag){
    $tag=param2array($tag);
    $limit = !empty($tag['limit']) ? $tag['limit'] : '';
    $order = !empty($tag['order']) ? $tag['order'] : 'sequence ASC';
    //根据参数生成查询条件
    $where['status'] = array('eq',1);
    if (!empty($tag['class_id'])) {
        $where['class_id'] = array('eq',$tag['class_id']);
    }
    if (!empty($tag['pos_id'])) {
        $where[] = array('exp','find_in_set('.$tag['pos_id'].',A.position) ');
    }
    if (!empty($tag['where'])) {
        $where[] = $tag['where'];
    }
    //$list=Db::name('content')->field('*')->alias('c')->join('content_article ca','c.content_id=ca.content_id')->where($where)->order($order)->limit($limit)->select();
    $list=model('ContentArticle')->loadList($where,$limit,$order);
    return $list;
}
function get_formlist($tag){
    $tag=param2array($tag);
    $limit = !empty($tag['limit']) ? $tag['limit'] : '';
    $order = !empty($tag['order']) ? $tag['order'] : 'data_id DESC';

    //设置模型
    $model = model('FieldData');
    $model->setTable(config('database.prefix').'ext_'.$tag['table']);
    //根据参数生成查询条件
    $where = array();
    if (!empty($tag['list_where'])) {
        $where[] = $tag['list_where'];
    }
    if (!empty($tag['where'])) {
        $where[] = $tag['where'];
    }
    //查询内容
    $list = $model->loadList($where,$limit,$order);
    //字段列表
    $where = array();
    $where['A.fieldset_id'] = $tag['fieldset_id'];
    $fieldList = model('FieldForm')->loadList($where);
    //格式化表单内容为基本数据
    $data = array();
    if(!empty($list)){
        $count=count($list);
        foreach ($list as $key => $value) {
            $data[$key]=$value;
            foreach ($fieldList as $v) {
                $data[$key][$v['field']] = model('FieldData')->revertField($value[$v['field']],$v['type'],$v['config']);
            }
            $data[$key]['furl'] = url('DuxCms/Form/info',array('id'=>$value['data_id']));
            if ($count-$key==1){
                $data[$key]['last']=1;
            }else{
                $data[$key]['last']=0;
            }
        }
    }
    return $data;
}
/**
 * 获取碎片
 * @param $mark
 * @return string|void
 */
function get_flag($mark){
    $where['label'] = $mark;
    $info = model('Fragment')->getWhereInfo($where);
    if(empty($info)){
        return ;
    }
    return htmlspecialchars_decode(html_out($info['content']));
}

/***************标签方法结束****************/
/**
 * 生成参数列表,以数组形式返回
 * @author rainfer <81818832@qq.com>
 * @param string
 * @return array
 */
function param2array($tag = '')
{
    $param = array();
    $array = explode(';',$tag);
    foreach ($array as $v){
        $v=trim($v);
        if(!empty($v)){
            list($key,$val) = explode(':',$v);
            $param[trim($key)] = trim($val);
        }
    }
    return $param;
}

