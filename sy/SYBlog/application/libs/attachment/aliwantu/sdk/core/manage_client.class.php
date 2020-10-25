<?php
if (! defined ( 'ALI_IMAGE_SDK_PATH' )) {
	define ( 'ALI_IMAGE_SDK_PATH', dirname ( __FILE__ ) );
}
require_once (ALI_IMAGE_SDK_PATH . '/conf/conf.class.php');
require_once (ALI_IMAGE_SDK_PATH . '/utils/encode_utils.class.php');
require_once (ALI_IMAGE_SDK_PATH . '/utils/mimetypes.class.php');
require_once (ALI_IMAGE_SDK_PATH . '/utils/manage_option.class.php');
require_once (ALI_IMAGE_SDK_PATH . '/utils/media_encode.class.php');
class ManageClient {
	private $manage_host;
	private $ak;
	private $sk;
	private $type;
	public function __construct($ak, $sk, $type = Conf::TYPE_TOP) {
		$this->ak = $ak;
		$this->sk = $sk;
		$this->type = $type;
		$this->manage_host = Conf::MANAGE_HOST_MEDIA;
	}
	/**文件是否存在 
	 * @param string $namespace 空间名，必须
	 * @param string $dir 路径
	 * @param string $filename 文件名
	 * @return array
	 */
	public function existsFile($namespace, $dir, $filename) {
		$resourceInfo = new ResourceInfo($namespace, $dir, $filename);
		list ( $isValid, $message ) = $resourceInfo->checkResourceInfo(true, true);
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		$resourceId = $resourceInfo->buildResourceId(); //得到资源ID
		$uri = '/' . Conf::MANAGE_API_VERSION . '/files/' . $resourceId . '/exist';
		return $this->_send_request ( 'GET', $uri );
	}
	/**获取文件的元信息(meta信息)
	 * @param string $namespace 空间名，必须
	 * @param string $dir 路径
	 * @param string $filename 文件名
	 * @return array
	 */
	public function getFileInfo($namespace, $dir, $filename) {
		$resourceInfo = new ResourceInfo($namespace, $dir, $filename);
		list ( $isValid, $message ) = $resourceInfo->checkResourceInfo(true, true);
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		$resourceId = $resourceInfo->buildResourceId(); //得到资源ID
		$uri = '/' . Conf::MANAGE_API_VERSION . '/files/' . $resourceId;
		return $this->_send_request ( 'GET', $uri );
	}
	/**重命名文件
	 * @param string $namespace 空间名，必须
	 * @param string $dir 路径
	 * @param string $filename 文件名
	 * @param string $newDir 新的路径
	 * @param string $newName 新的文件名
	 * @return array
	 */
	public function renameFile($namespace, $dir, $filename, $newDir, $newName) {
		$resourceInfo = new ResourceInfo($namespace, $dir, $filename); //老的资源
		list ( $isValid, $message ) = $resourceInfo->checkResourceInfo(true, true);
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		$newResourceInfo = new ResourceInfo($namespace, $newDir, $newName); //新的资源
		list ( $isValid, $message ) = $newResourceInfo->checkResourceInfo(true, true);
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		$resourceId = $resourceInfo->buildResourceId(); //老资源ID
		$newResourceId = $newResourceInfo->buildResourceId(); //新资源ID
		$uri = '/' . Conf::MANAGE_API_VERSION . '/files/' . $resourceId . "/rename/" . $newResourceId;
		return $this->_send_request ( 'POST', $uri );
	}
	/**获取指定目录下的文件列表
	 * @param string $namespace 空间名，必须
	 * @param string $dir 路径
	 * @param number $page 页数
	 * @param number $pageSize 每页显示的记录数
	 */
	public function listFiles($namespace, $dir, $page = 1, $pageSize = 100) {
		$manageOption = new ManageOption($namespace);
		$manageOption->setDir($dir)->setCurrentPage($page)->setPageSize($pageSize);
		list ( $isValid, $message ) = $manageOption->checkResourceInfo(true);
		if( $page<=0 || $pageSize<=0 ) {
			$isValid=false;
			$message = "Invalid parameters page or pageSize";
		}
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		$queryParas = $manageOption->buildListFilesParas(); //查询query参数
		$uri = '/' . Conf::MANAGE_API_VERSION . '/files?'.$queryParas;
		return $this->_send_request ( 'GET', $uri );
	}
	/**删除文件
	 * @param string $namespace 空间名，必须
	 * @param string $dir 路径
	 * @param string $filename 文件名
	 * @return array
	 */
	public function deleteFile($namespace, $dir, $filename) {
		$resourceInfo = new ResourceInfo($namespace, $dir, $filename);
		list ( $isValid, $message ) = $resourceInfo->checkResourceInfo(true, true);
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		$resourceId = $resourceInfo->buildResourceId(); //得到资源ID
		$uri = '/' . Conf::MANAGE_API_VERSION . '/files/' . $resourceId;
		return $this->_send_request ( 'DELETE', $uri );
	}
	/**文件夹是否存在
	 * @param string $namespace 空间名，必须
	 * @param string $dir 路径，即文件夹
	 * @return array
	 */
	public function existsFolder($namespace, $dir) {
		if (empty($namespace) || empty($dir))
			return $this->_errorResponse ( "InvalidArgument", "namespace or dir is empty" );
		if (strpos ( $dir, '/' ) !== 0)
			$dir = '/' . $dir;
		$resourceInfo = new ResourceInfo($namespace, $dir);
		$resourceId = $resourceInfo->buildResourceId(); //得到资源ID
		$uri = '/' . Conf::MANAGE_API_VERSION . '/folders/' . $resourceId . '/exist';
		return $this->_send_request ( 'GET', $uri );
	}
	/**创建文件夹
	 * @param string $namespace 空间名，必须
	 * @param string $dir 路径，即文件夹
	 * @return array
	 */
	public function createDir($namespace, $dir) {
		if (empty($namespace) || empty($dir))
			return $this->_errorResponse ( "InvalidArgument", "namespace or dir is empty" );
		if (strpos ( $dir, '/' ) !== 0)
			$dir = '/' . $dir;
		$resourceInfo = new ResourceInfo($namespace, $dir);
		$resourceId = $resourceInfo->buildResourceId(); //得到资源ID
		$uri = '/' . Conf::MANAGE_API_VERSION . '/folders/' . $resourceId;
		return $this->_send_request ( 'POST', $uri );
	}
	/**获取指定目录下的文件夹列表
	 * @param string $namespace 空间名，必须
	 * @param string $dir 路径，指定目录
	 * @param number $page 页数
	 * @param number $pageSize 每页显示的记录数
	 */
	public function listDirs($namespace, $dir, $page = 1, $pageSize = 100) {
		$manageOption = new ManageOption($namespace);
		$manageOption->setDir($dir)->setCurrentPage($page)->setPageSize($pageSize);
		list ( $isValid, $message ) = $manageOption->checkResourceInfo(true);
		if( $page<=0 || $pageSize<=0 ) {
			$isValid=false;
			$message = "Invalid parameters page or pageSize";
		}
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		$queryParas = $manageOption->buildListFilesParas(); //查询query参数
		$uri = '/' . Conf::MANAGE_API_VERSION . '/folders?'.$queryParas;
		return $this->_send_request ( 'GET', $uri );
	}
	/**删除文件夹
	 * @param string $namespace 空间名，必须
	 * @param string $dir 路径，即文件夹
	 * @return array
	 */
	public function deleteDir($namespace, $dir) {
		if (empty($namespace) || empty($dir))
			return $this->_errorResponse ( "InvalidArgument", "namespace or dir is empty" );
		if (strpos ( $dir, '/' ) !== 0)
			$dir = '/' . $dir;
		$resourceInfo = new ResourceInfo($namespace, $dir);
		$resourceId = $resourceInfo->buildResourceId(); //得到资源ID
		$uri = '/' . Conf::MANAGE_API_VERSION . '/folders/' . $resourceId;
		return $this->_send_request ( 'DELETE', $uri );
	}
	/*######################################华丽的分界线#######################################*/
	/*#######################上面是文件或文件夹的管理，下面是特色服务接口########################*/
	/*########################################################################################*/
	/**黄图扫描接口
	 * @param ManageOption $resInfos 待扫描图片资源
	 * @return array
	 */
	public function scanPorn( ManageOption $resInfos ) {
		$uri = '/'.Conf::SCAN_PORN_VERSION.'/scanPorn';
		list ( $isValid, $message, $bodyArray ) = $resInfos->checkFilesAndUrls(); //检测并得到黄图扫描所需参数
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		$httpBody = $this->createHttpBody($bodyArray);//http body字符串信息
		return $this->_send_request ( 'POST', $uri, $httpBody );
	}
	/**鉴黄反馈feedback接口
	 * @param ManageOption $pornFbInfos 反馈信息
	 * @return array
	 */
	public function pornFeedback( ManageOption $pornFbInfos ) {
		$uri = '/'.Conf::SCAN_PORN_VERSION.'/feedback';
		list ( $isValid, $message, $httpBody ) = $pornFbInfos->checkPornFeedbackInfos();
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		return $this->_send_request ( 'POST', $uri, $httpBody );
	}
	/**多媒体(音视频)转码服务接口
	 * @param MediaResOption $encodeOption 转码参数选项
	 * @return array
	 */
	public function mediaEncode( MediaResOption $encodeOption ) {
		$uri = '/'.Conf::MEDIA_ENCODE_VERSION.'/mediaEncode';
		list ( $isValid, $message, $httpBody ) = $encodeOption->checkOptionParameters();
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		return $this->_send_request ( 'POST', $uri, $httpBody );
	}
	/**多媒体转码任务查询接口
	 * @param string $taskId 转码任务ID
	 */
	public function mediaEncodeQuery($taskId) {
		if (empty( $taskId ))
			return $this->_errorResponse ( "InvalidArgument", "taskId is empty" );
		$uri = '/'.Conf::MEDIA_ENCODE_VERSION.'/mediaEncodeResult/'.$taskId;
		return $this->_send_request ( 'GET', $uri );
	}
	/**视频截图接口
	 * @param MediaResOption $snapshotOption 截图参数选项
	 * @return array
	 */
	public function videoSnapshot( MediaResOption $snapshotOption ) {
		$uri = '/'.Conf::MANAGE_API_VERSION.'/snapshot';
		list ( $isValid, $message, $httpBody ) = $snapshotOption->checkOptionParameters();
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		return $this->_send_request ( 'POST', $uri, $httpBody );
	}
	/**视频截图结果查询接口
	 * @param string $taskId 转码任务ID
	 */
	public function vSnapshotQuery( $taskId ) {
		if (empty( $taskId ))
			return $this->_errorResponse ( "InvalidArgument", "taskId is empty" );
		$uri = '/'.Conf::MANAGE_API_VERSION.'/snapshotResult/'.$taskId;
		return $this->_send_request ( 'GET', $uri );
	}
	/**
	 * 广告图扫描接口(beta)
	 * @param ManageOption $resInfos 待扫描图片资源
	 * @return array
	 */
	public function scanAdvertising( ManageOption $resInfos ) {
		$uri = '/3.1/scanAdvertising';
		list ( $isValid, $message, $bodyArray ) = $resInfos->checkFilesAndUrls(); //检测并得到广告图图扫描所需参数
		if (!$isValid) {
			return $this->_errorResponse ( "InvalidArgument", $message );
		}
		$httpBody = $this->createHttpBody($bodyArray);//http body字符串信息
		return $this->_send_request ( 'POST', $uri, $httpBody );
	}
	/**调用curl利用http上传数据
	 * @param string $method
	 * @param string $uri
	 * @param array $bodyArray
	 * @param array $headers
	 * @return array (isSuccess, ...)
	 */
	protected function _send_request($method, $uri, $httpBody = null, $headers = null) {
		$success = false;
		$result = null;
		//构建Http请求头
		$_headers = array ( 'Expect:' );
		$date = $this->currentMilliSecond(); //得到当前的时间戳，毫秒
		array_push ( $_headers, "Date: {$date}" );
		$authorization = $this->_getAuthorization ( $uri, $date, $httpBody );	//Http的Body需要加入管理鉴权
		array_push ( $_headers, "Authorization: {$authorization}" );
		array_push ( $_headers, "User-Agent: {$this->_getUserAgent()}" );
		if (! is_null ( $headers ) && is_array ( $headers )) {
			foreach ( $headers as $k => $v ) {
				array_push ( $_headers, "{$k}: {$v}" );
			}
		}
		$url = $this->_get_manage_url ( $uri ); //根据管理接口uri拼接成URL
		$ch = curl_init ( $url );
		try {
			//构建http请求体，并设置header部分属性
			$length = 0;
			if (! empty ( $httpBody )) {
				$length = @strlen ( $httpBody );
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, $httpBody );
				array_push ( $_headers, "Content-Type: application/x-www-form-urlencoded");
			}
			array_push ( $_headers, "Content-Length: {$length}" );
			curl_setopt ( $ch, CURLOPT_HEADER, 1 );							//设置头部
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $_headers );				//请求头
			curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 );						//超时时长
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );					//重传
			curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 0 );
			curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, $method );			//自定义请求
			//设置请求方式(GET或POST等)
			if ($method == 'PUT' || $method == 'POST') {
				curl_setopt ( $ch, CURLOPT_POST, 1 );
			} else {
				curl_setopt ( $ch, CURLOPT_POST, 0 );
			}
			//执行上传，然后获取服务端返回
			$response = curl_exec ( $ch );
			$http_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
			//解析返回结果，并判断是否上传成功
			$success = ($http_code == 200) ? true : false;					//判断是否上传成功
			$resStr = explode ( "\r\n\r\n", $response );
			$resBody = isset ( $resStr [1] ) ? $resStr [1] : '';
			$resArray = json_decode ( $resBody, true );						//解析得到结果
			$result = (empty ( $resArray )) ? array () : $resArray;
		} catch (Exception $e) {
			$success = false;
			$result = $this->_errorResponse("HTTPRequestException#".$e->getCode(), $e->getMessage());
		}
		curl_close ( $ch );//PHP5.3中不支持finally关键字。因此，为了兼容，这里取消finally
		$result ['isSuccess'] = $success;
		return $result;
	}
	/**根据$bodyArray构建http请求体。多个字段之间用&号分割*/
	protected function createHttpBody( $bodyArray ) {
		$bodyStr = '';
		foreach ($bodyArray as $key => $value) {
			$bodyStr .= ( empty($bodyStr) ? null : '&'); //添加分隔符
			$bodyStr .= "{$key}=".urlencode($value);
		}
		return $bodyStr;
	}
	/**UserAgent用户代理 */
	protected function _getUserAgent() {
		if ($this->type == "TOP") {
			return "ALIMEDIASDK_PHP_TAE/" . Conf::SDK_VERSION;
		} else {
			return "ALIMEDIASDK_PHP_CLOUD/" . Conf::SDK_VERSION;
		}
	}
	/**根据管理接口uri拼接成完整的URL*/
	protected function _get_manage_url($uri) {
		return Conf::MANAGE_HOST_MEDIA . $uri;
	}
	protected function _getNamespaceKey() {
		if ($this->type == "TOP") {
			return "namespace";
		} else {
			return "bucketName";
		}
	}
	/**获取管理鉴权信息*/
	protected function _getAuthorization($uri, $date, $httpBody) {
		$stringBeforeSign = "{$uri}\n{$httpBody}\n{$date}"; //1.生成待加签的原始字符串
		$signStr = hash_hmac( 'sha1', $stringBeforeSign, $this->sk); //2.使用SK对字符串计算HMAC-SHA1签名
		$preenCode = $this->ak.":".$signStr; //3.将签名与AK进行拼接
		$encodedStr = EncodeUtils::encodeWithURLSafeBase64( $preenCode ); //4.对拼接后的结果进行URL安全的Base64编码
		$manageToken = "ACL_" . $this->type . " " .$encodedStr;//5.最后为编码结果加上得到管理凭证
		return $manageToken;
	}
    /**得到当前时间的毫秒数*/
	protected function currentMilliSecond() {
	    list($microSec, $stampSec) = explode(' ', microtime());
	    $tempMilli =  sprintf('%03s', intval($microSec*1000));
	    $currentMilli = $stampSec.$tempMilli;
	    return $currentMilli;
	}
	/**反馈错误信息*/
	protected function _errorResponse($code = "UnknownError", $message = "unkonown error", $requestId = null) {
		return array (
				"isSuccess" => false,
				"code" => $code,
				"message" => $message,
				"requestId" => $requestId 
		);
	}
}
