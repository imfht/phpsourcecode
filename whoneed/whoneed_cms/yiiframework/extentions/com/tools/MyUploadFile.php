<?php
/**
 * 文件上传类
 *
 * 用于文件，图片上传
 *
 * @author		黑冰(001.black.ice@gmail.com)
 * @copyright	(c) 2012
 * @version		$Id$
 * @package		com.tools
 * @since		v0.1
 */

class MyUploadFile extends CComponent
{
    static public $file;
    
    static public function uploadImg($filed_name, $timestampe = '')
    {
        if(!empty($_FILES[$filed_name]['name']) && $_FILES[$filed_name]['error'] === UPLOAD_ERR_OK)
        {
			self::$file = $_FILES[$filed_name];
            $upload = CUploadedFile::getInstanceByName($filed_name);
            if($upload)
            {
	            // 获取文件后辍名
                $ext = explode('.', self::$file['name']);
                $ext = array_pop($ext);
                
                // 过滤不是.swf、.png、.gif、.jpg、.jpeg结尾的文件
                if(!in_array($ext, array('swf', 'png', 'jpg', 'jpeg', 'gif')))
                {
                    return '';
                }
	            
                // 文件名
				if(!$timestampe) $timestampe = time();
				$timetemp = date("Ymd");
                $photo = "/upload/{$timetemp}/{$timestampe}.{$ext}";
                if(!file_exists(WEB_ROOT."/upload/{$timetemp}"))
                {
                    mkdir(WEB_ROOT."/upload/{$timetemp}");
                }
	            $upload->saveAs(WEB_ROOT."/{$photo}");            
			}
        }
        else
        {
            $photo = '';
        }
        return $photo;
    }
	
	// 上传文件
    static public function uploadFile($filed_name, $timestampe = '')
    {
		$arrR = array();

        if(!empty($_FILES[$filed_name]['name']) && $_FILES[$filed_name]['error'] === UPLOAD_ERR_OK)
        {
			self::$file = $_FILES[$filed_name];
            $upload = CUploadedFile::getInstanceByName($filed_name);
            if($upload)
            {
	            // 获取文件后辍名
                $ext = explode('.', self::$file['name']);
                $ext = array_pop($ext);
                
                // 过滤不是.swf、.png、.gif、.jpg、.jpeg结尾的文件
                if(!in_array($ext, array('pdf', 'rar', 'doc', 'docx', 'xls', 'apk')))
                {
                     return '';
                }
	            
                // 文件名
				if(!$timestampe) $timestampe = time();
				$timetemp = date("Ymd");
                $photo = "/upload/{$timetemp}/{$timestampe}.{$ext}";
                if(!file_exists(WEB_ROOT."/upload/{$timetemp}"))
                {
                    mkdir(WEB_ROOT."/upload/{$timetemp}");
                }
	            $upload->saveAs(WEB_ROOT."/{$photo}");
				
				// 文件名称
				$arrR['file_name'] = $photo;
				
				// 文件类型
				$arrR['file_type'] = $ext;

				// 文件大小
				$arrR['file_size'] = $_FILES[$filed_name]['size'];
			}
        }

        return $arrR;
    }
	
	// 图片有单独域名
    static public function uploadImgCdn($filed_name, $timestampe = '')
    {
        if(!empty($_FILES[$filed_name]['name']) && $_FILES[$filed_name]['error'] === UPLOAD_ERR_OK)
        {
			self::$file = $_FILES[$filed_name];
            $upload = CUploadedFile::getInstanceByName($filed_name);
            if($upload)
            {
	            // 获取文件后辍名
                $ext = explode('.', self::$file['name']);
                $ext = array_pop($ext);
                
                // 过滤不是.swf、.png、.gif、.jpg、.jpeg结尾的文件
                if(!in_array($ext, array('swf', 'png', 'jpg', 'jpeg', 'gif')))
                {
                     $photo = '';
                }
	            
                // 文件名
				if(!$timestampe) $timestampe = time();
				$timetemp = date("Ymd");
                $photo = "/upload/{$timetemp}/{$timestampe}.{$ext}";
                if(!file_exists(CDN_ROOT."/upload/{$timetemp}"))
                {
                    mkdir(CDN_ROOT."/upload/{$timetemp}");
                }
	            $upload->saveAs(CDN_ROOT."{$photo}");
			}
        }
        else
        {
            $photo = '';
        }
		
		/*
		// 暂时取消，为了灵活性，在显示的时候，采取拼凑的方式
		if(Yii::app()->params['img_domain'] && $photo){
			$photo = Yii::app()->params['img_domain'].$photo;
		}*/

        return $photo;
    }


