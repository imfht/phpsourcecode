<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class JobAction extends BaseAction{

    // 招聘首页
    public function index() {
        $this->_list('Job','status=1');
        $this->display();
    }

    // 查看职位
    public function _empty($method){
        if(is_numeric($method)) {
            // 查看具体的职位信息
            $New = M("New");
            $vo   =  $New->find($method);
            if(!$vo  || $vo['status']   ==0 ) {
                $this->error('查看的职位不存在或已经删除！');
            }
            $this->title  =  $vo['title'];
            // 获取最新动态
            $list   =  include DATA_PATH.'~job.php';
            $this->assign('list',$list);
            $this->assign('vo',$vo);
            $this->display('read');
        }else{
            $this->error('错误操作');
        }
    }

}
?>