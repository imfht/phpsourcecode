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

namespace app\common\controller;

/**
 * 文件操作控制器基类
 */
class FileBase extends ControllerBase
{
    
    /**
     * 获取文件路径
     */
    public function getFileUrl($id = 0)
    {
        
        return $this->logicFile->getFileUrl($id);
    }
    
    /**
     * 获取图片路径
     */
    public function getPictureUrl($id = 0)
    {
        
        return $this->logicFile->getPictureUrl($id);
    }
}
