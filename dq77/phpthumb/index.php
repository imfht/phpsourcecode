<?php
/**
 * 这功能主要对图片进行修改和操作  可以生成各种前端需要的特定尺寸的图片<br />
 * 基本使用方法<br />
 * 把所有文件放到图片的文件夹里    需要开启apache的 URL重写模块<br />
 * 然后你可以这样访问<br />
 * 比如原始地址是:http://xxx.xxx/1.jpg  现在要 一张 30*30的1.jpg的图片 那么你可以这样
 * http://xxx.xxx/1.jpg_s=30-30.jpg
 * 系统会在第一次访问的时候  生成这张图片 
 */
/**
 * 参数说明
 * 如果一个条件有多个参数 那么就用#分割
 * s:图片宽与高 100#100 如果没有高 那么就按照宽度等比例缩放
 * q：输出如果是JPG格式的，可以规定它的输出质量 
 * p:图片的缩放质量
 * a:截图图片部分
 * cc:截图图片 从中心点开始计算
 * bg：输出时的背景（如果需要） 
 * r:旋转图片 
 * c:局部输出，宽高、起始位置 sw、sh、sx、sy
 * sfn：输出gif动画中的某一帧 
 * fltr[]：滤镜，可以有很多效果，包括锐化、模糊、旋翻转、水印、边框、遮照、色彩调整等
 */
$img_path = isset($_GET['thumb'])?$_GET['thumb']:false;

require_once 'thumb/ThumbLib.inc.php';
/**
 * 包含Uri的解析
 */
require_once THUMBLIB_BASE_PATH . '/Uri.php';
/*
 * 包含配置文件
 */
require_once THUMBLIB_BASE_PATH . '/Config.php';
/*
 * 包含参数验证文件
 */
require_once THUMBLIB_BASE_PATH . '/CheckParam.php';

$uri = new Thumb\Uri($img_path);

$checkParam = new Thumb\CheckParam();
$load_img = $uri->getImg();

$load_img = file_exists($load_img)?$load_img:\Thumb\Config::defaultImg;
$thumb = PhpThumbFactory::create($load_img);
$CurrentDimensions = $thumb->getCurrentDimensions();

//截图，前两个参数分别是需要解出的图片的右上角的坐标X,Y。 后面两个参数是需要解出的图片宽，高。
isset($uri->uri->crop) and 
$thumb->crop($uri->uri->crop[0], $uri->uri->crop[1], $uri->uri->crop[2], $uri->uri->crop[3]);


//把图片等比缩小到最大宽度 100px或者最高100px，当只输入一个参数的时候，是限制最宽的尺寸。
isset($uri->uri->size) and $checkParam->resize($uri->uri->size) and $thumb->resize($uri->uri->size['width'], $uri->uri->size['height']);

//把图片等比缩小到原来的百分数，比如50就是原来的50%。
isset($uri->uri->p) and $checkParam->resizePercent($uri->uri->p)  and $thumb->resizePercent($uri->uri->p);

//截取一个175px * 175px的图片，注意这个是截取，超出的部分直接裁切掉，不是强制改变尺寸。
isset($uri->uri->a) and $checkParam->adaptiveResize($uri->uri->a)  and $thumb->adaptiveResize($uri->uri->a['width'], $uri->uri->a['height']);


//从图片的中心计算，截取200px * 100px的图片。
isset($uri->uri->cc) and $checkParam->cropFromCenter($uri->uri->cc)  and $thumb->cropFromCenter($uri->uri->cc['width'], $uri->uri->cc['height']);

//把图片顺时针反转180度
isset($uri->uri->r) and $checkParam->rotateImageNDegrees($uri->uri->r)  and $thumb->rotateImageNDegrees($uri->uri->r);

//保存（生成）图片,你可以保存其他格式，详细参考文档
($img_path != \Thumb\Config::defaultImg) and $thumb->save($img_path);

/**
 * 所有选项
 * $thumb->getOptions();
 */
/**
 * 获得当前尺寸
 * $thumb->getCurrentDimensions();
 */
//var_dump($thumb->getPercent());
//($img_path != $config['default'])?header('Location: '.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']):$thumb->show();
$thumb->show();