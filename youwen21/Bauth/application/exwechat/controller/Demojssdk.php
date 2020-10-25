<?php

namespace app\exwechat\controller;

use think\Controller;
use youwen\exwechat\api\JSSDK\JSSDK;

/**
 * JSSDK开发案例
 */
class Demojssdk extends Controller
{
    public function test()
    {
        // echo '<pre>';
        // print_r( $this->request->url(true) );
        // exit('</pre>');
        echo '<pre>';
        print_r( sha1('jsapi_ticket=sM4AOVdWfPE4DxkXGEs8VA7chf1JZ70Hna9eQyhe7nt8mccytSsdO1rByJOCi5Wz5TlRtO2FsLWo8GMRCvcUig&noncestr=123456&timestamp=1488638082&url=http://demo.bauth.exwechat.com/index.php?s=/exwechat/Demojssdk/index') );
        exit('</pre>');
    }

    public function test1()
    {
        $jsapi_ticket = 'sM4AOVdWfPE4DxkXGEs8VA7chf1JZ70Hna9eQyhe7nt8mccytSsdO1rByJOCi5Wz5TlRtO2FsLWo8GMRCvcUig';
        $nonceStr = '123456';
        $timestamp = 1488638082;
        $url = 'http://demo.bauth.exwechat.com/index.php?s=/exwechat/Demojssdk/index';
        // $url = 'http://bauth.com/index.php?s=/exwechat/Demojssdk/test1';
        $str = "jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $sign = sha1($str);
        $class = new JSSDK('123');

        $url2 = $this->request->url(true);
        $signature = $class->signature($jsapi_ticket, $nonceStr, $timestamp, $url2 );
        
        echo '<pre>';
        echo $url, '<br/>', $url2, '<br/>';
        print_r( $sign );
        echo '<br/>';
        print_r( $signature );
        exit('</pre>');
    }

    /**
     * 获取到jsapi_ticket应缓存本地
     * @return [type] [description]
     * @author baiyouwen
     */
    public function index()
    {
        $token = 'ohU0c5NYqySsnLRclt7-so6F5L6qpksZ1Int5s0sHclh9QVLm5tDnG4oMqHi8XH5YwHjKF5_FeMoijARXF1rvrQVeDxDcZyICRq1CpM7MISz7jHF-Ig3cl8uMUatGPEAXJMcABAPBE';
        $class = new JSSDK($token);
        $appId = 'wx70fe57dfaad1a35f';
        $timestamp = time();
        $nonceStr = $class->createNonceStr();
        // $jsapi_ticket = $class->get_jsapi_ticket();
        $jsapi_ticket = 'sM4AOVdWfPE4DxkXGEs8VA7chf1JZ70Hna9eQyhe7nt8mccytSsdO1rByJOCi5Wz5TlRtO2FsLWo8GMRCvcUig';
        $url = $this->request->url(true);
        // $url = 'http://demo.bauth.exwechat.com/index.php?s=/exwechat/Demojssdk/index';
        $signature = $class->signature($jsapi_ticket, $nonceStr, $timestamp, $url);
        $jsApiList = $class->jsApiList();
        $this->assign('appId', $appId);
        $this->assign('timestamp', $timestamp);
        $this->assign('nonceStr', $nonceStr);
        $this->assign('signature', $signature);
        $this->assign('jsApiList', $jsApiList);
        return $this->fetch();
    }

    public function get_jsapi_ticket()
    {
        $class = new JSSDK($_GET['token']);
        $ret = $class->get_jsapi_ticket();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

}
