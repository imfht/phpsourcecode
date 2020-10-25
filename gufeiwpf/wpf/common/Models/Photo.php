<?php
namespace Wpf\Common\Models;
class Photo extends \Wpf\Common\Models\CommonModel{
    
    
    public $_config = array(
        'mimes'         =>  array(), //允许上传的文件MiMe类型
        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
        'exts'          =>  array("jpg","jpeg","png","gif"), //允许上传的文件后缀
        'rootPath'      =>  PUBLIC_PATH, //保存根路径
        'savePath'      =>  "/photo/", //保存路径
        'saveName'      =>  "index", //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'       =>  false, //文件保存后缀，false则没有后缀,空则使用原文件后缀
    );
    
    public function initialize(){
        parent::initialize();
    }
    
    public function onConstruct(){
        parent::onConstruct();
    }
    
    
    public function beforeCreate(){
        $this->addtime = time();
        if(ADMIN_UID){
            $this->add_adminid = ADMIN_UID;
        }
        if(UID){
            $this->add_uid = UID;
        }
    }
    
    public function beforeSave(){
        $this->updatetime = time();
        if(ADMIN_UID){
            $this->update_adminid = ADMIN_UID;
        }
        if(UID){
            $this->update_uid = UID;
        }
    }
    
    public function uploadfile($file,$config=array(),$uploadtype=false){
        
        
        
        if(! $this->checkFile($file,$config)){
            return false;
        }
        
        if($this->create()){
            $photoid = $this->id;
        }
        
        
        
        $base_config = $this->_config;
        $config = array_merge($base_config,$config);
        
        $config['savePath'] = $config['savePath'].$this->getPhotoPathById($photoid)."/";
        
        
        $savefile = $config['rootPath'].$config['savePath'].$config['saveName'];
        
        if($config['saveExt']){
            $savefile.= ".".$config['saveExt'];
        }elseif($config['saveExt'] === "" && $file->getExtension()){
            $savefile.= ".".$file->getExtension();
        }
        
        $savepath = dirname($savefile);
        
        if($uploadtype === false){
            $uploadtype = $this->getDI()->get('config')->PHOTO_UPLOAD_TYPE;
        }
        
        $upload = \Wpf\Common\Models\Upload\Upload::createPaymentInstance($uploadtype);
        
        if($upload->savefile($file,$savefile)){
            return $photoid;
        }else{
            return $upload->getError();
        }
        
        
    }
    
    public function checkFile($file,$config = array()){
        if(! $file){
            if ($this->getDI()->get('request')->hasFiles() == true) {
                $filearray = $this->getDI->get('request')->getUploadedFiles();
                $file = $filearray[0];            
            }else{
                $this->error = "文件参数错误";
                return false;
            }
        }
        $base_config = $this->_config;
        
        $config = array_merge($base_config,$config);
        
        if($config['mimes'] && (!in_array($file->getType(),$config['mimes']))){
            $this->error = "禁止的文件类型";
            return false;
        }
        
        if($config['maxSize'] && $file->getSize > $config['maxSize']){
            $this->error = "文件尺寸超过".$config['maxSize'];
            return false;
        }
        
        if($config['exts'] && (!in_array($file->getExtension(),$config['exts']))){
            $this->error = "禁止的文件后缀";
            return false;
        }
        
        return true;
        
    }
    
    public function getPhotoPathById($photoid){
        if(!$photoid) return;
        
        $baseNum = 10000; //每xx个文件新增加一个文件夹
        
        $subbaseNum = 100;
        
        $folder1 = intval($photoid/$baseNum);
        $folder2 = intval( ($photoid-$folder1*$baseNum)/$subbaseNum );
        $securefolder = $this->photourl_encode($photoid);
        $path = "{$folder1}/{$folder2}/{$securefolder}";
        
        return $path;
    }
    
    /**
     * 图片id压缩处理,此返回值将会作为地址的一部分
     * @param int $photoid
     */
	public function photourl_encode($photoid){
        $str = wpf_encrypt($photoid,$this->getDI()->get("config")->PHOTO_KEY);
		$str = str_replace(array("/", "+"), array("_", "-"), $str);
		return $str;
	}
    
    /**
	 * 图片id反解处理
	 * @param string $encodestr
	 */
	public function photourl_decode($encodestr){
		$encodestr = str_replace(array("_", "-"), array("/", "+"), $encodestr);        
        $id = wpf_decrypt($encodestr,$this->getDI()->get("config")->PHOTO_KEY);
		return $id;
	}
    
    public function geturl($photoid, $isurl=false,$photo_path="photo"){
        $path = $this->getPhotoPathById($photoid);
        if($isurl){            
            if($this->getDI()->get("config")->PHOTO_UPLOAD_TYPE == 1){
                return "http://7xkvs1.com1.z0.glb.clouddn.com"."/{$photo_path}/".$path."/index";
            }else{
                return STATIC_URL."/{$photo_path}/".$path."/index";
            }
        }else{
            return PUBLIC_PATH."/{$photo_path}/".$path."/";
        }
    }
    
    public function processImage($paths, $redirect, $url){
        $bak_paths = $paths;
        $img_name = array_pop($bak_paths);

        
        if(end($bak_paths) == "index"){
            array_pop($bak_paths);
        }
        

        $o_size = array();
        $r_size = array();
        
        foreach($this->getDI()->get("config")->PHOTO_ALLOW_SIZE->toArray() as $size){
            if(strpos($size,"*") === false){
                array_push($o_size,$size);
            }else{
                $size = '/^'.str_replace("*","(\d+)",$size).'$/';
                array_push($r_size,$size);
            }
        }
        
        
        $allow_size_status = false;
        
        if(in_array($img_name,$o_size)){
            $allow_size_status = true;
        }
        
        if($allow_size_status === false){
            foreach($r_size as $regex){
                if(preg_match($regex,$img_name)){
                    $allow_size_status = true;
                    break;
                }
            }
        }
        

        if(! $allow_size_status){
            return false;
        }
        

        array_shift($bak_paths);
        $destfn_path = PUBLIC_PATH.'/'.join("/", $bak_paths);
        
        $file = $destfn_path.'/'.$img_name;
        $destfn = $destfn_path.'/index';


        if(is_file($destfn)){
            $image = new \Phalcon\Image\Adapter\Gd($destfn);
        }else{
            return false;
        }
        
        

        if(stripos($img_name,"u")!==false){//居中裁剪
            list($width,$height) = explode('u', $img_name);
            $image->resize($width, $height,\Phalcon\Image::AUTO)->crop($width, $height)->save($file,100);            
        }elseif(stripos($img_name,"x")!==false){//等比例缩放，保持宽高比
            list($width,$height) = explode('x', $img_name);
            $image->resize($width, $height,\Phalcon\Image::INVERSE)->crop($width, $height)->save($file,100);
        //}elseif(stripos($img_name,"u")!==false){//固定尺寸缩放
//            list($width,$height) = explode('x', $img_name);
//            $image->resize($width, $height,\Phalcon\Image::NONE)->save($file);
        }else{
            return false;
        }
        
        
        $mime = $image->getMime();
        $this->getDI()->get('response')->setStatusCode(200);
        $this->getDI()->get('response')->setContentType($mime);
        $this->getDI()->get('response')->setContent(file_get_contents($file));
        
        return;
    }
}