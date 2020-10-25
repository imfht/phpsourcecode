<?php
/**
 * VgotFaster PHP Framework
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

namespace VF\Library;

/**
 * VgotFaster Image Library
 *
 * @package VgotFaster
 * @author Pader
 */
class Image {

	protected $set = array();
	protected $resource;  //图像资源句柄
	protected $width;
	protected $height;
	protected $current = array(); //存储正在处理的信息

	//支持处理的文件类型定义,并指出了输出图片的函数
	public $allowTypes = array(
		'jpg'  => 'imagejpeg',
		'gif'  => 'imagegif',
		'png'  => 'imagepng',
		'wbmp' => 'image2wbmp',
		'jpeg' => 'imagejpeg'
	);

	/**
	 * Image::__construct()
	 *
	 * @param $config
	 * @return void
	 */
	public function __construct($config=NULL)
	{
		if(!extension_loaded('gd') and !dl('gd.so')) {
			showError('PHP GD extension doesn\'t loaded, can not use Image library!');
		}

		$this->set = array(
			'srcImage'   => '',   //图片源文件
			'createPath' => '',   //图片创建路径

			'quality'    => 100,       //生成图片质量
			'imageType'      => '',    //图片 mines 类型
			'autoMakeDir'    => FALSE, //图片生成目录不存在时是否自动创建
			'transparency'   => FALSE, //默认不对 gif 和 png 进行透明处理

			'cornerCoverImage' => 'app/data/rounded_corner.gif',  //设定圆角蒙板图片

			'markFontColor' => '#FFFFFF',
			'markFontShadowColor' => '#585858',
			'fontFile'      => 'C:\\Windows\\Fonts\\Arial.ttf' //文字水印使用的默认字体
		);

		if(is_array($config) and !empty($config)) {
			$this->initialize($config);
		}
	}

	/**
	 * 设置处理配置
	 *
	 * @param array $config
	 * @return void
	 */
	public function initialize($config,$value=NULL)
	{
		if (is_array($config)) {
			foreach($config as $key => $val) {
				if(isset($this->set[$key])) {
					$this->set[$key] = $val;
				}
			}
		} else {
			if (isset($this->set[$config])) {
				$this->set[$config] = $value;
			}
		}

		//检查处理资源文件
		if($this->set['srcImage'] != '') {
			$this->checkFileExists($this->set['srcImage']);
			$this->open($this->set['srcImage']);
		}

		//自动创建目录
		if($this->set['createPath'] != '') {
			$path = dirname($this->set['createPath']);
			if(!is_dir($path)) {
				$VF =& getInstance();
				$VF->load->helper('directory');
				mkdirs($path);
			}
		}
	}

	/**
	 * 打开图片源
	 *
	 * 打开文件,读取图片资源句柄,并获取宽高
	 *
	 * @param string $filepath
	 * @param string $imageType 强制读取为格式
	 * @return void
	 */
	public function open($file,$imageType=NULL)
	{
		$src = '';
		if(function_exists('file_get_contents')) {
			$src = file_get_contents($file);
		} else {
			$handle = fopen($file,'r');
			while(!feof($handle)) {
				$src .= fgets($handle,4096);
			}
			fclose($handle);
		}

		if(empty($src)) {
			exit('Image resource is empty!');
		}

		$this->resource = imagecreatefromstring($src);
		$this->width = $this->getImageWidth($this->resource);
		$this->height = $this->getImageHeight($this->resource);
		$this->current = array(); //清除状态

		if(!is_null($imageType)) {
			$this->current['imageType'] = $imageType;
		} elseif(empty($this->current['imageType'])) {
			$this->current['imageType'] = $this->getImageType($file);
		}

		if ($this->set['transparency'] AND $this->current['imageType'] == 'gif') {
			$otsc = imagecolortransparent($this->resource);
			if ($otsc >= 0 AND $otsc < imagecolorstotal($this->resource)) {
				$this->current['trans'] = imagecolorsforindex($this->resource,$otsc);
			} else {
				$this->current['trans'] = array('red'=>255,'green'=>255,'blue'=>255,'alpha'=>127);
			}
		}

		return $this->resource;
	}

