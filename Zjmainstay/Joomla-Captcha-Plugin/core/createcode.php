<?php
/**
 * 用户验证码生成文件
 * @PreVersionAuthor:wangsl
 * @Author:Zjmainstay
 * @version : 1.0
 * @creatdate: 2013-10-4
 */
session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
if(!class_exists('Utilscaption')) require dirname(__FILE__) .'/utilscaption.class.php';
$rsi                   	= new Utilscaption();
$rsi->Length           	= 4;																//验证码字符个数
$rsi->TFontSize        	= array(15,17);														//字体大小范围
$rsi->Width            	= isset($_SESSION['imgWidth']) ? (int)$_SESSION['imgWidth'] : 50;	//验证码宽度
$rsi->Height           	= isset($_SESSION['imgHeight']) ? (int)$_SESSION['imgHeight'] : 25;	//验证码高度
$rsi->Chars            	= '0123456789';														//验证码字符
$rsi->TFontAngle       	= array(-20,20); 													//旋转角度
$rsi->FontColors      	= array('#f36161','#6bc146','#5368bd');  							//字体颜色,红绿蓝
$code                  	= $rsi->RandRSI();													//生成验证码字符
$_SESSION["CHECKCODE"] 	= $code;															//存储验证码session
$rsi->Draw();																				//绘制验证码图像
exit;
?>