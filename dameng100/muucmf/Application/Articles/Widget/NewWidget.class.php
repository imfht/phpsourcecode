<?php
namespace Articles\Widget;

use Think\Controller;

class NewWidget extends Controller{
    /* 显示指定分类的同级分类或子分类列表 */
    public function lists($map,$limit = 10)
    {
        $map['status']=1;
        $lists = D('Articles/Articles')->getList($map,'view desc',$limit,'id,category,title,cover,uid,create_time,view');
        
        foreach($lists as &$val){
            $val['user']=query_user(array('space_url','nickname'),$val['uid']);
        }
        unset($val);

        $this->assign('lists', $lists);
        $this->display('Widget/new');
    }
}