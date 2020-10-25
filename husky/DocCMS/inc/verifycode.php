<?php
session_start();
//-------------------------------------------------------------------------
$img_w       = 80;// 设置图片宽
$img_h       = 20;// 设置图片高
$pixel_num   = 200;//点越多干扰越大
$is_set_line = true;// 启用干扰线
$pixel_mode  = 2;// 干扰点模式,1,同色;2，杂色
//-------------------------------------------------------------------------

// 随机数产生器
function make_seed()
{
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}
mt_srand(make_seed());//4.2.0以下版本适用
$authnum = mt_rand(100, 99999);

// 加入session
$_SESSION['verifycode']=$authnum;

//生成验证码图片
Header("Content-type: image/PNG");
$im = imagecreatetruecolor($img_w, $img_h);

$bg_color = ImageColorAllocate($im, mt_rand(250,255),mt_rand(250,255),mt_rand(250,255));

// 绘制背景
imagefill($im,0,0,$bg_color);

$total_width = 0;
$word_info = array();

// 循环，获取文字信息
$word_length = strlen($authnum);
for($ii=0; $ii<$word_length; $ii++)
{
    $word_space = mt_rand(1,5);
    $font = rand(3,5);
    mt_rand(1,9)%2 == 0?$top = 1:$top = 3;
    $word_info[$ii]['char']   = substr($authnum,$ii,1);
    $word_info[$ii]['font']   = $font;
    $word_info[$ii]['offset'] = $top;
    if($ii == 0)
    {
        $word_info[$ii]['width'] = 0;
    }
    $word_info[$ii]['width']  = imageFontWidth($font)+$word_space;
    $word_info[$ii]['height'] = imageFontHeight($font);
    $word_info[$ii]['color']  = imageColorAllocate($im, mt_rand(0,50),mt_rand(0,150),mt_rand(0,200));

    // 文字总宽度
    $total_width += $word_info[$ii]['width'];

    // 取第一个字体的高度
    if($ii == 0)
    {
        $total_height = imagefontHeight($font);
    }
}

// 计算偏移
$offset_x = floor(($img_w - $total_width)/2);
$offset_y = floor(($img_h - $total_height)/2);

// 填充验证码
$wid = 0;
$i = 0;
foreach($word_info as $key=>$val)
{
    if($i>0)
    {
        $wid += $val['width'];
    }
    imagestring($im, $val['font'], $offset_x + $wid, $val['offset'] + $offset_y,
                $val['char'], $val['color']);
    $i++;
}
switch($pixel_mode)
{
    case 1:
        $pixel_color  = ImageColorAllocate($im,
                                        mt_rand(50,255),
                                        mt_rand(50,255),
                                        mt_rand(50,255));
        // 干扰象素
        for($i=0;$i<$pixel_num;$i++)
        {
            imagesetpixel($im, mt_rand()%$img_w , mt_rand()%$img_h , $pixel_color);
        }
        break;
    case '2':
        // 干扰象素
for ($i=0;$i<=128;$i++)
 {
 $pixel_color = imagecolorallocate ($im, rand(0,255), rand(0,255), rand(0,255));
 imagesetpixel($im,mt_rand(2,128),mt_rand(2,38),$pixel_color);
 }
        break;

    default:
        $pixel_color  = ImageColorAllocate($im,
                                        mt_rand(50,255),
                                        mt_rand(50,255),
                                        mt_rand(50,255));
        // 干扰象素
        for($i=0;$i<$pixel_num;$i++)
        {
            imagesetpixel($im, mt_rand()%$img_w , mt_rand()%$img_h , $pixel_color);
        }
        break;
}
ImagePNG($im);
ImageDestroy($im);