<?php
class ReleaseAction extends Action {
    public function release(){
        if(!I('tag')) $this->error("请求地址错误！无tag参数，正确写法{:U('Release/release',array('path'=>'/release',cid=>\$type['id'],'tag'=>\$type['module']))}");
        if(!I('cid')) $this->error("请求地址错误！无cid参数，正确写法{:U('Release/release',array('path'=>'/release',cid=>\$type['id'],'tag'=>\$type['module']))}");
        if(!I('path')) $this->error("请求地址错误！无path参数，正确写法{:U('Release/release',array('path'=>'/release',cid=>\$type['id'],'tag'=>\$type['module']))}");
        //权限判断
        $type=M('category')->find(I('cid'));
        if($type['release']==2) $this->error('禁止发布！');
        if($type['release']==1 && (Session('muid')==null)) $this->error('无权发布！');
        
        $c_where=array();
		$c_where['pid']=array('eq',I('cid'));
		$c_where['id']=array('eq',I('cid'));
		$c_where['_logic'] = 'OR';
        
        $this->tag=I('tag');
        $this->category=formatCat(M('category')->where($c_where)->select());
        $this->display(I('path'));
    }
    public function submit(){
        if(!$_GET['tag']) $this->error("请求地址错误!");
        if(!I('cid')) $this->error("请求地址错误!");
        $tag=$_GET['tag'];
        $cid=I('cid');
        
        //权限判断
        $type=M('category')->find($cid);
        if($type['release']==2) $this->error('禁止发布！');
        if($type['release']==1 && (Session('muid')==null)) $this->error('无权发布！');
        //
        
        $db=M($tag);
        $data=$db->create();
        $data['cdate']=time();
        $data['access']=0;
        $data['review']=0;
        $data['user']=Session('muid');
        $db->add($data);
        
        M('category')->where(array('id'=>$cid))->setInc('recount');//发布数量
        $this->success('发表成功！',U('Wenbon/a',array('cid'=>$cid)));
    }
}