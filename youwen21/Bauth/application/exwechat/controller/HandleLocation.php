<?php
namespace app\exwechat\controller;

/**
 * 微信地理位置消息－控制器
 * 微信地理位置共有三种消息方式（聊天发送位置｜上报地理位置｜菜单(高级)发送地理位置）
 * 三种位置,MsgType和Event区别是：(location| event & LOCATION| event & location_select)
 * 还有一种位置是在网页中JSSDK位置选取，不知道会传位置不，暂时没用那个功能
 */
class HandleLocation extends AbstractHandle
{

    private $msg;
    /** 
     * 此处是聊天发送个人位置，
     * 上报地址位置和菜单(高级)发送地理位置在HandleEvent中有处理
     */
    public function handle($arrayMsg='', $reylyContext='暂未开发此类型消息')
    {
        $this->msg = empty($arrayMsg) ? $this->exRequest->getMsg() : $arrayMsg;
        $this->saveToDB($this->msg);
        if($this->msg['MsgType']){

        }
        $scene = $this->getScene($this->msg['FromUserName'], 'location');
        if( $scene && $scene['sceneValue'] == 'yes')
        {
            $text = "聊天中的个人位置\n";
            $text .= 'Location_X:'.$this->msg['Location_X']."\n";
            $text .= 'Location_Y:'.$this->msg['Location_Y']."\n";
            $text .= 'Scale:'.$this->msg['Scale']."\n";
            $text .= 'Label:'.$this->msg['Label']."\n";
            // $text .= 'Poiname:'.$this->msg['Poiname'];
            $this->response($text);
        }
        exit; //阻止DEBUG信息输出
    }

    // 保存到数据
    // 对应数据库为 msg_text
    public function saveToDB($msg='')
    {
        $data['id'] = '';
        $data['status'] = 1;
        if('location' == $msg['MsgType']){
            $location = $this->locationChat($msg);
        }else{
            if('location_select' == $msg['Event']){
                $location = $this->locationSelect($msg);
            }else{
                $location = $this->locationReport($msg);
            }
        }
        $data = array_merge($location,$data);
        $ret = db('we_msg_location')->insert($data);
        return $ret;
    }

    // 聊天点+号 `位置` 
    public function locationChat($msg)
    {
        $location = [];
        $location['ToUserName'] = $msg['ToUserName'];
        $location['FromUserName'] = $msg['FromUserName'];
        $location['CreateTime'] = $msg['CreateTime'];
        $location['MsgType'] = $msg['MsgType'];
        // $location['Event'] = null;
        // $location['EventKey'] = null;
        $location['latitude'] = $msg['Location_X']; // 纬度
        $location['longitude'] = $msg['Location_Y']; // 经度
        $location['accuracy'] = $msg['Scale']; // 精度
        // $location['altitude'] = null;
        $location['Label'] = $msg['Label'];
        $location['MsgId'] = $msg['MsgId'];
        return $location;
    }

    // 菜单(高级)发送地理位置
    // 个人测试，菜单发送地理位置会上报三次！上报位置，聊天位置，菜单位置分别上传一次！
    // 选择位置发送，会发送聊天位置和菜单地理位置
    // 返回聊天界面，会上报地理位置， 两者几乎在同一时间
    public function locationSelect($msg)
    {
        $location = [];
        $location['ToUserName'] = $msg['ToUserName'];
        $location['FromUserName'] = $msg['FromUserName'];
        $location['CreateTime'] = $msg['CreateTime'];
        $location['MsgType'] = $msg['MsgType'];
        $location['Event'] = $msg['Event'];
        $location['EventKey'] = $msg['EventKey'];
        $location['latitude'] = $msg['SendLocationInfo']['Location_X']; // 纬度
        $location['longitude'] = $msg['SendLocationInfo']['Location_Y']; // 经度
        $location['accuracy'] = $msg['SendLocationInfo']['Scale']; // 精度
        // $location['altitude'] = null;
        $location['Label'] = $msg['SendLocationInfo']['Label'];
        $location['Poiname'] = $msg['SendLocationInfo']['Poiname'];
        // $location['MsgId'] = null;
        return $location;
    }

    // 微信上报地理位置（5秒一次或打开公众号聊天窗上报）
    // 打开聊天会上报，发送图片返回聊天会上报，打开网页返回聊天会上报，总之每次进聊天都会上报
    public function locationReport($msg)
    {
        $location = [];
        $location['ToUserName'] = $msg['ToUserName'];
        $location['FromUserName'] = $msg['FromUserName'];
        $location['CreateTime'] = $msg['CreateTime'];
        $location['MsgType'] = $msg['MsgType'];
        $location['Event'] = $msg['Event'];
        // $location['EventKey'] = null;
        $location['latitude'] = $msg['Latitude']; // 纬度
        $location['longitude'] = $msg['Longitude']; // 经度
        $location['accuracy'] = $msg['Precision']; // 精度
        // $location['altitude'] = null;
        // $location['Label'] = null;
        // $location['Poiname'] = null;
        // $location['MsgId'] = null;
        return $location;
    }
}
