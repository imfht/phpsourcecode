<?php
/**
 * image 扩展是 ModPHP 提供的用于进行图像处理的类，它基于 GD 库，并在其基础上增添了
 * 其他能力，如处理 bmp 图像、处理透明度、获取图像的 Data URL Scheme (Base64) 文本等。
 * 在对图像进行各种操作前，如画点、画线、画图形等，需要先设置好画笔的属性，如位置、颜色等。
 */
final class image{
	private static $file = ''; //文件名
	private static $src  = null; //文件资源
	private static $type = 0; //图像类型
	private static $mime = ''; //MIME 类型
	private static $width = 1; //宽度
	private static $height = 1; //高度
	private static $font = ''; //字体文件
	private static $fontsize = 12; //字体大小
	private static $x = 0; //横坐标
	private static $y = 0; //纵坐标
	private static $angle = 0; //旋转角度
	private static $color = 0; //前景颜色，设置时使用 rgba, rgb 或 16进制色(如 #FFF)
	private static $bgcolor = 0; //背景颜色，设置时使用 rgba, rgb 或 16进制色(如 #FFF)
	private static $opacity = 1; //不透明度
	private static $style = array(); //风格
	private static $points = array(); //多边形顶点坐标集，成对存在，如 array(0,0, 10,10) 则表示为(0,0) 和(10,10)
	private static $thickness = 1; //画笔厚度
	private static $brush = ''; //刷子
	private static $tile = ''; //贴图
	private static $filter = 0; //滤镜
	private static $quality = 100; //图像品质，仅 jpeg

	/** canvas() 创建画布 */
	private static function canvas($width, $height){
		$tmp = imagecreatetruecolor($width, $height);
		$color = imagecolorallocatealpha($tmp, 0, 0, 0, 127); //背景全透明
		imagesavealpha($tmp, true);
		imagealphablending($tmp, false);
		imagefill($tmp, 0, 0, $color);
		return $tmp;
	}

	/** getcolor() 解析并获取颜色 */
	private static function getcolor($str){
		$color = false;
		$src = &self::$src;
		if($str[0] == '#'){ //解析 #ffffff 格式的十六进制颜色代码
			$str = ltrim($str, '#');
			if(strlen($str) == 6){
				$color = imagecolorallocate($src, hexdec($str[0].$str[1]), hexdec($str[2].$str[3]), hexdec($str[4].$str[5]));
			}elseif(strlen($str) == 3) { //解析 #fff 形式缩写
				$color = imagecolorallocate($src, hexdec($str[0].$str[0]), hexdec($str[1].$str[1]), hexdec($str[2].$str[2]));
			}
		}elseif(stripos($str, 'rgb') === 0){ //解析 rgb/rgba 颜色
			$str = explode(',', str_replace(array('rgba', 'rgb', '(', ')', ' '), '', $str));
			if(isset($str[3])){ //rgba
				$str[3] = 127 - ceil((float)$str[3] * 127); //透明度
				$color = imagecolorallocatealpha($src, $str[0], $str[1], $str[2], $str[3]);
			}else{ //rgb
				$color = imagecolorallocate($src, $str[0], $str[1], $str[2]);
			}
		}
		return $color;
	}

