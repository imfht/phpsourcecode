<?php
class ContentAction extends CommonAction {
    public function index(){
        $tag=I('tag');
        $cid=I('cid');
        import('ORG.Util.Page'); 
        if($cid)
            $count= M($tag)->where(array('cid'=>$cid))->count();
        else
            $count= M($tag)->count();
            
        $Page= new Page($count,15);
        $limit=$Page->firstRow .',' .$Page->listRows;
        if($cid){
            $this->list=D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->where(array('cid'=>$cid))->order('cdate desc')->limit($limit)->select();
            $this->mcid=$cid;
        }
        else{
            $this->list=D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->order('cdate desc')->limit($limit)->select();
        }
        
        $this->module=M('module')->where(array('tag'=>$tag))->find();
        $this->fields=M('fields')->where(array('mtag'=>$tag,'ls'=>0))->order('orders asc')->select();
        $this->page=$Page->show();
        $this->category=M('category')->where(array('module'=>$tag))->select();
        $this->display(); 
    }
    public function add(){
        $tag=I('tag');
        $cid=I('cid');
        if($cid){
            $where=array();
            $where['mtag']=array('eq',$tag);
            $where['cid']=array('like','%|'.$cid.'%');
            $this->module=M('module')->where(array('tag'=>$tag))->find();
            $this->fields=M('fields')->where($where)->order('orders asc')->select();
            $this->category=M('category')->where(array('module'=>$tag))->select();
            $this->mcategory=$cid;
        }
        else{
            $this->module=M('module')->where(array('tag'=>$tag))->find();
            $this->category=M('category')->where(array('module'=>$tag))->select();
        }
        $this->display();
        
    }
    public function addhandle(){
        $tag=I('tag');
        $cid=I('cid');
        if($cid==0) $this->error('分类不能为空！');
        $db=M($tag);
        $data=$db->create();
        $data['cdate']=time();
        $data['udate']=time();
        $data['user']=session(C('USER_AUTH_KEY'));
        $mcat=M('category')->find($cid);
        /*if(I('access')==0){
            $data['access']=$mcat['access'];
        }
        if(I('review')==0){
            $data['review']=$mcat['review'];
        }*/
        $id=$db->add($data);
        
        //存附件
        $refile=I('refile');
        if(!empty($refile))
        {
            $img=explode("`",$refile);
            $imgData=array();
            for($i=0;$i<count($img)-1;$i++)
            {
                $imgData[$i]['pid']=$id;
                $imgData[$i]['tag']=$tag;
                $imgData[$i]['url']=$img[$i];
            }
            M('attachment')->addAll($imgData);
        }
        $this->success('添加完成',U('index',array('tag'=>$tag)));
    }
    
    public function delete(){
        $tag=I('tag');
        $id=I('id');
        
        M($tag)->where(array('id'=>$id))->delete();
        M('attachment')->where(array('tag'=>$tag,'pid'=>$id))->delete();
        M('discuss')->where(array('tag'=>$tag,'pid'=>$id))->delete();
        //$this->success('已删除！');
        $this->redirect('index',array('tag'=>$tag,'p'=>I('p')));
    }
    public function modify(){
        $tag=I('tag');
        $id=I('id');
       
            
        $this->module=M('module')->where(array('tag'=>$tag))->find();
        $this->category=M('category')->where(array('module'=>$tag))->select();
        
        $this->body=M($tag)->where(array('id'=>$id))->find();
        $this->attachment=M('attachment')->where(array('tag'=>$tag,'pid'=>$id))->select();
        
        $where=array();
        $where['mtag']=array('eq',$tag);
        $where['cid']=array('like','%|'. $this->body['cid'].'%');
        $this->fields=M('fields')->where($where)->order('orders asc')->select();
        $this->display();
    }
    public function modifyhandle(){
        $tag=I('tag');
        $cid=I('cid');
        $id=I('id');
        if($cid==0) $this->error('分类不能为空！');
        $db=M($tag);
        $data=$db->create();
        //$data['cdate']=time();
        $data['udate']=time();
        $mcat=M('category')->find($cid);
        /*if(I('access')==0){
            $data['access']=$mcat['access'];
        }
        if(I('review')==0){
            $data['review']=$mcat['review'];
        }*/
        $db->save($data);
        
        //存附件
        
        $refile=I('refile');
        if(!empty($refile))
        {
            $img=explode("`",$refile);
            $imgData=array();
            for($i=0;$i<count($img)-1;$i++)
            {
                $imgData[$i]['pid']=$id;
                $imgData[$i]['tag']=$tag;
                $imgData[$i]['url']=$img[$i];
            }
            M('attachment')->where(array('tag'=>$tag,'pid'=>$id))->delete();
            M('attachment')->addAll($imgData);
        }
        $this->success('修改完成',U('index',array('tag'=>$tag)));
    }
}