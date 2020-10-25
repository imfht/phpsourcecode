<?php
require_once ('resource_info.class.php');
/**管理时的选项*/
final class ManageOption extends ResourceInfo{
	/* 以下属性是”获取资源列表"方法所需的属性。即listFiles()、listDirs()方法中的参数 */
	private $currentPage;                            //当前页号
	private $pageSize;                               //每页的大小
	/* 以下属性是"扫描黄图和广告图"方法所需的属性。即scanPorn()和scanAdvertising方法中的参数 */
	private $filesArray;
	private $urlsArray;
	/* 以下属性是"鉴黄反馈"方法所需的属性。即pornFeedback()方法中的参数 */
	private $pornFbArray;
	/**
	 * UploadPolicy的构造函数
	 */
	public function __construct($namespace=NULL) {
		$this->namespace = $namespace;
		$this->currentPage = 1;
		$this->pageSize = 10;
	}
	/**生成文件列表listFiles()方法所需的参数*/
	public function buildListFilesParas() {
		return "namespace=" . $this->namespace . "&dir=" . urlencode($this->dir) .
				"&currentPage=" . $this->currentPage . "&pageSize=" . $this->pageSize;
	}
	/**校验待检测资源信息是否合法。如果合法，则返回http请求体<p> 返回格式{$isValid, $message, $bodyArray}*/
	public function checkFilesAndUrls(){
		if ( empty($this->filesArray) && empty($this->urlsArray) ) {
			return array ( false, "resource's info is empty" ); //待检测资源不能均为空，反馈错误
		}else if ( !empty($this->filesArray) && !empty($this->urlsArray) ) {
			return array ( false, "resource and url could not exist all at once" ); //均存在时，反馈错误
		}
		$bodyArray = array();
		$resourceStr = '';
		if( !empty($this->filesArray) ) {
			//检测文件是否合法。若合法则返回http请求体所需参数
			foreach ($this->filesArray as $resourceInfo) {
				list ( $valid, $msg ) = $resourceInfo->checkResourceInfo(true,true);//检测文件信息是否合法
				if(!$valid)
					return array ( $valid, $msg );
				$resourceStr .= ( empty($resourceStr) ? null : ';');
				$resourceStr.= $resourceInfo->buildResourceId();
			}
			$bodyArray['resourceId'] = $resourceStr;
		} else if ( !empty($this->urlsArray) ) {
			//检测URL是否合法。若合法则返回http请求体所需参数
			foreach ($this->urlsArray as $url) {
				if (empty($url))
					return array ( false, "url's info is invalid" );
				$resourceStr .= ( empty($resourceStr) ? null : ';');
				$resourceStr .= $this->urlencode_ch($url);
			}
			$bodyArray['url'] = $resourceStr;
		}
		return array ( true, 'valid', $bodyArray );
	}
	/**生成扫描黄图scanPorn()方法所需的参数*/
	public function buildScanPornParas() {
		$bodyArray = array();
		if (! empty ( $this->filesArray )) {
			$resourceStr = '';
			foreach ($this->filesArray as $resourceInfo) {
				$resourceStr .= ( empty($resourceStr) ? null : ';');
				$resourceStr.= $resourceInfo->buildResourceId();
			}
			$bodyArray['resourceId'] = $resourceStr;
		} else if (! empty ( $this->urlsArray )) {
			$urlStr = '';
			foreach ($this->urlsArray as $url) {
				$urlStr .= ( empty($urlStr) ? null : ';');
				$urlStr .= urlencode($url);
			}
			$bodyArray['url'] = $urlStr;
		}
		return $bodyArray;
	}
	/**生成扫描广告图scanAdvertising()方法所需的参数*/
	public function buildScanAdvertisingParas() {
		return $this->buildScanPornParas();
	}
	/**校验鉴黄反馈信息是否合法。如果合法，则返回http请求体<p> 返回格式{$isValid, $message, $httpBody}*/
	public function checkPornFeedbackInfos(){
		$httpBody = '';
		if (empty($this->pornFbArray))
			return array ( false, "porn feedback info is empty", null ); //鉴黄反馈信息为空，反馈错误
		foreach ($this->pornFbArray as $pornFbInfo) {
			$resourceInfo = $pornFbInfo['file']; //图片信息
			list ( $valid, $msg ) = $resourceInfo->checkResourceInfo(true,true);//检测图片信息是否合法
			if(!$valid)
				return array ( $valid, $msg, null );
			$type = $pornFbInfo['type'];
			$wrong = $pornFbInfo['wrong'];
			if ( !isset($type) || ($type!=0 && $type!=1) || !is_bool($wrong)) {
				return array ( false, "parameters 'type' or 'wrong' is invalid", null ); //参数type取值0、1， wrong是bool类型
			}
			$score = $pornFbInfo['score'];
			if ( $wrong && (!isset($score) || $score<0 || $score>1) ) {
				return array ( false, "parameters 'score' is invalid", null ); //当"wrong"=true时，参数score必须有，且取值范围为[0,1]
			}
			$pornFbInfo['file'] = $resourceInfo->toArray(); //将图片信息对象，转换为数组，并保存
			$httpBody .= ( empty($httpBody) ? null : '&'); //添加分隔符
			$httpBody .= "feedback=".urlencode(json_encode($pornFbInfo,true));//urlencode编码
		}
		return array ( true, "valid", $httpBody ); //鉴黄反馈信息合法，并返回http请求体
	}
	/*###################下面是属性的get和set方法###############*/
	/**设置当前页号*/
	public function setCurrentPage($currentPage) {
		$this->currentPage = $currentPage;
		return $this;
	}
	/**设置每页的大小 */
	public function setPageSize($pageSize) {
		$this->pageSize = $pageSize;
		return $this;
	}
	/**添加待扫描文件信息。*/
	public function addResource($namespace=null, $dir=null, $name=null) {
		if (!isset($this->filesArray))
			$this->filesArray = array();
		array_push($this->filesArray, new ResourceInfo($namespace,$dir,$name) );
	}
	/**添加待扫描URL信息。*/
	public function addUrl($url) {
		if (!isset($this->urlsArray))
			$this->urlsArray = array();
		array_push($this->urlsArray, $url);
	}
	/**添加鉴黄反馈信息。
	 * @param string $namespace 空间名[必须]。
	 * @param string $dir 路径。为空则默认根目录
	 * @param string $name 文件名。不能为空
	 * @param bool $type 黄图类型[必须]。0或者1，0是非黄图，1是黄图
	 * @param bool $wrong 鉴黄判断[必须]。true代表用户认为多媒体鉴黄服务的结果有问题。当为true的时候必须传score
	 * @param decimal $score 黄图分值。[可选]取值范围[0-1]，值越高则是黄图可能性越高
	 */
	public function addPornFbInfo($namespace=null, $dir=null, $name=null, $type=null, $wrong=null, $score=null) {
		if (!isset($this->pornFbArray))
			$this->pornFbArray = array();
		$pornFbInfo = array( "file"=>new ResourceInfo($namespace,$dir,$name) ,
				"type"=>$type, "wrong"=>$wrong );
		if ( $wrong )
			$pornFbInfo['score'] = $score; //当$wrong为true的时候必须传score
		array_push($this->pornFbArray, $pornFbInfo);
	}
	
}
