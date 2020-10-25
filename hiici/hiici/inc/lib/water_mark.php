<?php

function water_mark($bg_img_f, $wt_img_f) {

	$wt_info = getimagesize($wt_img_f);

	$wt_w = $wt_info[0];//取得水印图片的宽
	$wt_h = $wt_info[1];//取得水印图片的高 

	switch($wt_info[2]) {//取得水印图片的格式
	case 1: $wt_img = imagecreatefromgif($wt_img_f); break;
	case 2: $wt_img = imagecreatefromjpeg($wt_img_f); break;
	case 3: $wt_img = imagecreatefrompng($wt_img_f); break;
	default:
		echo '水印文件格式不被支持！^_^';
		return false;
	}

	$bg_info = getimagesize($bg_img_f);

	$bg_w = $bg_info[0];//取得背景图片的宽
	$bg_h = $bg_info[1];//取得背景图片的高 

	switch($bg_info[2]) {//取得背景图片的格式
	case 1: $bg_img = imagecreatefromgif($bg_img_f); break;
	case 2: $bg_img = imagecreatefromjpeg($bg_img_f); break;
	case 3: $bg_img = imagecreatefrompng($bg_img_f); break;
	default:
		echo '被加水印文件格式不被支持！^_^';
		return false;
	}

	//左下角加水印
	if ($bg_w < $bg_h) {
		$d_w = $bg_w/3;
		$d_h = ($wt_h*$bg_w)/(3*$wt_w);
	} else {
		$d_w = ($wt_w*$bg_h)/(10*$wt_h);
		$d_h = $bg_h/10;
	}

	$p_x = $bg_w/40;
	$p_y = $bg_h - ($d_h + $bg_h/40);

	//设定图像的混色模式
	imagealphablending($bg_img, true);

	//imagecopy($bg_img, $wt_img, $p_x, $p_y, 0, 0, $wt_w, $wt_h);//拷贝水印到目标文件 
	imagecopyresampled($bg_img, $wt_img, $p_x, $p_y, 0, 0, $d_w, $d_h, $wt_w, $wt_h);//拷贝水印到目标文件 

	//缩小图片
	$s_w = $s_h = 710; 
	if ($bg_w > $bg_h) {
		if ($s_w < $bg_w) {
			$n_w = $s_w; $n_h = $s_w*$bg_h/$bg_w;
		} else {
			$n_w = $bg_w; $n_h = $bg_h;
		}
	} else {
		if ($s_h < $bg_h) {
			$n_w = $s_w*$bg_w/$bg_h; $n_h = $s_h; 
		} else {
			$n_w = $bg_w; $n_h = $bg_h;
		}
	}
	$bg_img_n = imagecreatetruecolor($n_w, $n_h);
	imagecopyresampled($bg_img_n, $bg_img, 0, 0, 0, 0, $n_w, $n_h, $bg_w, $bg_h);

	@unlink($bg_img_f);

	switch($bg_info[2]) {//取得背景图片的格式
	case 1: imagegif($bg_img_n, $bg_img_f); break;
	case 2: imagejpeg($bg_img_n, $bg_img_f); break;
	case 3: imagepng($bg_img_n, $bg_img_f); break;
	default:
		echo '保存文件失败！^_^';
		return false;
	}

	//释放内存
	unset($wt_info, $bg_info);
	imagedestroy($wt_img);
	imagedestroy($bg_img);
	imagedestroy($bg_img_n);

	return true;
}
