<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Home\Controller;
use Think\Controller;
/**
 * 前台音乐数据处理
 */
class MusicController extends HomeController {
	
	public function detail() {
		//单个歌曲显示页
		$id=intval(I('id'));		
		$Songs =  M('Songs'); 
		if ($id){
			$map['status']=1;
			$map['id']=$id;
			//数据中查询指定的ID记录,
			$music=$Songs->where($map)->field(true)->find();
			if(!empty($music)){
				$user=M('Member')->where(array('status'=>1,'uid'=>$music['up_uid']))->field('uid,nickname,pic_id,songs,albums,follows,fans')->find();
				$this->assign('data',$music);
				$this->assign('user',$user);		
				$title = !empty($music['title'])? $music['title'] : $music['name'].'在线试听';
				$this->title = $title;
		    	$this->meat_title = $title.' - '.C('WEB_SITE_TITLE');
		    	$this->meat_keywords = !empty($music['keywords'])? $music['keywords'] : C('WEB_SITE_KEYWORD');
	       		$this->meat_description = !empty($music['description'])? $music['description'] : C('WEB_SITE_DESCRIPTION');
		    	$this->display();
		    }else{
		    	$this->error('你访问的页面不存在！');
		    }
    	}else{
    		$this->error('页面出错');
    	}
		
	}
    //获取音乐数据
    public function getlistData(){
		$id= intval(I("id"));	//用户提交的i
    	if (IS_AJAX && $id) {
    		$Songs = M('Songs');
			if (strpos($id,',')) {
				$map['id']=array('exp','IN('.$id.')');
			}else {
				$map['id']=(int)$id;						
			}
			$list=$Songs->field('up_name,up_id,description,music_down',true)->where($map)->select();	
			$Songs->where($map)->setInc('listens'); // 试听数加1	
			$this->ajaxReturn($list);
		}else{
			$this->show('页面出错');
		}
    }
    public function getData(){	
    	$id= intval(I("id"));	//用户提交的id
    	$type=I("type");	//用户提交的id			
    	if (IS_AJAX) {
    		$Songs = M('Songs');
    		if ($type=='setInc' && $id){
    			$map['id']=$id;
    			$Songs->where($map)->setInc('listens'); // 试听数加1;
    		}elseif ($id) {
				$map['id']=$id;				
				$list=$Songs->field('id,name,lrc,music_url,artist_id,artist_name,album_id,album_name,cover_url,up_uid,up_uname')->where($map)->find();
				$list['music_url'] = html_entity_decode($list['music_url']);	
				$img = './Uploads/BoWen/bw_'.$id.'.png';
    			if(file_exists($img)) {//存在
    				$list['isbowen'] = 1;
    			}else{
    				$list['isbowen'] = 0;
    			}
				$Songs->where($map)->setInc('listens'); // 试听数加1 
			}
			//记录临时试听记录
			$ListenRecord = cookie('ListenRecord');
			//只记录20条数据
			if((count($ListenRecord)) >= 20){$ListenRecord = array_splice($ListenRecord,1);}
			$ListenRecord[] = $id;	
			cookie('ListenRecord',array_unique($ListenRecord),30*24*3600);						
			//登录后记录试听
			/*if ($uid = is_login()){
				    $listen = M('UserListen');
		        	$map['uid']  = $uid;
		        	$map['music_id'] = $id ;
		        	$val = $listen->where($map)->find();
		        	if(!$val) {	    		
			        	$map['uname'] = get_nickname($uid );
			        	$map['music_name'] = $list['name'];  
			        	$map['create_time'] = NOW_TIME;			        	
			        	$listen->add($map);
		        	}else{        		
		        		$listen-> where($map)->setField('create_time',NOW_TIME);        		
		        	}
			}*/
			//$list['music_url']=urlencode($list['music_url']);
			$this->ajaxReturn($list);
		}else{
			$this->show('页面出错');
		}
    }
    
    //获取专辑音乐数据
    public function albumSongs(){
		$id= intval(I("id"));	//用户提交的id
    	if (IS_AJAX && $id) {					
			$this->ajaxReturn(get_Album_songs($id));
		}else{
			$this->show('页面出错');
		}
    }
    
