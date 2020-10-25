<?php
namespace workerbase\traits;
/**
 * @author fukaiyao
 */
trait BaseTool {
    /**
     * @author fukaiyao
     * 生成随机字符串
     * @param int $length    字符长度
     * @param bool $only_num    是否只取数字
     * @return string
     */
    public function createRandomStr($length, $only_num = false){
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符
        $strlen = 62;

        //只取数字
        if ($only_num) {
            $str = '0123456789';//10个字符
            $strlen = 10;
        }

        while($length > $strlen){
            $str .= $str;
            $strlen += $strlen;
        }
        $str = str_shuffle($str);
        return substr($str,0,$length);
    }
}