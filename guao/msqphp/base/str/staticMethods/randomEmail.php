<?php declare(strict_types = 1);
namespace msqphp\base\str;
/**
 * 创建随机邮箱
 * @func_name     randomEmail
 * @return string 邮箱
 */
return function () : string {
    $size = rand(2, 10);
    // 字母开头
    $email = static::randomString(1, 5);
    // 随机字母加_-
    $email = static::randomString($size, 6);
    $email .= '@';
    // 扩展个数(.com.cn之类)
    $ext = rand(2, 3);
    for($i=$ext;$i>0;--$i) {
        // 字母
        $len = rand(2, 3);
        $email .= static::randomString($len, 5);
        $email .= '.';
    }
    return rtrim($email, '.');
};