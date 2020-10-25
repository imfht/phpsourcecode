<?php

class ImglibController extends Controller {

	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		$this->title();
		
		$this->load->library('imagehandler');
		
		$this->imagehandler->setSrcImg("testfiles/test.jpg");
		$this->imagehandler->setDstImg("testfiles/new_test.jpg");
		$this->imagehandler->setMaskImg("testfiles/mark.gif");
		$this->imagehandler->setMaskPosition(1);
		$this->imagehandler->setMaskImgPct(80);
		$this->imagehandler->setDstImgBorder(4,"#dddddd");
		$this->imagehandler->createImg(1024,768); 
		
		echo '处理完成.';
	}

	private function title($title='图像处理类测试')
	{
		echo '<title>'.$title.'</title>';
	}
	
	function mooimage()
	{
		$this->load->library('image');
		
		echo '<title>MooPHP图像处理类</title>';
		
		
	}

}
