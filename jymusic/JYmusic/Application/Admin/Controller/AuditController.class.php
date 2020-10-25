<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+

namespace Admin\Controller;

/**
 * 后台音乐审核控制器
 */
class AuditController extends AdminController {
	public function index(){
		$map['status'] = 2;
        $list = $this->lists('Songs',$map,'id desc','id,name,up_uid,up_uname,album_name,genre_name,artist_name,add_time,music_url,status');   	
    	$this->assign('list',$list);
        $this->meta_title = '待审列表';
        $this->display();
    }
    

	//驳回
    function disable () {
    	if(IS_POST){
    	 	$id    =   I('post.id');
    	 	$uid   =   I('post.uid');
    	 	$map['id'] = $id;
    	 	$S = M('Songs');
    	 	$list = $S->where($map)->field('name,listen_file_id,down_file_id')->find();    	 	
    	 	//删除上传文件
    	 	$lid = $list['listen_file_id'];
    	 	$did = $list['down_file_id']; 
    	 	if($lid) {
    	 		$F = D('File');
    	 		if($lid == $did){
    	 			$F->delFile($lid);    	 			
    	 		}else{
    	 			$F->delFile($lid);
    	 			$F->delFile($did);
    	 		}
    	 	}  
    	 	//删除表中数据 
    	 	$S->where($map)->delete();	   	 	
    	 	//发送通知
			$title = '歌曲审核通知';
			$content = '你上传的音乐['.$list['name'].']未通过审核！';					
			D('Common/Message')->sendMsg($uid,$title,$content,$type='app');
			$this->success('操作成功');
    	
    	}else{
    	
    		$this->error('非法请求');
    	}
    
	}
    
    //通过审核
    public function pass () {    	    	
    	if(IS_POST){
    	 	$id    =   I('post.id');
    	 	$uid   =   I('post.uid');
    	 	$list =  M('Songs')->where(array('id'=>$id))->field('listen_file_id,name')->find();             	
	    	$map['file_id'] = $list['listen_file_id']; 
			$map['uid'] = $uid;
	    	$up['music_id'] = $id;	                	
	    	$up['music_name'] = $list['name'];
	    	$up['status'] = 1;
			M('Songs')-> where(array('id'=>$id))->setField('status',1);
	    	M('UserUpload')->where(array($map))->save($up);
	        M('Member')->where(array('uid'=>$uid))->setInc('songs',1);  //增加会员添加歌曲数量  
	        //发送通知
			$title = '歌曲审核通知';
			$content = '你上传的音乐['.$up['music_name'].']成功通过审核！&nbsp;&nbsp;&nbsp;<a style="color: #01b7f2" href="'.U('/Music/detail',array('id'=>$id)).'"> 听听</a>';
			$msg = D('Common/Message');						
			$msg->sendMsg($uid,$title,$content,$type='app');                   
			$this->success('操作成功');
    	
    	}else{
    	
    		$this->error('非法请求');
    	}
    
    } 	
}
