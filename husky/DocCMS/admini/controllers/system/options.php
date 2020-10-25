<?php
checkme(9);
function index()
{
}
function save()
{
	global $request;
	if(filesize(ABSPATH.'/config/doc-config-'.$request['l'].'.php')>0){
		if (empty($request['urlrewrite'])){
			$request['urlrewrite'] = 'false';
		}else{
			$request['urlrewrite'] = 'true';
		}
		if (empty($request['guestbookauditing'])){
			$request['guestbookauditing'] = 'false';
		}else{
			$request['guestbookauditing'] = 'true';
		}
		if (empty($request['commentauditing'])){
			$request['commentauditing'] = 'false';
		}else{
			$request['commentauditing'] = 'true';
		}
		if (empty($request['productISON'])){
			$request['productISON'] = 'false';
		}else{
			$request['productISON'] = 'true';
		}
		if (empty($request['orderISON'])){
			$request['orderISON'] = 'false';
		}else{
			$request['orderISON'] = 'true';
		}
		if (empty($request['guestbookISON'])){
			$request['guestbookISON'] = 'false';
		}else{
			$request['guestbookISON'] = 'true';
		}
		if (empty($request['webopen'])){
			$request['webopen'] = 'false';
		}else{
			$request['webopen'] = 'true';
		}
		if (empty($request['iswater'])){
			$request['iswater'] = 'false';
		}else{
			$request['iswater'] = 'true';
		}
		if (empty($request['docping'])){
			$request['docping'] = 'false';
		}else{
			$request['docping'] = 'true';
		}
		//水印图片上传
		if(!empty($_FILES['waterimgs']['name'])){
			require_once(ABSPATH.'/inc/class.upload.php');
			$upload = new Upload();
		    $waterimgs = UPLOADPATH.$upload->SaveFile('waterimgs');
			if(is_file(ABSPATH.WATERIMGS)){
				@unlink(ABSPATH.WATERIMGS);
			}
		}else{
			$waterimgs = WATERIMGS;
		}

		$request['cachetime']=$request['cachetime']?$request['cachetime']*(3600*24):'0';
		
		$tempStr = file2String(ABSPATH.'/config/doc-config-'.$request['l'].'.php');
		$tempStr = preg_replace("/\('WEBOPEN',.*?\)/i","('WEBOPEN',".$request['webopen'].")",$tempStr);
		$tempStr = preg_replace("/'WEBURL','.*?'/i","'WEBURL','".$request['weburl']."'",$tempStr);
		$tempStr = preg_replace("/'WEBSIZE','.*?'/i","'WEBSIZE','".$request['websize']."'",$tempStr);
		$tempStr = preg_replace("/'SITENAME','.*?'/i","'SITENAME','".$request['sitename']."'",$tempStr);
		$tempStr = preg_replace("/'SITEKEYWORDS','.*?'/i","'SITEKEYWORDS','".$request['sitekeywords']."'",$tempStr);
		$tempStr = preg_replace("/'SITESUMMARY','.*?'/i","'SITESUMMARY','".$request['sitesummary']."'",$tempStr);
		$tempStr = preg_replace("/'UPLOADPATH','.*?'/i","'UPLOADPATH','".$request['uploadpath']."'",$tempStr);
		$tempStr = preg_replace("/'TIMEZONENAME','.*?'/i","'TIMEZONENAME','".$request['timeZoneName']."'",$tempStr);
		$tempStr = preg_replace("/'HTMLPATH','.*?'/i","'HTMLPATH','".$request['htmlpath']."'",$tempStr);
		$tempStr = preg_replace("/'ROOTPATH','.*?'/i","'ROOTPATH','".$request['rootpath']."'",$tempStr);
		$tempStr = preg_replace("/\('URLREWRITE',.*?\)/i","('URLREWRITE',".$request['urlrewrite'].")",$tempStr);
		$tempStr = preg_replace("/\('CACHETIME',.*?\)/i","('CACHETIME','".$request['cachetime']."')",$tempStr);
		$tempStr = preg_replace("/\('GUESTBOOKAUDITING',.*?\)/i","('GUESTBOOKAUDITING',".$request['guestbookauditing'].")",$tempStr);
		$tempStr = preg_replace("/\('DOCPING',.*?\)/i","('DOCPING',".$request['docping'].")",$tempStr);
		$tempStr = preg_replace("/\('COMMENTAUDITING',.*?\)/i","('COMMENTAUDITING',".$request['commentauditing'].")",$tempStr);
		$tempStr = preg_replace("/\('EDITORSTYLE',.*?\)/i","('EDITORSTYLE','".$request['editorstyle']."')",$tempStr);
		
		$tempStr = preg_replace("/\('ISWATER',.*?\)/i","('ISWATER',".$request['iswater'].")",$tempStr);
		$tempStr = preg_replace("/\('WATERIMGS',.*?\)/i","('WATERIMGS','".$waterimgs."')",$tempStr);
		$tempStr = preg_replace("/\('paint_bgcolor',.*?\)/i","('paint_bgcolor','".$request['paint_bgcolor']."')",$tempStr);
		$tempStr = preg_replace("/\('articleWidth',.*?\)/i","('articleWidth','".$request['articleWidth']."')",$tempStr);
		$tempStr = preg_replace("/\('articleHight',.*?\)/i","('articleHight','".$request['articleHight']."')",$tempStr);
		$tempStr = preg_replace("/\('listWidth',.*?\)/i","('listWidth','".$request['listWidth']."')",$tempStr);
		$tempStr = preg_replace("/\('listHight',.*?\)/i","('listHight','".$request['listHight']."')",$tempStr);
		$tempStr = preg_replace("/\('productWidth',.*?\)/i","('productWidth','".$request['productWidth']."')",$tempStr);
		$tempStr = preg_replace("/\('productHight',.*?\)/i","('productHight','".$request['productHight']."')",$tempStr);
		$tempStr = preg_replace("/\('pictureWidth',.*?\)/i","('pictureWidth','".$request['pictureWidth']."')",$tempStr);
		$tempStr = preg_replace("/\('pictureHight',.*?\)/i","('pictureHight','".$request['pictureHight']."')",$tempStr);
		
		$tempStr = preg_replace("/\('productMiddlePicWidth',.*?\)/i","('productMiddlePicWidth','".$request['productMiddlePicWidth']."')",$tempStr);
		$tempStr = preg_replace("/\('productMiddlePicHight',.*?\)/i","('productMiddlePicHight','".$request['productMiddlePicHight']."')",$tempStr);
		$tempStr = preg_replace("/\('productSmallPicWidth',.*?\)/i","('productSmallPicWidth','".$request['productSmallPicWidth']."')",$tempStr);
		$tempStr = preg_replace("/\('productSmallPicHight',.*?\)/i","('productSmallPicHight','".$request['productSmallPicHight']."')",$tempStr);
		$tempStr = preg_replace("/\('pictureMiddlePicWidth',.*?\)/i","('pictureMiddlePicWidth','".$request['pictureMiddlePicWidth']."')",$tempStr);
		$tempStr = preg_replace("/\('pictureMiddlePicHight',.*?\)/i","('pictureMiddlePicHight','".$request['pictureMiddlePicHight']."')",$tempStr);
		$tempStr = preg_replace("/\('pictureSmallPicWidth',.*?\)/i","('pictureSmallPicWidth','".$request['pictureSmallPicWidth']."')",$tempStr);
		$tempStr = preg_replace("/\('pictureSmallPicHight',.*?\)/i","('pictureSmallPicHight','".$request['pictureSmallPicHight']."')",$tempStr);
		
		$tempStr = preg_replace("/\('videoWidth',.*?\)/i","('videoWidth','".$request['videoWidth']."')",$tempStr);
		$tempStr = preg_replace("/\('videoHight',.*?\)/i","('videoHight','".$request['videoHight']."')",$tempStr);
		$tempStr = preg_replace("/\('userWidth',.*?\)/i","('userWidth','".$request['userWidth']."')",$tempStr);
		$tempStr = preg_replace("/\('userHight',.*?\)/i","('userHight','".$request['userHight']."')",$tempStr);
		
		$tempStr = preg_replace("/\('listCount',.*?\)/i","('listCount','".$request['listCount']."')",$tempStr);
		$tempStr = preg_replace("/\('pictureCount',.*?\)/i","('pictureCount','".$request['pictureCount']."')",$tempStr);
		$tempStr = preg_replace("/\('productCount',.*?\)/i","('productCount','".$request['productCount']."')",$tempStr);
		$tempStr = preg_replace("/\('videoCount',.*?\)/i","('videoCount','".$request['videoCount']."')",$tempStr);
		$tempStr = preg_replace("/\('guestbookCount',.*?\)/i","('guestbookCount','".$request['guestbookCount']."')",$tempStr);
		$tempStr = preg_replace("/\('commentCount',.*?\)/i","('commentCount','".$request['commentCount']."')",$tempStr);
		$tempStr = preg_replace("/\('jobsCount',.*?\)/i","('jobsCount','".$request['jobsCount']."')",$tempStr);
		$tempStr = preg_replace("/\('calllistCount',.*?\)/i","('calllistCount','".$request['calllistCount']."')",$tempStr);
		$tempStr = preg_replace("/\('downloadCount',.*?\)/i","('downloadCount','".$request['downloadCount']."')",$tempStr);
		$tempStr = preg_replace("/\('solutionsCount',.*?\)/i","('solutionsCount','".$request['solutionsCount']."')",$tempStr);
		
		/*********邮箱服务器 配置*********/
		$tempStr = preg_replace("/\('productISON',.*?\)/i","('productISON',".$request['productISON'].")",$tempStr);
		$tempStr = preg_replace("/\('orderISON',.*?\)/i","('orderISON',".$request['orderISON'].")",$tempStr);
		$tempStr = preg_replace("/\('guestbookISON',.*?\)/i","('guestbookISON',".$request['guestbookISON'].")",$tempStr);
		$tempStr = preg_replace("/\('smtpPort',.*?\)/i","('smtpPort','".$request['smtpPort']."')",$tempStr);
		$tempStr = preg_replace("/\('smtpServer',.*?\)/i","('smtpServer','".$request['smtpServer']."')",$tempStr);
		$tempStr = preg_replace("/\('smtpId',.*?\)/i","('smtpId','".$request['smtpId']."')",$tempStr);
		$tempStr = preg_replace("/\('smtpPwd',.*?\)/i","('smtpPwd','".$request['smtpPwd']."')",$tempStr);
		$tempStr = preg_replace("/\('smtpSender',.*?\)/i","('smtpSender','".$request['smtpSender']."')",$tempStr);
		$tempStr = preg_replace("/\('smtpReceiver',.*?\)/i","('smtpReceiver','".$request['smtpReceiver']."')",$tempStr);
		$tempStr = preg_replace("/\('LOGINIP',.*?\)/i","('LOGINIP','".$request['loginip']."')",$tempStr);
		/*********支付宝接口 配置*********/
		$tempStr = preg_replace("/'PAY_ISPAY','.*?'/i","'PAY_ISPAY','".$request['is_pay']."'",$tempStr);
		$tempStr = preg_replace("/'PAY_ISJS','.*?'/i","'PAY_ISJS','".$request['is_js']."'",$tempStr);
		
		$tempStr = preg_replace("/'PAY_PARTNER','.*?'/i","'PAY_PARTNER','".$request['partner']."'",$tempStr);
		$tempStr = preg_replace("/'PAY_KEY','.*?'/i","'PAY_KEY','".$request['key']."'",$tempStr);
		$tempStr = preg_replace("/'PAY_SELLER','.*?'/i","'PAY_SELLER','".$request['seller']."'",$tempStr);
		$tempStr = preg_replace("/'PAY_SHOW_URL','.*?'/i","'PAY_SHOW_URL','".$request['show_url']."'",$tempStr);
		$tempStr = preg_replace("/'PAY_MAINNAME','.*?'/i","'PAY_MAINNAME','".$request['mainname']."'",$tempStr);
		/*********财付通接口 配置*********/
		$tempStr = preg_replace("/'PAY_ISPAY_TEN','.*?'/i","'PAY_ISPAY_TEN','".$request['is_pay_ten']."'",$tempStr);
		$tempStr = preg_replace("/'PAY_ISJS_TEN','.*?'/i","'PAY_ISJS_TEN','".$request['is_js_ten']."'",$tempStr);
		
		$tempStr = preg_replace("/'PAY_PARTNER_TEN','.*?'/i","'PAY_PARTNER_TEN','".$request['partner_ten']."'",$tempStr);
		$tempStr = preg_replace("/'PAY_KEY_TEN','.*?'/i","'PAY_KEY_TEN','".$request['key_ten']."'",$tempStr);
		$tempStr = preg_replace("/'PAY_SELLER_TEN','.*?'/i","'PAY_SELLER_TEN','".$request['seller_ten']."'",$tempStr);
		$tempStr = preg_replace("/'PAY_SHOW_URL_TEN','.*?'/i","'PAY_SHOW_URL_TEN','".$request['show_url_ten']."'",$tempStr);
		$tempStr = preg_replace("/'PAY_MAINNAME_TEN','.*?'/i","'PAY_MAINNAME_TEN','".$request['mainname_ten']."'",$tempStr);
			
		@chmod(ABSPATH.'/config/doc-config-'.$request['l'].'.php', 0777);
		string2file($tempStr,ABSPATH.'/config/doc-config-'.$request['l'].'.php');
		redirect(ROOTPATH.'/admini/index.php?m=system&s=options');
	}
	else
	{
		echo "文件不存在!";
	}
}
?>