<?php
class DiscussAction extends Action {
    //正常提交处理
    public function submit(){
        $pid=$_GET['pid'];
        $tag=$_GET['tag'];

        $body=D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->find($pid);
        $type=M('category')->find($body['cid']);
        
         if($body['review']==0){
            if($type['review']==3) $this->error('禁止评论！');
            if($type['review']==2 && (Session('muid')==null)) $this->error('无权评论！');
        }
        else{
            if($body['review']==3) $this->error('禁止评论！');
            if($body['review']==2 && (Session('muid')==null)) $this->error('无权评论！');
        }
        $db=M('Discuss');
        $data=$db->create();
        $data['pid']=$pid;
        $data['tag']=$tag;
        $data['cdate']=time();
        
        if(Session('muid')){
            $data['user']=Session('muid');
        }
        $db->add($data);
        D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->where(array('id'=>$pid))->setInc('recount');//评论加1
        $this->redirect(U('Wenbon/b',array('id'=>$pid,'t'=>$tag)));
    }
    //ajax提交处理
    public function submit_e(){
        $pid=$_GET['pid'];
        $tag=$_GET['tag'];
        
        $body=D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->find($pid);
        $type=M('category')->find($body['cid']);
        //去无用信息
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        $str=str_replace($qian,$hou,I(body));  
        if(empty($str)) {$this->ajaxReturn(1,'评论内容无效',1);exit();}
        if($body['review']==0){
            if($type['review']==3) {$this->ajaxReturn(1,'禁止评论',1);exit();}
            if($type['review']==2 && (Session('muid')==null)) {$this->ajaxReturn(2,'无权评论',2);exit();}
        }
        else{
            if($body['review']==3) {$this->ajaxReturn(1,'禁止评论',1);exit();}
            if($body['review']==2 && (Session('muid')==null)) {$this->ajaxReturn(2,'无权评论',2);exit();}
        }
        $db=M('Discuss');
        $data=$db->create();
        $data['pid']=$pid;
        $data['tag']=$tag;
        $data['cdate']=time();
        
        if(Session('muid')){
            $data['user']=Session('muid');
        }
        $db->add($data);
        D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->where(array('id'=>$pid))->setInc('recount');//评论加1
        $this->ajaxReturn(Session('muser.username'),'评论成功',0);
        exit();
    }
    //顶评论
    public function top(){
        $id=I('id');
        $cid=$_COOKIE[DISCUSSTOP];
        if($id){
            if($id==$cid) 
            {
                $this->ajaxReturn(1,'你已顶过该评论',1);
                exit();
            }
            M('discuss')->where('id='.$id)->setInc(top);
            $top=M('discuss')->find($id);
            setcookie('DISCUSSTOP',$id,time()+86400);
            $this->ajaxReturn($top[top],'操作成功',0);
        }else{
            $this->ajaxReturn(1,'操作错误',1);
        }
    }
}

?>