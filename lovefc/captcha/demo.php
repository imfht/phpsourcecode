<?php

/*
 * 验证码展示
 * @Author: lovefc 
 * @Email：fcphp@qq.com
 * @Date: 2019-09-28 15:37:21
 */


/* 加载文件，如果使用composer的加载就不需要手动加载 */
/** require __DIR__ . '/vendor/autoload.php'; **/
require __DIR__ . '/Src/Captcha.php';
require __DIR__ . '/Src/GIF/GIFEncoder.php';

/* 实例化 */
$ver = new FC\Captcha();

/* 验证码的一些设置,不设置也有默认 */

// 验证码宽度
$ver->width = 300;

// 验证码高度
$ver->height = 100;

// 验证码个数
$ver->nums = 4;

// 随机字符串
$ver->random = '舔狗不得好死';

// 随机数大小
$ver->font_size = 40;

// 字体路径
//$ver->font_path = __DIR__.'/Font/zhankukuhei.ttf';

// 是否为动态验证码
//$ver->is_gif = true;

// 动图帧数
//$ver->gif_fps = 10;


/* 生成验证码 */
$code = $ver->getCode();

/*
 $_SESSION['code'] = $code
 ....(这里自己存Session或者Redis)
*/

/* 生成验证码图片 */
$ver->doImg($code);
