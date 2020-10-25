<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class GuestbookController extends HomeBaseController {
    public function _initialize() {
        parent::_initialize();
        $meta=array(
            'meta_title'=>'提交建议',
        );
        $this->assign($meta);
    }

    public function index() {
        if(IS_POST){$this->addMsg();exit;}

        $cate=D('Category')->where("name='guestbook'")->find();
        if($cate){
            $cate['url']=U('/'.$cate['name']);
            $this->assign('CATEGORY',$cate);
            $this->assign('CID',$cate['id']);
            $this->assign('PID',$cate['pid']);
        }


        $count=M('Guestbook')->count();
        $Page =new \Lib\Page($count,10);

        $Page->url='guestbook';
        $page =$Page->show();
        $this->assign('page',$page);

        $list=M('Guestbook')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id desc')
            ->select();
        $this->assign('list',$list);
        $this->display();
    }

    public function addMsg(){

        $code=I('post.code','');
        $Verify = new \Think\Verify();
        if(!$Verify->check($code, 1)) $this->error('验证码错误');

        $data=D('Guestbook')->create();
        if(!$data) $this->error(D('Guestbook')->getError());

        $status=M('Guestbook')->add($data);
        if($status){
            $this->success('留言成功');
        }else{
            $this->error('留言成功');
        }
    }
    	
}
