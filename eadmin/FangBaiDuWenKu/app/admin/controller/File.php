<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\controller\ControllerBase;
use app\common\logic\File as LogicFile;
use Qiniu\json_decode;

/**
 * 文件控制器
 */
class File extends ControllerBase
{
    
    // 文件逻辑
    private static $fileLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
        
        self::$fileLogic = get_sington_object('fileLogic', LogicFile::class);
    }
    
    /**
     * 图片上传
     */
    public function pictureUpload()
    {
        
        $result = self::$fileLogic->pictureUpload();
        
        return json($result);
    }
    /**
     * 文件上传
     */
    public function fileUpload()
    {
    
    	$result = self::$fileLogic->fileUpload();
    
    	return json($result);
    }
    public function downloadFile($url,$name='',$local=1)
    {
    	
    	self::$fileLogic->download(json_decode(decrypt($url)),$name,$local);
    
    	
    }
}
