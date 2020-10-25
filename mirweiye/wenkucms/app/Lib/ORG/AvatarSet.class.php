<?php
class AvatarSet
{
// 例如http://127.0.0.1/wenku/qwef.php
	//本次页面请求的 url,链接中所有的字母全部变为小写
	//getbaseurl依赖他获得地址，这个函数不能动
	public function getThisUrl()
	{
		$thisUrl = $_SERVER['SCRIPT_NAME'];
		$thisUrl = "http://{$_SERVER['HTTP_HOST']}{$thisUrl}";
		return $thisUrl;
	}
	

	// 本次页面请求的 url路径，不含尾部的php文件名
	//http://127.0.0.1/wkcms/app/lib/例如
	//这个函数不能动，因为有两个函数依赖他获得url地址
	public function getBaseUrl()
	{
		$baseUrl = $this->getThisUrl();
		$baseUrl = substr( $baseUrl, 0, strrpos( $baseUrl, '/' ) + 1 );
		return $baseUrl;
	}

	// 例如D:\phpweb\wkcms\app\Lib\ORG\包含这个php文件的本地文件夹，区分大小写（尾部有一个 DIRECTORY_SEPARATOR 也就是\）
	public function getBasePath()
	{
		$basePath = $_SERVER['SCRIPT_FILENAME'];
		$basePath = substr( $basePath, 0, strrpos($basePath, '/' ) + 1 );
		$basePath = str_replace( '/', DIRECTORY_SEPARATOR, $basePath );
		return $basePath;
	}
	
	
	

	// 第一步：上传原始图片文件
	private function uploadAvatar( $uid ,$path='')
	{
		// 检查上传文件的有效性
		if ( empty($_FILES['Filedata']) ) {
			return -3; // No photograph be upload!
		}

		// 本地临时存储位置!!!!
		$tmpPath = $this->getimgdir($path) . "data" . DIRECTORY_SEPARATOR . "{$uid}";

		// 如果临时存储的文件夹不存在，先创建它
		$dir = dirname( $tmpPath );
		if ( !file_exists( $dir ) ) {
			@mkdir( $dir, 0777, true );
		}

		// 如果同名的临时文件已经存在，先删除它
		if ( file_exists($tmpPath) ) {
			@unlink($tmpPath);
		}

		// 把上传的图片文件保存到预定位置
		if ( @copy($_FILES['Filedata']['tmp_name'], $tmpPath) || @move_uploaded_file($_FILES['Filedata']['tmp_name'], $tmpPath)) {
			@unlink($_FILES['Filedata']['tmp_name']);
			list($width, $height, $type, $attr) = getimagesize($tmpPath);
			if ( $width < 10 || $height < 10 || $width > 3000 || $height > 3000 || $type == 4 ) {
				@unlink($tmpPath);
				return -2; // Invalid photograph!
			}
		} else {
			@unlink($_FILES['Filedata']['tmp_name']);
			return -4; // Can not write to the data/tmp folder!
		}

		// 用于访问临时图片文件的 url!!!!!
		$tmpUrl = $this->getimgurl($path) . "data/{$uid}";
		return $tmpUrl;
	}

	private function flashdata_decode($s) {
		$r = '';
		$l = strlen($s);
		for($i=0; $i<$l; $i=$i+2) {
			$k1 = ord($s[$i]) - 48;
			$k1 -= $k1 > 9 ? 7 : 0;
			$k2 = ord($s[$i+1]) - 48;
			$k2 -= $k2 > 9 ? 7 : 0;
			$r .= chr($k1 << 4 | $k2);
		}
		return $r;
	}

