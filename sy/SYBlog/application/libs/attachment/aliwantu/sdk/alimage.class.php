<?php
if (! defined ( 'ALI_IMAGE_SDK_PATH' )) {
	define ( 'ALI_IMAGE_SDK_PATH', dirname ( __FILE__ ) );
}
require_once (ALI_IMAGE_SDK_PATH . '/core/upload_client.class.php');
require_once (ALI_IMAGE_SDK_PATH . '/core/manage_client.class.php');
class AlibabaImage {
	private $upload_client;
	private $manage_client;
	private $ak;
	private $sk;
	private $type; // "TOP"和"CLOUD"两种模式
	
	/**
	 * 构造函数
	 * 
	 * @param string $ak
	 *        	云存储公钥
	 * @param string $sk
	 *        	云存储私钥
	 * @param string $type
	 *        	可选，兼容TOP与tea云的 ak/sk
	 * @throws Exception
	 */
	public function __construct($ak, $sk, $type = Conf::TYPE_TOP ) {
		$this->ak = $ak;
		$this->sk = $sk;
		$this->type = $type;
		$this->upload_client = new UploadClient( $ak, $sk, $type );
		$this->manage_client = new ManageCLient ( $ak, $sk, $type );
	}
	
	/**
	 * 直接上传文件,适合文件比较小的情况
	 */
	public function upload($filePath, UploadPolicy $uploadPolicy, UploadOption $uploadOption = null) {
		return $this->upload_client->upload ( $filePath, $uploadPolicy, $uploadOption );
	}
	
	/**
	 * 直接上传文件数据,适合数据量比较小的情况
	 */
	public function uploadData($data, UploadPolicy $uploadPolicy, UploadOption $uploadOption = null ) {
		return $this->upload_client->uploadData ( $data, $uploadPolicy, $uploadOption );
	}
	/*######################################华丽的分界线#######################################*/
	/*####################上面是上传文件的接口(自动分片)，下面是分片上传接口#####################*/
	/*#########################################################################################*/
	/**
	 * 创建分片上传任务，指定待上传的文件。即初始化分片上传
	 */
	public function multipartInit($filePath, UploadPolicy $uploadPolicy, UploadOption $uploadOption) {
		return $this->upload_client->multipartInit ( $filePath, $uploadPolicy, $uploadOption );
	}
	
	/**
	 * 创建分片上传任务，指定初始化分片任务的数据，即第一块数据
	 */
	public function multipartInitByData($data, UploadPolicy $uploadPolicy, UploadOption $uploadOption) {
		return $this->upload_client->multipartInitByData ( $data, $uploadPolicy, $uploadOption );
	}
	
	/**
	 * 分片上传，指定待上传的文件。需要指定UploadOption中文件块编号
	 */
	public function multipartUpload($filePath, UploadPolicy $uploadPolicy, UploadOption $uploadOption) {
		return $this->upload_client->multipartUpload ( $filePath, $uploadPolicy, $uploadOption);
	}
	
	/**
	 * 分片上传，指定待上传的数据。需要指定UploadOption中文件块编号
	 */
	public function multipartUploadByData($blockData, UploadPolicy $uploadPolicy, UploadOption $uploadOption) {
		return $this->upload_client->multipartUploadByData ( $blockData, $uploadPolicy, $uploadOption );
	}
	
	/**
	 * 完成分片上传任务。需要指定UploadOption中整个文件的md5值
	 */
	public function multipartComplete(UploadPolicy $uploadPolicy,  UploadOption $uploadOption) {
		return $this->upload_client->multipartComplete ( $uploadPolicy, $uploadOption );
	}
	
	/**
	 * 取消分片上传任务。需要保证UploadOption中有分片任务的uploadId和id
	 */
	public function multipartCancel(UploadPolicy $uploadPolicy,  UploadOption $uploadOption) {
		return $this->upload_client->multipartCancel ( $uploadPolicy, $uploadOption );
	}
	/*######################################华丽的分界线#######################################*/
	/*########################上面是分片上传的接口，下面是文件管理接口##########################*/
	/*########################################################################################*/
	/**
	 * 查看文件是否存在
	 */
	public function existsFile($namespace, $dir, $filename) {
		return $this->manage_client->existsFile ( $namespace, $dir, $filename );
	}
	
	/**
	 * 获取文件的元信息(meta信息) 
	 */
	public function getFileInfo($namespace, $dir, $filename) {
		return $this->manage_client->getFileInfo ( $namespace, $dir, $filename );
	}
	
	/**
	 * 重命名文件
	 */
	public function renameFile($namespace, $dir, $filename, $newDir, $newName) {
		return $this->manage_client->renameFile ( $namespace, $dir, $filename, $newDir, $newName );
	}
	
	/**
	 * 获取指定目录下的文件列表。dir为空，表示根目录。page指定页数，pageSize指定每页显示数量
	 */
	public function listFiles($namespace, $dir, $page = 1, $pageSize = 100) {
		return $this->manage_client->listFiles ( $namespace, $dir, $page, $pageSize );
	}
	
	/**
	 * 删除文件
	 */
	public function deleteFile($namespace, $dir, $filename) {
		return $this->manage_client->deleteFile ( $namespace, $dir, $filename );
	}
	
	/**
	 * 查看文件夹是否存在
	 */
	public function existsFolder($namespace, $dir) {
		return $this->manage_client->existsFolder ( $namespace, $dir );
	}
	
	/**
	 * 创建文件夹
	 */
	public function createDir($namespace, $dir) {
		return $this->manage_client->createDir ( $namespace, $dir );
	}
	
	/**
	 * 获取子文件夹列表。dir为空，表示根目录。page指定页数，pageSize指定每页显示数量
	 */
	public function listDirs($namespace, $dir, $page = 1, $pageSize = 100) {
		return $this->manage_client->listDirs ( $namespace, $dir, $page, $pageSize );
	}
	
	/**
	 * 删除文件夹
	 */
	public function deleteDir($namespace, $dir) {
		return $this->manage_client->deleteDir ( $namespace, $dir );
	}
	/*######################################华丽的分界线#######################################*/
	/*#######################上面是文件或文件夹的管理，下面是特色服务接口########################*/
	/*########################################################################################*/
	/**
	 * 黄图扫描接口
	 */
	public function scanPorn( ManageOption $resInfos) {
		return $this->manage_client->scanPorn ( $resInfos );
	}
	/**
	 * 鉴黄反馈feedback接口
	 */
	public function pornFeedback( ManageOption $pornFbInfos) {
		return $this->manage_client->pornFeedback ( $pornFbInfos );
	}
	/**
	 * 多媒体转码接口
	 */
	public function mediaEncode( MediaEncodeOption $encodeOption ){
		return $this->manage_client->mediaEncode( $encodeOption );
	}
	/**
	 * 多媒体转码任务查询接口
	 */
	public function mediaEncodeQuery( $taskId ) {
		return $this->manage_client->mediaEncodeQuery($taskId);
	}
	/**
	 * 视频截图接口
	 */
	public function videoSnapshot( SnapShotOption $snapshotOption ) {
		return $this->manage_client->videoSnapshot( $snapshotOption );
	}
	/**
	 * 视频截图结果查询接口
	 */
	public function vSnapshotQuery( $taskId ) {
		return $this->manage_client->vSnapshotQuery($taskId);
	}
	/**
	 * 广告图扫描接口(beta)
	 */
	public function scanAdvertising(  ManageOption $resInfos ) {
		return $this->manage_client->scanAdvertising($resInfos);
	}
}
