<?php
namespace app\common\controller;

use think\Config;
use think\Controller;
use app\common\model\WxWx;

/**
 * Index
 * 公共类；
 * 获取基本数据；
 * 自定义的、基本的方法；
 * @author WangWei
 */
class Index extends Controller {
	// 待输出的数据
	protected $out;
	
	// 输入的数据 array
	protected $in;
	public function __construct($in = array()) {
		parent::__construct ();
		$this->in = $in;
	}
	/**
	 * getDefaultWx
	 * 获取默认公众号的信息
	 * @return mixed[]
	 */
	public function getDefaultWx() {
		$WxWx = new WxWx ();
		$res = $WxWx->where ( 'active', 1 )->find ();
		if ($res) {
			return [ 
					'errCode' => 0,
					'data' => $res 
			];
		} else {
			$res = $WxWx->count ( 'id' );
			if ($res === 0) {
				return [ 
						'errMsg' => '尚未添加任何公众号',
						'errCode' => 101 
				];
			} else {
				return [ 
						'errCode' => 102,
						'errMsg' => '尚未设置默认公众号' 
				];
			}
		}
	}
	/**
	 * getAccessToken
	 * 获取默认或指定公众号的access_token
	 * @param number $id 公众号id，非默认公众号须传入该值
	 * @return string[]|number[]|mixed[]
	 */
	public function getAccessToken($id = NULL) {
		
		$res = $this->getDefaultWx ();
		if ($res ['errCode']) {
			return $res;
		}
		if ($res ['data'] ['access_token'] && $res ['data'] ['access_token_time'] > time () - 7195) {
			return [ 
					'errCode' => 0,
					'data' => $res ['data'] ['access_token'] 
			];
		}
		$id = $id === NULL ? $res ['data'] ['id'] : $id;
		Config::load ( __DIR__ . '/../config.php' );
		$config = config ();
		
		$url = str_replace ( 'APPID', trim($res ['data'] ['app_id']), $config ['wx_url'] ['get_access_token'] );
		$url = str_replace ( 'APPSECRET', trim($res ['data'] ['app_secret']), $url );
		$r = $this->doCurl ( $url );
		
		$r = json_decode ( $r, 1 );
		if (isset ( $r ['access_token'] )) {
			$WxWx = new \app\index\model\WxWx ();
			$r ['access_token_time'] = time ();
			$result = $WxWx->allowField ( true )->save ( $r, [ 
					'id' => $res ['data'] ['id'] 
			] );
			if (1 !== $result) {
				return [ 
						'errCode' => 103,
						'errMsg' => '更新公众号时出错' 
				];
			}
		} else {
			return [ 
					'errCode' => 104,
					'errMsg' => '错误代码：' . $r ['errcode'] . '；意义：' . $r ['errmsg'] 
			];
		}
		return [ 
				'errCode' => 0,
				'errMsg' => '成功',
				'data' => $r ['access_token'] 
		];
		
	}
	/**
	 * doCurl
	 * 执行 curl 的方法
	 * @param String $url 链接
	 * @param array $postfields 表单数据
	 * @param number $media 是否含对媒体文件,默认不含
	 * @return mixed
	 */
	public function doCurl($url, $postfields = NULL, $media = 0) {
		$ch = curl_init ();
		if (class_exists ( '/CURLFile' )) { //php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同
			curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, true );
		} else {
			if (defined ( 'CURLOPT_SAFE_UPLOAD' ) && version_compare(phpversion(), '7.0') < 0) {
				curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false );
			}
		}
		if ($media == 1) {
			curl_setopt ( $ch, CURLOPT_HEADER, false );
			curl_setopt ( $ch, CURLOPT_BINARYTRANSFER, true );
		}
		$is_https = true;
		$url = trim ( $url );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		if (stripos ( $url, "https" ) !== 0) {
			$is_https = false;
		}
		
		if ($is_https) {
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt ( $ch, CURLOPT_SSLVERSION, 1 );
		}
		if (! empty ( $postfields )) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postfields );
		}
		
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
		$result = curl_exec ( $ch );
		if (curl_errno ( $ch )) {
			$result = curl_error ( $ch );
		}
		curl_close ( $ch );
		return $result;
	}
	/**
	 * wxErrCode
	 * 解析微信返回的错误码/正确信息
	 * @param String $r 微信返回的字符串
	 * @return mixed[]
	 */
	public function wxErrCode($r = NULL) {
		$res = json_decode ( $r, true );
		if (!isset($res['errcode']) || $res ['errcode'] === 0 || empty ( $res ['errcode'] )) {
			return array (
					'errCode' => 0,
					'errMsg' => '成功',
					'data' => $res 
			);
		} else {
			$r = file_get_contents ( APP_PATH . '/common/errors.json' );
			$a = json_decode ( $r, true );
			$words = empty ( $words ) ? '' : $words;
			$meaning = isset($a [$res ['errcode']]) ? $a [$res ['errcode']] : $res ['errmsg'];
			$res = "错误代码：" . $res ['errcode'] . "。意为：" . $meaning;
			return array (
					'errCode' => 1,
					'errMsg' => $res 
			);
		}
	}
	/**
	 * getConfig
	 * 返回公共配置数组
	 * @return mixed[] 配置数组
	 */
	public function getConfig(){
		Config::load(APP_PATH.'/common/config.php');
		return config();
	}
	
	/**
	 * my_substr
	 * 截取字符串，前后添加“…”
	 * @param string $str 待截取的字符串
	 * @param number $start 开始位置
	 * @param number $length 长度
	 * @param string $encode 编码
	 * @return string 截取后的字符串
	 */
	public function my_substr($str,$start,$length,$encode=NULL){
		$encode=$encode===NULL?'utf-8':$encode;
		$len=mb_strlen($str,$encode);
		if($start>$len-1){
			return $str;
		}
		$pre=$start>0?'…':'';
		$after=$length+$start<$len?"…":'';
		return $pre.mb_substr($str,$start,$length,$encode).$after;
	}
}
