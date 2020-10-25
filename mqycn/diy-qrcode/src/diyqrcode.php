<?php

/**
 * 类名：DiyQrcode
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：http://gitee.com/mqycn/diy-qrcode/
 * 说明：核心类
 */

// 依赖：https://sourceforge.net/projects/phpqrcode/
require_once dirname(__FILE__) . '/../vendor/phpqrcode.php';

class DiyQrcode {

	private $qrLevel = 'M'; //二维码校正级别，可选：L、M、Q、H
	private $qrMatrix = 6; //矩阵的大小， 1-10

	private $qrTemplate = 'http://gitee.com/mqycn/diy-qrcode?qrcode=[KEY]'; //落地页路径模版

	private $qrType = 'png'; //二维码 输出类型

	private $qrX = 0; //插入点 X 的位置
	private $qrY = 0; //插入点 y 的位置
	private $qrW = 100; //二维码宽度
	private $qrH = 100; //二维码高度

	private $qrFont = ''; //字体文件
	private $qrText = false; //显示的文字，为false时不输出
	private $qrTextsize = 12; //字体大小
	private $qrTextx = 0; //文本插入点 X 的位置
	private $qrTexty = 0; //字体插入点 Y 的位置

	private $qrBackground = 'background.jpg'; //海报背景图
	private $qrSkin = 'skin1'; //海报背景图存放路径，替换路径中的[SKIN]

	private $key = 'DiyQrcode Demo'; //二维码正文内容

	public function __construct($config = array()) {
		$this->setConf($config);
	}

	/**
	 * 设置配置信息
	 */
	public function setConf($config = array()) {
		if (!is_array($config)) {
			$config = array();
		}

		//设置校正级别
		$this->saveConfig($config, 'level', array('L', 'M', 'Q', 'H'));

		//设置矩阵大小
		$this->saveConfig($config, 'matrix', range(1, 10));

		//设置矩阵大小
		$this->saveConfig($config, 'type', array('png', 'jpg'));

		//设置落地页地址
		$this->saveConfig($config, 'template');

		//设置背景图片地址和路径
		$this->saveConfig($config, 'skin');
		$this->saveConfig($config, 'background');

		//设置二维码的位置和尺寸
		$this->saveConfig($config, 'x', '/^[0-9]{1,4}$/');
		$this->saveConfig($config, 'y', '/^[0-9]{1,4}$/');
		$this->saveConfig($config, 'w', '/^[0-9]{1,4}$/');
		$this->saveConfig($config, 'h', '/^[0-9]{1,4}$/');

		//字体和文字设置
		$this->saveConfig($config, 'font', '/\.ttf$/');
		$this->saveConfig($config, 'text');
		$this->saveConfig($config, 'textsize', range(5, 50));
		$this->saveConfig($config, 'textx', '/^[0-9]{1,4}$/');
		$this->saveConfig($config, 'texty', '/^[0-9]{1,4}$/');
		$this->saveConfig($config, 'textcolor', '/^#[0-9A-Fa-f]{6}$/');

		//配置配置项是否正确
		list($image_path, $image_ext) = $this->getBackground();
		if (!is_file($image_path)) {
			throw new Exception("背景图片({$image_path})不存在", 1);
			die();
		}

		//如果需要输出文字，判断字体文件是否合法
		if ($this->qrText !== false) {
			$font_path = $this->getFont();
			if (!is_file($font_path)) {
				throw new Exception("字体文件({$font_path})不存在", 1);
				die();
			}
		}

	}

	/**
	 * 设置二维码正文
	 */
	public function setKey($key = '') {
		$this->key = $key;
	}

	/**
	 * 输出二维码
	 */
	public function output($file_name = null) {

		//打开模板图
		$image = $this->openImage();

		//添加二维码
		$this->appendQrcode($image);

		//如果存在 文字，则打印文字
		$this->appendText($image);

		header('content-type: image/' . $this->qrType);
		switch ($this->qrType) {
		case 'jpg':
			imagejpeg($image, $file_name, 50);
			break;
		default:
			imagepng($image, $file_name);
		}

		imagedestroy($image);

		if ($file_name === null) {
			die();
		} else {
			header('content-type: text/html'); //修改为正常的文档类型
			return array(
				'filename' => $file_name,
				'url' => $this->getUrl(str_replace('./', '[WEB_URI]', $file_name)),
			);
		}
	}

	/**
	 * 保存为文件
	 */
	public function save($file_name) {
		return $this->output($file_name);
	}

	/**
	 * 检查配置项是否正确
	 */
	private function saveConfig($config, $key, $validate = false) {
		if (isset($config[$key])) {

			$val = $config[$key];
			if (is_array($validate)) {
				if (!in_array($val, $validate)) {
					throw new Exception("配置项[{$key}]的值({$val})错误，有效值：[" . join(',', $validate) . "]", 1);
					die();
				}
			} elseif (is_string($validate)) {
				if (!preg_match($validate, $val)) {
					throw new Exception("配置项[{$key}]的值({$val})错误", 1);
					die();
				}
			} else {
				//无需校验
			}

			//设置值
			$conf_item = 'qr' . ucfirst($key);
			$this->$conf_item = $config[$key];

		}
	}

