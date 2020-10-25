<?php
/** 
 * File 文件模块扩展，除了继承自 Mod 类的用于操作数据库记录的方法，还包含其他的独有的方法。
 * file::upload() 用来上传文件，并支持多文件上传以及使用 Data URI Scheme 上传。
 * file::delete() 则用来删除上传的文件。
 * 除了这些，File 类还提供操作本地文件的其他方法，它们不能被浏览器等客户端直接访问。
 */
final class file extends mod{
	const TABLE = 'file';
	const PRIMKEY = 'file_id';
	private static $file = array(); //文件内容
	private static $filename = ''; //文件名
	private static $info = array(); //文件信息

	/** checkFileType() 检查文件类型 */
	private static function checkFileType(&$file = array()){
		$fileType = explode('|', strtolower(config('file.upload.acceptTypes'))); //获取配置
		$name = !empty($file['file_name']) ? $file['file_name'] : $file['name'];
		if(!in_array(extname($name), $fileType)){
			$file['error'] = lang('file.invalidType'); //不可用的类型作为错误处理并反馈给客户端
		}
	}

	/** checkFileSize() 检查文件大小 */
	private static function checkFileSize(&$file = array()){
		if($file["size"] == 0 || $file["size"] > config('file.upload.maxSize')*1024) {
			$file['error'] = lang('file.sizeTooLarge'); //体积超出限制作为错误处理并反馈给客户端
		}
	}

	/** uploadChecker() 上传前检查 */
	private static function uploadChecker(&$input = array()){
		if(config('mod.installed')){
			self::permissionChecker($input, 'add'); //检查添加记录的权限
			if(error()) return error();
		}
		self::checkFileType($input);
		self::checkFileSize($input);
	}

	/** saveUpload() 保存上传的文件 */
	private static function saveUpload($file = array(), &$encoding = false){
		$dataURIScheme = isset($file['tmp_data']); //是否为 Data URI Scheme 数据
		$uploadPath = config('file.upload.savePath');
		$keepName = config('file.upload.keepName'); //是否保留原始文件名
		$append = !empty($file['file_src']); //是否为追加数据
		if($append){
			$savepath = $file['file_src'];
		}else{
			$dir = $uploadPath.date('Y-m-d').'/'; //文件保存目录(按日期)
			if(!is_dir(__ROOT__.$dir)) mkdir(__ROOT__.$dir); //文件夹不存在则创建
			//获取文件 MD5 名称
			$md5Name = $dataURIScheme ? md5($file['tmp_data']) : md5_file($file['tmp_name']);
			if($keepName){
				if($dataURIScheme){
					if(empty($file['file_name'])){
						$mime = load_config_file('mime.ini');
						$ext = array_search($file['type'], $mime);
						$saveName = $md5Name.$ext;
					}else{
						$ext = '.'.extname($file['file_name']);
						$saveName = $file['file_name'];
					}
				}else{
					$ext = '.'.extname($file['name']);
					$saveName = $file['name'];
				}
			}else{
				if($dataURIScheme){
					$mime = load_config_file('mime.ini');
					$ext = array_search($file['type'], $mime);
					$saveName = $md5Name.$ext;
				}else{
					$ext = '.'.extname($file['name']);
					$saveName = $md5Name.$ext;
				}
			}
			$savepath = $dir.$saveName; //保存路径为 目录 + 文件 名称
		}
		$encoding = $keepName ? get_cmd_encoding() : false;
		if($encoding) $savepath = iconv('UTF-8', $encoding, $savepath);
		$path = __ROOT__.$savepath;
		if(!$append && $keepName && file_exists($path) && md5_file($path) != $md5Name){
			$savepath = substr($savepath, 0, -strlen($ext)).'_'.$md5Name.$ext; //获取唯一文件名
			$path = __ROOT__.$savepath;
		}
		$path = str_replace('\\', '/', realpath($path));
		if(!file_exists($path) || $append){
			if($append && !path_starts_with($path, __ROOT__.$uploadPath))
				error(lang('mod.permissionDenied')); //仅允许在上传目录中的文件后追加数据
			if(config('mod.installed') && !error()) do_hooks('file.save', $file); //执行挂钩函数
			if(error()) return false; //如果遇到错误，则不再继续
			if($append){ //追加数据
				if($dataURIScheme)
					$result = file_put_contents($path, $file['tmp_data'], FILE_APPEND);
				else
					$result = file_put_contents($path, file_get_contents($file['tmp_name']), FILE_APPEND);
			}else{
				if($dataURIScheme)
					$result = file_put_contents($path, $file['tmp_data']); //保存 Data URI scheme 文件
				else
					$result = move_uploaded_file($file['tmp_name'], $path); //保存常规文件
			}
			if($result === false) return false;
		}
		return $savepath;
	}

