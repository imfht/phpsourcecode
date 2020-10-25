<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * @author 大蒙<59262424@qq.com>
 */

namespace Articles\Widget;


use Think\Controller;

class HotWidget extends Controller{
    /* 显示指定分类的同级分类或子分类列表 */
    public function lists($category=0, $timespan = 7689600, $limit = 10)
    {
        if ($category != 0) {
            $cates=D('Articles/ArticlesCategory')->getCategoryList(array('pid'=>$category,'status'=>1));
            $cates=array_column($cates,'id');
            $map['category']=array('in',array_merge(array($category),$cates));
        }
        $map['status']=1;
        $map['update_time']=array('gt',time()-$timespan);//三个月以内
        $lists = D('Articles/Articles')->getList($map,'view desc',$limit,'id,title,cover,uid,create_time,view');
        $order = 0;//增加个序号
        foreach($lists as &$val){
            $val['order'] = ++$order;
            $val['user']=query_user(array('space_url','nickname'),$val['uid']);
        }
        unset($val);
        $this->assign('lists', $lists);
        $this->assign('category',$category);
        $this->display('Widget/hot');
    }
} 