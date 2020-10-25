<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;
use Think\Controller;
class SongsController extends AdminController {
    public function index($status = null,$title = null){
		$Songs =   D('Songs');
        /* 查询条件初始化 */
        //$map['uid'] = UID;
        if(isset($title)){
            $map['name']   =   array('like', '%'.$title.'%');
        }
        if(isset($status)){
            $map['status']  =   $status;
        }else{
            $map['status']  =   array('in', '0,1');
        }
        if ( isset($_GET['time-start']) ) {
            $map['update_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['update_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
        $list = $this->lists($Songs	,$map,'id desc','id,name,album_name,artist_name,genre_name,listens,recommend,rater,add_time,status');
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('status', $status);
        $this->assign('list', $list);
        $this->meta_title = '歌曲管理';
        $this->display();
	}
	public function add(){
		if(IS_POST){
            $Songs = D('Songs');
            $data = $Songs->create();
            $map['uid'] =$data['up_uid'];
            if($data){
                if($id = $Songs->add()){
                	M("Member")->where($map)->setInc('songs',1);//增加上传歌曲数量
                    $this->success('新增成功');
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Songs->getError());
            }
        } else {
        	$id = C('DT_SERVER_ID');
        	if ($id){
        		$server = 	get_server($id);
        	}else{
        		$server['downPath'] = null;
				$server['listenPath'] = null;
        	}
            $this->assign('server', $server);
			$this->meta_title = '添加歌曲';
			$this->display();
        }

	}
	
	public function mod($id = 0){
        if(IS_POST){
            $Songs = D('Songs');
            $data = $Songs->create();
            if($data){
                if($Songs->save()!== false){
                    $this->success('更新成功',Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Songs->getError());
            }
        } else {
            $data = array();
            /* 获取数据 */
            $data = M('Songs')->field(true)->find($id);
            if(false === $data){
                $this->error('获取后台数据信息错误');
            }
            $this->assign('data', $data);
			$this->meta_title = '修改歌曲';
			$this->display('add');
        }
	}
	
	/**
    * 删除
    */
    public function del(){
        $id = array_unique((array)I('ids',0));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
		//dump($id);
        $map = array('id' => array('in', $id) );
        if(M('Songs')->where($map)->delete()){
            //记录行为
            //action_log('update_channel', 'channel', $id, UID);
            $data['status']  = 1;
            $data['info'] = '删除成功';
        } else {
        	$data['status']  = 0;
            $data['info'] = '删除失败！';
        }
        $this->ajaxReturn($data);
    }
    
    /**
     * 批量处理歌曲
     */
    
   	public function batch () {
   		if (IS_AJAX){
   			$id=I('id');
   			$batch_id =I('batch_id');
   			$map['id']=array('exp','IN('.$id.')');
   			if ( '1' == $batch_id ){ // 1 批量推荐
   				$data = array('recommend'=>'1');
   			}elseif('2' == $batch_id){
   				$data = array('recommend'=>'0');
   			}elseif('3' == $batch_id){
   				$data = array('position'=>1);
   			}elseif('4' == $batch_id){
   				$data = array('position'=>0);
   			}
   			$arr = str2arr($id);
   			$len=count($arr);
   			for ($i = 0; $i < $len; $i++) {
   			 	 $map['id'] = $arr[$i];
   			 	 $data['update_time'] = NOW_TIME;
   			 	 M('Songs')-> where($map)->setField($data); 
   			}
		 	$info['status']='1'; 	 	
			$this->ajaxReturn($info);
		 }else{
		 	$this->error('非法请求');
		 }
   		
    }
        
    //批量导入
    public function bulkImport ($type = null) {   
    	header("Content-Type:text/html;charset=UTF-8");	    	
    	$path= C('SONGS_IMPORT_PATH');
    	//session('fileList', null);
    	if ($type == 'refresh'){
    		session('fileList',null); 
    		$lock = session('upload_path') . 'import.lock';            
            if(is_file($lock)){ unlink($lock);}
    	}
    	$fileList=session('fileList');
    	if (!isset($fileList)){ //判断缓存列表是否存在
	    	if(is_dir($path)){       	
	            //$fs=array(array(),array(),array());
	            if(!($dh=opendir($path))) return false;         
	            while(($entry=readdir($dh))!==false){
	                if($entry!="." && $entry!=".."){
	                	//$path2 = iconv("UTF-8","gb2312",$path."/".$entry);                	
	                    if(is_dir($file= file_realpath($path."/".$entry))){
	                    	//组合二级目录
	                    	$pathName = iconv("gb2312","UTF-8",$entry);
	                    	$fileList[] = array('path'=>$file,'pathName'=>$pathName);
	                    }elseif(is_file($file)){
	                    	//组合根目录导入文件
	                        $importList[]=array('path'=>$file,'fileName'=>$entry,'dirName'=>'');
	                    }
	                }
	            }    
	            closedir($dh);
	          	if(!empty($fileList)){ //二级目录
	          		foreach ($fileList as $v) {
	          			$path2 = $v['path'];
	          			$dh2= opendir($path2);
	          			//dump($dh2);
	          			while(($entry2=readdir($dh2))!==false){
	          				if($entry2!="." && $entry2!=".."){
	          					$file2=file_realpath($path2."/".$entry2);
	          					if(is_file($file2)){
									$importList2[]=array('path'=>$file2,'fileName'=>$entry2,'dirName'=>$v['pathName']);
		          		 		}
	          				}	          			
	          			}
	          		 	closedir($dh2);	
	          		} 	          			          			          	
	          	}
	          	if (!empty($importList) && !empty($importList2)){
	          		$importList=array_merge($importList,$importList2);//合并数组;	            	
	        	}elseif(empty($importList) && !empty($importList2)) {
	        		$importList = $importList2;
	        	}
	            session('fileList',$importList);
	    	}else{
	    		$this->assign('info','系统无法获取对应目录内容！');
	    	}
	    }
		//dump(session('fileList'));
	    $this->assign('import_path',$path);
	    if(!empty($importList)){
	    	$this->assign('list',$importList);
	    }else{
			 $this->assign('info','这是一个无内容的空目录哦');
		}
		$this->meta_title = '批量导入';
		$this->display();
    }
    //批量导入
    public function fileImport ($tables = null, $id = null, $start = null) { 
    	header("Content-Type:text/html;charset=UTF-8");	
    	if(IS_POST && !empty($tables) && is_array($tables)){ //初始化
    		session('post_data',I('post.'));
            $uploadPath = trim(C('ADMIN_UPMUSIC_PATH'));
    		 //检查是否有正在执行的任务   realpath
            $lock = $uploadPath."import.lock";            
            if(is_file($lock)){
                $this->error('检测到有一个导入任务正在执行，请稍后再试！');
            } else {
                //创建锁文件
                file_put_contents($lock, NOW_TIME);
            }
            $dir = $uploadPath.date('Y-m-d'). "/";
            $tab = array('id' => 0, 'start' => 0);         
            //缓存导入路径
            session('upload_path',$uploadPath);
            session('upload_import_path',$dir);
            session('backup_tables', $tables);
            if (file_exists($dir)){
            	$this->success('初始化成功！', '', array('tables' => $tables, 'tab' => $tab));
            }else{
            	 if(mkdir($dir)){
            	 	$this->success('初始化成功！', '', array('tables' => $tables, 'tab' => $tab));
            	 }else{
            	 	$this->error('初始化失败，导入文件创建失败！');
            	 }          	
            }
    	
    	}elseif (IS_GET && is_numeric($id) && is_numeric($start)) { //导入数据
    		$tables = session('backup_tables');
			if(isset($tables[$id])){
				//$uid      = is_login();
				$fileList = session('fileList');
				$data = session('post_data');				
				$data['status'] = '1';				
				$data['up_uid'] = isset($data['up_uid'])? $data['up_uid'] : UID;
				$data['up_uname'] = get_nickname($data['up_uid']);
				$data['rater'] = setrand($data['rater']);
				$data['listens'] = setrand($data['listens']);
				$data['download'] = setrand($data['download']);
				$data['add_time'] = $data['update_time']  = NOW_TIME;
				$unid = uniqid();
				$dir  = __ROOT__.str_replace('.','',session('upload_import_path'));
				if(rename($fileList[$id]['path'],session('upload_import_path').$unid.'.mp3')){
					//处理入库
					$mname = substr(file_name_convert($fileList[$id]['fileName']),0,-4);//获取名称
					$mnames = @explode("-", $mname);
					$f = count($mnames);
					if($f <= 2){
						$data['name'] = $mname ;//获取名称
					}else{												
						//处理歌曲名称包含" - "
						$names = "";
						for($n=2;$n<$f;$n++){
							if($n==($f-1)){
								$names .= $mnames[$n];
							}else{
								$names .= $mnames[$n]." - ";
							}
						}
						$data['name'] = $names ;//获取名称
						$data["tone"] = trim($mnames[0]);
						$data["bpm"] = trim($mnames[1]);
					}
								
					$data['music_url'] = $data['music_down'] = $dir.$unid.'.mp3';
					$G = M("Genre");
					//dump($data['genre_name']);
					$genre_id = $G->getFieldByName($fileList[$id]['dirName'],'id');
					if ($genre_id){
						$data['genre_id'] = $genre_id;
					}else{
 						$gdata['name'] = $fileList[$id]['dirName'];
 						$gdata['add_time'] = $gdata['update_time']  = NOW_TIME;
						$data['genre_id'] = $G->data($gdata)->add();
					}
					$data['genre_name'] = $fileList[$id]['dirName'];
					M('Songs')->add($data);	
					M("Member")->where(array('uid'=>$data['up_uid']))->setInc('songs',1);//增加上传歌曲数量			
                	$tab = array('id' => ++$id, 'start' => 0);
                	$this->success('导入完成！', '', array('tab' => $tab));
            	}else{
            		$this->error('导入失败！');
            		//dump(session('upload_import_path'));
            	}
            } else { //清空缓存
  				unlink(session('upload_path') . 'import.lock');              
            	session('upload_import_path', null);
            	session('upload_path',null);
            	session('backup_tables', null);
            	session('post_data',null);
            	session('fileList', null);            	
                $this->success('导入完成！');
            }    			
    	
    	}else { //出错
            $this->error('参数错误！');
        }

    }
    
    //根据曲风创建目录
    public function createGenreDir () {
    	if(IS_POST){
    		$path= C('SONGS_IMPORT_PATH');
     		$list = M('Genre')->field('name')->select();
     		$info = '';
     		foreach ($list as &$v) {
     			$dir = $path.iconv('utf-8', 'gbk', $v['name']);
     			//$dir = $path.$v['name'];
     			if(!is_dir($dir))  {
     				if(!mkdir($dir)){ 
   						$info .='创建'.$v['name'].'失败!/<br>';  
  					}else{
  						//dump('创建'.$v['name'].'成功!');
  						$info .='创建'.$v['name'].'成功!/<br>'; 					
     				}
     			}
     		} 
     		$this->success($info);
     	}else{
     		$this->error('参数错误！');
     	}
    
    }
    //更改歌曲状态
    public function setStatus () {
    	    	
    	return parent::setStatus('Songs');
    	
    }
            
}