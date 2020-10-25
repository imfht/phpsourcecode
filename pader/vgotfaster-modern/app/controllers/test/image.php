<?php

/*
操作设置方案

设置配置
$imagehandler->set($configArray);

配置及使用方法介绍

缩略图

裁切

水印图

水印文字



*/

/**
 * 图片处理演示控制器
 *
 * @package VgotFaster
 * @author pader
 * @copyright 2010
 * @version $Id$
 * @access public
 */
class ImageController extends Controller {

	var $srcImage = 'app/data/demo.jpg';
	var $toImage = 'app/data/demo_maked.jpg';
	var $toImage2 = 'app/data/ee.jpg';
	var $imagehandler;
	var $image;

	function __construct()
	{
		parent::Controller();
		$this->load->library('imagehandler');
		$this->load->library('image');

		if(!file_exists($this->srcImage)) {
			echo '演示图片文件不存在，请在 app/data 目录放置一个 demo.jpg 文件<br />';
			exit;
		}

	}

	function index()
	{
		echo '<h3>图像处理类演示</h3>';
		echo anchor('test/image/MakeThumb','生成缩略图'),'<br />';
		echo anchor('test/image/MakeCut','图片裁切'),'<br />';
		echo anchor('test/image/mineMake','本地库');
	}

	function MakeThumb()
	{
		$this->imagehandler->setCutType(0);
		$this->imagehandler->setSrcImg($this->srcImage);
		$this->imagehandler->setDstImg($this->toImage);
		//$this->imagehandler->setDstImgBorder(1);
		$r = $this->imagehandler->createImg(800,500);

		echo $r ? '图片缩小成功' : '图片缩小失败';
		echo '<p><img src="'.siteUrl('test/image/show').'" />';
	}

	function MakeCut()
	{
		$this->imagehandler->setCutType(2);
		$this->imagehandler->setSrcImg($this->srcImage);
		$this->imagehandler->setRectangleCut(817,703);
		$this->imagehandler->setSrcCutPosition(395,106);
		//$this->imagehandler->setDstImgBorder(10,'#FF00FF');
		//$this->imagehandler->flipH();
		$this->imagehandler->setDstImg($this->toImage);
		$r = $this->imagehandler->createImg(817,703);

		echo $r ? '图片裁切成功' : '图片裁失败';
		echo '<p><img src="'.siteUrl('test/image/show').'" /></p>';
	}

	function mineMake()
	{
        $this->image->open($this->srcImage);

		//$this->image->resize(380,'auto');
		$this->image->crop(200,200,300,280);
		//$this->image->drawBorder(1,'#000000');


		$r = $this->image->createImage($this->toImage);

		echo $r ? '图片处理成功' : '图片处理失败';

		echo '<p><img src="'.siteUrl('test/image/show').'" /></p>';
	}

	function demoSharp()
	{
		$this->image->open($this->toImage);
		$this->image->sharp(1);
		$r = $this->image->createImage($this->toImage2);
	}

	function demoRotate()
	{
		$this->image->open('app/data/m.jpg');
		$this->image->corner(50,TRUE);
		$this->image->resize(200,'auto');
		$r = $this->image->createImage($this->toImage2);
	}

	function fixtransparency()
	{
		$this->image->initialize('transparency',TRUE);

		$this->image->open('static/example.jpg');
		$this->image->resize(290);
		$this->image->save('static/example_result.jpg');

		$this->image->open('static/example.png');
		$this->image->resize(200);
		$this->image->save('static/example_result.png');

		$this->image->open('static/example.png');
		$this->image->resize(120);
		$this->image->save('static/example_result_2.png');

		$base = baseUrl();

		echo "<div style='background:url({$base}static/canvasbg.gif);text-align:center;padding:10px;'>
			<p>GIF</p><p><img src='{$base}static/example_result.jpg' /></p>
			<p>PNG</p><p><img src='{$base}static/example_result.png' /></p>
			<p>PNG</p><p><img src='{$base}static/example_result_2.png' /></p>
			</div>";
	}

	/**
	 * 预览图片生成结果
	 *
	 * @return void
	 */
	function show()
	{
		$image = file_get_contents($this->toImage);
		echo $image;
		unset($image);
	}

	function qrcode()
	{
		$this->load->library('qrcode');
		QRcode::png('http://vgotfaster.googlecode.com/');
	}

	public function convert()
	{
		$this->image->initialize(array('quality'=>90));

		$this->image->open(APPLICATION_PATH.'/data/test.gif', 'jpg');
		$this->image->save(APPLICATION_PATH.'/data/test_convert.jpg');
	}

	public function drawText() {
		$this->image->open(APPLICATION_PATH.'/data/000.jpg');

		$this->image->markImage(APPLICATION_PATH.'/data/mark.png');

		$this->image->save(APPLICATION_PATH.'/data/000_1.jpg');
	}

}
