<?php

function imageResize($srcFile, $toW = 285, $toH = 200, $toFile = ""){

	if($toFile==""){ $toFile = $srcFile; }

	$info = "";
	
	$data = GetImageSize($srcFile,$info);
	
	switch ($data[2]){
		
	case 1:
	
		if(!function_exists("imagecreatefromgif")){
	
			return false;
			
			exit;
	
		}
		
		$im = ImageCreateFromGIF($srcFile);
	
		break;
	
	case 2:
	
		if(!function_exists("imagecreatefromjpeg")){
	
			return false;
	
			exit;
	
		}
	
		$im = ImageCreateFromJpeg($srcFile);
	
		break;
	
	case 3:
	
		$im = ImageCreateFromPNG($srcFile);
	
		break;
	
	}
	
	$srcW = ImageSX($im);
	
	$srcH = ImageSY($im);
	
	$toWH = $toW / $toH;
	
	$srcWH = $srcW / $srcH;

	if($toWH <= $srcWH){

		$ftoW = $toW;

		$ftoH = $ftoW * ($srcH / $srcW);

	}else{

		$ftoH = $toH;

		$ftoW = $ftoH * ($srcW / $srcH);

	}

	if(function_exists("imagecreatetruecolor")){

		@$ni = ImageCreateTrueColor($toW, $toH);
		
		@$color = imagecolorallocate($ni, 255, 255, 255);
		
		//设置透明
		@imagecolortransparent($ni, $color);
		
		@imagefill($ni,0,0,$color);

		if($ni) ImageCopyResampled($ni, $im, ($toW - $ftoW) / 2, ($toH - $ftoH) / 2, 0, 0, $ftoW, $ftoH, $srcW, $srcH);

		else{

			$ni = ImageCreate($toW, $toH);
		
			ImageCopyResized($ni, $im, ($toW - $ftoW) / 2, ($toH - $ftoH) / 2, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
		
		}

	}else{

		$ni=ImageCreate($toW, $toH);
	
		ImageCopyResized($ni, $im, ($toW - $ftoW) / 2, ($toH - $ftoH) / 2, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
	
	}

	if(function_exists('imagejpeg')) ImageJpeg($ni, $toFile);

	else ImagePNG($ni, $toFile);

	ImageDestroy($ni);

	ImageDestroy($im);
	
	return true;

}

function despiteStr($str){
	
	if(get_magic_quotes_gpc()){
		
	     $str = stripslashes($str);

	}
	
	return $str;
	
}

function get_byte($bytes){
	
    if(empty($bytes)){
    	
        return '--';

    }

    $sizetext = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");

    return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . $sizetext[$i];

}

//创建目录
function mkdirs($dir){

	if(!is_dir($dir)){

		if(!mkdirs(dirname($dir))){

			return false;

		}

		if(!mkdir($dir, 0777)){

			return false;

		}

	}

	return true;

}

//$save_path 文件保存目录路径,$save_url 文件保存目录URL
function upload_json($save_path, $save_url){
	
	$save_path = empty($save_path) ? $_SERVER['DOCUMENT_ROOT'] : $save_path;

	$save_url = empty($save_url) ? '/' : $save_url;

	//定义允许上传的文件扩展名
	$ext_arr = array(
		'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
		'flash' => array('swf', 'flv'),
		'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
		'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
	);
	//最大文件大小
	$max_size = 5000000;

	//$save_path = realpath($save_path) . '/';

	mkdirs($save_path);
	
	//PHP上传失败
	if (!empty($_FILES['imgFile']['error'])) {
		switch($_FILES['imgFile']['error']){
			case '1':
				$error = '超过php.ini允许的大小。';
				break;
			case '2':
				$error = '超过表单允许的大小。';
				break;
			case '3':
				$error = '图片只有部分被上传。';
				break;
			case '4':
				$error = '请选择图片。';
				break;
			case '6':
				$error = '找不到临时目录。';
				break;
			case '7':
				$error = '写文件到硬盘出错。';
				break;
			case '8':
				$error = 'File upload stopped by extension。';
				break;
			case '999':
			default:
				$error = '未知错误。';
		}
		alert($error);
	}

	//有上传文件时
	if (empty($_FILES) === false) {
		//原文件名
		$file_name = $_FILES['imgFile']['name'];
		//服务器上临时文件名
		$tmp_name = $_FILES['imgFile']['tmp_name'];
		//文件大小
		$file_size = $_FILES['imgFile']['size'];
		//检查文件名
		if (!$file_name) {
			alert("请选择文件。");
		}
		//检查目录
		if (@is_dir($save_path) === false) {
			alert("上传目录不存在。");
		}
		//检查目录写权限
		if (@is_writable($save_path) === false) {
			alert("上传目录没有写权限。");
		}
		//检查是否已上传
		if (@is_uploaded_file($tmp_name) === false) {
			alert("上传失败。");
		}
		//检查文件大小
		if ($file_size > $max_size) {
			alert("上传文件大小超过限制。");
		}
		//检查目录名
		$dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
		if (empty($ext_arr[$dir_name])) {
			alert("目录名不正确。");
		}
		//获得文件扩展名
		$temp_arr = explode(".", $file_name);
		$file_ext = array_pop($temp_arr);
		$file_ext = trim($file_ext);
		$file_ext = strtolower($file_ext);
		//检查扩展名
		if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
			alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
		}
		//创建文件夹
		if ($dir_name !== '') {
			$save_path .= $dir_name . "/";
			$save_url .= $dir_name . "/";
			if (!file_exists($save_path)) {
				mkdir($save_path);
			}
		}
		if (!file_exists($save_path)) {
			mkdir($save_path);
		}
		//新文件名
		$new_file_name = md5(date("YmdHis") . '_' . rand(10000, 99999)) . '.' . $file_ext;
		//移动文件
		$file_path = $save_path . $new_file_name;
		if (move_uploaded_file($tmp_name, $file_path) === false) {
			alert("上传文件失败。");
		}
		@chmod($file_path, 0644);
		$file_url = $save_url . $new_file_name;
	
		header('Content-type: text/html; charset=UTF-8');
		$json = new Services_JSON();
		echo $json->encode(array('error' => 0, 'url' => $file_url, 'path' => $save_path));
		exit;
	}

}

function alert($msg) {
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 1, 'message' => $msg));
	exit;
}

