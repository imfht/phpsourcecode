<?php

namespace Home\Controller;
use Addons\Qiniu\QiniuAddon;
use Think\Upload;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends HomeController {

    /* 文件上传 */
    public function upload(){
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
		 $qiniuconfig=json_decode(M('addons')->where(array('name'=>'Qiniu'))->getField('config'),true);     	
                	
         if($qiniuconfig['open']==1){
         	$addon = new QiniuAddon();
            $return = $addon->upload($_FILES);
            $exts=explode('.', strrev($_FILES['download']['name']));
            if(in_array(strtolower(strrev($exts[0])), array('jpg','gif','png','jpeg'))){
            	$return['ext']=	3;
            }else{
            	$return['ext']=	4;
            }
            
            
         	$this->ajaxReturn($return);
         }else{
         $exts=explode('.', strrev($_FILES['download']['name']));
		
		if(in_array(strtolower(strrev($exts[0])), array('jpg','gif','png','jpeg'))){
		$return['ext']=	1;
        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
        $info = $Picture->upload(
            $_FILES,
            C('PICTURE_UPLOAD'),
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
        ); //TODO:上传到远程服务器

        
        
        /* 记录图片信息 */
        if($info){
            $return['status'] = 1;
           
            $info['download']['path']=(is_ssl()?'https://':'http://').$_SERVER['HTTP_HOST'].'/'.C('WEB_DIR').$info['download']['path'];
            
           // $info['Picture']['path']=$GLOBALS['_root'].$info['Picture']['path'];
            $return = array_merge($info['download'], $return);
            $return['info'] =$info[0]['path'];
          //  $return['info'] =$_FILES['download']['name'];
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }
        
        
      
     
        $this->ajaxReturn($return);
		}else{
			
		$return['ext']=	2;	
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
        	$return['status'] = 1;
            $return['data'] = U('File/download',array('id'=>think_encrypt($info['download']['id'])));
            $return['info'] = $info['download']['id'];
        } else {
            $return['status'] = 0;
            $return['info']   = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return,'JSON');
			
			
		}
         }
		
		
		
		
		
		
    }

    /* 下载文件 */
    public function download($id = null){
    	
    	$id=think_decrypt($id);
    	
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误！');
        }
        //     	
                	
         if(I('qn')==1){
         	$qiniuconfig=json_decode(M('addons')->where(array('name'=>'Qiniu'))->getField('config'),true); 
         	$addon = new QiniuAddon();
         	
         	$qn=M('Qiniu')->where(array('id'=>$id))->find();
         	M('Qiniu')->where(array('id'=>$id))->setInc('download');
         /* 执行下载 */ //TODO: 大文件断点续传
			// $filename=$fileurl;
         header("Content-Description: File Transfer");
			header('Content-type: ' . $qn['mime']);
			header('Content-Length:' . $qn['size']);
			if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
				header('Content-Disposition: attachment; filename="' . rawurlencode($qn['name']) . '"');
			} else {
				header('Content-Disposition: attachment; filename="' . $qn['name'] . '"');
			}

			if($qiniuconfig['global']==1){
                $size=readfile($qn['url']); 
			}else{
				$url=$addon->Qiniu_Sign($qn['url']);
				$size=readfile($url); 
			}
			
			exit;
			//return $url;
		//	exit;
			
			
         	
         }else{
          if(!D('File')->download(C('DOWNLOAD_UPLOAD.rootPath'),$id)){
            $this->error(D('File')->getError());
        }
         }
        
       

    }
    
  
}
