<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
class MusicController extends UserController {
    
   /**
     * 用户的音乐
   */
    public function index(){
    	$map['up_uid']  = UID;
    	$list = $this->lists('Songs',$map,'id desc','id,name,up_uid,up_uname,artist_id,artist_name,album_id,album_name,genre_name,genre_id,listens,rater,add_time');//获取歌曲数据集
    	$this->assign('list', $list);
    	$this->meat_title = '我的分享 - '.C('WEB_SITE_TITLE');
    	$this->assign('type', 'index');
		$this->display();
    }

   	/**
     * 待审核的音乐
   	*/
    public function audit(){
    	$map['up_uid']  = UID;    	
    	$list = $this->lists('Songs',$map,'id desc','id,name,genre_name,genre_id,add_time',2);//获取歌曲数据集
    	$this->assign('list', $list);
    	$this->meat_title = '待审核 - '.C('WEB_SITE_TITLE');
		$this->assign('type','audit');
		$this->display();
    }
           
    /**
     * 下载过的音乐
     */
    public function down(){
    	$map['uid'] = UID;
    	$ids = M('UserDown')->where(array('uid'=>UID))->field('music_id')->select();
    	if(!empty($ids)){
	    	$ids = array_column($ids, 'music_id');
	    	$map['id'] = array('in',$ids);
	    	$list = $this->lists('Songs',$map,'id desc','id,name,up_uid,up_uname,artist_id,artist_name,album_id,album_name,genre_name,genre_id,listens,rater,add_time');//获取歌曲数据集
	    	$this->assign('list',$list);
    	}
    	$this->meat_title = '我的下载 - '.C('WEB_SITE_TITLE');
		$this->assign('type','down');		
		$this->display();
    }
    
    
    //试听记录
    public function listen() {
	    $ListenRecord = cookie('ListenRecord');
		//dump($ListenRecord);
	    if(!empty($ListenRecord)){//判断是否有试听记录
		    $map['id']  = array('in',$ListenRecord);
    		$list = $this->lists('Songs',$map,'id desc','id,name,up_uid,up_uname,artist_id,artist_name,album_id,album_name,genre_name,genre_id,listens,rater,add_time');//获取歌曲数据集
    		$this->assign('list', $list);
    	}    	
    	$this->meat_title = '试听记录 - '.C('WEB_SITE_TITLE');
    	$this->assign('type', 'listen');
    	$this->display();
    }
    
    /**
     * 用户上传音乐
    */
    public function share($step=1){
    	   	
		$User = M("Songs");
		$time = strtotime(date("Y-m-d"));//获取0点的时间戳
		$map['up_uid'] = UID;
		$map['add_time'] = array('gt',$time);		
		$share_num= intval(C('ADD_SONG_NUM'));
		$up_err_num= intval(C('USER_UP_ERROR'));
		if(!$share_num){
			 $this->error('功能暂时关闭！'); 
		}
		$upnum = session('user_{UID}_upnum');
		if($upnum >= $up_err_num){
            $this->error('频繁上传，系统已锁，请24小时后再次上传！');
        }
		$count = $User->where($map)->count();
		if(intval($count) >= $share_num){
			 $this->error('每天只允许分享'.$share_num.'首歌曲'); 
		}		
		if(IS_POST){
            $Songs = D('Songs');     
            if($data = $Songs->create()){
            	$data['music_down'] = $data['music_url'];
                if($Songs->add($data)){
                	//删除锁
                	session('user_{UID}_upnum',null);
                	//发送审核提醒
                	$title = '歌曲待审核';
					$content = '用户'.$data[up_name].'上传了音乐'.$data['name'];
					$msg = D('Common/Message');						
					$msg->sendMsg(0,$title,$content,$type='app');
                    $this->success('分享成功，请等待审核...',U('Music/index')); 
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Songs->getError());
            }
		}else{
			$user_ip =   ip2long(get_client_ip());
			$this->assign('step',2);
			$this->assign('genreList',get_genre_tree());
			$this->meat_title = '分享音乐 - '.C('WEB_SITE_TITLE');
			$this->display();	
		}
		
    }    
    
      
    /**
     * 删除
     */     
     public function del(){
     	$id=I("id");	//用户提交的
     	$type=I("type");
    	if($id && IS_AJAX && UID)	{ 
    		if('listen' !=$type ){   	
	        	$fav= M("UserMusic"); // 实例化User对象
	        	//$map['model_id'] = 4;  //用户音乐 1-上传 ，2-下载，3-收藏，6-创建专辑
	        	$map['uid']  = UID;
	        	$map['music_id']  = $id;
	        	$map['model_id']  = array('in','1,2,3');	
	        	$data = $fav->where($map)->delete();
	        }else{
	        	$Listen = cookie('ListenRecord');
				foreach ($Listen as $key=>$value){
				    if ($value != $id) $arr[] = $value;
				}
	        	cookie('ListenRecord',$arr);        	 
	        	$data = true;
	        }
        	//dump($data);
        	if ($data){
        		$ajax['status']  = 1;
        		$ajax['info'] = "成功移除！";
        	}else{    		    		
    			$ajax['status']  = 0;
        		$ajax['info'] = "移除失败！";    			
    		}
    		$this->ajaxReturn($ajax);
		}else{ 
			$this->error('非法参数');
		}
    }
}