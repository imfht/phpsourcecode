<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;

//栏目详情页
class ShowController extends HomeBaseController {

    public function index($cname='',$id=0) {
        if( empty($cname) || strlen($cname) >15 ) E('非法操作');
        if( !is_numeric($id) ) E('非法操作');

        $cate=D('Home/Category')->getCategory($cname);
        unset($cate['setting']);
        $this->assign('CATEGORY',$cate);
        $this->assign('CID',$cate['id']);
        $this->assign('PID',$cate['pid']);

        //文章数据
        if(!$id) $id=I('id','','intval');
        $table=M('Model')->where("id={$cate['mid']}")->getField('model_table');
        $info=M($table)->find($id);
        $this->assign('show',$info);

        $meta=array(
            'meta_title'=>$info['title'],
            'meta_keywords'=>$info['keywords'],
            'meta_description'=>$info['description'],
        );
        $this->assign($meta);

        //上一篇 下一篇
        $page_next=M($table)->field('id,cid,title')->order('id DESC')->where("cid={$cate['id']} AND id < {$id}")->find();
        if(isset($page_next)) $page_next['url']=U('/'.$cate['name'].'/'.$page_next['id'],'','html',true);
        $page_prev=M($table)->field('id,cid,title')->order('id ASC')->where("cid={$cate['id']} AND id > {$id}")->find();
        if(isset($page_prev)) $page_prev['url']=U('/'.$cate['name'].'/'.$page_prev['id'],'','html',true);
        $this->assign('page_next',$page_next);
        $this->assign('page_prev',$page_prev);

        //相关阅读
        $list_about=M($table)->where("cid={$cate['id']}")->field('id,title,add_time')->order('view desc')->limit('5')->select();
        foreach ($list_about as &$v) {
            $v['url']=U('/'.$cate['name'].'/'.$v['id'],'','html',true);
        }
        $this->assign('list_about',$list_about);

        //更新浏览数
        $map = array('id' => $id);
        M($table)->where($map)->setInc('view');

        $tpl=isset($cate['template_show']) ? $cate['template_show'] : 'Show_index';
        $this->display($tpl);

    }
    	
}
