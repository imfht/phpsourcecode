<?php
/* 
 * upload for php
 * 注1：本程序特别针对HTML5上传,加入了特殊处理
 * @author xuhm
 * @todo 后期增加类封装以及多种返回支持
 */
header('Content-Type: text/html; charset=UTF-8');
$inputName='filedata';//表单文件域name
$attachDir=SITE_ROOT.'data/'.$_G['setting']['server_upload'][1]['dirpath'];//上传文件保存路径，结尾不要带/
$dirType=$_G['setting']['server_upload'][1]['dirtype'];//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
$maxAttachSize=$_G['setting']['server_upload'][1]['maxsize'];//最大上传大小，默认是20M
$upExt=$_G['setting']['server_upload'][1]['upext'];//上传扩展名
$msgType=2;//返回上传参数的格式：1，只返回url，2，返回参数数组
//ini_set('date.timezone','Asia/Shanghai');//时区
/*
 * thumb 1 表示需要 缩略图; 2 表示不需要缩略图 
 */
$thumb = isset($_REQUEST['thumb']) && !empty($_REQUEST['thumb']) ? $_REQUEST['thumb']:'1';
$thumb_width = isset($_REQUEST['thumb_width']) && !empty($_REQUEST['thumb_width']) ? $_REQUEST['thumb_width']:'200';
$thumb_height = isset($_REQUEST['thumb_height']) && !empty($_REQUEST['thumb_height']) ? $_REQUEST['thumb_height']:'200';
$err = "";
$msg = array();
$tempPath=$attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'_'.random(4).'.tmp';
$file_name='';
switch ($do) {
	case 'get_remote_img':
		if(!empty($_G['user_id'])){
			//DEBUG xheditor编辑器获取远程图片
			$attachDir='data/upload';//上传文件保存路径，结尾不要带/
			$upExt="jpg,jpeg,gif,png";
			$arrUrls=explode('|',$_POST['urls']);
			$urlCount=count($arrUrls);
			for($i=0;$i<$urlCount;$i++){
				$localUrl=saveRemoteImg($arrUrls[$i]);
				if($localUrl)$arrUrls[$i]=$localUrl;
			}
			echo implode('|',$arrUrls);
		}
		break;

	default:
		$return_array = array("errcode"=>'e_0001',"errmsg"=>lang('error','e_0001'),"data"=>$msg);
		if(!empty($_G['user_id'])){
		    if(!is_dir($attachDir))
		    {
		        @mkdir($attachDir, 0777);
		        @fclose(fopen($attachDir.'/index.htm', 'w'));
		    }
		    if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){//HTML5上传
		        file_put_contents($tempPath,file_get_contents("php://input"));
		        $file_name=urldecode($info[2]);
		    }else{//标准表单式上传
		        $upfile=@$_FILES[$inputName];
		        if(!isset($upfile)){
		        	$return_array = array("errcode"=>'e_2001',"errmsg"=>lang('error','e_2001'),"data"=>$msg);
		        }
		        elseif(!empty($upfile['error'])){
		            switch($upfile['error'])
		            {
		                case '1':
		                    $return_array = array("errcode"=>'e_2002',"errmsg"=>lang('error','e_2002'),"data"=>$msg);
		                    break;
		                case '2':
		                    $return_array = array("errcode"=>'e_2003',"errmsg"=>lang('error','e_2003'),"data"=>$msg);
		                    break;
		                case '3':
		                	$return_array = array("errcode"=>'e_2004',"errmsg"=>lang('error','e_2004'),"data"=>$msg);
		                    break;
		                case '4':
		                    $return_array = array("errcode"=>'e_2005',"errmsg"=>lang('error','e_2005'),"data"=>$msg);
		                    break;
		                case '6':
		                	$return_array = array("errcode"=>'e_2006',"errmsg"=>lang('error','e_2006'),"data"=>$msg);
		                    break;
		                case '7':
		                	$return_array = array("errcode"=>'e_2007',"errmsg"=>lang('error','e_2007'),"data"=>$msg);
		                    break;
		                case '8':
		                	$return_array = array("errcode"=>'e_2008',"errmsg"=>lang('error','e_2008'),"data"=>$msg);
		                    break;
		                case '999':
		                default:
		                	$return_array = array("errcode"=>'e_2009',"errmsg"=>lang('error','e_2009'),"data"=>$msg);
		            }
		        }elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none'){
		        	$return_array = array("errcode"=>'e_2005',"errmsg"=>lang('error','e_2005'),"data"=>$msg);
		        }else{
		            upload_file($upfile['tmp_name'],$tempPath);
		            if(!file_exists($tempPath)){
		            	$return_array = array("errcode"=>'e_2010',"errmsg"=>lang('error','e_2010'),"data"=>$msg);
		            }
		            $file_name=$upfile['name'];
		        }
		    }

		    if(empty($err)){
		        $fileInfo=pathinfo($file_name);
		        $extension=$fileInfo['extension'];
		        if(preg_match('/^('.str_replace(',','|',$upExt).')$/i',$extension))
		        {
		            $bytes=filesize($tempPath);
		            if($bytes > $maxAttachSize){
		            	$return_array = array("errcode"=>'e_2011',"errmsg"=>str_replace('###XXX###',formatBytes($maxAttachSize),lang('error','e_2011')),"data"=>$msg);
		            }else
		            {
		                switch($dirType)
		                {
		                    case 1: $attachSubDir = '/'.'day_'.date('Ymd'); break;
		                    case 2: $attachSubDir = '/'.'month_'.date('Ym'); break;
		                    case 3: $attachSubDir = '/'.'ext_'.$extension; break;
		                }
		                $attachDir = $attachDir.$attachSubDir;
		                if(!is_dir($attachDir))
		                {
		                    @mkdir($attachDir, 0777);
		                    @fclose(fopen($attachDir.'/index.htm', 'w'));
		                }
		                PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		                $newFilename=date("YmdHis").mt_rand(1000,9999).'_'.random(4).'.'.$extension;
		                $targetPath = $attachDir.'/'.$newFilename;
                        //DEBUG 阿里云OSS处理 TODO 待优化示例 http://help.aliyun.com/manual?spm=0.0.0.0.vVdtHC&helpId=181
                        if($_G['setting']['aliyun_oss']){
                            upload_file($tempPath,$targetPath);
                        }else{
                            rename($tempPath,$targetPath);
                            @chmod($targetPath,0755);
                        }
                        //DEBUG 生成缩略图
                        if($thumb==1 && !empty($thumb_width) && !empty($thumb_height)){
                            list($img_width, $img_height) = getimagesize($targetPath);
                            $thumbfile = '../'.$attachSubDir.'/'.$newFilename.'_small.jpg';
                            if($img_width > $thumb_width || $img_height > $thumb_height){
                                require_once libfile('class/image');
                                $img = new image;
                                if(!$img->Thumb($targetPath, $thumbfile, $thumb_width, $thumb_height)) {
                                	$return_array = array("errcode"=>'e_2013',"errmsg"=>lang('error','e_2013'),"data"=>$msg);
                                	echo json_ext($return_array);
                                    //echo "{'err':'".lang('error','e_2013')."','msg':".$msg."}";
                                    die();
                                }
                            }else{
                                upload_file($targetPath,$targetPath.'_small.jpg');
                            }
                        }
		                $target_path = str_replace(SITE_ROOT,'',$targetPath);
		                $target_path_json=jsonString($target_path);
		                if($msgType==1){
		                	$msg="'$target_path_json'";
		                }else{
		            		$msg=array('url'=>$target_path,'file_name'=>$file_name);
		            	}
		            }
		        }else{
		        	$return_array = array("errcode"=>'e_2014',"errmsg"=>lang('error','e_2014').$upExt,"data"=>$msg);
		        }
		        @unlink($tempPath);
		    }
		    if(file_exists($targetPath)){
		    	$return_array = array("errcode"=>'e_2000',"errmsg"=>lang('error','e_2000'),"data"=>$msg);
		        //echo "{'err':'".jsonString($err)."','msg':".$msg."}";
		    }else{
		    	$return_array = array("errcode"=>'e_2015',"errmsg"=>lang('error','e_2015'),"data"=>$msg);
		    }
		}else{
		    //DEBUG 无权限上传
		    $return_array = array("errcode"=>'e_2016',"errmsg"=>lang('error','e_2016'),"data"=>$msg);
		}
		echo json_ext($return_array);
		break;
}

