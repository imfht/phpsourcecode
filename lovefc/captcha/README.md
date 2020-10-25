# PHP动态验证码

使用简单方便，增加你的摸鱼时间。

自动判断中英文，中文时为中文随机数，英文时为英文随机数

![avatar](/image/1.gif) 

![avatar](/image/2.gif)

![avatar](/image/3.gif)

![avatar](/image/4.gif)

验证码字体是开源的站酷库黑体(中英文皆可用)，感谢站酷免费提供。

演示地址: https://api.lovefc.cn/captcha

### 安装

直接下载源码或者使用 composer 安装

````
{
    "require": {
        "lovefc/captcha": "0.0.1"
    }		
}
````

### 食用方法

````

/* 实例化 */
$ver = new FC\Captcha();

/* 验证码的一些设置 */

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

````

