<?php

/**
 * 验证码图片类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Tool
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

namespace sy\tool;
use \Sy;

class YCaptcha {
	protected $im = NULL;
	protected $fonts = ['ArialRounded', 'Broadw', 'Minecraft', 'SpicyRice'];
	protected $font = 0;
	protected $height;
	protected $width;
	protected $color;
	protected $chineseWord = NULL;
	protected $bgcolor;
	static $_instance = NULL;
	public static function i() {
		if (self::$_instance === NULL) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	/**
	 * 构造函数
	 * @access public
	 */
	public function __construct() {
		$this->font = 0;
	}
	/**
	 * 创建一个image
	 * @access public
	 * @param int $width 宽度
	 * @param int $height 高度
	 * @param mixed $bgcolor 背景颜色
	 */
	public function create($width, $height, $bgcolor) {
		if ($this->im !== NULL) {
			imagedestroy($this->im);
		}
		$this->im = imagecreatetruecolor($width, $height);
		$this->width = $width;
		$this->height = $height;
		$this->color = [];
		$this->color['white'] = imagecolorallocate($this->im, 255, 255, 255);
		$this->color['black'] = imagecolorallocate($this->im, 0, 0, 0);
		$this->color['gray'] = imagecolorallocate($this->im, 118, 151, 199); //灰色
		$this->color['green'] = imagecolorallocate($this->im, 86, 128, 20); //绿色
		$this->color['red'] = imagecolorallocate($this->im, 255, 0, 0); //红色
		$this->color['blue'] = imagecolorallocate($this->im, 31, 114, 164); //蓝色
		$this->color['orange'] = imagecolorallocate($this->im, 194, 72, 21); //橙色
		$this->color['purple'] = imagecolorallocate($this->im, 147, 14, 169); //紫色
		if (is_array($bgcolor)) {
			$this->bgcolor = imagecolorallocate($this->im, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
		} elseif (isset($this->color[$bgcolor])) {
			$this->bgcolor = $this->color[$bgcolor];
		} else {
			$this->bgcolor = $this->color['white'];
		}
		imagefilledrectangle($this->im, 0, 0, $width, $height, $this->bgcolor);
	}
	/**
	 * 设置font
	 * @access public
	 * @param int $font 字体编号
	 */
	public function setFont($font) {
		$this->font = $font;
	}
	/**
	 * 添加颜色
	 * @access public
	 * @param string $name 颜色名称
	 * @param int $r RGB
	 * @param int $g RGB
	 * @param int $b RGB
	 */
	public function addColor($name, $r, $g, $b) {
		$this->color[$name] = imagecolorallocate($this->im, $r, $g, $b);
	}
	/**
	 * 画噪点干扰
	 * @access public
	 * @param int $num 噪点数量
	 */
	public function drawPoint($num) {
		for ($i = 0; $i < $num; $i++) {
			imagesetpixel($this->im, mt_rand(0, $this->width), mt_rand(0, $this->height), $this->color['black']);
		}
	}
	/**
	 * 画椭圆干扰
	 * @access public
	 * @param int $num 椭圆数量
	 */
	public function drawArc($num) {
		for ($i = 0; $i < $num; $i++) {
			$fontcolor = imagecolorallocate($this->im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imagearc($this->im, mt_rand(-10, $this->width), mt_rand(-10, $this->height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $fontcolor);
		}
	}
	/**
	 * 写字
	 * @access public
	 * @param string $text 文字
	 * @param int $size 字体大小
	 * @param int $qinx 倾斜程度
	 * @param int $float_x 左右浮动
	 * @param int $float_y 上下浮动
	 * @param int $padding 每个字的padding
	 * @param array $color 允许的color，空为所有
	 */
	public function write($text, $qinx = 15, $float_x = 2, $floay_y = 2, $padding = 3, $color = NULL) {
		if ($color === NULL) {
			$color = $this->color;
		} else {
			foreach ($color as $k => $v) {
				if (is_array($v)) {
					$color[$k] = imagecolorallocate($this->im, $v[0], $v[1], $v[2]);
				} else {
					$color[$k] = $this->color[$v];
				}
			}
		}
		$length = mb_strlen($text);
		//计算
		$real_text_width = ($this->width - ($length * $padding * 2));
		$one_text_width = $real_text_width / $length;
		//字体大小
		$font_size = $one_text_width - 5;
		$real_text_height = ($this->height - ($padding * 2));
		$x = $padding;
		$y = $this->height - $padding;
		$font = Sy::$frameworkDir . 'data/' . $this->fonts[$this->font] . '.ttf';
		for ($i = 0; $i <= $length; $i++) {
			$qx = mt_rand(-$qinx, $qinx);
			//保证颜色和背景不同
			do {
				$this_color = $color[array_rand($color, 1)];
			} while ($this_color === $this->bgcolor);
			imagettftext($this->im, $font_size, $qx, $x + mt_rand(-$float_x, $float_x), $y + mt_rand(-$floay_y, $floay_y), $this_color, $font, mb_substr($text, $i, 1));
			//左边距
			$x += $padding * 2 + $one_text_width;
		}
	}
	/**
	 * 获得随机中文
	 * @access public
	 * @param int $word_num 词语数量
	 * @return string
	 */
	public function getChineseText($word_num = 2) {
		if ($this->chineseWord === NULL) {
			$this->chineseWord = require (SY_ROOT . 'data/chineseWord.php');
		}
		$length = count($this->chineseWord) - 1;
		if ($word_num >= $length) {
			return NULL;
		}
		if ($word_num === 1) {
			$word = array_rand($this->chineseWord, $word_num);
			return $this->chineseWord[$word];
		} else {
			$word = array_rand($this->chineseWord, $word_num);
			$word = array_intersect_key($this->chineseWord, $word);
			return implode('', $word);
		}
	}
	/**
	 * 输出图像
	 * @access public
	 * @param string $type 图像格式，可选：png，gif，jpg，wbmp，webp，xbm
	 * 注意：webp需要php>=5.5
	 */
	public function show($type = 'png') {
		switch ($type) {
			case 'png':
				imagepng($this->im);
				break;
			case 'gif':
				imagegif($this->im);
				break;
			case 'jpg':
				imagejpeg($this->im);
				break;
			case 'wbmp':
				imagewbmp($this->im);
				break;
			case 'webp':
				imagewebp($this->im);
				break;
			case 'xbm':
				imagexbm($this->im);
				break;
		}
	}
	/**
	 * 析构函数
	 */
	public function __destruct() {
		if ($this->im !== NULL) {
			imagedestroy($this->im);
			$this->im = NULL;
		}
	}
}