function jsonString($str)
{
	return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
}
function formatBytes($bytes) {
	if($bytes >= 1073741824) {
		$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
	} elseif($bytes >= 1048576) {
		$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
	} elseif($bytes >= 1024) {
		$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
	} else {
		$bytes = $bytes . 'Bytes';
	}
	return $bytes;
}

function upload_file($src_file,$target_file){
    @copy($src_file,$target_file);
    if(!file_exists($target_file) && function_exists('move_uploaded_file'))
    {
        @move_uploaded_file($src_file,$target_file);
    }
    if(!file_exists($target_file))
    {
        if (@is_readable($src_file) && (@$fp_s = fopen($src_file, 'rb')) && (@$fp_t = fopen($target_file, 'wb'))) {
            while (!feof($fp_s)) {
                $s = @fread($fp_s, 1024 * 512);
                @fwrite($fp_t, $s);
            }
            fclose($fp_s); fclose($fp_t);
        }
    }
}

//debug 远程抓取图片函数 start
function saveRemoteImg($sUrl){
	global $upExt,$maxAttachSize;
	$reExt='('.str_replace(',','|',$upExt).')';
	if(substr($sUrl,0,10)=='data:image'){
		//base64google图片
		if(!preg_match('/^data:image\/'.$reExt.'/i',$sUrl,$sExt))return false;
		$sExt=$sExt[1];
		$imgContent=base64_decode(substr($sUrl,strpos($sUrl,'base64,')+7));
	}
	else{
		//url图片
		if(!preg_match('/\.'.$reExt.'$/i',$sUrl,$sExt))return false;
		$sExt=$sExt[1];
		$imgContent=getUrl($sUrl);
	}
	if(strlen($imgContent)>$maxAttachSize)return false;
	$sLocalFile=getLocalPath($sExt);
	file_put_contents($sLocalFile,$imgContent);
	//DEBUG mime php.ini gd2
	$fileinfo= @getimagesize($sLocalFile);
	if(!$fileinfo||!preg_match("/image\/".$reExt."/i",$fileinfo['mime'])){
		@unlink($sLocalFile);
		return false;
	}
	return $sLocalFile;
}
//抓URL
function getUrl($sUrl,$jumpNums=0){
	$arrUrl = parse_url(trim($sUrl));
	if(!$arrUrl)return false;
	$host=$arrUrl['host'];
	$port=isset($arrUrl['port'])?$arrUrl['port']:80;
	$path=$arrUrl['path'].(isset($arrUrl['query'])?"?".$arrUrl['query']:"");
	$fp = @fsockopen($host,$port,$errno, $errstr, 30);
	if(!$fp)return false;
	$output="GET $path HTTP/1.0\r\nHost: $host\r\nReferer: $sUrl\r\nConnection: close\r\n\r\n";
	stream_set_timeout($fp, 60);
	@fputs($fp,$output);
	$Content='';
	while(!feof($fp))
	{
		$buffer = fgets($fp, 4096);
		$info = stream_get_meta_data($fp);
		if($info['timed_out'])return false;
		$Content.=$buffer;
	}
	@fclose($fp);
	global $jumpCount;
	if(preg_match("/^HTTP\/\d.\d (301|302)/is",$Content)&&$jumpNums<5)
	{
		if(preg_match("/Location:(.*?)\r\n/is",$Content,$murl))return getUrl($murl[1],$jumpNums+1);
	}
	if(!preg_match("/^HTTP\/\d.\d 200/is", $Content))return false;
	$Content=explode("\r\n\r\n",$Content,2);
	$Content=$Content[1];
	if($Content)return $Content;
	else return false;
}

function getLocalPath($sExt){
	global $dirType,$attachDir;
	switch($dirType)
	{
		case 1: $attachSubDir = 'day_'.date('Ymd'); break;
		case 2: $attachSubDir = 'month_'.date('Ym'); break;
		case 3: $attachSubDir = 'ext_'.$sExt; break;
	}
	$newAttachDir = $attachDir.'/'.$attachSubDir;
	if(!is_dir($newAttachDir))
	{
		@mkdir($newAttachDir, 0777);
		@fclose(fopen($newAttachDir.'/index.htm', 'w'));
	}
	PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
	$newFilename=date("YmdHis").mt_rand(1000,9999).'.'.$sExt;
	$targetPath = $newAttachDir.'/'.$newFilename;
	return $targetPath;
}
//debug 远程抓取图片函数 end
?>