    //获取音乐数据
    public function getTopMusic(){				
    	if (IS_AJAX) {
    		$map['position'] = 1;
			$list=M('Songs')->where($map)->field('id,name,artist_name,music_url,rater')->limit('20')->order('add_time desc')->select();
			$this->ajaxReturn($list);
		}else{
			$this->show('页面出错');
		}
    }
    
    
    //评分
    public  function rater () {
    	$post= I('post.');
    	if (IS_AJAX && $post['action'] == 'rating') {    		
    		$id = $post['idBox']; 
    		$rate = intval($post['rate']);
    		$S = M('songs');
    		$rat = $S->getFieldById($id,'rater');//获取评分
    		$data['id'] = $id ;
    		$data['rater'] = round(($rate+intval($rat))/2);
    		//写入评分
    		if($S->save($data)){
    			$this->success('评分成功,谢谢支持！');
    		}else{
    			$this->error('评分失败');
    		}
    	}
    }   
    
    //收藏音乐数据
    public function addFav(){
    	$sid 	= intval(I('id'));
    	$type 	= I('type');
    	$type = !empty($type)? $type : 'song';
    	// 获取当前用户ID
	    $uid      = is_login();
	    if( !$uid ){// 还没登录 
	         $data['info']   =   '请登录后再操作！'; // 提示信息内容
	         $data['status'] =   2;  // 状态 如果是success是1 error 是0 ,2没有登录
	         $data['url']    =   U('Member/login'); //跳转地址
	         $this->ajaxReturn($data);
	    }elseif ( IS_AJAX && $sid && $type){    		
    		if('song' == $type){//收藏歌曲
    		 	$map['type'] = 'song';
    		 	$table = M('Songs');
    		 	$info = '收藏了歌曲';
    		}elseif('album' == $type){//收藏专辑
    		 	$map['type'] = 'album';
    		 	$table = M('Album');
    		 	$info = '收藏了专辑';
    		}elseif('artist' == $type){//收藏艺术家
    		 	$map['type'] = 'artist';
    		 	$table = M('Artist');
    		 	$info = '收藏了艺术家';
    		}else{
    			$this->error('参数错误！');
    		}   			    	        	
        	$fav = M('UserFav');
        	$map['uid']  = $uid;
        	$map['music_id'] = $sid ;
        	$val = $fav->where($map)->find();
        	if(!$val) {	    		
	        	$map['uname'] = get_nickname($uid );
	        	$map['music_name'] = $table->getFieldById($sid ,'name');  
	        	$map['create_time'] = NOW_TIME;			        	
	        	$fav->add($map);
	        	$table ->where(array('id'=>$sid))->setInc('favtimes'); //增加收藏次数
	        	$this->success('成功'.$info .'-'.$map['music_name']);
        	}else{        		
        		$this->error('已收藏！');	        		
        	}
    	}else{
			$this->show('页面出错！');
		}
    }

	//删除收藏音乐数据
    public function delFav(){
    	$sid 	= intval(I('id'));
    	$type 	= I('type');
    	$type = !empty($type)? $type : 'song';
    	// 获取当前用户ID
	    $uid      = is_login();
	    if( !$uid ){// 还没登录 
	         $data['info']   =   '请登录后再操作！'; // 提示信息内容
	         $data['status'] =   2;  // 状态 如果是success是1 error 是0 ,2没有登录
	         $data['url']    =   U('Member/login'); //跳转地址
	         $this->ajaxReturn($data);
	    }elseif ( IS_AJAX && $sid && $type){    		
    		if('song' == $type){//收藏歌曲
    		 	$map['type'] = 'song';
    		 	$table = M('Songs');
    		}elseif('album' == $type){//收藏专辑
    		 	$map['type'] = 'album';
    		 	$table = M('Album');
    		}elseif('artist' == $type){//收藏艺术家
    		 	$map['type'] = 'artist';
    		 	$table = M('Artist');
    		}else{
    			$this->error('参数错误！');
    		}  			    	        	
        	$fav = M('UserFav');
        	$map['uid']  = $uid;
        	$map['music_id'] = $sid ;
        	$val = $fav->where($map)->delete();
        	if($val) {	
	        	$table ->where(array('id'=>$sid))->setDec('favtimes'); //减少收藏次数
	        	$this->success('成功移除收藏');
        	}else{        		
        		$this->error('执行操作失败');	        		
        	}
    	}else{
			$this->error('参数错误');
		}
    }
  
   	
	
