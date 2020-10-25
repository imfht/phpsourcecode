<?php
    class AdvertAction extends CommonAction {
        public function index(){
            $this->lists=M('advertisement')->select();
            $this->display();
        }
        public function iadd(){
            $this->display();
        }
        public function iddhandle(){
            $db=M('advertisement');
            $data=$db->create();
            $db->add($data);
            $this->success('添加完成!');
        }
        public function imodify(){
            $this->body=M('advertisement')->find(I('id'));
            $this->display();
        }
        public function imodifyhandle(){
            $db=M('advertisement');
            $data=$db->create();
            $db->save($data);
           $this->success('修改完成!');
        }
        public function idelete(){
            D('Advertisement')->relation(true)->where(array('id'=>I('id')))->delete();
            $this->redirect('index');
        }
        //////////////广告管理
        public function advert(){
            $pid=I('pid');
            $where=array();
            $where[sys]=array('neq',1);
            $where[ls]=array('eq',0);
            $this->fields=M('advertext_fields')->where($where)->order('orders asc')->select();
            $this->pid=$pid;
            $this->lists=M('advertext')->where(array('pid'=>$pid))->order('orders asc')->select();
            $this->display();
        }
        public function aadd(){
            $this->fields=M('advertext_fields')->where('sys!=1')->order('orders asc')->select();
            $this->pid=I('pid');
            $this->display();
        }
        public function aaddhandle(){
            $title=I('title');
            if(str_replace(' ','',$title)=='') $this->error('名称不能为空！');
            $db=M('advertext');
            $data=$db->create();
            $db->add($data);
            $this->success('添加完成!',U('Advert/advert',array('pid'=>I('pid'))));
        }
        public function amodify(){
            $this->fields=M('advertext_fields')->where('sys!=1')->order('orders asc')->select();
            $this->body=M('advertext')->find(I('id'));
            $this->display();
        }
        public function amodifyhandle(){
            $title=I('title');
            if(str_replace(' ','',$title)=='') $this->error('名称不能为空！');
            $db=M('advertext');
            $data=$db->create();
            unset($data['id']);
            $db->where(array('id'=>I('id')))->save($data);
            $this->success('修改完成!',U('Advert/advert',array('pid'=>I('pid'))));
        }
        public function adelete(){
            M('advertext')->delete(I('id'));
            $this->redirect('advert',array('pid'=>I('pid')));
        }
        public function orders(){
            //p(I());die;
            $db=M('advertext');
            foreach($_POST as $id=>$orders){
                $db->where(array('id'=>$id))->setField('orders',$orders);
            }
            $this->redirect('advert',array('pid'=>I('pid')));
        }
    }
    