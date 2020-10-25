<?php

namespace Home\Widget;
use Think\Controller;

/**
 * 微信文字处理
 */
class TextWidget extends Controller {

    public function index($data){
    	$result = array(
            'MsgType' => 'text',
            'Content' => "如果您不是留言的话……\n\r\n您可以发送“help”或者“？”获取使用帮助。\n\r\n如果您确定要留言，您放心，您的留言我已经收到，我会尽快回复你的。\n\r\n谢谢您的支持！^_^"
        );
    	$wx = new \Common\Lib\Weixin\Weixin();
    	if ( $data['Content'] == '退出' ) {
    		$wx->setValue('do',null);
    		$result = array(
                'MsgType' => 'text',
                'Content' => '您已经成功退出！'
            );
            return $wx->toWeixin($result);
    	}
        if ( $data['Content'] == '谢谢' ) {
        	$wx->setValue('do',null);
            $result = array(
                'MsgType' => 'text',
                'Content' => '不用谢，这是我们应该做的！感谢您的支持！'
            );
            return $wx->toWeixin($result);
        }
        if ( $data['Content'] == '快递' ) {
        	$wx->setValue('do', 'express');
        	$result = array(
                'MsgType' => 'text',
                'Content' => '请以 快递公司名称!快递单号 的格式输入您需要查询的快递（如：汇通!210541983361）'
            );
            return $wx->toWeixin($result);
        }
        if ( $data['Content'] == '翻译' ) {
        	$wx->setValue('do', 'translate');
        	$result = array(
                'MsgType' => 'text',
                'Content' => '请输入要翻译的词语或短语（中英文皆可）'
            );
            return $wx->toWeixin($result);
        }
        if ( $data['Content'] == '天气' ) {
        	$result = $wx->weather();
            return $wx->toWeixin($result);
        }
        
        /* 以下为API函数调用处理 */
        $lastdo = $wx->getValue('do');
        if ( $lastdo == 'express' ) {
        	$result = $wx->express($data['Content']);
        }
        if ( $lastdo == 'translate' ) {
        	$result = $wx->translate($data['Content']);
        }

        return $wx->toWeixin($result);
    }

    public function cxkj($data){
    	$wx = new \Common\Lib\Weixin\Weixin();
    	$result = array(
            'MsgType' => 'text',
            'Content' => "如果您不是留言的话……\n\r\n您可以发送“help”或者“？”获取使用帮助。\n\r\n如果您确定要留言，您放心，您的留言我已经收到，我会尽快回复你的。\n\r\n谢谢您的支持！^_^"
        );
        if ( $data['Content'] == '谢谢') {
            $result = array(
                'MsgType' => 'text',
                'Content' => '不用谢，这是我们应该做的！感谢您的支持！'
            );
        }
        if ( $data['Content'] == '测试') {
            $result = array(
                'MsgType' => 'news',
                'Content' => array(
                	array(
                		'香港环境保护协会网络中心建设',
                		'',
                		'http://wx.yanda.net.cn/uploads/cxkj/image/hkepa.png',
                		'http://hkepa.ysder.com'
                	),
                	array(
                		'唯尔易购B2C商城',
                		'',
                		'http://wx.yanda.net.cn/uploads/cxkj/image/hkepa200.png',
                		'http://www.wellego.com'
                	),
                ),
            );
        }
        
        return $wx->toWeixin($result);
    }

    public function hkepa($data)
    {
    	$result = array(
            'MsgType' => 'text',
            'Content' => "您输入的指令不能识别，请输入正确指令："
        );
    	$wx = new \Common\Lib\Weixin\Weixin();
    	if ( $data['Content'] == '退出' ) {
    		$wx->setValue('do',null);
    		$result = array(
                'MsgType' => 'text',
                'Content' => '您已经成功退出！'
            );
            return $wx->toWeixin($result);
    	}
        if ( $data['Content'] == '谢谢' ) {
        	$wx->setValue('do',null);
            $result = array(
                'MsgType' => 'text',
                'Content' => '不用谢，这是我们应该做的！感谢您的支持！'
            );
            return $wx->toWeixin($result);
        }
        if ( $data['Content'] == '快递' ) {
        	$wx->setValue('do', 'express');
        	$result = array(
                'MsgType' => 'text',
                'Content' => '请以 快递公司名称!快递单号 的格式输入您需要查询的快递（如：汇通!210541983361）'
            );
            return $wx->toWeixin($result);
        }
        if ( $data['Content'] == '翻译' ) {
        	$wx->setValue('do', 'translate');
        	$result = array(
                'MsgType' => 'text',
                'Content' => '请输入要翻译的词语或短语（中英文皆可）'
            );
            return $wx->toWeixin($result);
        }
        if ( $data['Content'] == '天气' ) {
        	$result = $wx->weather();
            return $wx->toWeixin($result);
        }
        
        /* 以下为API函数调用处理 */
        $lastdo = $data['lastdo'];
        if ( $lastdo == 'express' ) {
        	$result = $wx->express($data['Content']);
        }
        if ( $lastdo == 'translate' ) {
        	$result = $wx->translate($data['Content']);
        }

        return $wx->toWeixin($result);
    }

}