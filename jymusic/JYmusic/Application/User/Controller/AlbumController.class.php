<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
class AlbumController extends UserController {
    /**
	* 用户专辑
	*/    
    public function index(){
		$map['up_uid']  = UID;
    	$list = $this->lists('Album',$map,'id desc','');//获取歌曲数据集
    	$this->assign('list', $list);
    	$this->meat_title = '我的专辑- '.C('WEB_SITE_TITLE');
    	$this->assign('type', 'index');
		$this->display();
    }
    
   /**
     * 收藏的专辑
   */
    public function fav(){
    	$map['uid']  = UID;
    	$map['model_id']  = 4;//用户音乐 1-上传 ，2-下载，3-歌曲收藏，，4-专辑收藏，5-艺术家收藏，6-创建专辑
    	$id = M('UserMusic')->where($map)->getField('music_id',true);//获取收藏音乐ID
    	if(!empty($id)){
    		$map['id']  = array('in',$id);
    		$list = $this->lists('Album',$map,'id desc','');//获取歌曲数据集
    		$this->assign('list', $list);
    	}
    	$this->meat_title = '我的收藏 - '.C('WEB_SITE_TITLE');
    	$this->assign('type', 'fav');
		$this->display('index');
    }
    
	/**
	* 用户添加专辑
	*/
    public function create(){
		$User = M("UserMusic");
		$time = strtotime(date("Y-m-d"));//获取0点的时间戳
		$map['uid'] = UID;
		$map['up_time'] = array('gt',$time);
		$map['model_id'] = 6;  //用户音乐 1-上传 ，2-下载，3-歌曲收藏，，4-专辑收藏，5-艺术家收藏，6-创建专辑
		$count = $User->where($map)->count();
		$upnum= C('MAKE_ALBUM_NUM');		
		if(intval($count) >= intval($upnum)){
			 $this->error('每天只允许添加'.$upnum.'张专辑'); 
		}else{
			if(IS_POST){
	            $Album = D('Album');
	            $data = $Album->create();
	            if($data){
	            	//$data['status'] = '-1';
	                $id = $Album->add();
	                if($id){
	                	$map['model_id'] = 6;  //用户音乐 1-上传 ，2-下载，3-收藏，6-创建专辑	
	                	$up['file_id'] = I('post.file_id'); 
	                	$up['uid'] = UID; 
	                	$up['nickname'] = get_nickname(UID); 
	                	$up['album_id'] = $id; 
	                	$up['album_name'] = $data['name'];
	                	$up['album_pic'] = $data['album_pic'];
	                	$map['status'] = -1;
	                	$up['create_time'] = NOW_TIME;
	                	$User->add($up);//添加上传记录           	
	                    $this->success('新增成功',U('Index/index'));             
	                } else {
	                    $this->error('新增失败');
	                }
	            } else {
	                $this->error($Album->getError());
	            }
			}else{
				$this->meat_title = '添加音乐 - '.C('WEB_SITE_TITLE');
				$this->display();			
			}
		}
    }
}