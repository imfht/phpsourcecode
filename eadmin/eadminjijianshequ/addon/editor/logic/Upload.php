<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace addon\editor\logic;

use app\common\logic\File as LogicFile;

/**
 * 编辑器插件上传逻辑
 */
class Upload
{
    
    /**
     * 图片上传
     */
    public function pictureUpload()
    {
        
        $fileLogic = get_sington_object('fileLogic', LogicFile::class);
        
        $result = $fileLogic->pictureUpload('imgFile');
        
        if((is_array($result)&&!empty($result['errormsg']))||false === $result){
        	
        	$msg = empty($result['errormsg'])?'文件上传失败':$result['errormsg'];
        	
        	return [RESULT_ERROR => DATA_NORMAL, RESULT_MESSAGE => $msg]; 
        	
        }
        
        $url = get_picture_url($result['id']);
        
        return [RESULT_ERROR => DATA_DISABLE, RESULT_URL => $url];
    }
}