	/**
	 * imagecopymergealpha() 合并图像并保留透明度
	 * @static
	 * @param  resource $dst_im 目标图像资源
	 * @param  resource $src_im 原图像资源
	 * @param  int      $dst_x  目标图像 X 轴坐标
	 * @param  int      $dst_y  目标图像 Y 轴坐标
	 * @param  int      $src_x  原图像 X 轴坐标
	 * @param  int      $src_y  原图像 Y 轴坐标
	 * @param  int      $src_w  原图像裁剪宽度
	 * @param  int      $src_h  原图像裁剪高度
	 * @param  int      $pct    合并百分比，1 - 100
	 * @return bool
	 */
	static function imagecopymergealpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 
		if(!$pct) return true;
		elseif($pct == 100){ //全合并，原图像完全被目标图像覆盖
			imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
			return true;
		}
		$pct /= 100;
		$w = imagesx($src_im);
		$h = imagesy($src_im);
		imagealphablending($src_im, false);
		$minalpha = 127;
		for($x = 0;$x < $w;$x++){ //处理矩阵
			for($y = 0;$y < $h;$y++){
				$alpha =(imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
				if($alpha < $minalpha){ 
					$minalpha = $alpha;
				}
			}
			for($x = 0;$x < $w;$x++){ 
				for($y = 0;$y < $h;$y++){ 
					$colorxy = imagecolorat($src_im, $x, $y);
					$alpha =($colorxy >> 24) & 0xFF;
					if($minalpha !== 127){ 
						$alpha = 127 + 127 * $pct *($alpha - 127) /(127 - $minalpha);
					}else{ 
						$alpha += 127 * $pct;
					}
					$alphacolorxy = imagecolorallocatealpha($src_im,($colorxy >> 16) & 0xFF,($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);
					if(!imagesetpixel($src_im, $x, $y, $alphacolorxy)){ 
						return false;
					}
				}
			}
			imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
		}
		return true;
	}

	/** 
	 * imagecreatefrombmp() 从 BMP 文件创建图像
	 * @static
	 * @param  string $filename 文件名
	 * @return source           文件资源
	 */
	static function imagecreatefrombmp($filename){
		if(!$f1 = fopen($filename, "rb")) return FALSE;
		$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
		if($FILE['file_type'] != 19778) return FALSE;
		$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.'/Vcompression/Vsize_bitmap/Vhoriz_resolution'.'/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
		$BMP['colors'] = pow(2, $BMP['bits_per_pixel']);
		if($BMP['size_bitmap'] == 0){
			$BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
		}
		$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
		$BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
		$BMP['decal'] =($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
		$BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
		$BMP['decal'] = 4 -(4 * $BMP['decal']);
		if($BMP['decal'] == 4) $BMP['decal'] = 0;
		$PALETTE = array();
		if($BMP['colors'] < 16777216){
			$PALETTE = unpack('V' . $BMP['colors'], fread($f1, $BMP['colors'] * 4));
		}
		$IMG = fread($f1, $BMP['size_bitmap']);
		$VIDE = chr(0);
		$res = imagecreatetruecolor($BMP['width'], $BMP['height']);
		$P = 0;
		$Y = $BMP['height'] - 1;
		while($Y >= 0){
			$X = 0;
			while($X < $BMP['width']){
				if($BMP['bits_per_pixel'] == 24)
					$COLOR = unpack("V", substr($IMG, $P, 3) . $VIDE);
				elseif($BMP['bits_per_pixel'] == 16){
					$COLOR = unpack("n", substr($IMG, $P, 2));
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				}elseif($BMP['bits_per_pixel'] == 8){
					$COLOR = unpack("n", $VIDE . substr($IMG, $P, 1));
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				}elseif($BMP['bits_per_pixel'] == 4){
					$COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
					if(($P * 2) % 2 == 0)
						$COLOR[1] =($COLOR[1] >> 4);
					else  
						$COLOR[1] =($COLOR[1] & 0x0F);
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				}elseif($BMP['bits_per_pixel'] == 1){
					$COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
					if(($P * 8) % 8 == 0)
						$COLOR[1] = $COLOR[1] >> 7;
					elseif(($P * 8) % 8 == 1)
						$COLOR[1] =($COLOR[1] & 0x40) >> 6;
					elseif(($P * 8) % 8 == 2)
						$COLOR[1] =($COLOR[1] & 0x20) >> 5;
					elseif(($P * 8) % 8 == 3)
						$COLOR[1] =($COLOR[1] & 0x10) >> 4;
					elseif(($P * 8) % 8 == 4)
						$COLOR[1] =($COLOR[1] & 0x8) >> 3;
					elseif(($P * 8) % 8 == 5)
						$COLOR[1] =($COLOR[1] & 0x4) >> 2;
					elseif(($P * 8) % 8 == 6)
						$COLOR[1] =($COLOR[1] & 0x2) >> 1;
					elseif(($P * 8) % 8 == 7)
						$COLOR[1] =($COLOR[1] & 0x1);
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				}else return FALSE;
				imagesetpixel($res, $X, $Y, $COLOR[1]);
				$X++;
				$P += $BMP['bytes_per_pixel'];
			}
			$Y--;
			$P+=$BMP['decal'];
		}
		fclose($f1);
		return $res;
	}

	/** getxy() 获取 x, y */
	private static function getxy($x = 0, $y = 0){
		$x = $x ?: self::$x;
		$y = $y ?: self::$y;
		$x = $x >= 0 ? $x : imagesx(self::$src) + $x;
		$y = $y >= 0 ? $y : imagesy(self::$src) + $y;
		return array($x, $y);
	}

	/**
	 * set() 设置属性，在对图像进行各种操作前，如画点、画线、画图形等，需要先设置好画笔的属性，如位置、颜色等
	 * @static
	 * @param  string $k 属性名
	 * @param  string $v 属性值
	 * @return object    当前对象
	 */
	static function set($k, $v = null){
		$src = &self::$src;
		if($v === null) return isset(self::${$k}) ? self::${$k}: false;
		if($k == 'src'){
			if($src) imagedestroy($src);
		}elseif($k == 'style'){
			imagesetstyle($src, $v);
		}elseif($k == 'brush' || $k == 'tile') {
			$info = getimagesize($v);
			$func = 'imagecreatefrom'.substr($info['mime'], strpos($info['mime'], '/') + 1);
			if(function_exists($func)){
				$tmp = $func($v);
				$color = imagecolorallocate($tmp, 255, 255, 255);
				imagecolortransparent($tmp, $color);
				if($k == 'brush') imagesetbrush($src, $tmp); //设置笔刷
				elseif($k == 'tile') imagesettile($src, $tmp); //设置贴图
				imagedestroy($tmp);
			}
		}elseif($k == 'color' || $k == 'bgcolor'){ //设置颜色
			if(!$src) $src = self::canvas(1,1);
			$v = self::getcolor($v);
		}elseif($k == 'thickness'){
			imagesetthickness($src, $v);
		}
		self::${$k}= $v;
		return new self;
	}

	/**
	 * open() 打开一个图像
	 * @static
	 * @param  string  $file 图像路径及名称，无论是否存在这个文件
	 * @return object        当前对象
	 */
	static function open($file){
		self::$file = $file;
		$src = &self::$src;
		$src = @getimagesize($file);
		if($src){
			list($w, $h, $t) = $src;
			self::set('width', $w)->set('height', $h)->set('type', $t)->set('mime', $src['mime']); //设置图像属性
			$func = 'imagecreatefrom'.substr($src['mime'], strpos($src['mime'], '/') + 1);
			if(function_exists($func)){
				$src = $func($file);
			}elseif($src['mime'] == 'image/x-ms-bmp'){ //打开 BMP 图像
				$src = self::imagecreatefrombmp($file);
			}else{
				goto make;
			}
			imagesavealpha($src, true);
			imagealphablending($src, false);
			self::resize($w, $h); //将图像资源采样并保存在新画布上
		}else{
			make: //新建空白图像
			$src = self::canvas(self::$width, self::$height);
			list($x, $y) = self::getxy();
			imagefill($src, $x, $y, self::$bgcolor); //填充背景色
			$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			if($ext == 'jpg') $mime = 'jpeg'; //jpg
			elseif($ext == 'bmp') $mime = 'x-ms-bmp'; //bmp
			else $mime = $ext;
			self::$mime = 'image/'.$mime;
		}
		return new self;
	}

	/**
	 * resize() 调整图像大小
	 * @static
	 * @param  int    $width  [可选]宽度，默认 0，即自动设置
	 * @param  int    $height [可选]高度，默认 0，即自动设置
	 * @return object         当前对象
	 */
	static function resize($width = 0, $height = 0){
		$w = imagesx(self::$src);
		$h = imagesy(self::$src);
		if($width && !$height){
			$height = $width/$w * $h; //等比例调整高度
		}elseif(!$width && $height){
			$width = $height/$h * $w; //等比例调整宽度
		}elseif(!$width && !$height){
			return new self;
		}
		$tmp = self::canvas($width, $height);
		imagecopyresampled($tmp, self::$src, 0, 0, 0, 0, $width, $height, $w, $h); //采样保存
		return self::set('width', $width)->set('height', $height)->set('src', $tmp); //更新图像属性
	}

	/**
	 * cut() 裁剪图像
	 * @static
	 * @param  int     $width  宽度
	 * @param  int     $height 高度
	 * @param  integer $x      [可选]起始横坐标
	 * @param  integer $y      [可选]起始纵坐标
	 * @return object          当前对象
	 */
	static function cut($width, $height, $x = 0, $y = 0) {
		if($x) self::set('x', $x);
		if($y) self::set('y', $y);
		$tmp = self::canvas($width, $height);
		list($x, $y) = self::getxy();
		imagecopy($tmp, self::$src, 0, 0, $x, $y, $width, $height);
		return self::set('width', $width)->set('height', $height)->set('x', 0)->set('y', 0)->set('src', $tmp);
	}

	/**
	 * opacity() 设置图像不透明度
	 * @static
	 * @param  float|int $opacity 不透明度 0 - 1;
	 * @return object             当前对象
	 */
	static function opacity($opacity){
		$w = imagesx(self::$src);
		$h = imagesy(self::$src);
		$tmp = self::canvas($w, $h);
		self::imagecopymergealpha($tmp, self::$src, 0, 0, 0, 0, $w, $h, $opacity * 100); //保存真彩色透明度
		return self::set('src', $tmp)->set('opacity', $opacity);
	}

	/**
	 * dot() 画点
	 * @static
	 * @param  integer $x [可选]横坐标
	 * @param  integer $y [可选]纵坐标
	 * @return object     当前对象
	 */
	static function dot($x = 0, $y = 0){
		if($x) self::set('x', $x);
		if($y) self::set('y', $y);
		list($x, $y) = self::getxy();
		$tkns = self::$thickness;
		if($tkns == 1){
			imagesetpixel(self::$src, $x, $y, self::$color); //画 1 像素的点
		}else{
			imagefilledellipse(self::$src, $x, $y, $tkns, $tkns, self::$color); //超过 1 像素的圆形点
		}
		return new self;
	}

	/**
	 * line() 画线
	 * @static
	 * @param  int    $x 结束点横坐标
	 * @param  int    $y 结束点纵坐标
	 * @return object    当前对象
	 */
	static function line($x, $y){
		list($_x, $_y) = self::getxy();
		list($x, $y) = self::getxy($x, $y);
		imageline(self::$src, $_x, $_y, $x, $y, self::$color);
		return new self;
	}

	/**
	 * arc() 画椭圆弧
	 * @static
	 * @param  int    $start 开始角度
	 * @param  int    $end   结束角度
	 * @return object        当前对象
	 */
	static function arc($start, $end){
		list($x, $y) = self::getxy();
		list($_x, $_y) = self::getxy($start, $end);
		imagearc(self::$src, $x, $y, self::$width, self::$height, $_x, $_y, self::$color);
		return new self;
	}

	/**
	 * filledarc() 画填充的椭圆弧
	 * @static
	 * @param  int    $start 开始角度
	 * @param  int    $end   结束角度
	 * @param  int    $style 风格
	 * @return object        当前对象
	 */
	static function filledarc($start, $end, $style = IMG_ARC_CHORD){
		list($x, $y) = self::getxy();
		list($_x, $_y) = self::getxy($start, $end);
		imagefilledarc(self::$src, $x, $y, self::$width, self::$height, $_x, $_y, self::$color, $style);
		return new self;
	}

	/**
	 * ellipse() 画椭圆
	 * @static
	 * @return object 当前对象
	 */
	static function ellipse(){
		list($x, $y) = self::getxy();
		imageellipse(self::$src, $x, $y, self::$width, self::$height, self::$color);
		return new self;
	}

	/**
	 * filledellipse() 画填充的椭圆
	 * @static
	 * @return object  当前对象
	 */
	static function filledellipse(){
		list($x, $y) = self::getxy();
		imagefilledellipse(self::$src, $x, $y, self::$width, self::$height, self::$color);
		return new self;
	}

	/**
	 * polygon() 画多边形
	 * @static
	 * @param  string $points [可选]据点
	 * @return object         当前对象
	 */
	static function polygon($points = ''){
		if($points) self::set('points', $points);
		imagepolygon(self::$src, self::$points, count(self::$points)/2, self::$color);
		return new self;
	}

	/**
	 * filledpolygon() 画填充的多边形
	 * @static
	 * @param  string $points [可选]据点
	 * @return object         当前对象
	 */
	static function filledpolygon($points = ''){
		if($points) self::set('points', $points);
		imagefilledpolygon(self::$src, self::$points, count(self::$points)/2, self::$color);
		return new self;
	}

	/**
	 * rectangle() 画矩形
	 * @static
	 * @return object    当前对象
	 */
	static function rectangle(){
		list($x, $y) = self::getxy();
		imagerectangle(self::$src, $x, $y, $x+self::$width, $y+self::$height, self::$color);
		return new self;
	}

	/**
	 * filledrectangle() 画填充的矩形
	 * @static
	 * @return object    当前对象
	 */
	static function filledrectangle(){
		list($x, $y) = self::getxy();
		imagefilledrectangle(self::$src, $x, $y, $x+self::$width, $y+self::$height, self::$color);
		return new self;
	}

	/**
	 * fill() 填充颜色
	 * @static
	 * @param  string $color [可选]填充色(前景色)
	 * @return object        当前对象
	 */
	static function fill($color = ''){
		if($color && $color != IMG_COLOR_TILED) self::set('color', $color);
		list($x, $y) = self::getxy();
		imagefill(self::$src, $x, $y, self::$color);
		return new self;
	}

	/**
	 * rotate() 旋转图像，旋转图像会改变宽和高，原图形区域大小不变，外部会被对角线拉伸
	 * @static
	 * @param  int    $angle [可选]旋转角度
	 * @return object        当前对象
	 */
	static function rotate($angle = 0){
		if($angle) self::set('angle', $angle);
		$src = imagerotate(self::$src, self::$angle, self::$bgcolor);
		return self::set('src', $src)->set('width', imagesx($src))->set('height', imagesy($src));
	}

	/**
	 * reverse() 翻转图像
	 * @static
	 * @param  int    $direction 翻转方向：1 左右翻转，2 上下翻转
	 * @return object            当前对象
	 */
	static function reverse($direction){
		$w = imagesx(self::$src);
		$h = imagesy(self::$src);
		$tmp = self::canvas($w, $h);
		if($direction == 1){ //左右反转
			for($x=0 ;$x < $w; $x++){
				imagecopy($tmp, self::$src, $w-$x-1, 0, $x, 0, 1, $h);
			}
			self::set('src', $tmp);
		}elseif($direction == 2){ //上下颠倒
			for($y=0 ;$y < $h; $y++){
				imagecopy($tmp, self::$src, 0, $h-$y-1, 0, $y, $w, 1);
			}
			self::set('src', $tmp);
		}
		return new self;
	}

	/**
	 * filter() 设置滤镜
	 * @static
	 * @param  int     $filter [可选]滤镜
	 * @param  integer $arg1   [可选]可选参数1
	 * @param  integer $arg2   [可选]可选参数2
	 * @param  integer $arg3   [可选]可选参数3
	 * @return object          当前对象
	 */
	static function filter($filter = 0, $arg1 = 0, $arg2 = 0, $arg3 = 0){
		if($filter) self::set('filter', $filter);
		imagefilter(self::$src, self::$filter, $arg1, $arg2, $arg3);
		return new self;
	}

	/**
	 * text() 插入文本
	 * @static
	 * @param  string  $str      文本
	 * @param  boolean $vertical [可选]纵向插入，默认 false
	 * @return object            当前对象
	 */
	static function text($str, $vertical = false){
		if($vertical) self::set('angle', 90);
		$fs = self::$fontsize;
		$clr = self::$color;
		$src = &self::$src;
		list($x, $y) = self::getxy();
		if(self::$font){ //使用外部字体
			$y = $fs + self::$y;
			imagettftext($src, $fs, self::$angle, $x, $y, $clr, self::$font, $str);
		}else{ //使用内置字体
			if($vertical){
				imagestringup($src, $fs-7, $x, $y, $str, $clr); //纵向文本
			}else{
				imagestring($src, $fs-7, $x, $y, $str, $clr);
			}
		}
		return new self;
	}

	/**
	 * tile() 设置贴图
	 * @static
	 * @param  string $file    [可选]贴图文件
	 * @param  int    $width   [可选]宽度
	 * @param  int    $height  [可选]高度
	 * @param  int    $opacity [可选]不透明度
	 * @param  int    $rotate  [可选]旋转角度
	 * @return object          当前对象
	 */
	static function tile($file = '', $width = 0, $height = 0, $opacity = 1, $rotate = 0){
		if($file) self::set('tile', $file);
		$src = getimagesize($file);
		$func = 'imagecreatefrom'.substr($src['mime'], strpos($src['mime'], '/') + 1);
		if(function_exists($func)){
			$tmp = $func($file);
		}elseif(self::$mime == 'image/x-ms-bmp'){
			$tmp = self::imagecreatefrombmp($file); //BMP 贴图
		}else{
			return new self;
		}
		$width = $width ?: imagesx($tmp);
		$height = $height ?: imagesy($tmp);
		$_tmp = self::canvas($width, $height);
		imagecopyresampled($_tmp, $tmp, 0, 0, 0, 0, $width, $height, imagesx($tmp), imagesy($tmp)); //贴图采样
		if($rotate){
			$_tmp = imagerotate($_tmp, $rotate, self::$bgcolor); //旋转贴图
		}
		list($x, $y) = self::getxy();
		imagecopymerge(self::$src, $_tmp, $x, $y, 0, 0, $width, $height, $opacity * 100); //将贴图合并到原图上
		imagedestroy($tmp);
		imagedestroy($_tmp);
		return new self;
	}

	/**
	 * save() 保存图像
	 * @static
	 * @param  string  $file  [可选]文件名，不设置则为打开时的文件名
	 * @param  boolean $close [可选]保存后关闭图像，默认 true
	 * @return bool
	 */
	static function save($file = '', $close = true){
		return self::output($file ?: self::$file, $close);
	}

	/**
	 * output() 输出图像到文件或浏览器
	 * @static
	 * @param  string  $file  [可选]文件名，不设置则输出到浏览器
	 * @param  boolean $close [可选]输出后关闭图像，默认 true
	 * @return boolean
	 */
	static function output($file = '', $close = true){
		imagealphablending(self::$src, false);
		imagesavealpha(self::$src, true);
		$mime = self::$mime;
		$_file = $file ?: self::$file;
		$isURL = strpos($_file, '://');
		if($_file == self::$file && $mime){
			if($mime == 'image/x-ms-bmp'){
				$func = 'imagejpeg'; //bmp 图像当作 jpeg 保存
			}else{
				$func = 'image'.substr($mime, strpos($mime, '/') + 1);
			}
		}else{
			$ext = strtolower(pathinfo($_file, PATHINFO_EXTENSION));
			if($ext == 'jpg' || $ext == 'bmp') $ext = 'jpeg';
			$func = 'image'.$ext;
		}
		if(function_exists($func)){
			if($file){
				$dir = dirname($file);
				if(!is_dir($dir)) mkdir($dir, 0777, true); //创建文件夹
			}
			if($func == 'imagejpeg'){
				$result = imagejpeg(self::$src, $file ?: null, self::$quality); //jpeg 图像可以设置质量
			}else{
				$result = $func(self::$src, $file ?: null);
			}
			if($close) self::close();
			return $result;
		}else return false;
	}

	/**  close() 关闭图像 */
	static function close(){
		return self::$src && imagedestroy(self::$src);
	}

	/**
	 * getBinary() 获取图像的二值化文本
	 * @static
	 * @param  boolean $reverse [可选]反转 0 和 1，默认 false
	 * @param  boolean $close   [可选]获取后关闭图像，默认 true
	 * @return string           由 0 和 1 组成的二值化文本
	 */
	static function getBinary($reverse = false, $close = true){
		$data = array();
		for($i=0; $i < self::$height; ++$i){
			for($j=0; $j < self::$width; ++$j){
				$rgb = imagecolorsforindex(self::$src, imagecolorat(self::$src, $j, $i));
				if(!isset($data[$i])) $data[$i] = "";
				if(!$rgb['red'] && !$rgb['green'] && !$rgb['blue'] && !self::$bgcolor){
					$data[$i] .= (int)$reverse;
				}elseif($rgb['red'] < 125 || $rgb['green'] < 125 || $rgb['blue'] < 125){
					$data[$i] .= (int)!$reverse;
				}else{
					$data[$i] .= (int)$reverse;
				}
			}
		}
		if($close) self::close();
		return implode("\n", $data);
	}

	/**
	 * getDataURL() 获取 base64 编码的 Data URI Scheme 数据 
	 * @static
	 * @param  bool   $close [可选]获取后关闭图像，默认 true
	 * @return string        经过 base64 编码的 Data URI Scheme 数据
	 */
	static function getDataURL($close = true){
		ob_start();
		self::output('', $close);
		return 'data:'.self::$mime.';base64,'.base64_encode(ob_get_clean());
	}

	/** getBase64() image::getDataURL() 的别名 */
	static function getBase64($close = true){
		return self::getDataURL($close);
	}

	/**
	 * readFromDataURL() 从 base64 编码的 Data URI Scheme 数据中读取图像
	 * @param  string $data 经过 base64 编码的 Data URI Scheme 数据
	 * @return object       当前对象
	 */
	static function readFromDataURL($data){
		$file = '~'.uniqid();
		file_put_contents($file, base64_decode(ltrim(strstr($data, ','), ','))); //创建缓存文件
		$obj = self::open($file); //从缓存文件中读取图像
		self::$file = '';
		unlink($file); //删除缓存文件
		return $obj;
	}
}