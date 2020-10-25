<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 2/19/14
 * Time: 5:14 PM
 */

namespace Addons\LocalComment\Controller;

use Think\Controller;

class IndexController extends Controller
{

	
    public function addComment()
    {

       $config=  get_addon_config('LocalComment');
        $can_guest_comment=$config['can_guest_comment'];
        if(!$can_guest_comment){//不允许游客评论
            if(!is_login())
            {
                $this->error('请登录后评论。');
            }
        }

        //获取参数
        $app = strval($_REQUEST['app']);
        $mod = strval($_REQUEST['con']);
        $row_id = intval($_REQUEST['row_id']);
        $content = strval($_REQUEST['content']);
        $uid = intval($_REQUEST['uid']);
        $pid = intval($_REQUEST['pid']);
        
        if(M($mod)->where(array('id'=>$row_id))->getField('status')!=1){
        	$this->error('该文章尚未审核通过！');
        }
        
        
        $data = array('app' => $app, 'con' => $mod, 'row_id' => $row_id, 'content' => $content,'uid'=>is_login(),'pid'=>$pid);
       
        
        
        
        $commentModel = D('Addons://LocalComment/LocalComment');
        $data = $commentModel->create($data);
        if (!$data) {
            $this->error('评论失败：' . $commentModel->getError());
        }else{
        	 D($app.'/'.$mod)->where(array('id'=>$row_id))->setInc('reply_count');
        	 
        	 $rowinfo=D($app.'/'.$mod)->where(array('id'=>$row_id))->find();
        	 
        	 $data['content']=op_h($data['content'],'font');
        	 
        	 $commentModel->add($data);
        if(!is_login())
        {
            if($uid){
                $title ='游客' . '评论了您';
                $message = '评论内容：' . $content;
                $url = $_SERVER['HTTP_REFERER'];
                
                
                                if(strtolower($mod)=='article'){
		$rowurl = U('Home/Index/artc',array('id'=>$row_id));
	}
	if(strtolower($mod)=='music'){
		$rowurl = U('Home/Index/musicc',array('id'=>$row_id));
	}
	if(strtolower($mod)=='group'){
		$rowurl = U('Home/Index/groupc',array('id'=>$row_id));
	}
    sendMessage($rowinfo['uid'], '0', $title, $message.',链接地址：<a href="'.$rowurl.'">'.$rowinfo['title'].'</a>',  0);            
    
                
                
            }
            //返回结果
            $this->success('评论成功', 'refresh');
        }else{
            //给评论对象发送消息
            if($uid){
            	
                $user = D('Member')->find(getnowUid());
                $title = $user['nickname'] . '评论了您';
                $message = '评论内容：' . $content;
                $url = $_SERVER['HTTP_REFERER'];
                if($rowinfo['uid']!=getnowUid()){
                	
                
                if(strtolower($mod)=='article'){
		$rowurl = U('Home/Index/artc',array('id'=>$row_id));
	}
	if(strtolower($mod)=='music'){
		$rowurl = U('Home/Index/musicc',array('id'=>$row_id));
	}
	if(strtolower($mod)=='group'){
		$rowurl = U('Home/Index/groupc',array('id'=>$row_id));
	}
    sendMessage($rowinfo['uid'], getnowUid(), $title, $message.',链接地址：<a href="'.$rowurl.'">'.$rowinfo['title'].'</a>',  0);            
                
                
                
                 }
            }
        }
      //通知被@到的人
        $uids = get_at_uids($content);
        $uids = array_unique($uids);
        $uids = array_subtract($uids, array($uid));
        foreach ($uids as $uid) {
            $user = D('Member')->find(getnowUid());
            $title = $user['nickname'] . '@了您';
            $message = '评论内容：' . $content;
            $url = $_SERVER['HTTP_REFERER'];
            sendMessage($uid, getnowUid(), $title, $message.',链接地址：<a href="'.U('Index/artc',array('id'=>$row_id)).'">'.$rowinfo['title'].'</a>',  0);
        }


        //返回结果
        $this->success('评论成功');
        }
        
       
    }
   public function ding(){
    	$commentModel = D('Addons://LocalComment/LocalComment');
    	$uid=is_login();
    	$id=I('get.id');
    	if(cookie($uid.'commentding'.$id)!=''||cookie($uid.'commentcai'.$id)!=''){
    		$this->error('你已经对该内容进行过操作！');
    	}
    	$res=$commentModel->where(array('id'=>$id))->setInc('ding',1);
    	if($res===false){
    		$this->error('操作失败');
    	}else{
    		cookie($uid.'commentding'.$id,1);
    		$this->success('操作成功','',array('id'=>'commentding'.$id));
    	}
    	
    }
    public function cai(){
    	$commentModel = D('Addons://LocalComment/LocalComment');
    $uid=is_login();
    	$id=I('get.id');
        if(cookie($uid.'commentding'.$id)!=''||cookie($uid.'commentcai'.$id)!=''){
    		$this->error('你已经对该内容进行过操作！');
    	}
    	$res=$commentModel->where(array('id'=>$id))->setInc('cai',1);
    	if($res===false){
    		$this->error('操作失败');
    	}else{
    		cookie($uid.'commentcai'.$id,1);
    		$this->success('操作成功','',array('id'=>'commentcai'.$id));
    	}
    	
    
    }
    
    
}