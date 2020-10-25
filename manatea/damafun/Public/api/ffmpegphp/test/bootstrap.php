<?php
/**
 * This is bootsrap file required for running all the tests
 * for FFmpegPHP package. This file manages all necessary
 * settings and file imports.
 * 
 * Testing framework: PHPUnit (http://www.phpunit.de)
 * 
 * @category tests
 * @package FFmpegPHP  
 */

date_default_timezone_set('Europe/Bratislava');  

$basePath = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
require_once 'c:/lamp/apache24/htdocs/damafun/Public/api/ffmpegphp/FFmpegAutoloader.php';
/*require_once $basePath.'provider'.DIRECTORY_SEPARATOR.'OutputProvider.php';
require_once $basePath.'provider'.DIRECTORY_SEPARATOR.'AbstractOutputProvider.php';
require_once $basePath.'provider'.DIRECTORY_SEPARATOR.'FFmpegOutputProvider.php';
require_once $basePath.'provider'.DIRECTORY_SEPARATOR.'FFprobeOutputProvider.php';
require_once $basePath.'FFmpegAnimatedGif.php';
require_once $basePath.'FFmpegFrame.php';
require_once $basePath.'FFmpegMovie.php';
require_once $basePath.'adapter'.DIRECTORY_SEPARATOR.'ffmpeg_animated_gif.php';
require_once $basePath.'adapter'.DIRECTORY_SEPARATOR.'ffmpeg_frame.php';
require_once $basePath.'adapter'.DIRECTORY_SEPARATOR.'ffmpeg_movie.php';
*/
	$ffmpegInstance = new ffmpeg_movie("C:/Lamp/apache24/htdocs/damafun/Public/uploads/video/20150514230234_492.mp4",false);
			$cuttime = $ffmpegInstance->getDuration()/10;//获取截图时间点为视频时长的1/10
			$vcodec = $ffmpegInstance->getVideoCodec();//获取视频编码
			$acodec = $ffmpegInstance->getAudioCodec();//获取音频编码
			if(explode(' ',$vcodec)[0]=="h264"){
				echo $acodec;
			}
