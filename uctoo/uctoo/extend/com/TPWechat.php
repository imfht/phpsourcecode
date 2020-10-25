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
 *	微信公众平台PHP-SDK, ThinkPHP5实例
  *  @link http://git.oschina.net/uctoo/uctoo
 *  @version 1.0
 *  usage:
 *   $options = array(
 *			'token'=>'tokenaccesskey', //填写你设定的key
 *			'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
 *			'appid'=>'wxdk1234567890', //填写高级调用功能的app id
 *			'appsecret'=>'xxxxxxxxxxxxxxxxxxx' //填写高级调用功能的密钥
 *		);
 *	 $weObj = new TPWechat($options);
 *   $weObj->valid();
 *   ...
 *
 */
namespace com;
use think\Request;

class TPWechat extends Wechat
{
    protected $options = array(     //实例化wechat SDK的参数
        'token'=>APP_TOKEN, //填写你设定的key
        'encodingaeskey'=>'', //填写加密用的EncodingAESKey
        'appid'=>'', //填写高级调用功能的app id
        'appsecret'=>'', //填写高级调用功能的密钥
        'debug'=>'', //调试状态
        'logcallback'=>''   //记录日志的方法名
    );

    protected $member_public;   //数据库中保存的公众号信息

    //TP5 依赖注入的自动实例化方法
    public static function invoke()
    {

        $member_public = get_mpid_appinfo();            //数据库中保存的公众号信息
        $options['token'] = APP_TOKEN;
        $options['appid'] = $member_public['appid'];    //初始化options信息
        $options['appsecret'] = $member_public['secret'];
        $options['encodingaeskey'] = $member_public['encodingaeskey'];
        $options['debug'] = config('app_debug');            //调试状态跟随系统调试状态
        if($options['debug']){
            $options['logcallback'] = 'trace';              //微信类调试信息用trace方法记录到TP日志文件中
        }
        $weObj = new TPWechat($options);
        return $weObj;
    }
	/**
	 * log overwrite
	 * @see Wechat::log()
	 */
	/**
	protected function log($log){
		if ($this->debug) {
			if (function_exists($this->logcallback)) {
				if (is_array($log)) $log = print_r($log,true);
				return call_user_func($this->logcallback,$log);
			}else {
				return true;
			}
		}
		return false;
	}*/

	/**
	 * 重载设置缓存
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired){
		return cache($cachename,$value,$expired);
	}

	/**
	 * 重载获取缓存
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		return cache($cachename);
	}

	/**
	 * 重载清除缓存
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		return cache($cachename,null);
	}
}