	/** moreImage() 复制更多尺寸图像或删除更多尺寸图像 */
	private static function moreImage($src, $action){
		if(class_exists('image') && is_img($src)){
			$ext = '.'.extname($src);
			$basename = substr($src, 0, -strlen($ext));
			$pxes = str_replace(' ', '', config('file.upload.imageSizes')); //获取配置尺寸
			if($pxes){
				foreach (explode('|', $pxes) as $px) { //为每个尺寸创建/删除副本，副本命名如 src_64.png
					if($action == 'copy'){ //创建
						image::open($src)->resize((int)$px)->save($basename.'_'.$px.$ext);
					}elseif($action == 'delete'){ //删除
						if(file_exists($file = $basename.'_'.$px.$ext))
							unlink($file);
					}
				}
			}
		}
		return new self;
	}

	/**
	 * upload() 上传文件
	 * @static
	 * @param  array  $arg [可选]请求参数，可以包含所有的数据表字段，支持使用
	 *                     Data URI scheme 来传送使用 base64 编码的文件，但需要将
	 *                     其保存在 [file] 参数中，可以设置为数组同时传送多个文件
	 * @return array       刚上传的文件或者错误信息(错误信息为包含原始文件信息的数组)
	 */
	static function upload(array $arg = array()){
		$fname = !empty($arg['file_name']) ? $arg['file_name'] : ''; //上传时设置文件名
		if(isset($arg['file']) && (is_string($arg['file']) && stripos($arg['file'], 'data:') === 0 || is_array($arg['file']))){
			//处理 Data URI scheme 数据
			if(is_string($arg['file'])) $arg['file'] = array($arg['file']);
			foreach($arg['file'] as $file){ //处理多文件
				$start = stripos($file, 'data:') === 0 ? 5 : 0;
				$i = strpos($file, ',');
				$head = substr($file, 0, $i); //文件头
				$body = substr($file, $i+1); //文件主体
				if($j = strpos($head, ';')){ //经过编码处理的文件
					$type = substr($head, $start, $j); //文件类型(mimetype)
					$data = substr($head, $j+1) == 'base64' ? @base64_decode($body) : $body; //文件数据
				}else{ //未经编码的文件
					$type = substr($head, $start) ?: 'text/plain';
					$data = $body;
				}
				$_FILES['file'][] = array( //将文件保存在超全局变量 $_FILES 中
					'name' => $fname, //文件名
					'type' => $type, //mime 类型
					'error' => '', //错误信息
					'tmp_name' => '', //缓存文件名
					'tmp_data' => $data, //缓存数据
					'size' => strlen($data) //文件大小
					);
			}
		}else{
			$_FILES = get_uploaded_files(); //获得普通方式上传的文件
		}
		if(!$_FILES) return error(lang('mod.missingArguments'));
		$installed = config('mod.installed');
		$src = !empty($arg['file_src']) ? $arg['file_src'] : ''; //分片上传时的源文件地址
		// 获取相对路径
		if($src && path_starts_with($src, site_url()))
			$src = substr($src, strlen(site_url()));
		if($src && path_starts_with($src, __ROOT__))
			$src = substr($src, strlen(__ROOT__));
		$data = array();
		foreach ($_FILES as $files) { //遍历 $_FILES 并执行文件保存操作
			if(is_assoc($files)) $files = array($files);
			foreach ($files as &$file) {
				if($fname) $file['file_name'] = $fname;
				if($src) $file['file_src'] = $src;
				self::uploadChecker($file);
				if(error()) return error(); //遇到错误则停止上传操作
				if(!$file['error']){
					if($savepath = self::saveUpload($file, $encoding)){
						self::moreImage($savepath, 'copy'); //复制更多图片
						if(empty($arg['file_name']))
							$arg['file_name'] = $file['name'];
						if($encoding) $savepath = iconv($encoding, 'UTF-8', $savepath);
						$arg['file_src'] = $savepath;
						if($installed && !$src){
							$result = self::get(array('file_src'=>$arg['file_src'])); //检查文件记录是否已存在
							if(!$result['success']){
								error(null);
								do_hooks('file.add', $arg); //执行挂钩函数
								self::handler($arg, 'add');
								if(error()) return error();
								database::open(0)->insert('file', $arg, $id); //将文件信息存入数据库
								$result = self::get(array('file_id'=>$id));
							}
							do_hooks('file.add.complete', $result['data']); //执行上传文件后的回调函数
							$data[] = array_merge($arg, $result['data']);
						}else{
							$data[] = array_merge($arg, $file);
						}
					}else{
						$error = error();
						$file['error'] = $error ? $error['data'] : lang('file.uploadFailed');
					}
				}
				if($file['error']) $data[] = $file;
			}
		}
		foreach ($data as $datum) {
			if(($installed && (isset($datum['file_id']) || ($src && empty($datum['error'])))) || (!$installed && empty($datum['error'])))
				return success($data); //只要有一个文件上传成功则返回成功
		}
		return error($data);
	}

