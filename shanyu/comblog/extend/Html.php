<?php

class Html
{
    function compress($string) {
        $string = str_replace("\r\n", '', $string); //清除换行符
        $string = str_replace("\n", '', $string); //清除换行符
        $string = str_replace("\t", '', $string); //清除制表符
        $pattern = array(
            "/> *([^ ]*) *</", //去掉注释标记
            "/[\s]+/",
            "/<!--[^!]*-->/",
            "/\" /",
            "/ \"/",
            "'/\*[^*]*\*/'"
        );
        $replace = array(
            ">\\1<",
            " ",
            "",
            "\"",
            "\"",
            ""
        );
        return preg_replace($pattern, $replace, $string);
    }
    public function write($file,$string)
    {
        $string=$this->compress($string);
        $file_dir = dirname($file);
        if(!is_dir($file_dir)){
            mkdir($file_dir,'0777',true);
        }
        $status=file_put_contents($file,$string);
        return $status;
    }
}