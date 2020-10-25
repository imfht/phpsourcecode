<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/8/23
 * Time: 23:24
 */
namespace app\admin\controller;

use jswei\push\drive\GeTuiService;
use jswei\push\sdk\geTui\igetui\IGtTemplateTye;
use jswei\push\sdk\geTui\igetui\template\notify\IGtNotify;
use jswei\push\sdk\geTui\igetui\utils\AppMessageCondition;
use jswei\push\sdk\geTui\igetui\utils\OptType;

class Push extends Base
{

    protected $isSink = true;
    protected $sinkMethods = ['index'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 推送消息
     * @param int $type
     * @return mixed
     */
    public function index($type = 1)
    {
        $config = config('getui.');
        $push = new GeTuiService($config);
        $push->setLogoURL('https://gitee.com/uploads/69/144269_jswei.png');
        $push->setTitle('测试通知');
        $push->setBody('测试的通知他');
        $push->setLinkUrl('http://baidu.com');
        //设置通知模板
        //IGtTemplateTye::notifyInfo  打开app当setExtendedData时候透传信息
        //IGtTemplateTye::link 链接模板
        //IGtTemplateTye::transmission 透传模板
        //IGtTemplateTye::download 下载模板
        $push->setTemplateType(IGtTemplateTye::link);
        //实现的方法
        /*
         $push->sendAllAndroid();     //向所有安卓平台发送
         $push->sendAllIOS();        //向所有苹果平台发送
         $push->sendAll();           //向所有平台发送
         $push->sendOne('client_id1'); //单台设备发送
         $push->sendToUserList('');  //向指定用户列表发送['client_id1','client_id2'...]

        //其他的一些可以获取个推对象和模板进行操作更多的操作
        // 获取个推对象实例
        //$igeTui = $push->getIGeTui();
        //获取消息模板
        //$template = $push->getCurrentTemplate();*/
        //return $push->sendToUserList(['b0a1bdd6cc90e05dfa9c4104f12f175c'])->getResult();

        return $push
            ->sendAllAndroid()
            ->getResult();
    }
}