	//推荐音乐数据
    public function addRecommend(){
    	$sid 	= intval(I('id'));
    	$type 	= I('type');
    	$type = !empty($type)? $type : 'song';
    	// 获取当前用户ID
	    $uid      = is_login();
	    if( !$uid ){// 还没登录 
	         $data['info']   =   '请登录后再操作！'; // 提示信息内容
	         $data['status'] =   2;  // 状态 如果是success是1 error 是0 ,2没有登录
	         $data['url']    =   U('Member/login'); //跳转地址
	         $this->ajaxReturn($data);
	    }elseif ( IS_AJAX && $sid && $type){    		
    		if('song' == $type){//收藏歌曲
    		 	$map['type'] = 'song';
    		 	$table = M('Songs');
    		 	$info = '推荐了音乐';
    		}elseif('album' == $type){//收藏专辑
    		 	$map['type'] = 'album';
    		 	$table = M('Album');
    		 	$info = '推荐了专辑';
    		}elseif('artist' == $type){//收藏艺术家
    		 	$map['type'] = 'artist';
    		 	$table = M('Artist');
    		 	$info = '推荐了艺术家';
    		}else{
    			$this->error('参数错误！');
    		}   			    	        	
        	$Rec = M('UserRecommend');
        	$map['uid']  = $uid;
        	$map['music_id'] = $sid ;
        	$val = $Rec->where($map)->find();
        	if(!$val) {	    		
	        	$map['uname'] = get_nickname($uid );
	        	$map['music_name'] = $table->getFieldById($sid ,'name');  
	        	$map['create_time'] = NOW_TIME;			        	
	        	$Rec->add($map);
	        	$table ->where(array('id'=>$sid))->setInc('recommend'); //增加推荐次数
	        	$this->success('成功'.$info .'-'.$val['music_name']);
        	}else{        		
        		$this->error('你已推荐过');	        		
        	}
    	}else{
			$this->show('页面出错');
		}

    }
    
	//移除推荐音乐数据
    public function delRecommend(){
    	$sid 	= intval(I('id'));
    	$type 	= I('type');
    	$type = !empty($type)? $type : 'song';
    	// 获取当前用户ID
	    $uid      = is_login();
	    if( !$uid ){// 还没登录 
	         $data['info']   =   '请登录后再操作！'; // 提示信息内容
	         $data['status'] =   2;  // 状态 如果是success是1 error 是0 ,2没有登录
	         $data['url']    =   U('Member/login'); //跳转地址
	         $this->ajaxReturn($data);
	    }elseif ( IS_AJAX && $sid && $type){    		
    		if('song' == $type){
    		 	$map['type'] = 'song';
    		 	$table = M('Songs');
    		 	$info = '推荐了音乐';
    		}elseif('album' == $type){
    		 	$map['type'] = 'album';
    		 	$table = M('Album');
    		 	$info = '推荐了专辑';
    		}elseif('artist' == $type){
    		 	$map['type'] = 'artist';
    		 	$table = M('Artist');
    		 	$info = '推荐了艺术家';
    		}else{
    			$this->error('参数错误！');
    		}   			    	        	
        	$Rec = M('UserRecommend');
        	$map['uid']  = $uid;
        	$map['music_id'] = $sid ;
        	$val = $Rec->where($map)->delete();
        	if($val) {	     
	        	$table ->where(array('id'=>$sid))->setDec('recommend'); //减少推荐次数
	        	$this->success('移除推荐成功');
        	}else{        		
        		$this->error('执行操作失败');	        		
        	}
    	}else{
			$this->show('页面出错');
		}

    }


    //喜欢音乐
    public function addLike(){
    	$sid 	= intval(I('id'));
    	$type 	= I('type');
    	$type = !empty($type)? $type : 'song';
    	// 获取当前用户ID
	    $uid      = is_login();
	    if( !$uid ){// 还没登录 
	         $data['info']   =   '请登录后再操作！'; // 提示信息内容
	         $data['status'] =   2;  // 状态 如果是success是1 error 是0 ,2没有登录
	         $data['url']    =   U('Member/login'); //跳转地址
	         $this->ajaxReturn($data);
	    }elseif( IS_AJAX && $sid && $type){ 
    		if('song' == $type){//喜欢歌曲
    		 	$table = M('Songs');
    		 	$map['type'] = 'song';
    		 	$info = '音乐';
    		}elseif('album' == $type){//喜欢专辑
    		 	$table = M('Album');
    		 	$map['type'] = 'album';
    		 	$info = '专辑';
    		}elseif('artist' == $type){//喜欢艺术家
    		 	$table = M('Artist');
    		 	$map['type'] = 'artist';
    		 	$info = '艺术家';
    		}else{
    			$this->error('参数错误！');
    		}
    		$like = M('UserLike');
        	$map['uid']  = $uid;
        	$map['music_id'] = $sid ;
        	$val = $like->where($map)->find();
        	if(!$val) {	    		
	        	$map['uname'] = get_nickname($uid );
	        	$map['music_name'] = $table->getFieldById($sid ,'name');  
	        	$map['create_time'] = NOW_TIME;			        	
	        	$like->add($map);
    			$table ->where(array('id'=>$sid))->setInc('likes'); //增加收藏次数
    			$this->success('成功添加喜欢');
    		}else{        		
        		$this->error('已经添加喜欢');	        		
        	}
		}else{
			$this->show('页面出错');
		}	
	}

