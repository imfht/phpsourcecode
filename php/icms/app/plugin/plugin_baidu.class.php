<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
class plugin_baidu{
    public static $out = null;

    public static function push($urls) {
        self::ping($urls);
        foreach ((array)$urls as $key => $url) {
            self::xzh($url);
            self::RPC2($url);
        }
    }
    /**
     * [ping 百度站长平台 主动推送]
     * @param  [type] $urls [description]
     * @param  [type] $type [description]
     * @param  string $act  [urls:推送,update:更新,del:删除]
     * @return [type]       [description]
     */
    public static function ping($urls,$type=null,$act='urls') {
        $site          = iCMS::$config['api']['baidu']['sitemap']['site'];
        $access_token  = iCMS::$config['api']['baidu']['sitemap']['access_token'];

        if(iCMS::$config['plugin']['baidu']['sitemap']){
            $site          = iCMS::$config['plugin']['baidu']['sitemap']['site'];
            $access_token  = iCMS::$config['plugin']['baidu']['sitemap']['access_token'];
        }

        if(empty($site)||empty($access_token)){
            return false;
        }
        $api ='http://data.zz.baidu.com/'.$act.'?site='.$site.'&token='.$access_token;
        $type && $api.='&type='.$type;
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL            => $api,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => implode("\n",(array)$urls),
            CURLOPT_HTTPHEADER     => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        self::$out['ping'] = json_decode($result);
        if(self::$out['ping']->success){
            return true;
        }
        return false;
    }
    /**
     * [xzh description]
     * @param  [type] $urls [description]
     * @param  string $type [realtime:天级收录,batch:周级收录]
     * @param  [type] &$out [description]
     * @return [type]       [description]
     */
    public static function xzh($urls,$type='realtime',&$out=null) {
        $appid = iCMS::$config['plugin']['baidu']['xzh']['appid'];
        $token = iCMS::$config['plugin']['baidu']['xzh']['token'];

        if(empty($appid)||empty($token)){
            return false;
        }
        $api ='http://data.zz.baidu.com/urls?appid='.$appid.'&token='.$token;
        $type && $api.='&type='.$type;
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL            => $api,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => implode("\n",(array)$urls),
            CURLOPT_HTTPHEADER     => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        self::$out['xzh'] = json_decode($result,true);
        if(self::$out['xzh']['error']){
            return false;
        }
        return true;
    }
    /**
     * http://ping.baidu.com/ping.html
     * http://help.baidu.com/question?prod_id=99&class=0&id=3046
     * @param [type] $url [description]
     */
    public static function RPC2($url){
        $pingRpc  = 'http://ping.baidu.com/ping/RPC2';
        $baiduXML = '<?xmlversion="1.0"?>';
        $baiduXML .= '<methodCall>';
        $baiduXML .= '<methodName>weblogUpdates.ping</methodName>';
        $baiduXML .= '<params>';
        $baiduXML .= '<param><value><string>' . $url . '</string></value></param>';
        $baiduXML .= '<param><value><string>' . $url . '</string></value></param>';
        $baiduXML .= '</params>' . "\n";
        $baiduXML .= '</methodCall>';
        $header   = array(
            'Accept: */*',
            'Referer: http://ping.baidu.com/ping.html',
            'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36',
            'Host:ping.baidu.com',
            'Content-Type:text/xml',
        );
        $curl     = curl_init();
        curl_setopt($curl, CURLOPT_URL, $pingRpc);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $baiduXML);
        $xml = curl_exec($curl);
        curl_close($curl);
        self::$out['RPC2'] = iUtils::xmlToArray($xml);
        return self::$out['RPC2']->params->param->value->int?false:true;
    }

}
