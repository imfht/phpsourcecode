<?php
class Paint
{
	private $sFile,$sPath,$sFileName,$ext;
	private $im;
	private $width,$height;
	private $isWatermark,$markX,$markY,$markStr;
	/**
	 * 构造函数，用来创建一个新的文件。
	 *
	 * @param string $sFile 要载入的文件
	 * @return Paint
	 */
	function Paint($sFile)
	{
		//载入图片
		$this->Load($sFile);
		//图片打水印
		$this->SetWatermark($sFile);
	}
	function Load($sFile)
	{
		$this->sFile=$sFile;
		$this->prasePath();
		
		$filePath=$this->sPath.$this->sFileName;
		$filePath=$filePath[0]=='/'?ABSPATH.$filePath:$filePath;
		switch($this->ext){
			case "png":
				$this->im = imagecreatefrompng($filePath);
				break;
			case "gif":
				$this->im = imagecreatefromgif($filePath);
				break;
			case "bmp":
				$this->im = imagecreatefromwbmp($filePath);
				break;
			default:
				$this->im = imagecreatefromjpeg($filePath);
				break;
		}
		if(!$this->im){return false;}
		$this->width = imagesx($this->im);
		$this->height = imagesy($this->im);
		$this->isWatermark=false;
	}
	/**
	 * 缩放图像的方法
	 *
	 * @param int $sWidth 图像的宽
	 * @param int $sHeight 图像的高，可选，默认为宽的3/4
	 * @param bool $isFill 是否填充背景图 填充白色背景
	 * @param string $sOutFile 输出文件名 默认为 s_+原文件名
	 * @return unknown
	 */
	function Resize($sWidth,$sHeight=0,$prefix='',$isFill=true,$sOutFile='none')
	{
		$sHeight = $sHeight==0?$sWidth*3/4:$sHeight;
		
		if($sOutFile=='none')
		{
			$sOutFile=$this->sPath.$prefix.$this->sFileName;
		}
		
		$n_OriginalWidth 	= 	$this->width;
		$n_OriginalHeight	=	$this->height;
		if($isFill)
		{
			$newX=0;
			$newY=0;
			if($n_OriginalWidth>$n_OriginalHeight)
			{
				$newX=0;
				$newY=($sHeight-($sWidth/$n_OriginalWidth)*$n_OriginalHeight)/2;
				$newWidth=$sWidth;
				$newHeight=($sWidth/$n_OriginalWidth)*$n_OriginalHeight;
			}
			else
			{
				$newY=0;
				$newX=($sWidth-($sHeight/$n_OriginalHeight)*$n_OriginalWidth)/2;
				$newWidth=($sHeight/$n_OriginalHeight)*$n_OriginalWidth;
				$newHeight=$sHeight;
			}

			$newim = imagecreatetruecolor($sWidth, $sHeight);

		    $tempstr  = substr(paint_bgcolor,0,2).substr(paint_bgcolor,0,2);
		    $tempstr1 = (int)substr(paint_bgcolor,0,2).substr(paint_bgcolor,2,2);
		    $tempstr2 = (int)substr(paint_bgcolor,0,2).substr(paint_bgcolor,4,2);
		    $tempstr3 = (int)substr(paint_bgcolor,0,2).substr(paint_bgcolor,6,2);

			//设置颜色
			if(paint_bgcolor)	        
			$grey = imagecolorallocate($newim, hexdec($tempstr.$tempstr1), hexdec($tempstr.$tempstr2), hexdec($tempstr.$tempstr3));
			else
			$grey = imagecolorallocate($newim, 0xff, 0xff, 0xff);
						
			imagefilledrectangle($newim,0,0,$sWidth, $sHeight,$grey);
			imagecopyresampled($newim, $this->im, $newX, $newY, 0, 0, $newWidth, $newHeight, $n_OriginalWidth, $n_OriginalHeight);
		}
		else {
			//判断一下 原图大小 和现在的图的大小 如果 原图小的话就用原图尺寸
			if($n_OriginalWidth > $n_OriginalHeight){
				$newWidth = $sWidth;
				$newHeight = ($sWidth / $n_OriginalWidth) * $n_OriginalHeight;
			}else{
				$newHeight = $sHeight;
				$newWidth = ($sHeight / $n_OriginalHeight) * $n_OriginalWidth;
			}
			if(($n_OriginalWidth<$newWidth) && ($n_OriginalHeight<$newHeight))
			{
				$newHeight = $n_OriginalHeight;
				$newWidth  =  $n_OriginalWidth;
			}
				$newim = imagecreatetruecolor($newWidth, $newHeight);
				imagecopyresampled($newim, $this->im, 0, 0, 0, 0, $newWidth, $newHeight, $n_OriginalWidth, $n_OriginalHeight);
		}
		if($this->isWatermark)
		{
			$black = imagecolorallocate($newim, 0, 0, 0);
			imagestring($newim,4,$this->markX,$this->markY,$this->markStr,$black);
		}
		$rsOutFile=$sOutFile;
		$sOutFile=$sOutFile[0]=='/'?ABSPATH.$sOutFile:$sOutFile;	
		switch($this->ext){
		case "png":
			imagepng($newim,$sOutFile);
			break;
		case "gif":
			imagegif($newim,$sOutFile);
			break;
		case "bmp":
			imagewbmp($newim,$sOutFile);
			break;
		default:
			imagejpeg($newim,$sOutFile,90);
			break;
		}

		imagedestroy($newim);
		//缩略图生成后加水印
		//$this->SetWatermark($rsOutFile);
		return $rsOutFile;
	}
	/**
	 * 添加裁剪图像的方法
	 * @grysoft
	 */
	 function POST($key){
		 return $_POST[$key];
	 }
	 function Crop()
	 {
		    $x1=intval($this->POST("x1"));
			$y1=intval($this->POST("y1"));
			$x2=intval($this->POST("x2"));
			$y2=intval($this->POST("y2"));
			$url = $this->POST("url");
			
			if(!$url){
				die("截图不成功！");
			}		
		
			$src_img = ABSPATH.$url;
			$arr = explode('.', $src_img);
			$expand = $arr[count($arr)-1];
			$w = ($x2-$x1);
			$h = ($y2-$y1);	
			
			$arr=explode('/', $url);
			$find = $arr[count($arr)-1];
			$rep = "c_".$find;
			$save_url = str_ireplace($find,$rep,$url);
			$save_url_ab =  ABSPATH.$save_url;		
			//die("x1:".$c_pos_x."--"."y1:".$c_pos_x);
			//list($src_w,$src_h)=getimagesize($src_img);  // 获取原图尺寸
			switch($expand){
				case "jpg":
					$source=imagecreatefromjpeg($src_img);
					$croped=imagecreatetruecolor($w, $h);
					imagecopy($croped,$source,0,0,$x1,$y1,$w,$h);
					imagejpeg($croped,$save_url_ab);
					break;
				case "jpeg":
					$source=imagecreatefromjpeg($src_img);
					$croped=imagecreatetruecolor($w, $h);
					imagecopy($croped,$source,0,0,$x1,$y1,$w,$h);
					imagejpeg($croped,$save_url_ab);
					break;
				case "gif":
					$source=imagecreatefromgif($src_img);
					$croped=imagecreatetruecolor($w, $h);
					imagecopy($croped,$source,0,0,$x1,$y1,$w,$h);
					imagegif($croped,$save_url_ab);
					break;
				case "png":
					$source=imagecreatefrompng($src_img);
					$croped=imagecreatetruecolor($w, $h);
					imagecopy($croped,$source,0,0,$x1,$y1,$w,$h);
					imagepng($croped,$save_url_ab);
					break;
			}
			imagedestroy($croped);
			
			return $save_url;
	 }

/**
 * 图片水印
 * 
 * @grysoft
 * @$positon   水印位置  1:顶部居左, 2:顶部居右, 3:居中, 4:底部局左, 5:底部居右 
 * @$alpha     透明度    0:完全透明, 100:完全不透明
 * 
 */
	function SetWatermark($sFile, $positon=5, $alpha=50)
	{	 
		 $waterImg = ABSPATH.WATERIMGS;		 
		 if(!is_file($waterImg) || !ISWATER)
		 return ;
		 
		 $filePath=$sFile;
		 $filePath=$filePath[0]=='/'?ABSPATH.$filePath:$filePath;		 
		 
		 $srcImg = $filePath;
		 $savefile = $filePath;
		 
		 $srcinfo  = getimagesize($srcImg);
		 if (!$srcinfo) {
		  return -1;  //原文件不存在
		 }
		 $waterinfo = @getimagesize($waterImg);
		 if (!$waterinfo) {
		  return -2;  //水印图片不存在
		 }
		 $srcImgObj = $this->image_create_from_ext($srcImg);
		 if (!$srcImgObj) {
		  return -3;  //原文件图像对象建立失败
		 }
		 $waterImgObj = $this->image_create_from_ext($waterImg);
		 if (!$waterImgObj) {
		  return -4;  //水印文件图像对象建立失败
		 }
		 switch ($positon) {
		 //1顶部居左
		 case 1: $x=$y=0; break;
		 //2顶部居右
		 case 2: $x = $srcinfo[0]-$waterinfo[0]; $y = 0; break;
		 //3居中
		 case 3: $x = ($srcinfo[0]-$waterinfo[0])/2; $y = ($srcinfo[1]-$waterinfo[1])/2; break;
		 //4底部居左
		 case 4: $x = 0; $y = $srcinfo[1]-$waterinfo[1]; break;
		 //5底部居右
		 case 5: $x = $srcinfo[0]-$waterinfo[0]; $y = $srcinfo[1]-$waterinfo[1]; break;
		 default: $x=$y=0;
		 }
		 imagecopymerge($srcImgObj, $waterImgObj, $x, $y, 0, 0, $waterinfo[0], $waterinfo[1], $alpha);
		 switch ($srcinfo[2]) {
		 case 1: imagegif($srcImgObj, $savefile); break;
		 case 2: imagejpeg($srcImgObj, $savefile); break;
		 case 3: imagepng($srcImgObj, $savefile); break;
		 default: return -5;  //保存失败
		 }
		 imagedestroy($srcImgObj);
		 imagedestroy($waterImgObj);
		 return $this->sFile;
	}
		
	function image_create_from_ext($imgfile)
	{
		 $info = getimagesize($imgfile);
		 $im = null;
		 switch ($info[2]) {
		 case 1: $im=imagecreatefromgif($imgfile); break;
		 case 2: $im=imagecreatefromjpeg($imgfile); break;
		 case 3: $im=imagecreatefrompng($imgfile); break;
		 }
		 return $im;
	 }
	private function prasePath()
	{
		//提取路径与文件名
		if(preg_match('/(.*\/)(.*)/i',$this->sFile,$matchs))
		{
			$this->sPath=$matchs[1];
			$this->sFileName=$matchs[2];
		}
		else 
		{
			$this->sPath='';
		}
		$this->ext = substr(strrchr($this->sFileName, "."), 1);
	}
}