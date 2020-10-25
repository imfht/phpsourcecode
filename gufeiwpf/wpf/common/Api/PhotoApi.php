<?
namespace Wpf\Common\Api;
class PhotoApi {
    
    
    
    public function addphoto_one($file = "",$photo_path="photo"){
        global $di;
        
        
        
        
        
        if(! $file){
            if ($di->get('request')->hasFiles() == true) {
                $filearray = $di->get('request')->getUploadedFiles();
                $file = $filearray[0];
            }else{
                $this->error("非法请求！");
            }
        }
        
        
        $class = new \Wpf\Common\Models\Photo();
        
        if($photoid = $class->uploadfile()){
            $photoid = $class->id;
        }
        
        //$config['savePath'] = "/".$photo_path."/".self::getPhotoPathById($photoid)."/";
        
        if($class->uploadfile($file,$config)){
            
        }
        
        
        $file->moveTo('files/' . $file->getName());
        
        
        $config = array(
            'mimes'         =>  array(), //允许上传的文件MiMe类型
            'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
            'exts'          =>  array("jpg","jpeg","png","gif"), //允许上传的文件后缀
            'autoSub'       =>  false, //自动子目录保存文件
            'subName'       =>  array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath'      =>  C('PATH_FILES').'/', //保存根路径
            'savePath'      =>  $photo_path."/".self::getPhotoPathById($photoid)."/", //保存路径
            'saveName'      =>  "index", //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'saveExt'       =>  false, //文件保存后缀，false则没有后缀,空则使用原文件后缀
            'replace'       =>  true, //存在同名是否覆盖
            'hash'          =>  true, //是否生成hash编码
            'callback'      =>  false, //检测文件是否存在回调，如果存在返回文件信息数组
            'driver'        =>  '', // 文件上传驱动
            'driverConfig'  =>  array(), // 上传驱动配置
        );
        
        $upload = new \Think\Upload($config);
        $upinfo = $upload->uploadOne($file);
        
        $return = array();
        
         if(!$upinfo){
            $class = M("photo");
            $class->delete($photoid);
            $return['status'] = "error";
            $return['msg'] = $upload->getError();
        }else{
            $return['status'] = "ok";
            $upinfo['photoid'] = $photoid;
            $upinfo['url'] = self::geturl($photoid,1)."index";
            $upinfo["thumbnailUrl"] = self::geturl($photoid,1)."80x80";
            $return['msg'] = $upinfo;
            
        }
        
        return $return;
    }
    
    
}