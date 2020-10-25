<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Addons\Ftp\Controller;
use Admin\Controller\AddonsController;

class FtpController extends AddonsController{
	/* 扫描歌曲 */
	public function scan(){		
		$path = I('post.path');
		$exts=I('post.exts');	
		$config = extract($this->getConfig());
		$conn_id = @ftp_connect(trim($host),$port) or $this->error("FTP服务器连接失败");
       	@ftp_login($conn_id,$username,$password) or $this->error("FTP服务器登陆失败");
        @ftp_pasv($conn_id,1); // 打开被动模式 
    	$path = ftp_pwd($conn_id)  . str_replace('/', "", $path). '/';   
        if(!@ftp_chdir($conn_id, $path)){
            $this->error( '扫描目录不存在！');
            return false;
        } 	
    	$exts = explode(',', $exts);
       	$list = ftp_nlist($conn_id,$path); 
       	ftp_close($conn_id);
        for ($i = 0; $i < count($list); $i++) {
       		$ext = substr($list[$i],-4);
       		if (in_array(strtolower($ext),$exts)){
       	 	 	$musiclist[] = array(
       	 	 	'id'=>$i,
       	 	 	'name'=>str_replace($path, "", $list[$i]),
       	 	 	);
       		}
        }
        if(!empty($musiclist)){//只存在音乐文件
        	F('FtpMusicList',$musiclist);//缓存音乐文件
 			if (substr($server, -1) == '/' ){
       			$ftpConfig['ftpserver']=rtrim($server, "/");
       		}else{
				$ftpConfig['ftpserver']=$server;
			}
        	if (substr($path, -1) == '/' ){
       			$ftpConfig['ftppath']=$path;
       		}else{
       			$ftpConfig['ftppath']=$path.'/';
       		}
       		F('ftpConfig',$ftpConfig);//缓存配置
    		$data['info'] = '此目录下共扫描到音乐['.count($musiclist).']首';
       		$data['status'] = 1;
       		$data['url'] = addons_url('Ftp://Ftp/fileList');            	
    	}else{            		
    		$this->error ( "没有扫描到音乐数据");
    	}   	
    	$this->ajaxReturn($data);   
		
		//$this->display(T('Addons://Ftp@Ftp/scan'));		
	}
	
	public function fileList(){
		$lock = "./Uploads/Music/storage.lock";            
        if(is_file($lock)){unlink($lock);}
		$list = F('FtpMusicList');
		$this->assign('list',$list);
		$this->assign('meta_title','Ftp服务器文件列表');
		$this->display(T('Addons://Ftp@Ftp/fileList'));
	}
	
	
	public function storage () {
		header("Content-Type:text/html;charset=UTF-8");
		$tables = I('post.tables'); $id = I('get.id');  $start =I('get.start');			 
    	if(IS_POST && is_array($tables)){ //初始化
    		$data = I('post.');
    		$data['status'] = '1';
			$data['up_uname'] = get_nickname($data['up_uid']);
			$data['genre_name'] = get_genre_name($data['genre_id']);
			$tab = array('id' => 0, 'start' => 0); 
    		session('post_storage_data',$data);
    		 //检查是否有正在执行的任务   realpath
            $lock = "./Uploads/Music/storage.lock";            
            if(is_file($lock)){
                $this->error('检测到有一个导入任务正在执行，请稍后再试！');
            } else {
                //创建锁文件
                file_put_contents($lock, NOW_TIME);
            }
			$fileList = F('FtpMusicList');
            session('storage_tables', $tables);
            $this->success('初始化成功！', '', array('tables' => $tables, 'tab' => $tab));
    	}elseif (IS_GET && is_numeric($id) && is_numeric($start)) { //导入数据   		
			$tables = session('storage_tables');		
			$listkey = $tables[$id];
			if(isset($listkey)){
				$fileList = F('FtpMusicList');
				$ftpConfig = F('ftpConfig');//缓存配置
				ksort($fileList);
				$data = session('post_storage_data');
				$data['add_time'] = $data['update_time']  = NOW_TIME;
				$data['listens'] = setrand($data['listens']);
				$data['download'] = setrand($data['download']);
				$index = '';
				$music = '';				
				foreach($fileList as $key=>$val){
					if ($val['id'] == $listkey){
						$music = $val['name'];
						$index = $key;
						break;
					}					
				}
				$data['name'] = substr(file_name_convert($music),0,-4);//获取名称
				$server = $ftpConfig['ftpserver'];					
				$data['server'] = $server;
				$data['music_url'] = $ftpConfig['ftppath'].$music;
				if (!empty($data['down_dir'])){
					$data['music_down'] = $data['down_dir'].$music;
				}else{
					$data['music_down']  = $data['music_url'];
				}
				$Songs = M('Songs');
				$data = $Songs->create($data);
				$map['uid'] =$data['up_uid'];	
				if($Songs->add($data)){					
					M("Member")->where($map)->setInc('songs',1);//增加上传歌曲数量
					if (count($fileList) > 1){
						unset($fileList[$index]);
						F('FtpMusicList',$fileList);	
					}else{
						F('FtpMusicList',null);
						F('ftpConfig',null);
					}		
                	$tab = array('id' => ++$id, 'start' => 0);
                	$this->success('入库完成！', '', array('tab' => $tab));
                }else{
                	$this->error('导入失败！');
                }
            } else { //清空缓存
  				unlink('./Uploads/Music/storage.lock');  //删除锁文件            	
            	session('storage_tables', null);
            	session('post_storage_data',null);         	
                $this->success('入库完成',1);
            }    			
    	
    	}else { //出错
            $this->error('参数错误！');
        }			
	}
	
	public function checkList () {
		$config = extract($this->getConfig());
		$login= ftp_connect(trim($host), trim($port), trim($timeout));//登录服务器
		$list = F('FtpMusicList');
		if(!empty($list) && $login){
			$data['status'] = '1';
		}else{
			$data['status'] = '0';
		}
		$this->ajaxReturn($data);
	}
	
	protected function getConfig(){
		$setting = M('Addons')->where(array('name' => 'Ftp'))->getField('config');
		return json_decode($setting, true);		
	}

}
