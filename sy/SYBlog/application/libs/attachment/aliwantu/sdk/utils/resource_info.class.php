<?php
/**文件资源类*/
class ResourceInfo{
	/* 以下属性是资源ID所需的属性*/
	protected $namespace;                              //空间名，必须
	protected $dir;                                    //路径
	protected $name;                                   //文件名信息
	/* 以下属性是资源ID所需的属性*/
	/**
	 * ResourceOption的构造函数
	 */
	public function __construct($namespace, $dir=null, $name=null) {
		$this->namespace = $namespace;
		$this->dir = $dir;
		$this->name = $name;
	}
	/**检测资源ID的是否合法。$dirState表示是否检测dir，$nameState表示是否检测filename<p>
	 * 返回格式{$isValid, $message}*/
	public function checkResourceInfo( $dirState = false, $nameState = false ) {
		if (empty ( $this->namespace ))
			return array ( false, "namespace is empty.[{$this->toString()}]" ); // 判断是否设置空间名
		else
			return $this->checkFileInfo( $dirState, $nameState ); // 判断dir和name的合法性
	}
	/**检测文件信息是否合法。$dirState表示是否检测dir，$nameState表示是否检测filename<p>
	 * 返回格式{$isValid, $message}*/
	public function checkFileInfo( $dirState = false, $nameState = false ) {
		if ($nameState && empty ( $this->name )) {
			return array ( false, "file's name is empty.[{$this->toString()}]" ); // 1：若需要进行文件名name检测，则判断文件名是否为空
		}
		if ($dirState) {
			if (empty ( $this->dir ))
				$this->dir = '/'; // 2：判断路径是否为空，若为空，则默认为根目录'/'
			else if (strpos ( $this->dir, '/' ) !== 0)
				$this->dir = '/' . $this->dir; // 3：判断路径是否以'/'开头，若不是，则添加'/'
		}
		return array ( true, null );
	}
	/**资源ID(resourceId)的生成*/
	public function buildResourceId() {
		$jsonData = array ();
		if (! empty ( $this->namespace ))
			array_push ( $jsonData, urldecode($this->namespace) );
		if (! empty ( $this->dir ))
			array_push ( $jsonData, urldecode($this->dir) );
		if (! empty ( $this->name ))
			array_push ( $jsonData, urldecode($this->name) );
		return EncodeUtils::encodeWithURLSafeBase64 ( json_encode ( $jsonData, true ) );
	}
	/**对URL中文进行编码*/
	protected function urlencode_ch($str) {
		return preg_replace_callback ( '/[^\0-\127]+/', function ($match) {
			return urlencode ( $match [0] );
		}, $str );
	}
	public function toString() {
		return "namespace={$this->namespace}, dir={$this->dir}, name={$this->name}";
	}
	public function toArray() {
		return array("namespace"=>$this->namespace, "dir"=>$this->dir, "name"=>$this->name);
	}
	/*###################下面是属性的get和set方法###############*/
	/**设置路径*/
	public function setNamespace($namespace) {
		$this->namespace = $namespace;
		return $this;
	}
	/**设置路径*/
	public function setDir($dir) {
		$this->dir = $dir;
		return $this;
	}
	/**设置文件名*/
	public function setName($filename) {
		$this->name = $filename;
		return $this;
	}
}