	// 上传文件
    static public function uploadApkFile($filed_name, $timestampe = '')
    {
        if(!empty($_FILES[$filed_name]['name']) && $_FILES[$filed_name]['error'] === UPLOAD_ERR_OK)
        {
			self::$file = $_FILES[$filed_name];
            $upload = CUploadedFile::getInstanceByName($filed_name);
            if($upload)
            {
                /*
	            // 获取文件后辍名
                $ext = explode('.', self::$file['name']);
                $ext = array_pop($ext);
                
                // 过滤不是.swf、.png、.gif、.jpg、.jpeg结尾的文件
                if(!in_array($ext, array('apk')))
                {
                    return '';
                }*/
	            
                // 文件名
				$timetemp = date("Ymd");
                $photo = "/upload_apk/{$timetemp}/".self::$file['name'];
                if(!file_exists(CDN_ROOT."/upload_apk/{$timetemp}"))
                {
                    mkdir(CDN_ROOT."/upload_apk/{$timetemp}");
                }
	            $upload->saveAs(CDN_ROOT."/{$photo}");            
            }
        }

        return $photo;
    }

    public static function pageUploadCdnImg($filed_name, $timestampe = ''){
        if(!empty($_FILES[$filed_name]['name']) && $_FILES[$filed_name]['error'] === UPLOAD_ERR_OK)
        {
            self::$file = $_FILES[$filed_name];
            $upload = CUploadedFile::getInstanceByName($filed_name);
            if($upload)
            {
                // 获取文件后辍名
                $ext = explode('.', self::$file['name']);
                $ext = array_pop($ext);

                // 过滤不是.swf、.png、.gif、.jpg、.jpeg结尾的文件
                if(!in_array($ext, array( 'png', 'jpg', 'jpeg', 'gif')))
                {
                    exit('文件类型不合法');
                }

                // 文件名
                if(!$timestampe) $timestampe = time();
                $timetemp = date("Ymd");
                $photo = "/upload/{$timetemp}/{$timestampe}.{$ext}";
                if(!file_exists(CDN_ROOT."/upload/{$timetemp}"))
                {
                    mkdir(CDN_ROOT."/upload/{$timetemp}");
                }
                $upload->saveAs(CDN_ROOT."{$photo}");
            }
        }
        else
        {
            exit('上传文件有误');
        }

        /*
        // 暂时取消，为了灵活性，在显示的时候，采取拼凑的方式
        if(Yii::app()->params['img_domain'] && $photo){
            $photo = Yii::app()->params['img_domain'].$photo;
        }*/

        return $photo;
    }

    // 上传视频
    static public function uploadVideo($filed_name, $timestampe = '')
    {
        if(!empty($_FILES[$filed_name]['name']) && $_FILES[$filed_name]['error'] === UPLOAD_ERR_OK)
        {
			self::$file = $_FILES[$filed_name];
            $upload = CUploadedFile::getInstanceByName($filed_name);
            if($upload)
            {
                /*
	            // 获取文件后辍名
                $ext = explode('.', self::$file['name']);
                $ext = array_pop($ext);
                
                // 过滤不是.swf、.png、.gif、.jpg、.jpeg结尾的文件
                if(!in_array($ext, array('apk')))
                {
                    return '';
                }*/
	            
                // 文件名
				$timetemp = date("Ymd");
                $photo = "/upload_video/{$timetemp}/".self::$file['name'];
                if(!file_exists(CDN_ROOT."/upload_video/{$timetemp}"))
                {
                    mkdir(CDN_ROOT."/upload_video/{$timetemp}",0777,true);
                }
	            $upload->saveAs(CDN_ROOT."/{$photo}");            
            }
        }
        return $photo;
    }
}
