<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------
/**
 *	腾讯AI PHP-SDK, ThinkPHP5示例
 *  @link http://git.oschina.net/uctoo/uctoo
 *  @version 0.1
 *  usage:
 *   $options = array(
 *   'appid'=>'', //填写调用接口的appid
 *   'app_key'=>'' //填写调用接口的密钥
 *   );
 *	 $aiObj = new Ai($options);
 *   $text = input('text');
 *   $image = input('image');
 *   if(!empty($image)){
 *     $file = get_cover($image,'url');
 *     $base64_image_content = base64_encode(file_get_contents($file));  //图片提交到接口需要base64编码
 *   }
 *   $result = $aiObj->nlp_wordseg($text);
 *   $params['ret'] = json_encode($result,JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
 *   ...
 *
 */
namespace com\qq\ai;

use com\qq\ai\AiException;
use com\qq\ai\ErrorCode;

class Ai
{
	const API_URL_PREFIX = 'https://api.ai.qq.com/fcgi-bin';
	//基本文本分析
	const NLP_WORDSEG = '/nlp/nlp_wordseg?';
    const NLP_WORDPOS = '/nlp/nlp_wordpos?';
    const NLP_WORDNER = '/nlp/nlp_wordner?';
    const NLP_WORDSYN = '/nlp/nlp_wordsyn?';
    //语义解析
    const NLP_WORDCOM = '/nlp/nlp_wordcom?';
    //情感分析
    const NLP_TEXTPOLAR = '/nlp/nlp_textpolar?';
    //机器翻译
    const NLP_TEXTTRANS = '/nlp/nlp_texttrans?';
    //智能闲聊
	const NLP_TEXTCHAT = '/nlp/nlp_textchat?';
	//图片识别
	const VISION_SCENER = '/vision/vision_scener?';
	const VISION_OBJECTR = '/vision/vision_objectr?';
	//智能鉴黄
	const VISION_PORN = '/vision/vision_porn?';
	//身份证OCR识别
    const OCR_IDCARDOCR = '/ocr/ocr_idcardocr?';
    //名片OCR识别
    const OCR_BCOCR = '/ocr/ocr_bcocr?';
    //行驶证驾驶证OCR识别
    const OCR_DRIVERLICENSEOCR = '/ocr/ocr_driverlicenseocr?';
    //营业执照OCR识别
    const OCR_BIZLICENSEOCR = '/ocr/ocr_bizlicenseocr?';
    //银行卡OCR识别
    const OCR_CREDITCARDOCR = '/ocr/ocr_creditcardocr?';
    //通用OCR识别
    const OCR_GENERALOCR = '/ocr/ocr_generalocr?';
    //人脸美妆
    const PTU_FACECOSMETIC = '/ptu/ptu_facecosmetic?';
    //人脸变妆
    const PTU_FACEDECORATION = '/ptu/ptu_facedecoration?';
    //图片滤镜
    const PTU_IMGFILTER = '/ptu/ptu_imgfilter?';
    //人脸融合
    const PTU_FACEMERGE = '/ptu/ptu_facemerge?';
    //大头贴
    const PTU_FACESTICKER = '/ptu/ptu_facesticker?';
    //颜龄检测
    const PTU_FACEAGE = '/ptu/ptu_faceage?';

	private $appid;
	private $app_key;
	private $_receive;
    protected $values = array();
	public $debug =  false;
	public $ret = 404;
	public $msg = "no access";
	public $logcallback;

	public function __construct($options)
	{
		$this->appid = isset($options['appid'])?$options['appid']:'';
		$this->app_key = isset($options['app_key'])?$options['app_key']:'';
		$this->debug = isset($options['debug'])?$options['debug']:false;
		$this->logcallback = isset($options['logcallback'])?$options['logcallback']:'trace';
	}

    /**
     * 日志记录，可被重载。
     * @param mixed $log 输入日志
     * @return mixed
     */
    protected function log($log){
    		if ($this->debug && function_exists($this->logcallback)) {
    			if (is_array($log)) $log = print_r($log,true);
    			return call_user_func($this->logcallback,$log);
    		}
    }

    /**
     * 获取微信服务器发来的信息
     */
	public function getRev()
	{
		if ($this->_receive) return $this;
		$postStr = !empty($this->postxml)?$this->postxml:file_get_contents("php://input");
		//兼顾使用明文又不想调用valid()方法的情况
		$this->log($postStr);
		if (!empty($postStr)) {
			$this->_receive = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		}
		return $this;
	}