	//移除喜欢音乐
    public function delLike(){
    	$sid 	= intval(I('id'));
    	$type 	= I('type');
    	$type = !empty($type)? $type : 'song';;
    	// 获取当前用户ID
	    $uid      = is_login();
	    if( !$uid ){// 还没登录 
	         $data['info']   =   '请登录后再操作！'; // 提示信息内容
	         $data['status'] =   2;  // 状态 如果是success是1 error 是0 ,2没有登录
	         $data['url']    =   U('Member/login'); //跳转地址
	         $this->ajaxReturn($data);
	    }elseif( IS_AJAX && $sid && $type){ 
    		if('song' == $type){//喜欢歌曲
    		 	$table = M('Songs');
    		 	$map['type'] = 'song';
    		 	$info = '音乐';
    		}elseif('album' == $type){//喜欢专辑
    		 	$table = M('Album');
    		 	$map['type'] = 'album';
    		 	$info = '专辑';
    		}elseif('artist' == $type){//喜欢艺术家
    		 	$table = M('Artist');
    		 	$map['type'] = 'artist';
    		 	$info = '艺术家';
    		}else{
    			$this->error('参数错误！');
    		}
    		$like = M('UserLike');
        	$map['uid']  = $uid;
        	$map['music_id'] = $sid ;
        	$val = $like->where($map)->delete();
        	if($val) {
    			$table ->where(array('id'=>$sid))->setDec('likes'); //增加收藏次数
    			$this->success('成功移除喜欢');
    		}else{        		
        		$this->error('执行操作失败');        		
        	}
		}else{
			$this->show('页面出错');
		}	
	}  
    
    //关注
    public function addFollow(){
    	$id 	= intval(I('id'));
    	// 获取当前用户ID
	    $uid      = is_login();
	    if( !$uid ){// 还没登录 
	         $data['info']   =   '请登录后再操作！'; // 提示信息内容
	         $data['status'] =   2;  // 状态 如果是success是1 error 是0 ,2没有登录
	         $data['url']    =   U('Member/login'); //跳转地址
	         $this->ajaxReturn($data);
	    }else{
	    
		    if ($id  == $uid){	    	
		    	$this->error('自己不能关注自己！');
		    }
		    $num = M('Member')->getFieldById($uid,'follows');//获取关注数量
		    $mix = intval(C('USER_FOLLOW_MIX'));
		   	if ($num  >= $mix){	    	
		    	$this->error('最多关注 '.$mix .'位');
		    }
		    
		    if ( IS_AJAX && $id){  			    	        	
	        	$fans = M('Fans');
	        	$map['fans_uid']  = $uid;
	        	$map['follow_uid'] = $id ;
	        	$val = $fans->where($map)->find();
	        	if(!$val) {	    		
		        	$map['fans_uname'] = get_nickname($uid );
					$map['follow_uname'] = get_nickname($id);
		        	$map['create_time'] = NOW_TIME;			        	
		        	$fans->add($map);
		        	M('Member')->where(array('uid'=>$id))->setInc('fans'); //增加粉丝数
		        	M('Member')->where(array('uid'=>$uid))->setInc('follows'); //增加关注数
		        	$content =$map['fans_uname'].'关注了你';
		        	D('Common/Message')->sendMsg($id,'粉丝数增加',$content,$type='app');
		        	$this->success('成功关注' .'-'.$map['follow_uname']);
	        	}else{        		
	        		$this->error('已关注');	        		
	        	}
	    	}else{
				$this->show('页面出错');
			}
		}
    }
    
