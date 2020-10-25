<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace addon\editor\controller;

use app\common\controller\AddonBase;
use addon\editor\logic\Upload as LogicUpload;

/**
 * 编辑器插件上传控制器
 */
class Upload extends AddonBase
{
    
    /**
     * 图片上传
     */
    public function pictureUpload()
    {
        
        $UploadLogic = new LogicUpload();
        
        $result = $UploadLogic->pictureUpload();
        
        exit(json_encode($result));
    }
}