	/**
	 * 图像圆角处理
	 *
	 * @param int $radius 圆角值大小
	 * @param array|bool $corner 数组元素分别对应 左上,右上,左下,右下 为 TRUE 时全圆角, FALSE 无圆角
	 * @param string $coverImage 设定蒙板图片
	 * @return
	 */
	public function corner($radius=20,$corner=array(1,1,1,1),$coverImage=NULL)
	{
		//四角设定
		if(is_bool($corner)) {
			if($corner == TRUE) {
				$topLeft = true;
				$bottomLeft = true;
				$bottomRight = true;
				$topRight = true;
			} else return;
		} elseif(is_array($corner) and count($corner) >= 4) {
			$topLeft = (bool)$corner[0];
			$topRight = (bool)$corner[1];
			$bottomLeft = (bool)$corner[2];
			$bottomRight = (bool)$corner[3];
		}

		if(is_null($coverImage)) {
			$coverImage = $this->set['cornerCoverImage'];
		}

		//强制使用 gif 格式蒙板图片
		if(!file_exists($coverImage)) showError('The corner cover image doesn\'t exists!');
		elseif(!preg_match('/\.gif$/i',$coverImage)) showError('The corner cover image type not <b>gif</b>, please use gif image!');

		$cornerSource = imagecreatefromgif($coverImage);
		$cornerW = imagesx($cornerSource);
		$cornerH = imagesy($cornerSource);

		$cornerIm = imagecreatetruecolor($radius, $radius);
		imagecopyresampled($cornerIm, $cornerSource, 0, 0, 0, 0, $radius, $radius, $cornerW, $cornerH);

		$cornerW = imagesx($cornerIm);
		$cornerH = imagesy($cornerIm);

		$black = imagecolorallocate($this->resource,0,0,0);

		//左上
		if($topLeft == true) {
			imagecolortransparent($cornerIm, $black);
			imagecopymerge($this->resource, $cornerIm, 0, 0, 0, 0, $cornerW, $cornerH, 100);
		}

		//左下
		if ($bottomLeft == true) {
			$rotated = imagerotate($cornerIm, 90, 0);
			imagecolortransparent($rotated, $black);
			imagecopymerge($this->resource, $rotated, 0, ($this->height - $cornerH), 0, 0, $cornerW, $cornerH, 100);
		}

		//右下
		if ($bottomRight == true) {
			$rotated = imagerotate($cornerIm, 180, 0);
			imagecolortransparent($rotated, $black);
			imagecopymerge($this->resource, $rotated, ($this->width - $cornerW), ($this->height - $cornerH), 0, 0, $cornerW, $cornerH, 100);
		}

		//右上
		if ($topRight == true) {
			$rotated = imagerotate($cornerIm, 270, 0);
			imagecolortransparent($rotated, $black);
			imagecopymerge($this->resource, $rotated, ($this->width - $cornerW), 0, 0, 0, $cornerW, $cornerH, 100);
		}

		//$this->resource = $image;
	}

	/**
	 * 裁切图片
	 *
	 * 当 X 或 Y 轴为空时,将自动取图像中间区域进行裁切
	 *
	 * @param int $width
	 * @param int $height
	 * @param int $x Axis X, if is null, then crop will be horizontal center
	 * @param int $y Axis Y, if is null, then crop will be vertical middle
	 * @return void
	 */
	public function crop($width,$height,$x=NULL,$y=NULL)
	{
		if(is_null($x)) $x = round(($this->width - $width) / 2);
		if(is_null($y)) $y = round(($this->height - $height) / 2);

		$newIm = $this->imageCreate($width,$height);
		imagecopy($newIm,$this->resource,0,0,$x,$y,$width,$height);

		$this->width = $width;
		$this->height = $height;
		$this->resource = $newIm;
		unset($newIm);
	}

	/**
	 * 画边框
	 *
	 * @param integer $size
	 * @param string $color
	 * @param string $pos 'in' or 'out' border
	 * @return void
	 */
	public function drawBorder($size=1,$color='#000000',$pos='in')
	{
		//中间图像区域的宽高
		$inWidth = $this->width - $size * 2;
		$inHeight = $this->height - $size * 2;

		if($pos == 'out') {
		$cWidth = $this->width + $size * 2;
		$cHeight = $this->height + $size * 2;
		$srcStart= 0;
			$inWidth = $this->width;
			$inHeight = $this->height;
		} else {
			$cWidth = $this->width;
			$cHeight = $this->height;
			$srcStart= $size;
			$inWidth = $this->width - $size * 2;
			$inHeight = $this->height - $size * 2;
		}

		//创建一个单色填充图
		$c = $this->parseColor($color);
		$newIm = $this->imageCreate($cWidth,$cHeight);

		$color = imagecolorallocate($newIm,$c[0],$c[1],$c[2]);
		imagefilledrectangle($newIm,0,0,$cWidth,$cHeight,$color);// 填充背景色

		imagecopyresampled($newIm,$this->resource,$size,$size,$srcStart,$srcStart,$inWidth,$inHeight,$inWidth,$inHeight);

		$this->resource = $newIm;
	}

