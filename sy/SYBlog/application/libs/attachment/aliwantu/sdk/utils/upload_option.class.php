<?php
class UploadOption{
	/*optionType用于标识UploadOption的类型，即：普通上传、初始化分片上传、分片上传、分片上传完成*/
	public $optionType;
	
	/*以下属性是上传时的可选参数。即Rest API中Http请求Body中所需的可选参数*/
	public $dir;                                    // 顽兔空间的图片路径(如果UploadPolicy中不指定dir属性，则生效)
	public $name;                                   // 上传到服务端的文件名(如果UploadPolicy中不指定name属性，则生效)
	public $metaArray;                              // 用户自定义的文件meta信息("meta-"为参数前缀, "*"为用户用于渲染的自定义Meta信息名)
	public $varArray;                               // 用户自定义的魔法变量("var-"为参数前缀, "*"为用户用于渲染的自定义魔法变量名)
	private $md5;                                   // 文件md5值(推荐提供此参数进行一致性检查)
	private $size;                                  // 文件大小
	private $content;                               // 文件内容(在http请求体Body中必须位于参数的最后一位)
	
	/*以下属性是用户根据自己应用需求，可选的配置*/
	public $blockSize;                              // 文件分片的大小。针对分片上传。
	public $timeout;                                // 进行http连接的超时时间
	
	/*以下属性是用于分片上传时的参数，仅用于分片上传。用户在调用分片上传时可以选择配置。*/
	private $uploadId;                              // OSS分片上传ID（OSS用于区分上传的id）
	private $uniqueId;                              // 服务上传唯一ID（多媒体服务用于区分上传的id）
	private $partNumber;                            // 分片文件块上传成功后返回的文件块编号
	private $eTag;                                  // 分片文件块上传成功后返回的Tag标签(由md5和其他标记组成)
	private $array_PartNum_ETag;                    // 分片上传服务端返回的所有 块编号partNumber 和 标记ETag
	