//$save_path 文件保存目录路径,$save_url 文件保存目录URL
function file_manager_json($save_path, $save_url){
	
	$save_path = empty($save_path) ? $_SERVER['DOCUMENT_ROOT'] : $save_path;

	$save_url = empty($save_url) ? '/' : $save_url;
	
	//图片扩展名
	$ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
	
	//目录名
	$dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);
	if (!in_array($dir_name, array('', 'image', 'flash', 'media', 'file'))) {
		echo "Invalid Directory name.";
		exit;
	}
	if ($dir_name !== '') {
		$root_path .= $dir_name . "/";
		$root_url .= $dir_name . "/";
		if (!file_exists($root_path)) {
			mkdir($root_path);
		}
	}
	
	//根据path参数，设置各路径和URL
	if (empty($_GET['path'])) {
		$current_path = realpath($root_path) . '/';
		$current_url = $root_url;
		$current_dir_path = '';
		$moveup_dir_path = '';
	} else {
		$current_path = realpath($root_path) . '/' . $_GET['path'];
		$current_url = $root_url . $_GET['path'];
		$current_dir_path = $_GET['path'];
		$moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
	}
	echo realpath($root_path);
	//排序形式，name or size or type
	$order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);
	
	//不允许使用..移动到上一级目录
	if (preg_match('/\.\./', $current_path)) {
		echo 'Access is not allowed.';
		exit;
	}
	//最后一个字符不是/
	if (!preg_match('/\/$/', $current_path)) {
		echo 'Parameter is not valid.';
		exit;
	}
	//目录不存在或不是目录
	if (!file_exists($current_path) || !is_dir($current_path)) {
		echo 'Directory does not exist.';
		exit;
	}
	
	//遍历目录取得文件信息
	$file_list = array();
	if ($handle = opendir($current_path)) {
		$i = 0;
		while (false !== ($filename = readdir($handle))) {
			if ($filename{0} == '.') continue;
			$file = $current_path . $filename;
			if (is_dir($file)) {
				$file_list[$i]['is_dir'] = true; //是否文件夹
				$file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
				$file_list[$i]['filesize'] = 0; //文件大小
				$file_list[$i]['is_photo'] = false; //是否图片
				$file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
			} else {
				$file_list[$i]['is_dir'] = false;
				$file_list[$i]['has_file'] = false;
				$file_list[$i]['filesize'] = filesize($file);
				$file_list[$i]['dir_path'] = '';
				$file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
				$file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
				$file_list[$i]['filetype'] = $file_ext;
			}
			$file_list[$i]['filename'] = $filename; //文件名，包含扩展名
			$file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
			$i++;
		}
		closedir($handle);
	}
	
	usort($file_list, 'cmp_func');
	
	$result = array();
	//相对于根目录的上一级目录
	$result['moveup_dir_path'] = $moveup_dir_path;
	//相对于根目录的当前目录
	$result['current_dir_path'] = $current_dir_path;
	//当前目录的URL
	$result['current_url'] = $current_url;
	//文件数
	$result['total_count'] = count($file_list);
	//文件列表数组
	$result['file_list'] = $file_list;
	
	//输出JSON字符串
	header('Content-type: application/json; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode($result);

}

