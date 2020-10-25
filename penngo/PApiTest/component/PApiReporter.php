<?php
class PApiReporter extends HtmlReporter{
	private $character_set;
	function __construct($character_set = 'UTF-8') {
		parent::__construct('UTF-8');
		//$this->character_set = $character_set;
		$this->character_set = $character_set;
	}
	
	function paintHeader($test_name) {
		$this->sendNoCacheHeaders();
		print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">";
		print "<html>\n<head>\n<title>$test_name</title>\n";
		print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=" .
				$this->character_set . "\">\n";
		$jsFile = dirname (__FILE__) . '/../libs/js/c.js';
		$cssFile = dirname (__FILE__) . '/../libs/js/s.css';
		$js = file_get_contents($jsFile);
		print "<script type=\"text/javascript\">\n".$js."</script>\n";
		$css = file_get_contents($cssFile);
		print "<style type=\"text/css\">\n".$css."</style>\n";
		print "<style type=\"text/css\">\n";
        print $this->getCss() . "\n";
	        print "</style>\n";
		print "</head>\n<body>\n";
		print "<h1>$test_name</h1>\n";
		
		//$file=dirname (__FILE__) . '/../libs/js/Expanded.gif';
		$file=dirname (__FILE__) . '/../libs/js/Collapsed.gif';
		$type=getimagesize($file);//取得图片的大小，类型等
		$fp=fopen($file,"r")or die("Can't open file");
		$file_content=chunk_split(base64_encode(fread($fp,filesize($file))));//base64编码
		switch($type[2]){//判读图片类型
			case 1:$img_type="gif";break;
			case 2:$img_type="jpg";break;
			case 3:$img_type="png";break;
		}
		$img='data:image/'.$img_type.';base64,'.$file_content;//合成图片的base64编码
		//print "\n" .$img."\n";
		flush();
	}
}