    //移除关注
    public function delFollow(){
    	$id 	= intval(I('id'));
    	// 获取当前用户ID
	    $uid      = is_login();
	    if( !$uid ){// 还没登录 
	         $data['info']   =   '请登录后再操作！'; // 提示信息内容
	         $data['status'] =   2;  // 状态 如果是success是1 error 是0 ,2没有登录
	         $data['url']    =   U('Member/login'); //跳转地址
	         $this->ajaxReturn($data);
	    }else{
	    
		    if ($id  == $uid){	    	
		    	$this->error('自己不能取消关注自己！');
		    }		    
		    if ( IS_AJAX && $id ){  			    	        	
	        	$fans = M('Fans');
	        	$map['fans_uid']  = $uid;
	        	$map['follow_uid'] = $id ;
	        	$val = $fans->where($map)->delete();;
	        	if($val) {
		        	M('Member')->where(array('uid'=>$id))->setDec('fans'); //增加粉丝数
		        	M('Member')->where(array('uid'=>$uid))->setDec('follows'); //增加关注数
		        	$this->success('成功取消关注');
	        	}else{        		
	        		$this->error('取消关注失败');	        		
	        	}
	    	}else{
				$this->show('页面出错');
			}
		}
    	
    }
    
   //移除下载过音乐数据
    public function delDown(){
    	$sid 	= intval(I('id'));
    	// 获取当前用户ID
	    $uid      = is_login();
	    if( !$uid ){// 还没登录 
	         $data['info']   =   '请登录后再操作！'; // 提示信息内容
	         $data['status'] =   2;  // 状态 如果是success是1 error 是0 ,2没有登录
	         $data['url']    =   U('Member/login'); //跳转地址
	         $this->ajaxReturn($data);
	    }elseif ( IS_AJAX && $sid){		    	        	
        	$down = M('UserDown');
        	$map['uid']  = $uid;
        	$map['music_id'] = $sid ;
        	$val = $down->where($map)->delete();
        	if($val) {
	        	$this->success('成功移除下载音乐');
        	}else{        		
        		$this->error('执行操作失败');	        		
        	}
    	}else{
			$this->show('页面出错');
		}

    }
    
    //移除试听过音乐数据
    /*public function delisten(){
    	$sid 	= intval(I('id'));
    	// 获取当前用户ID
	    $uid      = is_login();
	    if( !$uid ){// 还没登录 
	         $data['info']   =   '请登录后再操作！'; // 提示信息内容
	         $data['status'] =   2;  // 状态 如果是success是1 error 是0 ,2没有登录
	         $data['url']    =   U('Member/login'); //跳转地址
	         $this->ajaxReturn($data);
	    }elseif ( IS_AJAX && $sid){		    	        	
        	$listen = M('UserListen');
        	$map['uid']  = $uid;
        	$map['music_id'] = $sid ;
        	$val = $listen->where($map)->delete();
        	if($val) {
	        	$this->success('成功移除试听音乐');
        	}else{        		
        		$this->error('执行操作失败');	        		
        	}
    	}else{
			$this->show('页面出错');
		}

    }*/

	//移除临时试听记录
    public function delListen() {
		$id 	= intval(I('id'));
		$id = strval($id);
    	if (IS_AJAX ){
	    	$ListenRecord = cookie('ListenRecord');
	    	foreach( $ListenRecord as $key => $v ) {
				if($id ==$v) unset($ListenRecord[$key]);				
			}
			cookie('ListenRecord',$ListenRecord);
			$this->success('执行操作成功');
    	}else{   		
    		$this->error('参数错误');
    	}
    }
    
