<?php
require dirname(__FILE__) . '/../Wechat/Robot.php';

class MyRobot extends Wechat_Robot {

    protected function handleText($inMessage, &$outMessage)
    {
        $outMessage = new Wechat_OutMessage_Text();
        $outMessage->setContent('Hello World!');
    }

    protected function handleImage($inMessage, &$outMessage)
    {
        throw new Exception('功能正在开发中，敬请期待~~~');
    }

    protected function handleVoice($inMessage, &$outMessage)
    {
        throw new Exception('功能正在开发中，敬请期待~~~');
    }

    protected function handleVideo($inMessage, &$outMessage)
    {
        throw new Exception('功能正在开发中，敬请期待~~~');
    }

    protected function handleLocation($inMessage, &$outMessage)
    {
        throw new Exception('功能正在开发中，敬请期待~~~');
    }

    protected function handleLink($inMessage, &$outMessage)
    {
        throw new Exception('功能正在开发中，敬请期待~~~');
    }

    protected function handleEvent($inMessage, &$outMessage)
    {
        throw new Exception('功能正在开发中，敬请期待~~~');
    }

    protected function handleDeviceEvent($inMessage, &$outMessage)
    {
        throw new Exception('功能正在开发中，敬请期待~~~');
    }

    protected function handleDeviceText($inMessage, &$outMessage)
    {
        throw new Exception('功能正在开发中，敬请期待~~~');
    }
}
