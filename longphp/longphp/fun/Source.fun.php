<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

function autoload($uri){
    $file = '';
    $uri_arr = explode('/', $uri);
    $classname = ucwords(strtolower(array_pop($uri_arr)));
    
    $action = 'Action_'.$classname;
    $classname = htmlspecialchars($classname, ENT_QUOTES, 'UTF-8');
    if($uri_arr){
        $file = implode('/', $uri_arr).'/';
    }
    $file_dir = DIR_CONTROLLER.$file.$classname.'.controller.php';

    if(!file_exists($file_dir)){
        if(ENVIRONMENT == 'development'){
			exit('file not found');
		}else{
			header('HTTP/1.1 404 Not Found'); 
			header("status: 404 Not Found"); 
		}
    }

	require_once $file_dir;
	if(!class_exists($action)){
		if(ENVIRONMENT == 'development'){
			exit('控制器：'.$action.' 不存在');
		}else{
			header('HTTP/1.1 404 Not Found'); 
			header("status: 404 Not Found"); 
		}
	}
}

/**
* @param string $string 原文或者密文
* @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
* @param string $key 密钥
* @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
* @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
*
* @example
*
*  $a = authcode('abc', 'ENCODE', 'key');
*  $b = authcode($a, 'DECODE', 'key');  // $b(abc)
*
*  $a = authcode('abc', 'ENCODE', 'key', 3600);
*  $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空
*/
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0){
  $ckey_length = 4;  
  // 随机密钥长度 取值 0-32;
  // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
  // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
  // 当此值为 0 时，则不产生随机密钥
 
  $key = md5($key ? $key : EABAX::getAppInf('KEY'));
  $keya = md5(substr($key, 0, 16));
  $keyb = md5(substr($key, 16, 16));
  $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
 
  $cryptkey = $keya.md5($keya.$keyc);
  $key_length = strlen($cryptkey);
 
  $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
  $string_length = strlen($string);
 
  $result = '';
  $box = range(0, 255);
 
  $rndkey = array();
  for($i = 0; $i <= 255; $i++)
  {
    $rndkey[$i] = ord($cryptkey[$i % $key_length]);
  }
 
  for($j = $i = 0; $i < 256; $i++)
  {
    $j = ($j + $box[$i] + $rndkey[$i]) % 256;
    $tmp = $box[$i];
    $box[$i] = $box[$j];
    $box[$j] = $tmp;
  }
 
  for($a = $j = $i = 0; $i < $string_length; $i++)
  {
    $a = ($a + 1) % 256;
    $j = ($j + $box[$a]) % 256;
    $tmp = $box[$a];
    $box[$a] = $box[$j];
    $box[$j] = $tmp;
    $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
  }
 
  if($operation == 'DECODE')
  {
    if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16))
    {
      return substr($result, 26);
    }
    else
    {
      return '';
    }
  }
  else
  {
    return $keyc.str_replace('=', '', base64_encode($result));
  }
}

/**
 * 大M方法 加载模型
 * $model   模块名称 如 aaa/bbb 就是目录 model下的 aaa/Bbb.model.php 模块名称就是Bbb
 * $db 就是数据库配置文件的key
 *
 * */
function M($model, $db = NULL){
    global $global_mysql_object;
    $arr = explode('/', $model);
    $file = '';
    $count = count($arr) - 1;
    for($i = 0; $i < $count; $i++){
        $file .= $arr[$i].'/';
    }
    $filename = ucfirst($arr[$count]);
    $file .= $filename.'.model.php';

    if(!file_exists(DIR_MODEL.$file)){
        if(ENVIRONMENT == 'development'){
            exit('模型文件：'.DIR_MODEL.$file.' 不存在');
        }else {
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
        }

        return false;
    }
    require_once DIR_LIB.'Model.lib.php';
    require_once DIR_MODEL.$file;

    $model_class_name = '\Model\\'.$filename;

    if(!class_exists($model_class_name)){
        if(ENVIRONMENT == 'development'){
            exit('模型类：'.$filename.' 不存在');
        }else {
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
        }

        return false;
    }

    $db_arr = include_once DIR_CONF.'db.conf.php';
    if(ENVIRONMENT != 'production' && file_exists(DIR_CONF.ENVIRONMENT.'/'.'db.conf.php')){
        $db_arr = include_once DIR_CONF.ENVIRONMENT.'/'.'db.conf.php';
    }
    require_once DIR_CLASS.'Mysql.class.php';
    if(empty($global_mysql_object[$db])){
        $global_mysql_object[$db] = new Mysql($db_arr[$db]['host'], $db_arr[$db]['port'], $db_arr[$db]['name'], $db_arr[$db]['pass'], $db_arr[$db]['database'], $db_arr[$db]['prefix'], $db_arr[$db]['charset']);
    }

    $model = new $model_class_name;

    $model->init($global_mysql_object[$db]);

    return $model;
}

function arr2tree($arr, $pid = 0, $key = 'pid'){
    $return_data = [];
    foreach($arr as $k => $v){
        if($v[$key] == $pid){
            $_tmp_return_data = $v;
            $_tmp_return_data['children'] = arr2tree($arr, $v['id'], $key);
            $return_data[] = $_tmp_return_data;
        }
    }
    return $return_data;
}