	public function __construct() {
		$this->optionType = UpOptionType::COMMON_UPLOAD_TYPE;//默认普通上传类型
		$this->metaArray = array();
		$this->varArray = array();
		$this->blockSize = Conf::BLOCK_DEFF_SIZE;   //默认2M
		$this->timeout = Conf::HTTP_TIMEOUT;        //默认超时30s
		$this->array_PartNum_ETag = array();
	}
	/**得到上传时http请求体所需的参数*/
	public function getParaArray() {
		switch ( $this->optionType ) {
			case UpOptionType::COMMON_UPLOAD_TYPE :
			case UpOptionType::BLOCK_INIT_UPLOAD :
				return $this->getParas_Common_BlockInit();
			case UpOptionType::BLOCK_RUN_UPLOAD :
				return $this->getParas_BlockRun();
			case UpOptionType::BLOCK_COMPLETE_UPLOAD :
				return $this->getParas_BlockComplete();
			case UpOptionType::BLOCK_CANCEL_UPLOAD :
				return $this->getParas_BlockCancel();
			default:return null;
		}
	}
	/** 构造 普通上传 或者 初始化分片上传 所需的参数 */
	private function getParas_Common_BlockInit() {
		$paraArray = array ();
		if (isset ( $this->dir ))
			$paraArray ['dir'] = $this->dir;
		if (isset ( $this->name ))
			$paraArray ['name'] = $this->name;
		$paraArray ['md5'] = md5 ( $this->content ); // 计算文件md5
		$paraArray ['size'] = strlen ( $this->content ); // 计算文件大小
		$paraArray ['content'] = $this->content;
		$this->createMetaVars ( $paraArray, "meta", $this->metaArray );
		$this->createMetaVars ( $paraArray, "var", $this->varArray );
		return $paraArray;
	}
	/** 构造 分片上传过程中 所需的参数 */
	private function getParas_BlockRun() {
		$paraArray = array ();
		$paraArray ['uploadId'] = $this->uploadId;
		$paraArray ['id'] = $this->uniqueId;
		$paraArray ['partNumber'] = $this->partNumber;
		$paraArray ['md5'] = md5 ( $this->content ); // 计算文件md5
		$paraArray ['size'] = strlen ( $this->content ); // 计算文件大小
		$paraArray ['content'] = $this->content;
		return $paraArray;
	}
	/** 构造 分片上传完成时 所需的参数 */
	private function getParas_BlockComplete() {
		$paraArray = array ();
		$paraArray ['uploadId'] = $this->uploadId;
		$paraArray ['id'] = $this->uniqueId;
		$paraArray ['md5'] = $this->md5;
		$parts = EncodeUtils::encodeWithURLSafeBase64 ( json_encode ( $this->array_PartNum_ETag ) );
		$paraArray ['parts'] = $parts; // 所有文件块的编号partNumber 和 标记ETag，需要进行base64的编码
		return $paraArray;
	}
	/** 构造 分片上传取消 所需的参数 */
	private function getParas_BlockCancel() {
		return array ('id'=>$this->uniqueId, 'uploadId'=>$this->uploadId);
	}
	/**
	 * 构建上传http请求体的meta-*和var-*选项参数。将tempArr中的元素加上前缀prefix，然后保存到paraArr中
	 * @param array $paraArr 最终的数组
	 * @param string $prefix 前缀
	 * @param array $tempArr 待添加的数组
	 * @return array $paraArr 最终的数组
	 */
	private function createMetaVars($paraArr, $prefix, $tempArr) {
		foreach ( $tempArr as $key => $val ) {
			$key = $prefix . '-' . $key;
			$paraArr [$key] = $val;
		}
		return $paraArr;
	}
	/**设置待上传的数据。该方法开发者不需要调用，该方法根据用户数据自动调用
	 * @param string $data 字符串 */
	public function setContent($data) {
		$this->content = $data;
	}
	/**设置MD5值。该方法主要用于在分片上传完成时调用
	 * @param string $value md5值 */
	public function setMd5($value) {
		$this->md5 = $value;
	}
	/**得到MD5值。该方法主要用于在分片上传完成时调用*/
	public function getMd5() {
		return $this->md5;
	}
	/*下面四个函数均是用于分片上传时的设置，开发者不需要调用*/
	/**分片上传时用于设置uploadId */
	public function setUploadId($uploadId) {
		$this->uploadId = $uploadId;
	}
	/**分片上传时用于设置id */
	public function setUniqueIdId($id) {
		$this->uniqueId = $id;
	}
	/**分片上传时，用于获取分片上传时的块编号partNumber */
	public function getPartNumber() {
		return $this->partNumber;
	}
	/**分片上传时，用于设置分片上传时的块编号partNumber */
	public function setPartNumber($partNumber) {
		$this->partNumber = $partNumber;
	}
	/**分片上传过程中，用于保存所有的 块编号partNumber 和 标记ETag*/
	public function addPartNumberAndETag($partNumber, $eTag) {
		$this->eTag = $eTag;
		$tempArray = array("partNumber"=>$partNumber, "eTag"=>$eTag);
		array_push($this->array_PartNum_ETag, array("partNumber"=>$partNumber, "eTag"=>$eTag) );
	}
	/**检测分片上传的参数。即uploadId、uniqueId是否有值*/
	public function checkMutipartParas(){
		return isset($this->uploadId) && isset($this->uniqueId);
	}
}
/**
 * 用于标识UploadOption对象的类型
 * @author yisheng.xp
 */
class UpOptionType {
	//下面的常量用于标识UploadOption对象适用的类型
	const COMMON_UPLOAD_TYPE = 0;		//普通上传时的UploadOption类型
	const BLOCK_INIT_UPLOAD = 1;		//分片初始化时的UploadOption类型
	const BLOCK_RUN_UPLOAD = 2;			//分片上传过程中的UploadOption类型
	const BLOCK_COMPLETE_UPLOAD = 3;	//分片上传完成时的UploadOption类型
	const BLOCK_CANCEL_UPLOAD = 4;		//分片上传取消时的UploadOption类型
}