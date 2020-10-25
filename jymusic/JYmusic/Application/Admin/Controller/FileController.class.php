<?php
namespace Admin\Controller;
use JYmusic\HttpDownload;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends AdminController {
	     /**
     * 上传歌曲
     * 
     */
    public function uploadMusic(){

        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $file= D('File');
        $music_driver = C('MUSIC_UPLOAD_DRIVER');        
		$adminCon = C('MUSIC_UPLOAD');
		$adminCon['rootPath'] = trim(C('ADMIN_UPMUSIC_PATH'));
		$adminCon['maxSize'] = trim(C('ADMIN_UPMUSIC_MAX'));
		$adminCon['exts'] = trim(C('ADMIN_UPMUSIC_EXTS'));
		$info = $file->upload(
            $_FILES,
            $adminCon,
            C('MUSIC_UPLOAD_DRIVER'),
            C("UPLOAD_{$music_driver}_CONFIG")
        ); //TODO:上传到远程服务器
				
        /* 记录信息 */
        if($info){
        	$return = array_merge($info['user_file'], $return);
	        	if($return['ishave']){
	        		$return['status'] = 0;
	           		$return['info']   = '文件['.$return['name'].']已存在';	           		
	           	}else{
					$return['status'] = 1;
	           	}
        } else {
            $return['status'] = 0;
            $return['info']   = $file->getError();
        }		
        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /* 文件上传 */
    public function upload(){
		$return  = array('status' => 1, 'info' => '上传成功!', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		);

        /* 记录附件信息 */
        if($info){
            $return['data'] = think_encrypt(json_encode($info['download']));
            $return['info'] = $info['download']['name'];
        } else {
            $return['status'] = 0;
            $return['info']   = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /* 下载文件 */
    public function download($id = null){
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误！');
        }

        $logic = D('Download', 'Logic');
        if(!$logic->download($id)){
            $this->error($logic->getError());
        }

    }

    /**
     * 上传图片
     * @author huajie <banhuajie@163.com>
     */
    public function uploadPicture(){
        //TODO: 用户登录检测
		$type = I('get.type');
        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
       	$adminCon = C('PICTURE_UPLOAD');
		$adminCon['rootPath'] = trim(C('ADMIN_UPPIC_PATH'));
		$adminCon['maxSize'] = trim(C('ADMIN_UPPIC_MAX'));
		$adminCon['exts'] = trim(C('ADMIN_UPPIC_EXTS'));
        $info = $Picture->upload(
            $_FILES,
            $adminCon,
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
        ); //TODO:上传到远程服务器
        /* 记录图片信息 */ 
           
        if($info){
        	$return['status'] = 1;
        	$return = array_merge($info['user_file'], $return);
        	if (empty($return['url']) && !empty($type)){
        		if ($type == 'song'){
        			$size = trim(C('SONG_COVER_SIZE'));
        		}elseif($type == 'artist'){
        			$size = trim(C('ARTIST_COVER_SIZE'));
        		}elseif($type == 'album'){
        			$size = trim(C('ALBUM_COVER_SIZE'));
        		}elseif($type == 'genre'){
        			$size = trim(C('GENRE_COVER_SIZE'));
        		}
        		$img = '.'.$return['path'];
        		$size = explode(",",$size);
        		$image = new \Think\Image(); 
        		$image->open($img);// 生成一个固定大小为150*150的缩略图并保存为thumb.jpg
        		$image->thumb($size['0'],$size['1'],\Think\Image::IMAGE_THUMB_FIXED)->save($img);
        	}            
           
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }
        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }
    
    
    /*下载远程音乐文件*/
   
    public function serverDown() {
        $uuid = 'download';
        if (I('get.type') == 'percent') {//获取下载进度
            //show_json($_SESSION[$uuid]);
            if (isset($_SESSION['download'])){
                $info = $_SESSION['download'];
                $result = array(
                    'length'    => (int)$info['length'],
                    'size'      => (int)filesize($info['path']),
                    'time'      => mtime()
                );
            	$this->ajaxReturn($result);
            }else{
                $this->error('文件下载失败');
            }
        }

        //下载
        $save_path = trim(C('ADMIN_UPMUSIC_PATH')).date('Y-m-d').'/';
        if(!is_dir($save_path))  {
     		if(!mkdir($save_path)) $this->error('目录创建失败');
     	}
        if (!is_writeable($save_path)) $this->error('目录不可写');
        $url = rawurldecode(I('get.url'));
        if (!empty($url)){
        	$header = url_header($url);
        	if (!$header) $this->error('文件下载失败，请检查链接是否正确！');
        }else{
        	$this->error('请正确填写下载地址！');
        }
        $save_file_path = $save_path.urldecode($header['name']);
        session_start();
        $_SESSION['download'] = array('length'=>$header['length'],'path'=>$save_file_path);
        session_write_close();
        if (file_download($url,$save_file_path)){ 
        	$data['name'] = file_name_convert($header['name']);
        	$oldFile = $save_path.$data['name'];
        	$data['md5']  = md5_file($oldFile);
	        $data['sha1'] = sha1_file($oldFile);       	     	      
        	$File = M('File');    	
        	/* 查找文件 */
        	$map = array('md5' => $data['md5'],'sha1'=>$data['sha1'],);
        	$Filedata =  $File->field(true)->where($map)->find();
        	if(!empty($Filedata)){
        		unlink($oldFile); //删除上传文件
        		session('download',null);
        		$this->error('文件['.$data['name'].']已存在，所在目录：'.$Filedata['savepath'].$Filedata['savename']);
        	}else{        			        		        	
	        	$data['ext']  = pathinfo($header['name'], PATHINFO_EXTENSION);
	        	$data['savename'] = uniqid().'.'.$data['ext'];
	        	$newfile = $save_path.$data['savename'];
	        	rename($oldFile,$newfile);
	        	$data['size']  = $_SESSION['download']['length']; 
	        	$data['savepath'] = $save_path;
	        	$finfo    = finfo_open(FILEINFO_MIME);
				$data['mime'] = finfo_file($finfo, $newfile);
				finfo_close($finfo);    	
	        	/* 记录文件信息 */	        	      	
	            if($File->create($data) && ($id = $File ->add())){
	                  $return['id'] = $id;
	            } else {
	                 //TODO: 文件上传成功，但是记录文件信息失败，需记录日志
	                 unset($data);
	                 $return['id'] = 0;
	            }
        	}
            session('download',null);
            $return['status'] = 1;
        	$return['info'] = '文件下载成功';
        	$return['save_path'] = __ROOT__.substr($newfile,1);
            $this->ajaxReturn($return);
        }else{
            $this->error('文件下载失败');
        }
    }
    


}
