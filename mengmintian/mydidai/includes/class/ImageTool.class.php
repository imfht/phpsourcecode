<?php
class ImageTool{
	

	//获取文件信息	
	static public function imageInfo($dst){
		//判断文件是否存在
		if(!file_exists($dst)){
			return false;
		}
		//获取文件信息
		if(!$info = getimagesize($dst)){
			return false;
		}
		$img = array();
		$img['width'] = $info[0];
		$img['height'] = $info[1];
		$img['mime'] = substr($info['mime'],strripos($info['mime'],'/')+1);
		return $img;
	}

	//生成缩略图
	static public function thumb($dst,$width=100,$height=150,$save=null){
		//获取文件信息，并判断
		if(!$tinfo = self::imageInfo($dst)){
			return false;
		}


		//计算并取得最小比例
		$calc = min($width/$tinfo['width'],$height/$tinfo['height']);

		//创建原始图片画布
		$dfu = 'imagecreatefrom'.$tinfo['mime'];
		$dim = $dfu($dst);
		//创建缩略图片画布
		$tim = imagecreatetruecolor($width,$height);
		//创建白色画布并填充
		$white = imagecolorallocate($tim, 255, 255, 255);
		imagefill($tim,0,0,$white);
		//生成缩略图
		$dwidth = (int)$tinfo['width']*$calc;
		$dheight = (int)$tinfo['height']*$calc;
		$paddingx = (int)($width - $dwidth)/2;
		$paddingy = (int)($height - $dheight)/2;
		imagecopyresampled($tim,$dim,$paddingx,$paddingy,0,0,$dwidth,$dheight,$tinfo['width'],$tinfo['height']);
		//判断要保存在哪里
		if (!$save) {
			$save = $dst;
			unlink($dst);
		}
		//保存图片
		$cref = 'image'.$tinfo['mime'];
		$cref($tim,$save);
		//销毁资源
		imagedestroy($dim);
        imagedestroy($tim);

        return $save;
	}

	//生成验证码
	static public function code(){
		//生成画布
		$cim = imagecreatetruecolor(40,20);
		//随机背景颜色并填充
		$gco = imagecolorallocate($cim,mt_rand(210,255),mt_rand(210,255),mt_rand(210,255));
		//随机字体颜色
		$fco = imagecolorallocate($cim,mt_rand(0,255),mt_rand(0,100),mt_rand(0,255));
		imagefill($cim,0,0,$gco);
		//随机生成数字验证码
		$num = mt_rand(1000,9999);
		$_SESSION['code'] = $num;
		//写入数字
		imagestring($cim,5,2,2,$num,$fco);
		header("Content-type: image/png");
		imagepng($cim);
		imagedestroy($cim);
	}
	

}
?>