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

namespace addon\editor\controller;

use app\common\controller\AddonBase;

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
        
        $result = $this->logicUpload->pictureUpload();
        
        return throw_response_exception($result);
    }
}