	/**
	 * 水平翻转
	 *
	 * @return void
	 */
	public function flipX()
	{
		$size = $this->getImageSize($this->resource);
		$newIm = $this->imageCreate($size['w'],$size['h']);

		for ($x=0; $x<$size['w']; $x++) {
			imagecopy($newIm, $this->resource, $size['w'] - $x - 1, 0, $x, 0, 1, $size['h']);
		}

		$this->resource = $newIm;
	}

	/**
	 * 垂直翻转
	 *
	 * @return void
	 */
	public function flipY()
	{
		$size = $this->getImageSize($this->resource);
		$newIm = $this->imageCreate($size['w'],$size['h']);

		for($y=0; $y<$size['h']; $y++) {
			imagecopy($newIm, $this->resource, 0, $size['h'] - $y - 1, 0, $y, $size['w'], 1);
		}

		$this->resource = $newIm;
	}

	/**
	 * 图像反色处理
	 *
	 * @return void
	 */
	public function invertColor()
	{
		$bgColor = 0;
		$newIm = $this->imageCreate($this->width, $this->height);

		for($y=0; $y < $this->height; $y++) {
			for($x=0; $x < $this->width; $x++) {
				$colorPixel = imagecolorat($this->resource, $x, $y);
				$colorReverse = (~$colorPixel) & 0xFFFFFF ;  //php用的32位的, 所以需要去掉最开始8个1
				imagesetpixel($newIm,$x,$y,$colorReverse);
			}
		}

		$this->resource = $newIm;
	}

	/**
	 * 对图像使用滤镜
	 *
	 * @param int 滤镜效果类型（常量）：
	 *    IMG_FILTER_NEGATE: 将图像中所有颜色反转。
	 *    IMG_FILTER_GRAYSCALE: 将图像转换为灰度的。
	 *    IMG_FILTER_BRIGHTNESS: 改变图像的亮度。用 arg1 设定亮度级别。
	 *    IMG_FILTER_CONTRAST: 改变图像的对比度。用 arg1 设定对比度级别。
	 *    IMG_FILTER_COLORIZE: 与 IMG_FILTER_GRAYSCALE 类似，不过可以指定颜色。用 arg1，arg2 和 arg3 分别指定 red，blue 和 green。每种颜色范围是 0 到 255。
	 *    IMG_FILTER_EDGEDETECT: 用边缘检测来突出图像的边缘。
	 *    IMG_FILTER_EMBOSS: 使图像浮雕化。
	 *    IMG_FILTER_GAUSSIAN_BLUR: 用高斯算法模糊图像。
	 *    IMG_FILTER_SELECTIVE_BLUR: 模糊图像。
	 *    IMG_FILTER_MEAN_REMOVAL: 用平均移除法来达到轮廓效果。
	 *    IMG_FILTER_SMOOTH: 使图像更柔滑。用 arg1 设定柔滑级别。
	 * @return void
	 */
	public function filter($filterType) {
		$args = array_merge(array($this->resource, $filterType), func_get_args());
		call_user_func_array('imagefilter', $args);
	}

	/**
	 * 水印
	 *
	 * @param string|array $createPath
	 * @return void
	 */
	public function writeText($words, $fontSize=10, $wordMargin=8, $fontFile=NULL) {
		$words = iconv('gb2312', 'utf-8', $words);
		if (is_null($fontFile)) $fontFile = $this->set['fontFile'];

		$wordBox = imagettfbbox($fontSize, 0, $fontFile, $words);  //获取字符串区域坐标
		$wordWidth = $wordBox[2] - $wordBox[0];  //字符串宽度
		$wordHeight = $wordBox[1] - $wordBox[7];  //字符串高度

		$wordRight = $this->width - $wordMargin;  //字符串右边坐标
		$wordLeft = $wordRight - $wordWidth;  //字符串左边坐标
		$wordBottom = $this->height - $wordMargin;  //字符串底部坐标
		$wordTop = $wordBottom - $wordHeight;  //字符串顶部坐标


		$fontColor = imagecolorallocatealpha($this->resource,255,255,255,0);  //字体颜色
		$shadowColor = imagecolorallocatealpha($this->resource,0,0,0,95);  //阴影颜色

		imagettftext($this->resource,$fontSize,0,$wordLeft + 1,$wordBottom + 1,$shadowColor,$fontFile,$words);  //绘制字符串
		imagettftext($this->resource,$fontSize,0,$wordLeft,$wordBottom,$fontColor,$fontFile,$words);  //绘制字符串阴影
	}