    //获取用户音乐列表   
    public function myMusicList() {
    	$type = I('type');
    	$limit  = I('limit');
    	$limit = !empty($limit)? intval($limit) : 10;	
    	$uid  = is_login(); 
    	if( !$uid ){// 还没登录 
    		if (IS_GET) {
    			$this->show('请登录后再操作！');
    		}else{
	         	$data['info']   =   '请登录后再操作！'; // 提示信息内容
	         	$data['status'] =   2;  // 状态 如果是success是1 error 是0 ,2没有登录
	         	$data['url']    =   U('Member/login'); //跳转地址
	         	$this->ajaxReturn($data);
	    	}
	    }elseif (IS_AJAX ){    	   	    	
	    	switch ($type) { 	   		
	    		case 'like':   
	    			$Model= M('UserLike'); 
	    			$join = 'USER_LIKE';    			
	    		break;
	    		
	    		case 'recommend':   
	    			$Model= M('UserRecommend'); 
	    			$join = 'USER_RECOMMEND';    			
	    		break;    		
	    		
	    		case 'fav':   
	    			$Model= M('UserFav'); 
	    			$join = 'USER_FAV';    			
	    		break; 	
	    		
	    		case 'down':   
	    			$Model= M('UserDown'); 
	    			$join = 'USER_DOWN';    			
	    		break; 	
	    		
	    		case 'share':   
	    			$Model= M('UserUpload'); 
	    			$join = 'USER_UPLOAD';    			
	    		break; 
	    		
	    		case 'listen':   
	    			$Model= M('UserListen'); 
	    			$join = 'USER_LISTEN';    			
	    		break; 	
	    		
	    		default: $this->error('参数错误');	    	
	    	}
	    	$map['uid'] = $uid;
	    	$list = $Model->join('__SONGS__ ON __'.$join.'__.music_id = __SONGS__.id')->where($map)->order('create_time desc')->limit($limit)->field('music_id,music_name,genre_name,genre_id,artist_name,artist_id,album_name,album_id')->select();	    	
	    	if (!empty($list)){
	    		if (IS_GET) {
	    			$this->assign('list',$list);
	    			$this->display(':Ajaxget/myMusicList');
	    		}elseif (IS_POST){	    		
	    			$this->ajaxReturn($list);
	    		}
	    	}else{
	    		$this->show('暂时无记录！');
	    	}
	    }else{
			$this->show('页面出错');
		}
    }
    
    
    //评论
    public function comment () {
       	$id= intval(I("id"));	//用户提交的id      	
       	$content=I("content",'','htmlspecialchars');	//用户提交的内容
    	if (IS_AJAX && $id) {
    		// 获取当前用户ID
    		$uid  = is_login();        
	       	if(!$uid){// 还没登录 
	            $this->error('你还没有登录！',U('Member/login'));
	        }elseif($content != ''){
	        	$comment = M('Comment');
				$time = strtotime(date("Y-m-d"));//获取0点的时间戳
				$map['uid'] = $uid;
				$map['comment_time'] = array('gt',$time);
				$count = $comment->where($map)->count();
				$cnum= C('USER_COMMENT_NUM');	
	        	if(intval($count) >= intval($cnum)){ //检测评论次数
			 		$this->error('亲! ~每个用户1天只允许'.$cnum.'条评论哦！'); 
				}else{	
					//表示根据用户的name获取用户的id值。
					$name = M('Songs')->getFieldById($id,'name');        	        	
		        	//$data['name'] = $id ;
		        	$data['infos_id'] = $id ;
		        	$data['infos_name'] = $name;  
		        	$data['uid'] = $uid ; 
	            	$data['user'] = get_nickname($uid); 
	            	$data['content'] = $content;
	            	$data['user_ip']      =  get_client_ip();
	            	$data['comment_time'] = NOW_TIME;
	            	$data['status'] = 1;
	            	$cid=$comment->add($data);
	            	if($cid){
	            		M('Songs')->where(array('id'=>$id))->setInc('comment'); // 推收藏数加1
	            		$this->success('评论成功！');	            		
	            	}
	        	}
	        }else{
	        	$this->error('评论失败');	        	
	        }
	        
	    }
    
    } 
    
     //试听记录
    public function listenRecord() {
    	if (IS_AJAX ){
	    	$ListenRecord = cookie('ListenRecord');
	    	$limit  = I('limit');
    		$limit = !empty($limit)? intval($limit) : 10;	    	
	    	if(!empty($ListenRecord)){
		    	foreach ($ListenRecord as $v) {//拼接
		    	 	$str .= $v.',';
		    	} 
		    	$str = rtrim($str,',');
		    	$map['id']=array('exp','IN('.$str.')');
		    	$list=M('songs')->field('up_name,up_id,description,music_down',true)->where($map)->limit($limit)->select();	    		
	    		if (IS_GET) {
	    			$this->assign('list',$list);
	    			$this->display(':Ajaxget/listenRecord');
	    		}else{
	    			$this->ajaxReturn($list);
	    		}
	    	}else{
	    		if (IS_GET) {
	    			$this->show('暂无记录');
	    		}else{
	    			$this->error('暂无记录');
	    		}
	    	}
    	}else{   		
    		$this->error('参数错误');
    	}
    }
    
}