	// 第二步：上传分割后的三个图片数据流
	private function rectAvatar( $uid ,$path='')
	{
	    // 本地用户头像文件夹!!!!
		$tmpPath = $this->getimgdir($path) . "{$uid}" . DIRECTORY_SEPARATOR ;

		// 如果本地用户头像文件夹不存在，先创建它
	    if ( is_dir( $tmpPath ) ) {
			
	        $handle = opendir($tmpPath);
	        while (($file = readdir($handle)) !== false)
	          {
		         if ($file != "." && $file != ".." && is_file("$tmpPath/$file"))
		         {
			       unlink("$tmpPath/$file");
		         }
	           }
	           closedir($handle);
		}
		if ( !is_dir( $tmpPath ) ) {
			@mkdir( $tmpPath, 0777, true );
		}
        
		
		
		
		// 从 $_POST 中提取出三个图片数据流
		$bigavatar    = $this->flashdata_decode( $_POST['avatar1'] );
		$md5uid=md5($uid); 
		
		
		$middleavatar = $this->flashdata_decode( $_POST['avatar2'] );
		$smallavatar  = $this->flashdata_decode( $_POST['avatar3'] );
		if ( !$bigavatar || !$middleavatar || !$smallavatar ) {
			return '<root><message type="error" value="-2" /></root>';
		}

		// 保存为图片文件
		$bigavatarfile    = $this->getimgdir($path) . "{$uid}" . DIRECTORY_SEPARATOR . "{$md5uid}_big.jpg";
		$middleavatarfile = $this->getimgdir($path) . "{$uid}" . DIRECTORY_SEPARATOR . "{$md5uid}_120.jpg";
		$smallavatarfile  = $this->getimgdir($path) . "{$uid}" . DIRECTORY_SEPARATOR . "{$md5uid}_48.jpg";

		$success = 1;
		$fp = @fopen($bigavatarfile, 'wb');
		@fwrite($fp, $bigavatar);
		@fclose($fp);

		$fp = @fopen($middleavatarfile, 'wb');
		@fwrite($fp, $middleavatar);
		@fclose($fp);

		$fp = @fopen($smallavatarfile, 'wb');
		@fwrite($fp, $smallavatar);
		@fclose($fp);

		// 验证图片文件的正确性
		$biginfo    = @getimagesize($bigavatarfile);
		$middleinfo = @getimagesize($middleavatarfile);
		$smallinfo  = @getimagesize($smallavatarfile);
		if ( !$biginfo || !$middleinfo || !$smallinfo || $biginfo[2] == 4 || $middleinfo[2] == 4 || $smallinfo[2] == 4 ) {
			file_exists($bigavatarfile) && unlink($bigavatarfile);
			file_exists($middleavatarfile) && unlink($middleavatarfile);
			file_exists($smallavatarfile) && unlink($smallavatarfile);
			$success = 0;
		}

		// 删除临时存储的图片!!!!!
		$tmpPath = $this->getimgdir($path) . "data" . DIRECTORY_SEPARATOR . "{$uid}";
		@unlink($tmpPath);

		return '<?xml version="1.0" ?><root><face success="' . $success . '"/></root>';
	}

	// 从客户端访问头像图片的 url!!!!!
	public function getAvatarUrl( $uid, $size='120' )
	{
		$md5uid=md5($uid);
		return $this->getimgurl($path) . "{$uid}/{$md5uid}_{$size}.jpg";
	}

	// 处理 HTTP Request
	// 返回值：如果是可识别的 request，处理后返回 true；否则返回 false。
	public function processRequest()
	{
		// 从 input 参数里拆解出自定义参数
		$arr = array();
		parse_str( $_GET['input'], $arr );
		$uid = intval($arr['uid']);
        $path = trim($arr['path']);
		if ( $_GET['a'] == 'uploadavatar') {

			// 第一步：上传原始图片文件
			echo $this->uploadAvatar( $uid ,$path);
			return true;

		} else if ( $_GET['a'] == 'rectavatar') {
		
			// 第二步：上传分割后的三个图片数据流
			echo $this->rectAvatar( $uid,$path );
			return true;
		}

		return false;
	}
   //得到头像存储的绝对路径，例如D:
	public function getimgdir($path){
		$url=$this->getBasePath();
		$url = str_replace( 'app\\Lib\\ORG\\', '', $url );
		
		//$url=$url.'data/upload/avatar/';
		$url=$url.$path.'avatar/';
		$url = str_replace( '/', DIRECTORY_SEPARATOR, $url );
		
		return $url;
		
	}
   //得到头像存储的url，例如HTTP://
	public function getimgurl($path){
		$url=$this->getBaseUrl();
		$url = str_replace( 'app/lib/org/', '', $url );
		
		
		
		
		$url=$url.$path.'avatar/';
		//$url=$url.'data/upload/avatar/';
		return $url;
		
	}
	//得到头像上传的php文件路径
	public function getphpurl(){
		$url=$this->getBaseUrl();
		$url=$url.'app/lib/org/avatarset.class.php';
		
		return $url;
		
	}
    //获得头像上传swf的路径
    public function getswfurl(){
		$url=$this->getBaseUrl();
		$url=$url.'public/js/avatar/';
		
		return $url;
		
	}
	
	// 编辑页面中包含 camera.swf 的 HTML 代码
	public function renderHtml( $uid )
	{
		// 把需要回传的自定义参数都组装在 input 里
		//$input = urlencode( "uid={$uid},path={$path}" );
		
		$path=C('wkcms_attach_path');
        $input = urlencode("uid={$uid}&path={$path}");
		$baseUrl = $this->getswfurl();
		//$uc_api =C('wkcms_attach_path');
		$uc_api = urlencode($this->getphpurl());
		
		
		$urlCameraFlash = "{$baseUrl}camera.swf?ucapi={$uc_api}&input={$input}";
		$urlCameraFlash = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 

codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="447" height="477" id="mycamera" align="middle">
				<param name="allowScriptAccess" value="always" />
				<param name="scale" value="exactfit" />
				<param name="wmode" value="transparent" />
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />
				<param name="movie" value="'.$urlCameraFlash.'" />
				<param name="menu" value="false" />
				<embed src="'.$urlCameraFlash.'" quality="high" bgcolor="#ffffff" width="447" height="477" name="mycamera" align="middle" 

allowScriptAccess="always" allowFullScreen="false" scale="exactfit"  wmode="transparent" type="application/x-shockwave-flash" 

pluginspage="http://www.macromedia.com/go/getflashplayer" />
			</object>';
		return $urlCameraFlash;
	}
}


$au = new AvatarSet();
if ( $au->processRequest() ) {
	exit();
}
?>