//排序
function cmp_func($a, $b) {
	global $order;
	if ($a['is_dir'] && !$b['is_dir']) {
		return -1;
	} else if (!$a['is_dir'] && $b['is_dir']) {
		return 1;
	} else {
		if ($order == 'size') {
			if ($a['filesize'] > $b['filesize']) {
				return 1;
			} else if ($a['filesize'] < $b['filesize']) {
				return -1;
			} else {
				return 0;
			}
		} else if ($order == 'type') {
			return strcmp($a['filetype'], $b['filetype']);
		} else {
			return strcmp($a['filename'], $b['filename']);
		}
	}
}

function stopDefaultTag($str){

	if(get_magic_quotes_gpc()){//如果get_magic_quotes_gpc()是打开的

		$str = stripslashes($str);//将字符串进行处理

	}

	return $str;

}

class Services_JSON
{
   /**
    * constructs a new JSON instance
    *
    * @param    int     $use    object behavior flags; combine with boolean-OR
    *
    *                           possible values:
    *                           - SERVICES_JSON_LOOSE_TYPE:  loose typing.
    *                                   "{...}" syntax creates associative arrays
    *                                   instead of objects in decode().
    *                           - SERVICES_JSON_SUPPRESS_ERRORS:  error suppression.
    *                                   Values which can't be encoded (e.g. resources)
    *                                   appear as NULL instead of throwing errors.
    *                                   By default, a deeply-nested resource will
    *                                   bubble up with an error, so all return values
    *                                   from encode() should be checked with isError()
    */
    function Services_JSON($use = 0)
    {
        $this->use = $use;
    }

   /**
    * convert a string from one UTF-16 char to one UTF-8 char
    *
    * Normally should be handled by mb_convert_encoding, but
    * provides a slower PHP-only method for installations
    * that lack the multibye string extension.
    *
    * @param    string  $utf16  UTF-16 character
    * @return   string  UTF-8 character
    * @access   private
    */
    function utf162utf8($utf16)
    {
        // oh please oh please oh please oh please oh please
        if(function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
        }

        $bytes = (ord($utf16{0}) << 8) | ord($utf16{1});

        switch(true) {
            case ((0x7F & $bytes) == $bytes):
                // this case should never be reached, because we are in ASCII range
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0x7F & $bytes);

            case (0x07FF & $bytes) == $bytes:
                // return a 2-byte UTF-8 character
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0xC0 | (($bytes >> 6) & 0x1F))
                     . chr(0x80 | ($bytes & 0x3F));

