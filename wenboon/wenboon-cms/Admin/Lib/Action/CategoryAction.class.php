<?php
    class CategoryAction extends CommonAction {
        public function index(){
            $category=M('category')->order('orders asc')->select();
            $this->category=node_merge($category);
            $this->display();
        }
        //排序
        public function orders(){
            $db=M('category');
            foreach($_POST as $id=>$orders){
                $db->where(array('id'=>$id))->setField('orders',$orders);
            }
            $this->redirect('index');
        }
        //添加
        public function add(){
            $Category=M('category')->select();
            $this->Module=M('module')->select();
            $this->Category=node_merge($Category);
            $this->display();
        }
        public function addHandle(){
            //添加category表
            $db=M('category');
            $data=$db->create();
            if(!$data['title']) $this->error('名称不能为空！');
            if(!$data['module']) $this->error('模型不能为空！');
            if(!$data['list']) $this->error('列表页不能为空！');
            if(!$data['content']) $this->error('内容页不能为空！');
            if($data['urlname']){
                $cout=$db->where(array('urlname'=>$data['urlname']))->count();
                if($cout!=0) $this->error('URL别名 字段有重复!');
            }
            $cid=$db->add($data);
            
            //修改fields表cid字段
            $dt_field=$_POST['tagfields'];
            foreach ($dt_field as $v){
                $tem=M('fields')->where(array('tag'=>$v,'mtag'=>$data['module']))->find();
                $cid_str=$tem['cid'].'|'.$cid;
                M('fields')->where(array('tag'=>$v,'mtag'=>$data['module']))->setField('cid',$cid_str);
            }
            
            $this->success('添加成功!');
        }
        //删除
        public function delete(){
            $db=M('category');
            $cout=$db->where(array('pid'=>I('id')))->count();
            if($cout) $this->error('请先删除下级类目！');
            //删除fields表中所有相关cid
            $where['cid']=array('like',"%|".I('id')."%");
            $dt_field=M('fields')->where($where)->select();
            
            foreach ($dt_field as $v){
                $cid_str=str_replace('|'.I('id'),'',$v['cid']);
                M('fields')->where(array('tag'=>$v['tag'],'mtag'=>$v['mtag']))->setField('cid',$cid_str);
            }
            //删除category表
            $db->where(array('id'=>I('id')))->delete();
            
            $this->success('删除成功');
        }
        //修改
        public function modify(){
            $Category=M('category')->select();
            $Category=node_merge($Category);
            $this->Category=$Category;
            $this->mCategory=M('category')->find(I('id'));
            $this->pCategory=M('category')->find($this->mCategory['pid']); 
            $this->mModule=M('module')->find($this->mCategory['module']);
            $this->Module=M('module')->select();
            $this->mFields=M('fields')->field('cid,title,tag')->where(array('mtag'=>$this->mCategory['module']))->select();
            $this->display();
        }
        public function modifyHandle(){
            $db=M('category');
            $data=$db->create();
            if(!$data['title']) $this->error('名称不能为空！');
            if(!$data['module']) $this->error('模块不能为空！');
            if(!$data['list']) $this->error('列表页不能为空！');
            if(!$data['content']) $this->error('内容页不能为空！');
            if($data['urlname']){
                $wheres['urlname']=array('eq',$data['urlname']);
                $wheres['id']=array('neq',I('id'));
                $cout=$db->where($wheres)->count();
                if($cout!=0) $this->error('URL别名 字段有重复!');
            }
            //删除fields表中所有相关cid
            $where['cid']=array('like',"%|".I('id')."%");
            $dt_field=M('fields')->where($where)->select();
            
            foreach ($dt_field as $v){
                $cid_str=str_replace('|'.I('id'),'',$v['cid']);
                M('fields')->where(array('tag'=>$v['tag'],'mtag'=>$v['mtag']))->setField('cid',$cid_str);
            }
            //修改fields表cid字段
            $get_field=$_POST['tagfields'];
            foreach ($get_field as $v){
                $tem=M('fields')->where(array('tag'=>$v,'mtag'=>$data['module']))->find();
                $cid_str=$tem['cid'].'|'.I('id');
                M('fields')->where(array('tag'=>$v,'mtag'=>$data['module']))->setField('cid',$cid_str);
            }
            //修改category表
            $db->where(array('id'=>I('id')))->save($data);
            $this->success('修改成功!');
        }
        //删除图片
        public function deleteImgHandle(){
            
        }
        //获取模型字段
        public function getfields(){
            $mtag=I('mtag');
            if($mtag){
                $db=M('fields');
                $dt=$db->field('cid,title,tag')->where(array('mtag'=>$mtag))->select();
                echo json_encode($dt);
            }
        }
    }
?>