	/**
	 * 获取微信服务器发来的信息
	 */
	public function getRevData()
	{
		return $this->_receive;
	}

	/**
	 * GET 请求
	 * @param string $url
	 */
	private function http_get($url){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	/**
	 * POST 请求
	 * @param string $url
	 * @param array $param
	 * @param boolean $post_file 是否文件上传
	 * @return string content
	 */
	private function http_post($url,$param,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
	        if (PHP_VERSION_ID >= 50500 && class_exists('\CURLFile')) {
	            	$is_curlFile = true;
	        } else {
	        	$is_curlFile = false;
	            	if (defined('CURLOPT_SAFE_UPLOAD')) {
	                	curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
	            	}
	        }
		if (is_string($param)) {
	            	$strPOST = $param;
	        }elseif($post_file) {
	            	if($is_curlFile) {
		                foreach ($param as $key => $val) {
		                    	if (substr($val, 0, 1) == '@') {
		                        	$param[$key] = new \CURLFile(realpath(substr($val,1)));
		                    	}
		                }
	            	}
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	/**
	 * 设置缓存，按需重载
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired){
		//TODO: set cache implementation
		return false;
	}

	/**
	 * 获取缓存，按需重载
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		//TODO: get cache implementation
		return false;
	}

	/**
	 * 清除缓存，按需重载
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		//TODO: remove cache implementation
		return false;
	}


	/**
	 * 微信api不支持中文转义的json结构
	 * @param array $arr
	 */
	static function json_encode($arr) {
		if (count($arr) == 0) return "[]";
		$parts = array ();
		$is_list = false;
		//Find out if the given array is a numerical array
		$keys = array_keys ( $arr );
		$max_length = count ( $arr ) - 1;
		if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
			$is_list = true;
			for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
				if ($i != $keys [$i]) { //A key fails at position check.
					$is_list = false; //It is an associative array.
					break;
				}
			}
		}
		foreach ( $arr as $key => $value ) {
			if (is_array ( $value )) { //Custom handling for arrays
				if ($is_list)
					$parts [] = self::json_encode ( $value ); /* :RECURSION: */
				else
					$parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
			} else {
				$str = '';
				if (! $is_list)
					$str = '"' . $key . '":';
				//Custom handling for multiple data types
				if (!is_string ( $value ) && is_numeric ( $value ) && $value<2000000000)
					$str .= $value; //Numbers
				elseif ($value === false)
				$str .= 'false'; //The booleans
				elseif ($value === true)
				$str .= 'true';
				else
					$str .= '"' . addslashes ( $value ) . '"'; //All other things
				// :TODO: Is there any more datatype we should be in the lookout for? (Object?)
				$parts [] = $str;
			}
		}
		$json = implode ( ',', $parts );
		if ($is_list)
			return '[' . $json . ']'; //Return numerical JSON
		return '{' . $json . '}'; //Return associative JSON
	}

	/**
	 * 获取签名
	 * @param array $arrdata 签名数组
	 * @param string $method 签名方法
	 * @return boolean|string 签名值
	 */
	public function getSignature($arrdata,$method="sha1") {
		if (!function_exists($method)) return false;
		ksort($arrdata);
		$paramstring = "";
		foreach($arrdata as $key => $value)
		{
			if(strlen($paramstring) == 0)
				$paramstring .= $key . "=" . $value;
			else
				$paramstring .= "&" . $key . "=" . $value;
		}
		$Sign = $method($paramstring);
		return $Sign;
	}

	/**
	 * 生成随机字串
	 * @param number $length 长度，默认为16，最长为32字节
	 * @return string
	 */
	public function generateNonceStr($length=16){
		// 密码字符集，可任意添加你需要的字符
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0; $i < $length; $i++)
		{
			$str .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
		return $str;
	}

    /**
     * 获取毫秒级别的时间戳
     */
    private static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode(" ", microtime());
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2[0];
        return $time;
    }

    /**
     * 设置签名，详见签名生成算法
     * @return string 签名
     */
    public function setSign()
    {
        $sign = $this->makeSign();
        $this->values['sign'] = $sign;
        return $sign;
    }

    /**
     * 获取签名，详见签名生成算法的值
     * @return string 值
     **/
    public function getSign()
    {
        return $this->values['sign'];
    }

