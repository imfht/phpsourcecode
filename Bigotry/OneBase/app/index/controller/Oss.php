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

namespace app\index\controller;


class Oss extends IndexBase
{
    /**
     *  上传静态文件到oss
     */
    public function uploadStaticFileToOss()
    {
        if(!IS_CLI) : return "请在cli模式下运行!防止浏览器超时";endif;
        $root = "static";
        $error = $this->logicOss->uploadStaticFile($root);
        return $error;
    }
}