	/** add() file::upload() 方法的别名 */
	static function add(array $arg = array()){
		return self::upload($arg);
	}

	/**
	 * delete() 删除文件
	 * @static
	 * @param  array  $arg [可选]请求参数，可以包含所有的数据表字段，但应该提供下面这些字段中的一个：
	 *                     [file_id] => 文件 ID，
	 *                     [file_src] => 文件保存地址(系统未安装时必须提供)
	 * @return array       操作结果
	 */
	static function delete($arg = array()){
		if(is_string($arg) && !is_numeric($arg))
			$arg = array('file_src' => $arg);
		$installed = config('mod.installed');
		$keepName = config('file.upload.keepName');
		$encoding = $keepName ? get_cmd_encoding() : false;
		if(empty($arg['file_id']) && empty($arg['file_src']) || (!$installed && empty($arg['file_src'])))
			return error(lang('mod.missingArguments'));
		$_arg = array();
		if(!empty($arg['file_id'])) $_arg['file_id'] = $arg['file_id'];
		if(!empty($arg['file_src'])){
			if(path_starts_with($arg['file_src'], __ROOT__)) //获取相对路径
				$arg['file_src'] = substr($arg['file_src'], strlen(__ROOT__));
			$_arg['file_src'] = $arg['file_src'];
			$src = str_replace('\\', '/', realpath(__ROOT__.$arg['file_src'])); //获取绝对路径
			if(!path_starts_with($src, __ROOT__.config('file.upload.savePath')))
				return error(lang('mod.permissionDenied')); //只允许删除上传的文件
			if($encoding) $src = iconv('UTF-8', $encoding, $src);
		}
		if(($installed && get_file($_arg)) || (!$installed && file_exists($src))){ //判断文件记录是否存在
			if($installed){
				$arg['file_id'] = file_id();
				$result = parent::delete($arg); //删除数据库记录
				if(error()) return error();
				$src = file_src();
				if(path_starts_with($src, site_url()))
					$src = __ROOT__.substr($src, strlen(site_url())); //将绝对 URL 地址转换为绝对磁盘地址
				if($encoding) $src = iconv('UTF-8', $encoding, $src);
			}
			if($installed)
				$deleted = $result['success'] ? @unlink($src) : false; //删除文件
			else
				$deleted = @unlink($src);
			if($deleted){ //删除更多副本
				self::moreImage($src, 'delete'); //删除图片副本
				$dir = dirname($src);
				if(is_empty_dir($dir)) rmdir($dir); //移除空目录
			}
			if($installed){
				return $result;
			}else{
				return $deleted ? success(lang('mod.deleted', lang('file.label'))) : error(lang('mod.deleteFailed', lang('file.label')));
			}
		}
		return error(lang('mod.notExists', lang('file.label')));
	}

