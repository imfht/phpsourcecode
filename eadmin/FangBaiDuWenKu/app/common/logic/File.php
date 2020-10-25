<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\logic;

use think\Image;
use Qiniu\json_decode;

/**
 * 文件处理逻辑
 */
class File extends LogicBase
{
    
    // 图片模型
    public static $pictureModel = null;

    // 文件模型
    public static $fileModel    = null;
    protected $fileext;
    protected $filesize;
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        $this->fileext=config('upload_file_ext');
        $this->filesize=config('upload_file_size');
        
        $this->pictureext=config('upload_picture_ext');
        $this->picturesize=config('upload_picture_size');
        
        //这里需要判断当前的用户属于哪个组，这个组是否有特殊的附件上传限制，大小取最小的，后缀取共有值
        
        self::$fileModel    = model($this->name);
        self::$pictureModel = model('Picture');
    }
    /**
     * 下载方法
     */
    public function download($url,$name,$local)
    {
    	
    	$down = new \org\Http();
    	
    	if($local==1){
    		
    	
    		$down->download($url,$name);
    			
    	
    		
    	}else{
    
    		
    
    	}
    	 
    	 
    }
    /**
     * 获取上传文件信息
     */
    public function getFileInfo($where = [], $field = true)
    {
    
    	return self::$fileModel->getInfo($where, $field);
    }
    /**
     * 获取上传图片信息
     */
    public function getPictureInfo($where = [], $field = true)
    {
    
    	return self::$pictureModel->getInfo($where, $field);
    }
    /**
     * 图片上传
     * small,medium,big
     */
    public function pictureUpload($name = 'file', $thumb_config = ['small' => 100, 'medium' => 500, 'big' => 1000])
    {
       
        $object_info = request()->file($name);
        
        $sha1  = $object_info->hash();
        $md5  = $object_info->hash('md5');
        
        $picture_info = self::$pictureModel->getInfo(['sha1' => $sha1], 'id,name,path,sha1');
        
        
        
        if (!empty($picture_info)) {
        	
        	$picture_info['headpath']=WEB_PATH_PICTURE.$picture_info['path'];
        	$picture_info['userheadpath']=SYS_DSS. 'uploads'    . SYS_DSS.'picture'.SYS_DSS.$picture_info['path'];
        	$picture_info['hasupload']=1;
        	
        	session('last_uploadid',null);
        	
        	return $picture_info; 
        }
      
        $object = $object_info->validate(['size'=>$this->picturesize,'ext'=>$this->pictureext])->move(PATH_PICTURE);
        //此处读取配置文件，看可以传什么文件
         if($object){
         	 $save_name = $object->getSaveName();
        
        $save_path = PATH_PICTURE . $save_name;
        
        $picture_dir_name = substr($save_name, 0, strrpos($save_name, DS));
        
        $filename = $object->getFilename();
        
        $thumb_dir_path = PATH_PICTURE . $picture_dir_name . DS . 'thumb';
        
        !file_exists($thumb_dir_path) && @mkdir($thumb_dir_path, 0777, true);
        
        Image::open($save_path)->thumb($thumb_config['small']   , $thumb_config['small'])->save($thumb_dir_path  . DS . 'small_'  . $filename);
        Image::open($save_path)->thumb($thumb_config['medium']  , $thumb_config['medium'])->save($thumb_dir_path . DS . 'medium_' . $filename);
        Image::open($save_path)->thumb($thumb_config['big']     , $thumb_config['big'])->save($thumb_dir_path    . DS . 'big_'    . $filename);
        
        $data = ['name' => $filename, 'path' => $picture_dir_name. SYS_DSS . $filename, 'sha1' => $sha1,'md5' => $md5];
        
        $result = self::$pictureModel->addInfo($data);
        
        session('last_uploadid',$result);
        
        $this->checkStorage($result,self::$pictureModel);
        
        
        $data['headpath']=WEB_PATH_PICTURE.$picture_dir_name. SYS_DSS . $filename;
        $data['userheadpath']=SYS_DSS. 'uploads'    . SYS_DSS.'picture'.SYS_DSS.$picture_dir_name. SYS_DSS . $filename;
        
        
        if ($result) : $data['id'] = $result; return $data; endif;
        return  false;
        
         }else{
         	
		  return array('errormsg' => $object_info->getError(),'code'=>0);
		   
		}
        
        
       
    }
    /**
     * 文件上传
     * small,medium,big
     */
    public function fileUpload($name = 'file')
    {
    	
        
        $object_info = request()->file($name);
        
        $sha1  = $object_info->hash();
        $md5  = $object_info->hash('md5');
        
        
        $file_info = self::$fileModel->getInfo(['sha1' => $sha1], 'id,name,savepath,sha1');
       
        
        if (!empty($file_info)){
        	
        	$file_info['headpath']=WEB_PATH_FILE.$file_info['savepath'];
        	
        	if(model('doccon')->where(['fileid'=>$file_info['id']])->count()>0){
        		
        		$file_info['hasupload']=1;
        		 
        		session('last_uploadid',null);
        		
        	}else{
        		session('last_uploadid',$file_info['id']);
        	}
        	
        	
        	
        	return $file_info;
        }
       
        $object = $object_info->validate(['size'=>$this->filesize,'ext'=>$this->fileext])->move(PATH_FILE);
        
        //此处读取配置文件，看可以传什么文件
         
        if($object){
        $ext = $object->getExtension();
        $save_name = $object->getSaveName();
        
        $save_path = PATH_FILE . $save_name;
        
        $file_dir_name = substr($save_name, 0, strrpos($save_name, DS));
        
        $filename = $object->getFilename();
        
        $fileinfo=$object->getInfo();
        
       $filenamearr = explode('.'.$ext, $fileinfo['name']);
        
        $data = ['name' => $filenamearr[0],'ext'=>$ext,'size' => $fileinfo['size'],'mime' => $fileinfo['type'], 'savepath' => $file_dir_name. SYS_DSS . $filename,'savename' => $filename, 'sha1' => $sha1,'md5' => $md5];
        
        $result = self::$fileModel->addInfo($data);
        
        session('last_uploadid',$result);
        
        $this->checkStorage($result,self::$fileModel);
        
        $data['headpath']=WEB_PATH_FILE.$file_dir_name. SYS_DSS . $filename;
        
       
       
        if ($result) : $data['id'] = $result; return $data; endif;
        
        return  false;
        }else{
        	
		return array('errormsg' => $object_info->getError(),'code'=>0);
		
		}
        
    }
    /**
     * 云存储
     */
    public function checkStorage($result = 0,$model)
    {
        
        $storage_driver = config('storage_driver');
        
        if (empty($storage_driver)) : return false; endif;
        
        $StorageModel = model('Storage', 'service');

        $StorageModel->setDriver($storage_driver);

        $storage_result = $StorageModel->upload($result);
        
        $model->setFieldValue(['id' => $result], 'url', $storage_result);
    }
   
}
