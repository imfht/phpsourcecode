<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\controller;

use app\common\controller\FileBase;

/**
 * 文件控制器
 */
class File extends FileBase
{
    
    /**
     * 图片上传
     */
    public function pictureUpload()
    {
        
        $result = $this->logicFile->pictureUpload();

        return json($result);
    }
    
    /**
     * 文件上传
     */
    public function fileUpload()
    {
        
        $result = $this->logicFile->fileUpload();

        return json($result);
    }
}