	/**
	 * 生成二维码链接
	 */
	private function getUrl($url = false) {
		if ($url === false) {
			$url = $this->qrTemplate;
		}

		$url = str_replace('[KEY]', $this->key, $url);

		//替换访问的域名
		$scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http');
		$root_uri = $scheme . '://' . $_SERVER['SERVER_NAME'] . '/';
		$url = str_replace('[WEB_ROOT]', $root_uri, $url);

		//替换访问的路径
		$ins_arr = explode('/', $_SERVER['SCRIPT_NAME']);
		$ins_arr[count($ins_arr) - 1] = '';
		unset($ins_arr[0]);
		$ins_path = join('/', $ins_arr);
		$url = str_replace('[WEB_PATH]', $ins_path, $url);

		//替换访问的域名和路径
		$url = str_replace('[WEB_URI]', $root_uri . $ins_path, $url);

		return $url;
	}

	/**
	 * 生成二维码
	 */
	private function createQrcode() {

		//二维码落地链接
		$qr_url = $this->getUrl();

		ob_start();
		QRcode::png($qr_url, false, $this->qrLevel, $this->qrMatrix, 2);
		$image = ob_get_contents();
		ob_end_clean();

		return imagecreatefromstring($image);
	}

	/**
	 * 在模板图上添加二维码
	 */
	private function appendQrcode($image) {

		//生成二维码
		$qrcode = $this->createQrcode();

		$src_width = imagesx($qrcode);
		$src_height = imagesy($qrcode);

		//合并图片
		$dst_x = $this->qrX; //目标图像开始 x 坐标
		$dst_y = $this->qrY; //目标图像开始 y 坐标，x,y同为 0 则从左上角开始

		$src_x = 0; //拷贝图像开始 x 坐标
		$src_y = 0; //拷贝图像开始 y 坐标，x,y同为 0 则从左上角开始拷贝
		$src_w = $this->qrW; //（从 src_x 开始）拷贝的宽度
		$src_h = $this->qrH; //（从 src_y 开始）拷贝的高度

		//合并图片
		imagecopyresized($image, $qrcode, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $src_width, $src_height);
		imagedestroy($qrcode);

	}

	/**
	 * 在模板图上增加文字
	 */
	private function appendText($image) {

		if ($this->qrText !== false) {

			$size = $this->qrTextsize; //字体的尺寸。根据 GD 的版本，为像素尺寸（GD1）或点（磅）尺寸（GD2）。
			$fontfile = $this->getFont(); //是想要使用的 TrueType 字体的路径
			$text = str_replace('[KEY]', $this->key, $this->qrText); //UTF-8 编码的文本字符串
			$angle = 0; //角度制表示的角度，0 度为从左向右读的文本。更高数值表示逆时针旋转。例如 90 度表示从下向上读的文本。
			$x = $this->qrTextx; //由 x，y 所表示的坐标定义了第一个字符的基本点（大概是字符的左下角）。这和 imagestring() 不同，其 x，y 定义了第一个字符的左上角。例如 "top left" 为 0, 0。
			$y = $this->qrTexty; //Y 坐标。它设定了字体基线的位置，不是字符的最底端。

			//颜色索引。使用负的颜色索引值具有关闭防锯齿的效果
			$color_r = hexdec(substr($this->qrTextcolor, 1, 2));
			$color_g = hexdec(substr($this->qrTextcolor, 3, 2));
			$color_b = hexdec(substr($this->qrTextcolor, 5, 2));
			$color = imagecolorallocate($image, $color_r, $color_g, $color_b);

			imagettftext($image, $size, $angle, $x, $y, $color, $fontfile, $text);
		}
	}

	/**
	 * 打开模板图
	 */
	private function openImage() {

		list($image_path, $image_ext) = $this->getBackground();

		switch ($image_ext) {
		case 'jpg':
			$background = imagecreatefromjpeg($image_path);
			break;
		case 'png':
			$background = imagecreatefrompng($image_path);
			break;
		case 'gif':
			$background = imagecreatefromgif($image_path);
			break;
		default:
			throw new Exception("不支持{$image_ext}格式的背景文件", 1);
			die();
		}
		return $background;
	}

	/**
	 * 获取背景图信息
	 */
	private function getBackground() {

		//获取图片后缀
		$image_arr = explode('.', $this->qrBackground);
		$image_ext = $image_arr[count($image_arr) - 1];

		return [$this->parsePath($this->qrBackground), $image_ext];
	}

	/**
	 * 获取背景图信息
	 */
	private function getFont() {
		return realpath($this->parsePath($this->qrFont));
	}

	/**
	 * 解析路径
	 */
	private function parsePath($path) {
		$skin = './qrcode.' . $this->qrSkin . '/';

		$path = str_replace('[SKIN]', $skin, $path);
		return $path;
	}
}

?>