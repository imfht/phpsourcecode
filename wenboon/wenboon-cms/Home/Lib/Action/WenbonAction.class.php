<?php
class WenbonAction extends Action {
     Public function _initialize(){
        $this->site=M('site')->find(1);
     }
     Public function _empty(){
        if(empty($cid)) $this->error('页面不存在！');
     }
      //模式一
     Public function a(){
        $cid=I('cid');
        $id=I('id');
        
        if(!empty($cid))
        {
            $type=M('category')->find($cid);
            if($type['access']==3) $this->error('禁止访问！');
            if($type['access']==2 && (Session('muid')==null)) $this->error('无权访问！');
            $type['url']=U('Wenbon/a',array('cid'=>$type['id']));
            
            $this->position=formatCat(getparents(M('category')->select(),$cid));
            $this->type=$type;
            $this->display($type['module'].'/'.$type['list']);
        }
        else if(!empty($id)){
            $tag=I('t');
            if(empty($id)) $this->error('页面不存在！');
            if(empty($tag)) $this->error('页面不存在！');
            
            $body=D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->where(array('id'=>$id))->select();
            $body=formatCon($body); 
            $body=$body[0];   
            $type=M('category')->find($body['cid']);
            $type['url']=U('Wenbon/a',array('cid'=>$type['id']));
            
            //该条信息的访问权限
            if($body['access']==0){
                if($type['access']==3) $this->error('禁止访问！');
                if($type['access']==2 && (Session('muid')==null)) $this->error('无权访问！');
            }
            else{
                if($body['access']==3) $this->error('禁止访问！');
                if($body['access']==2 && (Session('muid')==null)) $this->error('无权访问！');
            }
            
            $this->body=$body;
            $this->type=$type;
            $this->position=formatCat(getparents(M('category')->select(),$body['cid']));
            D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->where(array('id'=>$id))->setInc('account');//访问加1
            $this->display($type['module'].'/'.$type['content']);
        }
     }
     //其他模式
     Public function b(){
            $cid=I('catid');
            $id=I('itemid');
            if(!empty($cid)){
                $type=M('category')->find($cid);
                if($type['access']==3) $this->error('禁止访问！');
                if($type['access']==2 && (Session('muid')==null)) $this->error('无权访问！');
                $type['url']=U('/catid/'.$type['id']);
                
                $this->position=formatCat(getparents(M('category')->select(),$cid));
                $this->type=$type;
                $this->display($type['module'].'/'.$type['list']);
                return;
            }
            else if(!empty($id))
            {
                $tag=I('t');
                if(empty($tag)) $this->error('页面不存在！');
                
                $body=D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->where(array('id'=>$id))->select();
                $body=formatCon($body); 
                $body=$body[0];   
                $type=M('category')->find($body['cid']);
                $type['url']=U('/catid/'.$type['id']);
                
                //该条信息的访问权限
                if($body['access']==0){
                    if($type['access']==3) $this->error('禁止访问！');
                    if($type['access']==2 && (Session('muid')==null)) $this->error('无权访问！');
                }
                else{
                    if($body['access']==3) $this->error('禁止访问！');
                    if($body['access']==2 && (Session('muid')==null)) $this->error('无权访问！');
                }
                
                $this->body=$body;
                $this->type=$type;
                $this->position=formatCat(getparents(M('category')->select(),$body['cid']));
                D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->where(array('id'=>$id))->setInc('account');//访问加1
                $this->display($type['module'].'/'.$type['content']);
                return;
            }
            else{
                $this->error('页面不存在！');
            }
     }
     //别名catid时匹配使用
     public function c(){
            $cid=I('catid');
            $id=I('itemid');
            if(!empty($cid)){
                $type=M('category')->where(array('urlname'=>$cid))->select();
                $type=$type[0];
                if($type['access']==3) $this->error('禁止访问！');
                if($type['access']==2 && (Session('muid')==null)) $this->error('无权访问！');
                $type['url']=U('/catid/'.$type['urlname']);
                
                $this->position=formatCat(getparents(M('category')->select(),$type[id]));
                $this->type=$type;
                $this->display($type['module'].'/'.$type['list']);
                return;
            }
            else if(!empty($id))
            {
                $tag=I('t');
                if(empty($tag)) $this->error('页面不存在！');
                
                $body=D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->where(array('id'=>$id))->select();
                $body=formatCon($body); 
                $body=$body[0];   
                $type=M('category')->find($body['cid']);
                $type['url']=U('/catid/'.$type['id']);
                
                //该条信息的访问权限
                if($body['access']==0){
                    if($type['access']==3) $this->error('禁止访问！');
                    if($type['access']==2 && (Session('muid')==null)) $this->error('无权访问！');
                }
                else{
                    if($body['access']==3) $this->error('禁止访问！');
                    if($body['access']==2 && (Session('muid')==null)) $this->error('无权访问！');
                }
                
                $this->body=$body;
                $this->type=$type;
                $this->position=formatCat(getparents(M('category')->select(),$body['cid']));
                D('Content')->table(C('DB_PREFIX').$tag)->relation(true)->where(array('id'=>$id))->setInc('account');//访问加1
                $this->display($type['module'].'/'.$type['content']);
                return;
            }
            else{
                $this->error('页面不存在！');
            }
     }
    
}
?>