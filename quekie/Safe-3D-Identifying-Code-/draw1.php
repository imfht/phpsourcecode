<?php
header ('Content-Type: image/png');
$num=4;//字母数
$fontroom=90;
$im_x=$num*$fontroom;
$im_y=80;
session_start();

$im = @imagecreatetruecolor($im_x, $im_y*2) or die('Cannot Initialize new GD image stream');
$red = imagecolorallocate($im, 255, 0, 0);
$white= imagecolorallocate($im, 255, 255, 255);
$black=imagecolorallocate($im, 0, 0, 0);
$blue=imagecolorallocate($im, 0, 0, 0);
imagefill($im,0,0,$blue);
$randcolor= imagecolorallocate($im, rand(100,255),rand(100,255), rand(100,255));
//imagestring($im, 8, 5, 5,  'A Simple Text String', $text_color);
$font='.\fonts\3D.ttf';
$text="WWWW";

$angel=0;
//imagettftext($im,$size,$angel,$im_x/16,$im_y,$red,$font,$text);
$posizion =$im_y*1.7;
$text=make_rand(4);
$_SESSION['yzm']=$text;
//$text="大家啊好";
//写字
for ($i=1;$i<=$num;$i++)
	{
		
		$tmp =substr($text,$i-1,1);
		$array = array(-1,1);
		$p = array_rand($array);
		$an = $array[$p]*mt_rand(1,20);//角度
		$size = rand($im_y*1,$im_y*1+rand(1,2));
		
		imagettftext($im, $size, $an, ($i-1)*$fontroom+20,$posizion, $red, $font,$tmp);
	}


//----------------------------横线
		$with = 3;
		
		$xiangwei=mt_rand(0,314)/100;
		$jl=mt_rand(7,10);
		for ($x=15;$x<$im_x-30;$x=$x+30)
		{
			if($x%45==0)
			{
				$shu=$x+rand(-45,45);
				$long=rand($im_y,$im_y*1.8);
				$begin=rand(0,$im_y);
				for($tmp=0;$tmp<=$long/2;$tmp++)
				{
					for($tmp2=1;$tmp2<=3;$tmp2++){
						imagesetpixel($im,$x+$tmp+$tmp2-30, ($begin+$tmp)%($im_y*2), $red);
					}
					
				}
			}
			//if($x%20==0)$with = mt_rand(1,5);
			//if($x%40==0)$randcolor= imagecolorallocate($im, rand(100,200),rand(100,200), rand(100,200));
			$y=rand(30,150);
			$y1=rand(30,150);
			$y2=rand(30,150);
			for($tmp=1;$tmp<=30;$tmp++)
			{   
				
				for($w=-$with/2;$w<=$with/2;$w++)
				{
					imagesetpixel($im,$x+$tmp-30, $y, $red);
					imagesetpixel($im,$x+$tmp-30, $y1, $red);
					imagesetpixel($im,$x+$tmp-30, $y2, $red);
			    }
			}
			
		 }
//----------------------------横线





		
		
		 
		 /*
		 	$with = mt_rand(1,2);
		
		$xiangwei=mt_rand(0,314)/100;
		$jl=mt_rand(7,10);
		for ($x=15;$x<$im_x-15;$x++)
		{
			if($x%20==0)$with = mt_rand(2,6);
			//if($x%40==0)$randcolor= imagecolorallocate($im, rand(100,200),rand(100,200), rand(100,200));
			    for($w=-$with/2;$w<=$with/2;$w++){
				imagesetpixel($im,$x, $posizion+sin($x/$im_x*13+$xiangwei)*40 +sin($x/$im_x*17+$xiangwei)*40+$w-$im_y*0.2, $red);
			}
			
		 }
	 */
	 
	//扭曲
		 
		 
		/* 
		 //横线
		 $with = 5;
		$posizion = 300;
		for ($x=1;$x<=$im_x;$x++)
		{
			
			for($y=$posizion-$with;$y<=$posizion+$with;$y++)
			{
				imagesetpixel($im,$x, $y, $red);
			}
			/*$x=$px/$rand1;
			if ($x!=0)
			{
				$y=sin($x);
			}
			$py=$y*$rand2;
		 }*/
	
          
   $im2 = imagecreatetruecolor ($im_x, $im_y*2);

	imagefill($im2, 16, 13, $black);
	
	$with = mt_rand(1,2);
		$posizion = $im_y/4;
		$xiangwei=mt_rand(0,314)/100;
		$jl=mt_rand(7,10);
		
	for ( $x=0; $x<$im_x; $x++) {
		for ( $y=0; $y<$im_y*2; $y++) {
			$rgb = imagecolorat($im,$x , $y);
			
			$x2=$x+$posizion+sin($y/$im_y*11+$xiangwei)*3+sin($y/$im_y*19+$xiangwei*2)*3;
			$y2=$y+$posizion+sin($x/$im_x*7+$xiangwei)*3+sin($x/$im_x*17+$xiangwei*2)*3;
			//if($y2<0)$y2=-$y2+$im_y;
			//if($y2>$im_y)$y2=$y2%$im_y;
			
			//if($x2<0)$x2=-$x2+$im_x;
			//if($x2>$im_x)$x2=$x2%$im_x;
			//if(rand(1,9)==1&& imagecolorat($im2,$x , $y)==$black)imagesetpixel ($im2, $x ,$y, $red);
			if($y2>=0&&$y2<$im_y*2&&$x2>=0&&$x2<$im_x){
				if($y2<$im_y*2-3)$y2=$y2+rand(0,2);
				
		imagesetpixel ($im2, $x ,$y2, $rgb);}
			
		}
	}
	
	for ( $x=0; $x<$im_x; $x++) {
		for ( $y=0; $y<$im_y*2; $y++) { if(rand(1,9)==1)imagesetpixel ($im2, $x ,$y, $red); }
	}
	for ( $x=$im_x-30; $x<$im_x; $x++) {
		for ( $y=0; $y<$im_y*2; $y++) { if(imagecolorat($im2,$x , $y)==$black)imagesetpixel ($im2, $x ,$y, $blue); }
	}
	
	$new=imagecreatetruecolor($im_x, $im_y);
	imagecopyresized($new, $im2, 0,0, 0, 70, $im_x,$im_y, $im_x,$im_y);
	
          	
/*	for ( $x=0; $x<$im_x; $x++) {
		for ( $y=0; $y<$im_y*2; $y++) { if(rand(1,2)==1&& imagecolorat($im2,$x , $y)==$black)imagesetpixel ($im2, $x ,$y, $red);   }}
   for ( $x=0; $x<$im_x; $x++) {
		for ( $y=0; $y<$im_y*2; $y++) { if(rand(1,6)==1)imagesetpixel ($im2, $x ,$y, $red);   }}
	for ( $x=0; $x<$im_x; $x++) {
		for ( $y=0; $y<$im_y*2; $y++) { if(rand(1,6)==1&& imagecolorat($im2,$x , $y)==$black)imagesetpixel ($im2, $x ,$y, $red);   }}*/

imagepng($new);
imagedestroy($im2);
imagedestroy($im);
?>

<?php 
function make_rand($length="4"){//验证码文字生成函数
	$str="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$result="";
	for($i=0;$i<$length;$i++){
		$num[$i]=rand(0,25);
		$result.=$str[$num[$i]];
	}
	return $result;
}
?>