	public function markImage($markImageFile) {
		//获取水印图片的信息
		$src = '';
		if (function_exists('file_get_contents')) {
			$src = file_get_contents($markImageFile);
		} else {
			$handle = fopen($markImageFile, 'r');
			while(!feof($handle)) {
				$src .= fgets($handle,4096);
			}
			fclose($handle);
		}

		if (empty($src)) {
			exit('MarkImage resource is empty!');
		}

		$mark = imagecreatefromstring($src);
		list($mWidth, $mHeight) = getimagesize($markImageFile);

		if (!$size) {
			exit($this->ERROR['unalviable']);
		}

		//将目标图片拷贝到背景图片上
		imagecopy($this->resource, $mark, 10, 100, 0, 0, $mWidth, $mHeight);
	}

	/**
	 * 更改大小
	 *
	 * 正常用法 resize(123,456)
	 * 宽度自动 resize('auto',386)
	 * 高度自动 resize(386[,'auto'])  默认高度自动
	 *
	 * @param int|string $newWidth Number or 'auto', if this is 'auto' then height must be a number
	 * @param int|string $newHeight Number or 'auto'
	 * @return void
	 */
	public function resize($width,$height='auto')
	{
		if((is_numeric($width) and $width < 1) or (is_numeric($height) and $height < 1)) {
			showError('New image width or height can\'t be zero!');
		}

		//Auto adjust width or height
		if(strtolower($width) == 'auto') $width = round($height * ($this->width / $this->height));
		elseif(strtolower($height) == 'auto') $height = round($width / ($this->width / $this->height));

		$newIm = $this->imageCreate($width,$height);
		imagecopyresampled($newIm,$this->resource,0,0,0,0,$width,$height,$this->width,$this->height);

		$this->width = $width;
		$this->height = $height;
		$this->resource = $newIm;
	}

	/**
	 * 旋转图像
	 *
	 * @param float $angle 旋转角度
	 * @param string $direction 旋转方向, 'left' or 'right'
	 * @return void
	 */
	public function rotate($angle=90.0,$direction='right')
	{
		$newIm = $this->imageCreate($this->width, $this->height);
		imagecopyresized($newIm, $this->resource, 0, 0, 0, 0, $this->width, $this->height, $this->width, $this->height);
		$colorWhite = imagecolorallocate($newIm, 255, 255, 255);

		//因为 imagerotate 始终以左侧开始，而我们常都是向右侧旋转,所以向右侧时, 需要一番计算
		if($direction == 'right') {
			$angle = $angle + (180 - $angle) * 2;
		}

		$this->resource = imagerotate($newIm, $angle, $colorWhite, 1);

		//重设图像大小
		$size = $this->getImageSize();
		$this->width = $size['w'];
		$this->height = $size['h'];
	}

	/**
	 * 锐化图像
	 *
	 * 由于锐化需要逐个象素处理,极耗系统资源,不建议使用
	 *
	 * @param integer $sharp 锐化程度 0.1 - 1
	 * @return void
	 */
	public function sharp($sharp=0.7)
	{
		$newIm = $this->imageCreate($this->width, $this->height);
		$cnt = 0;
		for($x=0; $x<$this->width; $x++) {
			for($y=0; $y<$this->height; $y++) {
				$srcClr1 = imagecolorsforindex($newIm, imagecolorat($this->resource,($x - 1 < 0 ? 0 : $x - 1), ($y - 1 < 0 ? 0 : $y - 1)));
				$srcClr2 = imagecolorsforindex($newIm, imagecolorat($this->resource, $x, $y));
				$r = intval($srcClr2["red"] + $sharp * ($srcClr2["red"] - $srcClr1["red"]));
				$g = intval($srcClr2["green"] + $sharp * ($srcClr2["green"] - $srcClr1["green"]));
				$b = intval($srcClr2["blue"] + $sharp * ($srcClr2["blue"] - $srcClr1["blue"]));
				$r = min(255, max($r, 0));
				$g = min(255, max($g, 0));
				$b = min(255, max($b, 0));
				if(($dstClr = imagecolorexact($this->resource, $r, $g, $b)) == -1) $DST_CLR = imagecolorallocate($this->resource, $r, $g, $b);
				$cnt++;
				if($dstClr == -1) die("color allocate faile at $x, $y ($cnt).");
				imagesetpixel($newIm, $x, $y, $dstClr);
			}
		}

		$this->resource = $newIm;
	}