	/**
	 * open() 打开一个文件，不存在则创建
	 * @static
	 * @param  string $filename 文件名
	 * @return object           当前对象
	 */
	static function open($filename){
		self::$filename = $filename;
		if(file_exists($filename)){
			$file = file($filename);
			self::$file = array(); //清空原内容(如果有)
			foreach ($file as $v) {
				self::$file[] = rtrim($v, "\r\n"); //将文件内容按行保存到内存中
			}
			$info = stat($filename);
			foreach ($info as $k => $v) { //保存文件属性
				if($k == 'ino'){
					$k = 'inode';
				}elseif($k == 'blksize'){
					$k = 'blocksize';
				}
				if(!is_int($k)) self::$info[$k] = $v;
			}
		}
		return new self;
	}

	/**
	 * prepend() 在文件开头前插入新行内容
	 * @static
	 * @param  string $str 文本内容
	 * @return object      当前对象
	 */
	static function prepend($str){
		array_unshift(self::$file, $str);
		return self::resetTime();
	}

	/**
	 * append() 在文件末尾插入新行内容
	 * @static
	 * @param  string $str 文本内容
	 * @return object      当前对象
	 */
	static function append($str){
		array_push(self::$file, $str);
		return self::resetTime();
	}

	/**
	 * write() 写入文件内容
	 * @static
	 * @param  string $str     文本内容
	 * @param  bool   $rewrite [可选]覆盖重写，默认 false
	 * @return object          当前对象
	 */
	static function write($str, $rewrite = false){
		if(!$rewrite) return self::append($str)->resetTime(); //在文件末端添加新行插入
		self::$file = explode("\n", $str); //覆盖重写
		return self::resetTime();
	}

	/**
	 * insert() 在文件中插入文本
	 * @static
	 * @param  string  $str    文本内容
	 * @param  integer $line   [可选]在指定行前插入，如果小于 0，则从后往前计算行数，如 -1 (默认)代表倒数第一行
	 * @param  integer $column [可选]在指定列插入，如果不设置，则插入为新行
	 * @return object
	 */
	static function insert($str, $line = -1, $column = null){
		$file = &self::$file;
		if(!$file){
			return self::append($str)->resetTime(); //未指定行数，在末尾添加新行插入
		}
		if($line < 0){
			if($line == -1) $column = null;
			$line = count($file) + $line + 1; //将行数倒数
		}
		if($column === null){ //不指定插入列
			$arr = array_slice($file, $line);
			array_splice($file, $line);
			$file = array_merge($file, array($str), $arr);
		}else{ //指定插入列
			if(extension_loaded('mbstring')){
				$_str = mb_substr($file[$line], 0, $column, 'UTF-8'); //指定列前面的数据
				$__str = mb_substr($file[$line], $column, mb_strlen($file[$line], 'UTF-8'), 'UTF-8'); //指定列及后面的数据
			}else{
				$_str = substr($file[$line], 0, $column);
				$__str = substr($file[$line], $column, strlen($file[$line]));
			}
			$file[$line] = $_str.$str.$__str;
		}
		return self::resetTime();
	}

	/** output() 输出文件内容 */
	static function output(){
		echo implode("\n", self::$file);
		return new self;
	}

	/**
	 * save() 保存文件
	 * @static
	 * @param  string $filename [可选]文件名，不设置则默认为打开时的文件名
	 * @return int              文件长度
	 */
	static function save($filename = ''){
		self::resetTime(); //重设文件时间
		$filename = $filename ?: self::$filename;
		$dir = dirname($filename);
		if(!is_dir($dir)) mkdir($dir, 0777, true); //创建文件夹
		return file_put_contents($filename, implode("\n", self::$file));
	}

	/** getContents() 获取文件内容 */
	static function getContents(){
		return implode("\n", self::$file);
	}

	/** 
	 * getInfo() 获取文件信息
	 * @static
	 * @param  string $key [可选]指定获取的信息，不设置则获取所有
	 * @return mixed       文件信息
	 */
	static function getInfo($key = ''){
		$info = &self::$info;
		$info['size'] = strlen(self::getContents());
		$info['atime'] = time();
		if(!$key) return $info;
		return isset($info[$key]) ? $info[$key] : false;
	}

	/** resetTime() 更新文件的修改时间 */
	private static function resetTime(){
		self::$info['ctime'] = self::$info['mtime'] = time();
		return new self;
	}
}