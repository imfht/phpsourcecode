<?php
namespace app\exwechat\controller;

use youwen\exwechat\exRequest;
use youwen\exwechat\exXMLMaker;
/**
 * 微信消息父控制器
 * 定义基本方法
 * @author baiyouwen <youwen21@yeah.net>
 */
abstract class AbstractHandle
{
    // exRequest对象  方便子类调用
    protected $exRequest;

    public function __construct()
    {
        if(is_null($this->exRequest)){
            $this->exRequest = exRequest::instance();
        }
    }

    // 此方法用对象更合式
    // 子类必须实现此方法的业务
    public function handel()
    {
    }

    /**
     * 响应信息输出
     * @param  [type] $augment 要返回的内容
     * @param  string $type    输出的信息类型
     * @author baiyouwen
     */
    public function response($augment, $type='text')
    {
        switch ($type) {
            case 'text':
                echo (new exXMLMaker())->createText($augment);
                break;
            case 'news':
                echo (new exXMLMaker())->createNews($augment);
                break;
            default:
                echo (new exXMLMaker())->createText('回复消息体类型不可解析');
                break;
        }
        exit;
    }
}
