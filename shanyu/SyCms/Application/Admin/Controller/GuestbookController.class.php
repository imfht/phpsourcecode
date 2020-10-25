<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class GuestbookController extends AdminBaseController {

    public function index(){

        //分页
        $limit=$this->_page('Guestbook');

        //数据
        $list=M('Guestbook')
            ->limit($limit)
            ->where($where)
            ->order('id desc')
            ->select();
        $this->assign('list',$list);

        $this->display();
    }
    public function add(){
        if(IS_POST){$this->addPost();exit;}
        $info=M('Guestbook')->find($id);
        $this->assign('info',$info);
        $this->display();
    }
    private function addPost(){
        $Model=D('Guestbook');
        $data=$Model->create();
        if(!$data) $this->error($Model->getError());

        $return=$Model->add($data);
        if($return) $this->success('添加成功');
        else $this->error('添加失败');
    }

    public function edit($id){
        if(IS_POST){$this->editPost();exit;}

        $info=M('Guestbook')->find($id);
        $this->assign('info',$info);
        $this->display();
    }
    private function editPost(){
        $Model=D('Guestbook');
        $data=$Model->create();
        if(!$data) $this->error($Model->getError());

        $return=$Model->save($data);
        if($return) $this->success('修改成功');
        else $this->error('修改失败');
    }

    public function del($id){
        $return=M('Guestbook')->delete($id);
        if($return) $this->success('删除成功');
        else $this->error('删除失败');
    }

}