            case (0xFFFF & $bytes) == $bytes:
                // return a 3-byte UTF-8 character
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0xE0 | (($bytes >> 12) & 0x0F))
                     . chr(0x80 | (($bytes >> 6) & 0x3F))
                     . chr(0x80 | ($bytes & 0x3F));
        }

        // ignoring UTF-32 for now, sorry
        return '';
    }

   /**
    * convert a string from one UTF-8 char to one UTF-16 char
    *
    * Normally should be handled by mb_convert_encoding, but
    * provides a slower PHP-only method for installations
    * that lack the multibye string extension.
    *
    * @param    string  $utf8   UTF-8 character
    * @return   string  UTF-16 character
    * @access   private
    */
    function utf82utf16($utf8)
    {
        // oh please oh please oh please oh please oh please
        if(function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
        }

        switch(strlen($utf8)) {
            case 1:
                // this case should never be reached, because we are in ASCII range
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return $utf8;

            case 2:
                // return a UTF-16 character from a 2-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0x07 & (ord($utf8{0}) >> 2))
                     . chr((0xC0 & (ord($utf8{0}) << 6))
                         | (0x3F & ord($utf8{1})));

            case 3:
                // return a UTF-16 character from a 3-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr((0xF0 & (ord($utf8{0}) << 4))
                         | (0x0F & (ord($utf8{1}) >> 2)))
                     . chr((0xC0 & (ord($utf8{1}) << 6))
                         | (0x7F & ord($utf8{2})));
        }

        // ignoring UTF-32 for now, sorry
        return '';
    }

   /**
    * encodes an arbitrary variable into JSON format
    *
    * @param    mixed   $var    any number, boolean, string, array, or object to be encoded.
    *                           see argument 1 to Services_JSON() above for array-parsing behavior.
    *                           if var is a strng, note that encode() always expects it
    *                           to be in ASCII or UTF-8 format!
    *
    * @return   mixed   JSON string representation of input var or an error if a problem occurs
    * @access   public
    */
    function encode($var)
    {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false';

            case 'NULL':
                return 'null';

            case 'integer':
                return (int) $var;

            case 'double':
            case 'float':
                return (float) $var;

            case 'string':
                // STRINGS ARE EXPECTED TO BE IN ASCII OR UTF-8 FORMAT
                $ascii = '';
                $strlen_var = strlen($var);

               /*
                * Iterate over every character in the string,
                * escaping with a slash or encoding to UTF-8 where necessary
                */
                for ($c = 0; $c < $strlen_var; ++$c) {

                    $ord_var_c = ord($var{$c});

                    switch (true) {
                        case $ord_var_c == 0x08:
                            $ascii .= '\b';
                            break;
                        case $ord_var_c == 0x09:
                            $ascii .= '\t';
                            break;
                        case $ord_var_c == 0x0A:
                            $ascii .= '\n';
                            break;
                        case $ord_var_c == 0x0C:
                            $ascii .= '\f';
                            break;
                        case $ord_var_c == 0x0D:
                            $ascii .= '\r';
                            break;

                        case $ord_var_c == 0x22:
                        case $ord_var_c == 0x2F:
                        case $ord_var_c == 0x5C:
                            // double quote, slash, slosh
                            $ascii .= '\\'.$var{$c};
                            break;

                        case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
                            // characters U-00000000 - U-0000007F (same as ASCII)
                            $ascii .= $var{$c};
                            break;

                        case (($ord_var_c & 0xE0) == 0xC0):
                            // characters U-00000080 - U-000007FF, mask 110XXXXX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c, ord($var{$c + 1}));
                            $c += 1;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xF0) == 0xE0):
                            // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c + 1}),
                                         ord($var{$c + 2}));
                            $c += 2;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xF8) == 0xF0):
                            // characters U-00010000 - U-001FFFFF, mask 11110XXX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c + 1}),
                                         ord($var{$c + 2}),
                                         ord($var{$c + 3}));
                            $c += 3;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xFC) == 0xF8):
                            // characters U-00200000 - U-03FFFFFF, mask 111110XX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c + 1}),
                                         ord($var{$c + 2}),
                                         ord($var{$c + 3}),
                                         ord($var{$c + 4}));
                            $c += 4;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xFE) == 0xFC):
                            // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c + 1}),
                                         ord($var{$c + 2}),
                                         ord($var{$c + 3}),
                                         ord($var{$c + 4}),
                                         ord($var{$c + 5}));
                            $c += 5;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;
                    }
                }

                return '"'.$ascii.'"';

            case 'array':
               /*
                * As per JSON spec if any array key is not an integer
                * we must treat the the whole array as an object. We
                * also try to catch a sparsely populated associative
                * array with numeric keys here because some JS engines
                * will create an array with empty indexes up to
                * max_index which can cause memory issues and because
                * the keys, which may be relevant, will be remapped
                * otherwise.
                *
                * As per the ECMA and JSON specification an object may
                * have any string as a property. Unfortunately due to
                * a hole in the ECMA specification if the key is a
                * ECMA reserved word or starts with a digit the
                * parameter is only accessible using ECMAScript's
                * bracket notation.
                */

                // treat as a JSON object
                if (is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
                    $properties = array_map(array($this, 'name_value'),
                                            array_keys($var),
                                            array_values($var));

                    foreach($properties as $property) {
                        if(Services_JSON::isError($property)) {
                            return $property;
                        }
                    }

                    return '{' . join(',', $properties) . '}';
                }

                // treat it like a regular array
                $elements = array_map(array($this, 'encode'), $var);

                foreach($elements as $element) {
                    if(Services_JSON::isError($element)) {
                        return $element;
                    }
                }

                return '[' . join(',', $elements) . ']';

            case 'object':
                $vars = get_object_vars($var);

                $properties = array_map(array($this, 'name_value'),
                                        array_keys($vars),
                                        array_values($vars));

                foreach($properties as $property) {
                    if(Services_JSON::isError($property)) {
                        return $property;
                    }
                }

                return '{' . join(',', $properties) . '}';

            default:
                return ($this->use & SERVICES_JSON_SUPPRESS_ERRORS)
                    ? 'null'
                    : new Services_JSON_Error(gettype($var)." can not be encoded as JSON string");
        }
    }

   /**
    * array-walking function for use in generating JSON-formatted name-value pairs
    *
    * @param    string  $name   name of key to use
    * @param    mixed   $value  reference to an array element to be encoded
    *
    * @return   string  JSON-formatted name-value pair, like '"name":value'
    * @access   private
    */
    function name_value($name, $value)
    {
        $encoded_value = $this->encode($value);

        if(Services_JSON::isError($encoded_value)) {
            return $encoded_value;
        }

        return $this->encode(strval($name)) . ':' . $encoded_value;
    }

   /**
    * reduce a string by removing leading and trailing comments and whitespace
    *
    * @param    $str    string      string value to strip of comments and whitespace
    *
    * @return   string  string value stripped of comments and whitespace
    * @access   private
    */
    function reduce_string($str)
    {
        $str = preg_replace(array(

                // eliminate single line comments in '// ...' form
                '#^\s*//(.+)$#m',

                // eliminate multi-line comments in '/* ... */' form, at start of string
                '#^\s*/\*(.+)\*/#Us',

                // eliminate multi-line comments in '/* ... */' form, at end of string
                '#/\*(.+)\*/\s*$#Us'

            ), '', $str);

        // eliminate extraneous space
        return trim($str);
    }

   /**
    * decodes a JSON string into appropriate variable
    *
    * @param    string  $str    JSON-formatted string
    *
    * @return   mixed   number, boolean, string, array, or object
    *                   corresponding to given JSON input string.
    *                   See argument 1 to Services_JSON() above for object-output behavior.
    *                   Note that decode() always returns strings
    *                   in ASCII or UTF-8 format!
    * @access   public
    */
    function decode($str)
    {
        $str = $this->reduce_string($str);

        switch (strtolower($str)) {
            case 'true':
                return true;

            case 'false':
                return false;

            case 'null':
                return null;

            default:
                $m = array();

                if (is_numeric($str)) {
                    // Lookie-loo, it's a number

                    // This would work on its own, but I'm trying to be
                    // good about returning integers where appropriate:
                    // return (float)$str;

                    // Return float or int, as appropriate
                    return ((float)$str == (integer)$str)
                        ? (integer)$str
                        : (float)$str;

                } elseif (preg_match('/^("|\').*(\1)$/s', $str, $m) && $m[1] == $m[2]) {
                    // STRINGS RETURNED IN UTF-8 FORMAT
                    $delim = substr($str, 0, 1);
                    $chrs = substr($str, 1, -1);
                    $utf8 = '';
                    $strlen_chrs = strlen($chrs);

                    for ($c = 0; $c < $strlen_chrs; ++$c) {

                        $substr_chrs_c_2 = substr($chrs, $c, 2);
                        $ord_chrs_c = ord($chrs{$c});

                        switch (true) {
                            case $substr_chrs_c_2 == '\b':
                                $utf8 .= chr(0x08);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\t':
                                $utf8 .= chr(0x09);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\n':
                                $utf8 .= chr(0x0A);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\f':
                                $utf8 .= chr(0x0C);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\r':
                                $utf8 .= chr(0x0D);
                                ++$c;
                                break;

                            case $substr_chrs_c_2 == '\\"':
                            case $substr_chrs_c_2 == '\\\'':
                            case $substr_chrs_c_2 == '\\\\':
                            case $substr_chrs_c_2 == '\\/':
                                if (($delim == '"' && $substr_chrs_c_2 != '\\\'') ||
                                   ($delim == "'" && $substr_chrs_c_2 != '\\"')) {
                                    $utf8 .= $chrs{++$c};
                                }
                                break;

                            case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $c, 6)):
                                // single, escaped unicode character
                                $utf16 = chr(hexdec(substr($chrs, ($c + 2), 2)))
                                       . chr(hexdec(substr($chrs, ($c + 4), 2)));
                                $utf8 .= $this->utf162utf8($utf16);
                                $c += 5;
                                break;

                            case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
                                $utf8 .= $chrs{$c};
                                break;

                            case ($ord_chrs_c & 0xE0) == 0xC0:
                                // characters U-00000080 - U-000007FF, mask 110XXXXX
                                //see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                                $utf8 .= substr($chrs, $c, 2);
                                ++$c;
                                break;

                            case ($ord_chrs_c & 0xF0) == 0xE0:
                                // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                                // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                                $utf8 .= substr($chrs, $c, 3);
                                $c += 2;
                                break;

                            case ($ord_chrs_c & 0xF8) == 0xF0:
                                // characters U-00010000 - U-001FFFFF, mask 11110XXX
                                // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                                $utf8 .= substr($chrs, $c, 4);
                                $c += 3;
                                break;

                            case ($ord_chrs_c & 0xFC) == 0xF8:
                                // characters U-00200000 - U-03FFFFFF, mask 111110XX
                                // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                                $utf8 .= substr($chrs, $c, 5);
                                $c += 4;
                                break;

                            case ($ord_chrs_c & 0xFE) == 0xFC:
                                // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                                // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                                $utf8 .= substr($chrs, $c, 6);
                                $c += 5;
                                break;

                        }

                    }

                    return $utf8;

                } elseif (preg_match('/^\[.*\]$/s', $str) || preg_match('/^\{.*\}$/s', $str)) {
                    // array, or object notation

                    if ($str{0} == '[') {
                        $stk = array(SERVICES_JSON_IN_ARR);
                        $arr = array();
                    } else {
                        if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
                            $stk = array(SERVICES_JSON_IN_OBJ);
                            $obj = array();
                        } else {
                            $stk = array(SERVICES_JSON_IN_OBJ);
                            $obj = new stdClass();
                        }
                    }

                    array_push($stk, array('what'  => SERVICES_JSON_SLICE,
                                           'where' => 0,
                                           'delim' => false));

                    $chrs = substr($str, 1, -1);
                    $chrs = $this->reduce_string($chrs);

                    if ($chrs == '') {
                        if (reset($stk) == SERVICES_JSON_IN_ARR) {
                            return $arr;

                        } else {
                            return $obj;

                        }
                    }

                    //print("\nparsing {$chrs}\n");

                    $strlen_chrs = strlen($chrs);

                    for ($c = 0; $c <= $strlen_chrs; ++$c) {

                        $top = end($stk);
                        $substr_chrs_c_2 = substr($chrs, $c, 2);

                        if (($c == $strlen_chrs) || (($chrs{$c} == ',') && ($top['what'] == SERVICES_JSON_SLICE))) {
                            // found a comma that is not inside a string, array, etc.,
                            // OR we've reached the end of the character list
                            $slice = substr($chrs, $top['where'], ($c - $top['where']));
                            array_push($stk, array('what' => SERVICES_JSON_SLICE, 'where' => ($c + 1), 'delim' => false));
                            //print("Found split at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");

                            if (reset($stk) == SERVICES_JSON_IN_ARR) {
                                // we are in an array, so just push an element onto the stack
                                array_push($arr, $this->decode($slice));

                            } elseif (reset($stk) == SERVICES_JSON_IN_OBJ) {
                                // we are in an object, so figure
                                // out the property name and set an
                                // element in an associative array,
                                // for now
                                $parts = array();
                                
                                if (preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
                                    // "name":value pair
                                    $key = $this->decode($parts[1]);
                                    $val = $this->decode($parts[2]);

                                    if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
                                        $obj[$key] = $val;
                                    } else {
                                        $obj->$key = $val;
                                    }
                                } elseif (preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
                                    // name:value pair, where name is unquoted
                                    $key = $parts[1];
                                    $val = $this->decode($parts[2]);

                                    if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
                                        $obj[$key] = $val;
                                    } else {
                                        $obj->$key = $val;
                                    }
                                }

                            }

                        } elseif ((($chrs{$c} == '"') || ($chrs{$c} == "'")) && ($top['what'] != SERVICES_JSON_IN_STR)) {
                            // found a quote, and we are not inside a string
                            array_push($stk, array('what' => SERVICES_JSON_IN_STR, 'where' => $c, 'delim' => $chrs{$c}));
                            //print("Found start of string at {$c}\n");

                        } elseif (($chrs{$c} == $top['delim']) &&
                                 ($top['what'] == SERVICES_JSON_IN_STR) &&
                                 ((strlen(substr($chrs, 0, $c)) - strlen(rtrim(substr($chrs, 0, $c), '\\'))) % 2 != 1)) {
                            // found a quote, we're in a string, and it's not escaped
                            // we know that it's not escaped becase there is _not_ an
                            // odd number of backslashes at the end of the string so far
                            array_pop($stk);
                            //print("Found end of string at {$c}: ".substr($chrs, $top['where'], (1 + 1 + $c - $top['where']))."\n");

                        } elseif (($chrs{$c} == '[') &&
                                 in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {
                            // found a left-bracket, and we are in an array, object, or slice
                            array_push($stk, array('what' => SERVICES_JSON_IN_ARR, 'where' => $c, 'delim' => false));
                            //print("Found start of array at {$c}\n");

                        } elseif (($chrs{$c} == ']') && ($top['what'] == SERVICES_JSON_IN_ARR)) {
                            // found a right-bracket, and we're in an array
                            array_pop($stk);
                            //print("Found end of array at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");

                        } elseif (($chrs{$c} == '{') &&
                                 in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {
                            // found a left-brace, and we are in an array, object, or slice
                            array_push($stk, array('what' => SERVICES_JSON_IN_OBJ, 'where' => $c, 'delim' => false));
                            //print("Found start of object at {$c}\n");

                        } elseif (($chrs{$c} == '}') && ($top['what'] == SERVICES_JSON_IN_OBJ)) {
                            // found a right-brace, and we're in an object
                            array_pop($stk);
                            //print("Found end of object at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");

                        } elseif (($substr_chrs_c_2 == '/*') &&
                                 in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {
                            // found a comment start, and we are in an array, object, or slice
                            array_push($stk, array('what' => SERVICES_JSON_IN_CMT, 'where' => $c, 'delim' => false));
                            $c++;
                            //print("Found start of comment at {$c}\n");

                        } elseif (($substr_chrs_c_2 == '*/') && ($top['what'] == SERVICES_JSON_IN_CMT)) {
                            // found a comment end, and we're in one now
                            array_pop($stk);
                            $c++;

                            for ($i = $top['where']; $i <= $c; ++$i)
                                $chrs = substr_replace($chrs, ' ', $i, 1);

                            //print("Found end of comment at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");

                        }

                    }

                    if (reset($stk) == SERVICES_JSON_IN_ARR) {
                        return $arr;

                    } elseif (reset($stk) == SERVICES_JSON_IN_OBJ) {
                        return $obj;

                    }

                }
        }
    }

    /**
     * @todo Ultimately, this should just call PEAR::isError()
     */
    function isError($data, $code = null)
    {
        if (class_exists('pear')) {
            return PEAR::isError($data, $code);
        } elseif (is_object($data) && (get_class($data) == 'services_json_error' ||
                                 is_subclass_of($data, 'services_json_error'))) {
            return true;
        }

        return false;
    }
}

if (class_exists('PEAR_Error')) {

    class Services_JSON_Error extends PEAR_Error
    {
        function Services_JSON_Error($message = 'unknown error', $code = null,
                                     $mode = null, $options = null, $userinfo = null)
        {
            parent::PEAR_Error($message, $code, $mode, $options, $userinfo);
        }
    }

} else {

    /**
     * @todo Ultimately, this class shall be descended from PEAR_Error
     */
    class Services_JSON_Error
    {
        function Services_JSON_Error($message = 'unknown error', $code = null,
                                     $mode = null, $options = null, $userinfo = null)
        {

        }
    }

}