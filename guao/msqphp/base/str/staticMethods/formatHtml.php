<?php declare(strict_types = 1);
namespace msqphp\base\str;
return function (string $content) :string {
    $content = preg_replace_callback('/<script([\\s\\S]*)>([\\s\\S]*)<\\/script>/', function($matches){
        $result = '<script'.$matches[1].'>';
        $js = $matches[2];
        $pattern = [
            '/\\/\\/([^\\n\\r]*)/', '/\\/\\*([\\s\\S]*)\\*\\// ', '/^\\s*/', '/
/',
        ];
        $js = preg_replace($pattern, '', $js);
        $js = preg_replace('/([\\{\\}\\;])\\s+/', '\\1', $js);
        return $result . $js . '</script>';
    }, $content);
    // 删除空格,注释
    return preg_replace('/\\<\\!\\-\\-([\\s\\S]*)\\-\\-\\>/', '', preg_replace('/\\>\\s*\\</', '><', $content));
};