<?php
if (! defined ( 'ALI_IMAGE_SDK_PATH' )) {
	define ( 'ALI_IMAGE_SDK_PATH', dirname ( __FILE__ ) );
}
require_once (ALI_IMAGE_SDK_PATH . '/conf/conf.class.php');
require_once (ALI_IMAGE_SDK_PATH . '/utils/encode_utils.class.php');
require_once (ALI_IMAGE_SDK_PATH . '/utils/upload_policy.class.php');
require_once (ALI_IMAGE_SDK_PATH . '/utils/upload_option.class.php');

class UploadClient {
	private $upload_host;
	private $ak;
	private $sk;
	private $type; // "CLOUD" or "TOP";
	public function __construct($ak, $sk, $type = Conf::TYPE_TOP) {
		$this->ak = $ak;
		$this->sk = $sk;
		$this->type = $type;
		$this->upload_host = Conf::UPLOAD_HOST_MEDIA;
	}
	/**上传文件。根据文件大小判断是否进行分片
	 * @param string $filePath
	 *        	文件路径
	 * @param UploadPolicy $uploadPolicy
	 *        	上传策略
	 * @param UploadOption $uploadOption
	 *        	上传选项
	 * @return array
	 */
	public function upload($filePath, UploadPolicy $uploadPolicy, UploadOption $uploadOption = null) {
		$encodePath=iconv('UTF-8','GB2312',$filePath); //中文需要转换成gb2312，file_exist等函数才能识别
		if (! file_exists ( $encodePath )) {
			return $this->_errorResponse ( "FileNotExist", "file not exist" );
		}
		if( empty($uploadOption) ) {
			$uploadOption = new UploadOption();     //如果用户没有传递UploadOption，则生成一个默认的
		}
		if ( empty($uploadOption->name) ) {
			$uploadOption->name = basename ( $filePath );        //如果用户没有设置name属性，则使用上传时的文件名
		}
		// UploadPolicy 和 UploadOption检查
		list ( $isValid, $message ) = $this->checkUploadInfo ( $uploadPolicy, $uploadOption );
		if (! $isValid) {
			return $this->_errorResponse ( "ErrorUploadInfo", "error upload policy or option:" . $message );
		}
		$fileSize = filesize ( $encodePath );
		// 文件大于设定的分片大小(默认2M)，则进行分片上传uploadSuperfile()
		if ($fileSize > ($uploadOption->blockSize)) {
			return $this->uploadSuperFile ( $encodePath, $uploadPolicy, $uploadOption );
		}
		// 文件不大于设定的分片大小(默认2M)，则直接上传uploadMiniFile()
		return $this->uploadMiniFile ( $encodePath, $uploadPolicy, $uploadOption );
	}
	/**上传小文件
	 * @param string $filePath 文件路径
	 * @param UploadPolicy $uploadPolicy 上传策略
	 * @param UploadOption $uploadOption 上传选项
	 * @return array
	 */
	protected function uploadMiniFile($filePath, UploadPolicy $uploadPolicy, UploadOption $uploadOption = null) {
		$data = file_get_contents ( $filePath );
		$uploadOption->setContent($data);
		$url = $this->upload_host . Conf::UPLOAD_API_UPLOAD;		//普通上传的API
		return $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
	}
	/**分片上传大文件
	 * @param string $filePath 文件路径
	 * @param UploadPolicy $uploadPolicy 上传策略
	 * @param UploadOption $uploadOption 上传选项
	 * @return array
	 */
	protected function uploadSuperFile($filePath, UploadPolicy $uploadPolicy, UploadOption $uploadOption = null ) {
		$fileSize = filesize ( $filePath );		// 文件大小
		$blockSize = $uploadOption->blockSize;	// 文件分片大小
		$blockNum = intval ( ceil ( $fileSize / $blockSize ) ); // 文件分片后的块数
		for($i = 0; $i < $blockNum; $i ++) {
			$currentSize = $blockSize; // 当前文件块的大小
			if (($i + 1) === $blockNum) {
				$currentSize = ($fileSize - ($blockNum - 1) * $blockSize); // 计算最后一个块的大小
			}
			$offset = $i * $blockSize; // 当前文件块相对于文件开头的偏移量（块的起始位置）
			$blockData = file_get_contents ( $filePath, 0, null, $offset, $currentSize ); //当前文件块的数据
			$uploadOption->setContent($blockData); // 设置待上传的文件块
			$httpRes = null;
			if (0 == $i) {
				// 分片初始化阶段
				$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_INIT;		//初始化分片上传的API
				$uploadOption->optionType = UpOptionType::BLOCK_INIT_UPLOAD;	//初始化分片时的Option类型
				$httpRes = $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
				$uploadId = isset ( $httpRes ['uploadId'] ) ? $httpRes ['uploadId'] : null;// 分片上传ID（OSS用于区分上传的id）
				$id = isset ( $httpRes ['id'] ) ? $httpRes ['id'] : null;// 上传唯一ID（多媒体服务用于区分上传的id）
				$uploadOption->setUploadId($uploadId);
				$uploadOption->setUniqueIdId($id);
			} else {
				// 分片上传过程中
				$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_UPLOAD;		//分片上传过程中的API
				$uploadOption->optionType = UpOptionType::BLOCK_RUN_UPLOAD;		//分片上传过程中的Option类型
				$uploadOption->setPartNumber($i + 1);							//
				$httpRes = $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
			}
			// 如果分片上传失败，则取消cancel分片上传任务，然后返回错误信息
			if (! $httpRes ['isSuccess']) {
				$message = $httpRes ['message'];
				$code = $httpRes ['code'];
				$requestId = $httpRes ['requestId'];
				$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_CANCEL;		//取消分片任务的API
				$uploadOption->optionType = UpOptionType::BLOCK_CANCEL_UPLOAD;	//取消分片任务时的Option类型
				$this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption ); // 不判断取消分片任务返回的结果
				return $this->_errorResponse ( $code, "fail upload block file:" . $message, $requestId );
			}
			// 保存 块编号partNumber 和 标记ETag，用于分片完成时的参数设置
			$uploadOption->addPartNumberAndETag($httpRes ['partNumber'], $httpRes ['eTag']);
		}
		// 分片上传完成
		$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_COMPLETE;			//完成分片上传任务的API
		$uploadOption->optionType = UpOptionType::BLOCK_COMPLETE_UPLOAD;		//完成分片上传任务时的Option类型
		$uploadOption->setMd5(md5_file ( $filePath ));							//文件Md5
		return $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
	}
	/**上传字符串/二进制数据
	 * @param string $data
	 *        	文件数据
	 * @param UploadPolicy $uploadPolicy
	 *        	上传策略
	 * @param UploadOption $uploadOption
	 *        	上传选项
	 * @return array
	 */
	public function uploadData( $data, UploadPolicy $uploadPolicy, UploadOption $uploadOption = null  ) {
		if( empty($uploadOption) ) {
			$uploadOption = new UploadOption();     //如果用户没有传递UploadOption，则生成一个默认的
		}
		// UploadPolicy 和 UploadOption检查
		list ( $isValid, $message ) = $this->checkUploadInfo ( $uploadPolicy, $uploadOption );
		if (! $isValid) {
			return $this->_errorResponse ( "ErrorUploadInfo", "error upload policy or option:" . $message );
		}
		$dataSize = strlen ( $data );
		// 文件大于设定的分片大小(默认2M)，则进行分片上传uploadSuperfile()
 		if ($dataSize > ($uploadOption->blockSize)) {
			return $this->uploadSuperData ( $data, $uploadPolicy, $uploadOption );
		}
		// 文件不大于设定的分片大小(默认2M)，则直接上传uploadMiniFile()
		$uploadOption->setContent($data);
		$url = $this->upload_host . Conf::UPLOAD_API_UPLOAD;		//普通上传的API
		return $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
	}
	/**分片上传大文件数据
	 * @param string $data 文件数据
	 * @param UploadPolicy $uploadPolicy 上传策略
	 * @param UploadOption $uploadOption 上传选项
	 * @return array
	 */
	protected function uploadSuperData($data, UploadPolicy $uploadPolicy, UploadOption $uploadOption = null ) {
		$dataSize = strlen ( $data );			// 文件大小
		$blockSize = $uploadOption->blockSize;	// 文件分片大小
		$blockNum = intval ( ceil ( $dataSize / $blockSize ) ); // 文件分片后的块数
		for($i = 0; $i < $blockNum; $i ++) {
			$currentSize = $blockSize; // 当前文件块的大小
			if (($i + 1) === $blockNum) {
				$currentSize = ($dataSize - ($blockNum - 1) * $blockSize); // 计算最后一个块的大小
			}
			$offset = $i * $blockSize; // 当前文件块相对于文件开头的偏移量（块的起始位置）
			$blockData = substr($data, $offset, $currentSize); //当前文件块的数据
			$uploadOption->setContent($blockData); // 设置待上传的文件块
			$httpRes = null;
			if (0 == $i) {
				// 分片初始化阶段
				$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_INIT;		//初始化分片上传的API
				$uploadOption->optionType = UpOptionType::BLOCK_INIT_UPLOAD;	//初始化分片时的Option类型
				$httpRes = $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
				$uploadId = isset ( $httpRes ['uploadId'] ) ? $httpRes ['uploadId'] : null;// 分片上传ID（OSS用于区分上传的id）
				$id = isset ( $httpRes ['id'] ) ? $httpRes ['id'] : null;// 上传唯一ID（多媒体服务用于区分上传的id）
				$uploadOption->setUploadId($uploadId);
				$uploadOption->setUniqueIdId($id);
			} else {
				// 分片上传过程中
				$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_UPLOAD;		//分片上传过程中的API
				$uploadOption->optionType = UpOptionType::BLOCK_RUN_UPLOAD;		//分片上传过程中的Option类型
				$uploadOption->setPartNumber($i + 1);							//
				$httpRes = $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
			}
			// 如果分片上传失败，则取消cancel分片上传任务，然后返回错误信息
			if (! $httpRes ['isSuccess']) {
				$message = $httpRes ['message'];
				$code = $httpRes ['code'];
				$requestId = $httpRes ['requestId'];
				$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_CANCEL;		//取消分片任务的API
				$uploadOption->optionType = UpOptionType::BLOCK_CANCEL_UPLOAD;	//取消分片任务时的Option类型
				$this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption ); // 不判断取消分片任务返回的结果
				return $this->_errorResponse ( $code, "fail upload block file:" . $message, $requestId );
			}
			// 保存 块编号partNumber 和 标记ETag，用于分片完成时的参数设置
			$uploadOption->addPartNumberAndETag($httpRes ['partNumber'], $httpRes ['eTag']);
		}
		// 分片上传完成
		$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_COMPLETE;			//完成分片上传任务的API
		$uploadOption->optionType = UpOptionType::BLOCK_COMPLETE_UPLOAD;		//完成分片上传任务时的Option类型
		$uploadOption->setMd5(md5 ( $data ));							//文件Md5
		return $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
	}
	/**创建分片上传任务，指定待上传的文件。即初始化分片上传
	 * @param string $filePath 文件路径
	 * @param UploadPolicy $uploadPolicy 上传策略
	 * @param UploadOption $uploadOption 上传选项
	 * @return array 初始化分片上传的结果
	 */
	public function multipartInit($filePath, UploadPolicy $uploadPolicy,  UploadOption $uploadOption) {
		$encodePath=iconv('UTF-8','GB2312',$filePath); //中文需要转换成gb2312，file_exist等函数才能识别
		if (! file_exists ( $encodePath )) {
			return $this->_errorResponse ( "FileNotExist", "file not exist" );
		}
		if( empty($uploadOption) ) {
			$uploadOption = new UploadOption();     //如果用户没有传递UploadOption，则生成一个默认的
		}
		if ( empty($uploadOption->name) ) {
			$uploadOption->name = basename ( $filePath );        //如果用户没有设置name属性，则使用上传时的文件名
		}
		$blockData = file_get_contents ( $encodePath, 0, null, 0, $uploadOption->blockSize );
		return $this->multipartInitByData ( $blockData, $uploadPolicy, $uploadOption );
	}
	/**创建分片上传任务，指定初始化分片任务的数据，即第一块数据
	 * @param string $blockData 文件数据
	 * @param UploadPolicy $uploadPolicy 上传策略
	 * @param UploadOption $uploadOption 上传选项
	 * @return array 初始化分片上传的结果
	 */
	public function multipartInitByData($blockData, UploadPolicy $uploadPolicy,  UploadOption $uploadOption) {
		if( empty($uploadOption) ) {
			$uploadOption = new UploadOption();     //如果用户没有传递UploadOption，则生成一个默认的
		}
		// UploadPolicy 和 UploadOption检查
		list ( $isValid, $message ) = $this->checkUploadInfo ( $uploadPolicy, $uploadOption );
		if (! $isValid) {
			return $this->_errorResponse ( "ErrorUploadInfo", "error upload policy or option:" . $message );
		}
		// 数据大小不等于设定的分片大小(默认2M)，则无法完成初始化
		$dataSize = strlen ( $blockData );			// 数据文件大小
 		if ($dataSize != ($uploadOption->blockSize)) {
 			return $this->_errorResponse ( "MultipartInitError", "UploadOption's blockSize is not equal to data's size" );
 		}
 		$uploadOption->setContent($blockData); // 设置待上传的文件块
 		$uploadOption->optionType = UpOptionType::BLOCK_INIT_UPLOAD;	//初始化分片时的Option类型
		$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_INIT;		//初始化分片上传的API
		$httpRes = $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
		//若成功返回，则保存初始化成功的uploadId、id 以及 partNumber、eTag
		$uploadId = isset ( $httpRes ['uploadId'] ) ? $httpRes ['uploadId'] : null;// 分片上传ID（OSS用于区分上传的id）
		$id = isset ( $httpRes ['id'] ) ? $httpRes ['id'] : null;// 上传唯一ID（多媒体服务用于区分上传的id）
		$uploadOption->setUploadId($uploadId);
		$uploadOption->setUniqueIdId($id);
		if (isset ( $httpRes ['partNumber'] ) && isset ( $httpRes ['eTag'] )) {
			$uploadOption->addPartNumberAndETag($httpRes ['partNumber'], $httpRes ['eTag']);
		}
		return $httpRes;
	}
	/**分片上传，指定待上传的文件。需要指定UploadOption中文件块编号
	 * @param string $filePath 文件路径
	 * @param UploadPolicy $uploadPolicy 上传策略
	 * @param UploadOption $uploadOption 上传选项
	 * @return array 初始化分片上传的结果
	 */
	public function multipartUpload($filePath, UploadPolicy $uploadPolicy,  UploadOption $uploadOption) {
		$encodePath=iconv('UTF-8','GB2312',$filePath); //中文需要转换成gb2312，file_exist等函数才能识别
		if (! file_exists ( $encodePath )) {
			return $this->_errorResponse ( "FileNotExist", "file not exist" );
		}
		$fileSize = filesize ( $encodePath );		// 文件大小
		$blockSize = $uploadOption->blockSize;	// 文件分片大小
		$blockNum = intval ( ceil ( $fileSize / $blockSize ) ); // 文件分片后的块数
		$currentSize = $blockSize; // 当前文件块的大小
		if ($uploadOption->getPartNumber() == $blockNum) {
			$currentSize = ($fileSize - ($blockNum - 1) * $blockSize); // 计算最后一个块的大小
		}
		$offset = ($uploadOption->getPartNumber() - 1) * $blockSize; // 当前文件块相对于文件开头的偏移量（块的起始位置）
		$blockData = file_get_contents ( $encodePath, 0, null, $offset, $currentSize );
		return $this->multipartUploadByData ( $blockData, $uploadPolicy, $uploadOption );
	}
	/**分片上传，指定待上传的数据。需要指定UploadOption中文件块编号
	 * @param string $filePath 文件路径
	 * @param UploadPolicy $uploadPolicy 上传策略
	 * @param UploadOption $uploadOption 上传选项
	 * @return array 分片上传的结果
	 */
	public function multipartUploadByData($blockData, UploadPolicy $uploadPolicy,  UploadOption $uploadOption) {
		$partNumber = $uploadOption->getPartNumber();//php 5.3的版本使用empty()传递函数返回值会报错。所以为了兼容，增加临时变量
		// 检查分片上传所需的参数是否设置正确
		if ( !$uploadOption->checkMutipartParas() || empty($partNumber) ) {
			return $this->_errorResponse ( "MultipartUploadError", "multipart upload's parameters(id,uploadId,partNumber) error");
		}
 		$uploadOption->setContent($blockData); // 设置待上传的文件块
 		$uploadOption->optionType = UpOptionType::BLOCK_RUN_UPLOAD;		//分片上传过程中的Option类型
		$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_UPLOAD;		//分片上传过程中的API
		$httpRes = $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
		if (isset ( $httpRes ['partNumber'] ) && isset ( $httpRes ['eTag'] )) {
			$uploadOption->addPartNumberAndETag($httpRes ['partNumber'], $httpRes ['eTag']);
		}
		return $httpRes;
	}
	/**完成分片上传任务。需要指定UploadOption中整个文件的md5值
	 * @param UploadPolicy $uploadPolicy 上传策略
	 * @param UploadOption $uploadOption 上传选项
	 * @return array 分片上传完成的结果
	 */
	public function multipartComplete(UploadPolicy $uploadPolicy,  UploadOption $uploadOption) {// 检查分片上传所需的参数是否设置正确
		$fileMd5 = $uploadOption->getMd5();//php 5.3的版本使用empty()传递函数返回值会报错。所以为了兼容，增加临时变量
		if ( !$uploadOption->checkMutipartParas() || empty($fileMd5) ) {
			return $this->_errorResponse ( "MultipartCompleteError", "multipart upload's parameters(id,uploadId,md5) error");
		}
		$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_COMPLETE;			//完成分片上传任务的API
		$uploadOption->optionType = UpOptionType::BLOCK_COMPLETE_UPLOAD;		//完成分片上传任务时的Option类型
		return $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
	}
	/**取消分片上传任务。需要保证UploadOption中有分片任务的uploadId和id
	 * @param UploadPolicy $uploadPolicy 上传策略
	 * @param UploadOption $uploadOption 上传选项
	 * @return array 分片上传完成的结果
	 */
	public function multipartCancel(UploadPolicy $uploadPolicy,  UploadOption $uploadOption) {
		if ( !$uploadOption->checkMutipartParas() ) {
			return $this->_errorResponse ( "MultipartCancelError", "multipart upload's parameters(id,uploadId) error");
		}
		$url = $this->upload_host . Conf::UPLOAD_API_BLOCK_CANCEL;		//取消分片任务的API
		$uploadOption->optionType = UpOptionType::BLOCK_CANCEL_UPLOAD;	//取消分片任务时的Option类型
		return $this->_send_request ( 'POST', $url, $uploadPolicy, $uploadOption );
	}
	/**调用curl利用http上传数据
	 * @param string $method
	 * @param string $url
	 * @param UploadPolicy $uploadPolicy
	 * @param UploadOption $uploadOption
	 * @return array (isSuccess, ...)
	 */
	protected function _send_request( $method, $url, UploadPolicy $uploadPolicy, UploadOption $uploadOption) {
		$success = false;
		$result = null;
		$ch = curl_init ( $url );
		try {
			//构建Http请求头和请求体
			$_headers = array ( 'Expect:' );
			$token = $this->_getUploadToken ( $uploadPolicy );
			array_push ( $_headers, "Authorization: {$token}" );
			array_push ( $_headers, "User-Agent: {$this->_getUserAgent()}" );
			$length = 0;
			if (! empty ( $uploadOption )) {
				list ( $contentType, $httpBody ) = $this->BuildMultipartForm ( $uploadOption );
				$length = @strlen ( $httpBody );
				array_push ( $_headers, "Content-Type: {$contentType}" );
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, $httpBody );			//请求体
			}
			array_push ( $_headers, "Content-Length: {$length}" );
			curl_setopt ( $ch, CURLOPT_HEADER, 1 );							//设置头部
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $_headers );				//请求头
			curl_setopt ( $ch, CURLOPT_TIMEOUT, $uploadOption->timeout );	//超时时长
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
	/**uploadPolicy和uploadOption 合法性检查
	 * @param UploadPolicy $uploadPolicy 上传策略
	 * @param UploadOption $uploadOption 上传选项
	 * @return array($isValid, $message)
	 */
	protected function checkUploadInfo(UploadPolicy $uploadPolicy, UploadOption $uploadOption) {
		$isValid = true;
		$message = null;
		// 1：判断是否设置空间名
		if (empty ( $uploadPolicy->bucket ) && empty ( $uploadPolicy->namespace )) {
			$isValid = false;
			$message = 'namespace or bucket is empty';
		} else if (empty ( $uploadPolicy->name )) {
			// 2：优先使用uploadPolicy中的name，如果为空，则使用uploadOption中的name
			if (empty ( $uploadOption->name )) {
				$isValid = false;
				$message = "file's name is empty"; // 如果uploadPolicy和uploadOption中的文件名name都为空，则返回错误信息
			}
		}
		if (true === $isValid) {
			// 3：优先使用uploadPolicy中的dir
			if (! empty ( $uploadPolicy->dir )) {
				if (strpos ( $uploadPolicy->dir, '/' ) !== 0) {
					$uploadPolicy->dir = '/' . $uploadPolicy->dir;//如果dir不为空，且其前面没有以"/"开头，则为其添加
				}
			}
			// 4：如果uploadPolicy中的dir为空，则使用uploadOption中的dir
			if (! empty ( $uploadOption->dir )) {
				if (strpos ( $uploadOption->dir, '/' ) !== 0) {
					$uploadOption->dir = '/' . $uploadOption->dir;//如果dir不为空，且其前面没有以"/"开头，则为其添加
				}
			}
			// 5：判断用户设置的文件分块大小，是否在指定的范围内。如果不在，则设置为默认Conf::BLOCK_DEFF_SIZE = 2M
			if ( ($uploadOption->blockSize > Conf::BLOCK_MAX_SIZE) || ($uploadOption->blockSize < Conf::BLOCK_MIN_SIZE) ) {
				$uploadOption->blockSize = Conf::BLOCK_DEFF_SIZE;
			}
		}
		return array ( $isValid, $message  );
	}
	/**构建Http请求的Body
	 * @param UploadOption $uploadOption
	 * @return array (contentType,httpBody)
	 */
	protected function BuildMultipartForm(UploadOption $uploadOption) {
		$bodyArray = array ();
		$mimeBoundary = md5 ( microtime () );
		$paraArray = $uploadOption->getParaArray();
		foreach ( $paraArray as $name => $val ) {
			if ($name != 'content') {
				array_push ( $bodyArray, '--' . $mimeBoundary );
				array_push ( $bodyArray, "Content-Disposition: form-data; name=\"$name\"" );
				array_push ( $bodyArray, 'Content-Type: text/plain; charset=UTF-8' );
				array_push ( $bodyArray, '' );
				array_push ( $bodyArray, $val );
			}
		}
		if (isset ( $paraArray ['content'] )) {
			array_push ( $bodyArray, '--' . $mimeBoundary );
			$fileName = empty($uploadOption->name) ? "temp" : $uploadOption->name;
			array_push ( $bodyArray, "Content-Disposition: form-data; name=\"content\"; filename=\"{$fileName}\"" );
			array_push ( $bodyArray, "Content-Type: application/octet-stream" );
			array_push ( $bodyArray, '' );
			array_push ( $bodyArray, $paraArray ['content'] );
		}
		
		array_push ( $bodyArray, '--' . $mimeBoundary . '--' );
		array_push ( $bodyArray, '' );
		
		$httpBody = implode ( "\r\n", $bodyArray );
		$contentType = 'multipart/form-data; boundary=' . $mimeBoundary;
		return array (
				$contentType,
				$httpBody 
		);
	}
	/**UserAgent用户代理 */
	protected function _getUserAgent() {
		if ($this->type == "TOP") {
			return "ALIMEDIASDK_PHP_TAE/" . Conf::SDK_VERSION;
		} else {
			return "ALIMEDIASDK_PHP_CLOUD/" . Conf::SDK_VERSION;
		}
	}
	/**生成上传凭证
	 * @param UploadPolicy $uploadPolicy
	 * @return string 上传时的凭证token
	 */
	protected function _getUploadToken(UploadPolicy $uploadPolicy) {
		$encodedPolicy = EncodeUtils::encodeWithURLSafeBase64 ( json_encode ( $uploadPolicy ) );
		$signed = hash_hmac ( 'sha1', $encodedPolicy, $this->sk );
		$tempStr = $this->ak . ":" . $encodedPolicy . ":" . $signed;
		$token = "UPLOAD_AK_" . $this->type . " " . EncodeUtils::encodeWithURLSafeBase64 ( $tempStr );
		return $token;
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
