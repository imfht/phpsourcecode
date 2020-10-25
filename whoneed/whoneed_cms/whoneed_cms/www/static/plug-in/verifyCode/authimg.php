<?PHP
/**
 * 生成验证码图片文件
 *
 * @author		Arthur(ArthurXF@gmail.com)
 * @copyright	(c) 2006 by bizeway.com
 * @version		$Id$
 * @package		plug-in
 */
if(empty($_SERVER["HTTP_REFERER"])) exit;
@session_start();

$img_width=50;  //先定义图片的长、宽
$img_height=20;

srand(microtime() * 100000);
$nmsg = '';
for($Tmpa=0;$Tmpa<4;$Tmpa++){
	$nmsg.=dechex(rand(0,15));
	//$nmsg.= rand(0,9);
}


$_SESSION['authCode'] = $nmsg;

$aimg = imageCreate($img_width,$img_height);    //生成图片
ImageColorAllocate($aimg,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));      //图片底色，ImageColorAllocate第1次定义颜色PHP就认为是底色了

//下面该生成雪花背景了，其实就是在图片上生成一些符号
$arrLabel = array('!','@','#','$','%','^','&','*','a','b','c','d','e','f','g','h','i','k','1','2','3','4','5','6','7','8','9','0');
for ($i=1; $i<=250; $i++){
	shuffle($arrLabel);
	imageString($aimg,1,mt_rand(1,$img_width),mt_rand(1,$img_height),"*",imageColorAllocate($aimg,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255)));
//其实也不是雪花，就是生成＊号而已。为了使它们看起来"杂乱无章、5颜6色"，就得在1个1个生成它们的时候，让它们的位置、颜色，甚至大小都用随机数，rand()或mt_rand都可以完成。
}

//上面生成了背景，现在就该把已经生成的随机数放上来了。道理和上面差不多，随机数1个1个地放，同时让他们的位置、大小、颜色都用成随机数~~
//为了区别于背景，这里的颜色不超过200，上面的不小于200
for ($i=0;$i<strlen($_SESSION['authCode']);$i++){
	imageString($aimg, mt_rand(3,5),$i*$img_width/4+mt_rand(1,4),mt_rand(1,$img_height/4), $_SESSION['authCode'][$i],imageColorAllocate($aimg,mt_rand(0,200),mt_rand(0,200),mt_rand(0,200)));
}
Header("Content-type: image/png");  //告诉浏览器，下面的数据是图片
ImagePng($aimg);           //生成png格式
ImageDestroy($aimg);
?>