	/**
	 * 保存图片
	 *
	 * @param string $path 文件路径
	 * @param int $quality 1 - 100
	 * @return void
	 */
	public function save($path,$quality=NULL)
	{
		return $this->createImage($path,$quality);
	}

	/**
	 * 生成图片
	 *
	 * @param string $createPath
	 * @param int $quality 1 - 100
	 * @return void
	 */
	public function createImage($createPath='',$quality=NULL)
	{
		$imageType = $this->current['imageType'];
		$function = $this->allowTypes[$imageType];

		if(function_exists($function)) {
			!$createPath AND $createPath = $this->set['createPath'];
			is_null($quality) AND $quality = $this->set['quality'];

			//imagepng 的质量工作范围在 0-9 之间，超出会报错
			if ($imageType == 'png' AND (!$quality OR $quality > 9)) $quality = 9;

			call_user_func_array($function,array($this->resource,$createPath,$quality));

			return imagedestroy($this->resource);// 释放

		} else {
			return FALSE;
		}
	}

	/**
	 * 获取资源句柄图像的尺寸
	 *
	 * @param resource $srcHandle
	 * @return
	 */
	public function getImageSize($src=NULL)
	{
		if(empty($src)) {
			$src =& $this->resource;
		}
		$size = array(
			$this->getImageWidth($src),
			$this->getImageHeight($src)
		);
		list($size['w'],$size['h']) = $size;
		return $size;
	}

	/**
	 * 获取资源句柄图像宽度
	 *
	 * @param resource $src
	 * @return float|int
	 */
	public function getImageWidth($src)
	{
		return imagesx($src);
	}

	/**
	 * 获取资源句柄图像高度
	 *
	 * @param resource $src
	 * @return float|int
	 */
	public function getImageHeight($src)
	{
		return imagesy($src);
	}

	/**
	 * 获取图像类型扩展名
	 *
	 * @param string $filepath
	 * @return string
	 */
	public function getImageType($filepath)
	{
		$typeList = array(
			1 => 'gif',
			2 => 'jpg',
			3 => 'png',
			//4 => 'swf',
			//5 => 'psd',
			6 => 'bmp',
			15 => 'wbmp'
		);

		$imgInfo = @getimagesize($filepath);
		return isset($typeList[$imgInfo[2]]) ? $typeList[$imgInfo[2]] : FALSE;
	}

	/**
	 * 检查图片类型是否合法
	 *
	 * @param string $imageExt
	 * @return bool
	 */
	private function checkValid($imageExt)
	{
		return array_key_exists($imageExt,$this->allowTypes);
	}

	/**
	 * 检查图片文件是否存在
	 *
	 * @param mixed $path
	 * @return void
	 */
	private function checkFileExists($path)
	{
		if(!file_exists($path)) {
			showError('Source File: '.$path.' doesn\'t exists!');
		}
	}

	/**
	 * 分析颜色
	 *
	 * @param string $color 十六进制网页形式颜色
	 * @return array
	 */
	private function parseColor($color)
	{
		$arr = array();
		$l = strlen($color);
		for($i=1; $i<$l; $i++) {
			$arr[] = hexdec(substr($color,$i,2));
			$i++;
		}
		return $arr;
	}

	/**
	 * 创建新图像用于绘制
	 *
	 * @param int $width
	 * @param int $height
	 * @return Image Resource
	 */
	private function imageCreate($width,$height) {
		if ($this->current['imageType'] == 'gif') {
			if ($this->set['transparency']) {
				$newIm = imagecreate($width,$height);
				$color = imagecolorallocate($newIm,$this->current['trans']['red'],$this->current['trans']['green'],$this->current['trans']['blue']);
				imagefill($newIm,0,0,$color);
				imagecolortransparent($newIm,$color);
			} else {
				$newIm = imagecreatetruecolor($width,$height);
			}
		} else {
			$newIm = imagecreatetruecolor($width,$height);
			if ($this->current['imageType'] == 'png') {
				imagealphablending($newIm,FALSE);
				imagesavealpha($newIm,TRUE);
			}
		}

		return $newIm;
	}

}

//#