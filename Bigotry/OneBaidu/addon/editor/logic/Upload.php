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

namespace addon\editor\logic;

use app\common\logic\File as LogicFile;
use app\common\model\Addon;

/**
 * 编辑器插件上传逻辑
 */
class Upload extends Addon
{

    /**
     * 图片上传
     */
    public function pictureUpload()
    {
        
        $result = get_sington_object('fileLogic', LogicFile::class)->pictureUpload('imgFile');
        
        if (false === $result) : return [RESULT_ERROR => DATA_NORMAL, RESULT_MESSAGE => '文件上传失败']; endif;
        
        $url = get_picture_url($result['id']);
        
        return [RESULT_ERROR => DATA_DISABLE, RESULT_URL => $url];
    }
}