    /**
     * 判断签名，详见签名生成算法是否存在
     * @return true 或 false
     **/
    public function isSignSet()
    {
        return array_key_exists('sign', $this->values);
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function toUrlParams()
    {
        $buff = "";
        foreach ($this->values as $k => $v) {
            if ($v !== "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 生成签名
     * @return string 签名，本函数不覆盖sign成员变量，如要设置签名需要调用setSign方法赋值
     */
    public function makeSign()
    {
        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->toUrlParams();
        //签名步骤二：在string后加入KEY
        $string = $string . "&app_key=" . $this->app_key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 获取设置的值
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * 分词，对文本进行智能分词识别，支持基础词与混排词粒度
     * @param string $text 待分析文本，GBK编码，非空且长度上限1024字节
     * @return boolean|array url 成功则返回分析文本后的结果
     */
    public function nlp_wordseg($text){

        $this->values['text'] = urlencode(iconv('UTF-8', 'GBK', $text));
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->setSign();//签名

        $params =  $this->toUrlParams();

        $result = $this->http_get(self::API_URL_PREFIX.self::NLP_WORDSEG.$params);
        if ($result)
        {
            $resultU = mb_convert_encoding($result, "UTF-8", "GBK");
            $json = json_decode($resultU,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 词性标注	对文本进行分词，同时为每个分词标注正确的词性
     * @param string $text 待分析文本，GBK编码，非空且长度上限1024字节
     * @return boolean|array url 成功则返回分析文本后的结果
     */
    public function nlp_wordpos($text){

        $this->values['text'] = urlencode(iconv('UTF-8', 'GBK', $text));
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->setSign();//签名

        $params =  $this->toUrlParams();

        $result = $this->http_get(self::API_URL_PREFIX.self::NLP_WORDPOS.$params);
        if ($result)
        {
            $resultU = mb_convert_encoding($result, "UTF-8", "GBK");
            $json = json_decode($resultU,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 专有名词识别	对文本进行专有名词的分词识别，找出文本中的专有名词
     * @param string $text 待分析文本，GBK编码，非空且长度上限1024字节
     * @return boolean|array url 成功则返回分析文本后的结果
     */
    public function nlp_wordner($text){

        $this->values['text'] = urlencode(iconv('UTF-8', 'GBK', $text));
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->setSign();//签名

        $params =  $this->toUrlParams();

        $result = $this->http_get(self::API_URL_PREFIX.self::NLP_WORDNER.$params);
        if ($result)
        {
            $resultU = mb_convert_encoding($result, "UTF-8", "GBK");
            $json = json_decode($resultU,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 同义词识别	识别文本中存在同义词的分词，并返回相应的同义词
     * @param string $text 待分析文本，GBK编码，非空且长度上限1024字节
     * @return boolean|array url 成功则返回分析文本后的结果
     */
    public function nlp_wordsyn($text){

        $this->values['text'] = urlencode(iconv('UTF-8', 'GBK', $text));
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->setSign();//签名

        $params =  $this->toUrlParams();

        $result = $this->http_get(self::API_URL_PREFIX.self::NLP_WORDSYN.$params);
        if ($result)
        {
            $resultU = mb_convert_encoding($result, "UTF-8", "GBK");
            $json = json_decode($resultU,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 意图成分识别	对文本进行意图识别，快速找出意图及上下文成分
     * @param string $text 待分析文本，统一采用UTF-8编码，非空且长度上限1024字节
     * @return boolean|array url 成功则返回分析文本后的结果
     */
    public function nlp_wordcom($text){

        $this->values['text'] = urlencode($text);
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->setSign();//签名

        $params =  $this->toUrlParams();

        $result = $this->http_get(self::API_URL_PREFIX.self::NLP_WORDCOM.$params);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 情感分析识别	对文本进行情感分析，快速判断情感倾向（正面或负面）
     * @param string $text 待分析文本，统一采用UTF-8编码，非空且长度上限1024字节
     * @return boolean|array url 成功则返回分析文本后的结果
     */
    public function nlp_textpolar($text){

        $this->values['text'] = urlencode($text);
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->setSign();//签名

        $params =  $this->toUrlParams();

        $result = $this->http_get(self::API_URL_PREFIX.self::NLP_TEXTPOLAR.$params);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }


    /**
     * 文本翻译	对文本进行翻译，支持中文/英语自动识别和翻译
     * @param string $text 待分析文本，统一采用UTF-8编码，非空且长度上限1024字节
     * @param int $type 0自动识别（中英文互转）,1中文翻译成英文,2英文翻译成中文
     * @return boolean|array url 成功则返回分析文本后的结果
     */
    public function nlp_texttrans($text, $type = 0){

        $this->values['text'] = urlencode($text);
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['type'] = $type;
        $this->setSign();//签名

        $params =  $this->toUrlParams();

        $result = $this->http_get(self::API_URL_PREFIX.self::NLP_TEXTTRANS.$params);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }


    /**
     * 基础闲聊接口提供基于文本的基础聊天能力，可以让您的应用快速拥有具备上下文语义理解的机器聊天功能。
     * @param string $question 用户输入的聊天内容 UTF-8编码，非空且长度上限100字节
     * @param string $session 会话标识（应用内唯一）UTF-8编码，非空且长度上限32字节
     * @return boolean|array url 成功则返回分析文本后的结果
     */
    public function nlp_textchat($question, $session = '0'){
        $this->values['question'] = urlencode($question);
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['session'] = $session;
        $this->setSign();//签名

        $params =  $this->toUrlParams();

        $result = $this->http_get(self::API_URL_PREFIX.self::NLP_TEXTCHAT.$params);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 场景识别	对图行进行场景识别，快速找出图片中包含的场景信息
     * @param int $format 图片格式 1.JPG格式（image/jpeg）
     * @param int $topk 返回结果个数 [1, 5]
     * @param string $image 待识别图片,原始图片的base64编码数据（解码后大小上限1MB）
     * @return boolean|array url 成功则返回场景识别后的结果
     */
    public function vision_scener($format = 1, $topk = 5, $image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['format'] = $format;
        $this->values['topk'] = $topk;
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::VISION_SCENER,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 物体识别	对图行进行物体识别，快速找出图片中包含的物体信息
     * @param int $format 图片格式 1.JPG格式（image/jpeg）
     * @param int $topk 返回结果个数 [1, 5]
     * @param string $image 待识别图片,原始图片的base64编码数据（解码后大小上限1MB）
     * @return boolean|array url 成功则返回物体识别后的结果
     */
    public function vision_objectr($format = 1, $topk = 5, $image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['format'] = $format;
        $this->values['topk'] = $topk;
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::VISION_OBJECTR,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 智能鉴黄	识别一个图像是否为色情图像
     * @param string $image 待识别图片,原始图片的base64编码数据（解码后大小上限1MB）
     * @return boolean|array url 成功则返回智能鉴黄后的结果
     */
    public function vision_porn($image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::VISION_PORN,$data);
        if ($result)
        {
            //return $result;   //json_decode出错，json结构不标准。应该拒绝处理的！
            $result = preg_replace('# #', '', $result);  //先去掉所有空格
            $result = str_replace("tag_name\":","tag_name\":\"",$result);
            $result = str_replace(",\"tag_confidence","\",\"tag_confidence",$result);
            $result = str_replace("tag_confidence\":","tag_confidence\":\"",$result);   //还是处理了
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 身份证OCR识别	识别身份证图像上面的详细身份信息
     * @param int $card_type 身份证图片类型，0-正面(有名字的一面)，1-反面
     * @param string $image 原始图片的base64编码数据（解码后大小上限1MB，支持JPG、PNG、BMP格式）
     * @return boolean|array url 成功则返回身份证OCR识别后的结果
     */
    public function ocr_idcardocr($card_type,$image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['card_type'] = $card_type;
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::OCR_IDCARDOCR,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

   /**
  * 名片OCR识别	识别名片图像上面的字段信息
  * @param string $image 原始图片的base64编码数据（解码后大小上限1MB，支持JPG、PNG、BMP格式）
  * @return boolean|array url 成功则返回名片OCR识别后的结果
  */
    public function ocr_bcocr($image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::OCR_BCOCR,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 行驶证驾驶证OCR识别	识别行驶证或驾驶证图像上面的字段信息
     * @param int $type 识别类型，0-行驶证识别，1-驾驶证识别
     * @param string $image 原始图片的base64编码数据（解码后大小上限1MB，支持JPG、PNG、BMP格式）
     * @return boolean|array url 成功则返回行驶证驾驶证OCR识别后的结果
     */
    public function ocr_driverlicenseocr($type,$image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['type'] = $type;
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::OCR_DRIVERLICENSEOCR,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 营业执照OCR识别	识别营业执照上面的字段信息
     * @param string $image 原始图片的base64编码数据（解码后大小上限1MB，支持JPG、PNG、BMP格式）
     * @return boolean|array url 成功则返回营业执照OCR识别后的结果
     */
    public function ocr_bizlicenseocr($image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::OCR_BIZLICENSEOCR,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 银行卡OCR识别	识别银行卡上面的字段信息
     * @param string $image 原始图片的base64编码数据（解码后大小上限1MB，支持JPG、PNG、BMP格式）
     * @return boolean|array url 成功则返回银行卡OCR识别后的结果
     */
    public function ocr_creditcardocr($image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::OCR_CREDITCARDOCR,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 通用OCR识别	识别上传图像上面的字段信息
     * @param string $image 原始图片的base64编码数据（解码后大小上限1MB，支持JPG、PNG、BMP格式）
     * @return boolean|array url 成功则返回通用OCR识别后的结果
     */
    public function ocr_generalocr($image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::OCR_GENERALOCR,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 人脸美妆	给定图片和美妆编码，对原图进行人脸美妆特效处理
     * @param int $cosmetic 美妆编码
     * @param string $image 原始图片的base64编码数据（解码后大小上限1MB，支持JPG、PNG、BMP格式）
     * @return boolean|array url 成功则返回人脸美妆后的结果
     */
    public function ptu_facecosmetic($cosmetic, $image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['cosmetic'] = $cosmetic;
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::PTU_FACECOSMETIC,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 人脸变妆	给定图片和变妆编码，对原图进行人脸变妆特效处理
     * @param int $decoration 美妆编码,正整数[1,22]
     * @param string $image 原始图片的base64编码数据（解码后大小上限1MB，支持JPG、PNG、BMP格式）
     * @return boolean|array url 成功则返回人脸变妆后的结果
     */
    public function ptu_facedecoration($decoration, $image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['decoration'] = $decoration;
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::PTU_FACEDECORATION,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 图片滤镜接口提供滤镜特效功能，可以帮您快速实现原始图片的滤镜特效处理。
     * @param int $filter 滤镜特效编码,正整数[1,32]
     * @param string $image 原始图片的base64编码数据（解码后大小上限1MB，支持JPG、PNG、BMP格式）
     * @return boolean|array url 成功则返回滤镜处理后的结果
     */
    public function ptu_imgfilter($filter, $image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['filter'] = $filter;
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::PTU_IMGFILTER,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 人脸融合	给定图片和融合模板，对原图进行人脸融合特效处理。
     * @param int $type 融合素材模板编码,正整数[1,28]
     * @param string $image 原始图片的base64编码数据（解码后大小上限1MB，支持JPG、PNG、BMP格式）
     * @return boolean|array url 成功则返回人脸融后的结果
     */
    public function ptu_facemerge($type, $image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['model'] = $type;
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();
        $result = $this->http_post(self::API_URL_PREFIX.self::PTU_FACEMERGE,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 大头贴	给定图片和大头贴编码，对原图进行大头贴特效处理
     * @param int $sticker 滤镜特效编码,正整数[1,32]
     * @param string $image 原始图片的base64编码数据（解码后大小上限1MB，支持JPG、PNG、BMP格式）
     * @return boolean|array url 成功则返回滤镜处理后的结果
     */
    public function ptu_facesticker($sticker, $image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['sticker'] = $sticker;
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::PTU_FACESTICKER,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }

    /**
     * 颜龄检测	给定图片，对原图进行人脸颜龄检测处理
     * @param string $image 原始图片的base64编码数据（原始图片的base64编码数据（大小上限500KB），仅支持JPG、PNG类型图片，尺寸长宽不超过1080）
     * @return boolean|array url 成功则返回滤镜处理后的结果
     */
    public function ptu_faceage($image){
        $this->values['app_id'] = $this->appid;
        $this->values['time_stamp'] = time();
        $this->values['nonce_str'] = self::generateNonceStr();
        $this->values['image'] = urlencode($image);
        $this->setSign();//签名

        $data =  $this->toUrlParams();

        $result = $this->http_post(self::API_URL_PREFIX.self::PTU_FACEAGE,$data);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['ret'])) {
                $this->ret = $json['ret'];
                $this->msg = $json['msg'];
                return $json;
            }
            return $json;
        }
        return false;
    }
}