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
 *	微信开放平台PHP-SDK, ThinkPHP5实例
  *  @link http://git.oschina.net/uctoo/uctoo
 *  @version 1.0
 *  usage:
 *   $options = array(
 *			'token'=>'tokenaccesskey', //填写你设定的key
 *			'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
 *			'appid'=>'wxdk1234567890', //填写高级调用功能的app id
 *			'appsecret'=>'xxxxxxxxxxxxxxxxxxx' //填写高级调用功能的密钥
 *		);
 *	 $weObj = new TPWechatOpen($options);
 *   $weObj->getAuthorizerInfo($appid);
 *   ...
 *
 */
namespace com;
use think\Request;

class TPWechatOpen extends WechatOpen
{
    //TP5 依赖注入的自动实例化方法
    public static function invoke()
    {

        $component = get_component();            //数据库中保存的第三方平台信息

        $options['token'] = APP_TOKEN;
        $options['component_appid'] = $component['appid'];    //初始化options信息
        $options['component_appsecret'] = $component['appsecret'];
        $options['component_access_token'] = $component['component_access_token'];
        $options['encodingaeskey'] = $component['encodingAesKey'];
        $options['debug'] = config('app_debug');            //调试状态跟随系统调试状态
        if($options['debug']){
            $options['logcallback'] = 'trace';              //微信类调试信息用trace方法记录到TP日志文件中
        }
        $weObj = new TPWechatOpen($options);

        return $weObj;
    }

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



