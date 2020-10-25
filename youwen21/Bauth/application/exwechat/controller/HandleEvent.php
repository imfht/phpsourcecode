<?php
namespace app\exwechat\controller;

/**
 * 微信事件控制器
 *
 */
class HandleEvent extends AbstractHandle
{

    public function handle($arrayMsg='')
    {
        $msg = empty($arrayMsg) ? $this->exRequest->getMsg() : $arrayMsg;
        if('LOCATION' != $msg['Event'] || 'location_select' != $msg['Event']){
            $this->_saveToDB($msg);
            // 用户场景捕获
            $scene = (new SceneCatch())->getScene($this->msg['FromUserName'], 'location');
        }
        switch ($msg['Event']) {
            // 关注公众号
            case 'subscribe':
            // 
                break;
            // 取消关注公众号
            case 'unsubscribe':
                break;
            // 扫描带参数二维码事件
            case 'scan':break;
            break;
            // 自定义菜单事件
            case 'CLICK':
                $this->response('你点击了菜单: '.$msg['EventKey']);
                break;
            // 模板消息发送成功通知
            case 'TEMPLATESENDJOBFINISH':break;
            // 菜单跳转链接
            case 'VIEW':break;
            // 扫码推事件的事件推送
            case 'scancode_push':
                $this->response('扫码行为: 二维码内容是： '.$msg['ScanCodeInfo']['ScanResult']);
            break;
            // 扫码推事件且弹出“消息接收中”提示框的事件推送
            case 'scancode_waitmsg':
                $this->response('扫码行为: 二维码内容是： '.$msg['ScanCodeInfo']['ScanResult']);
            break;
            // 弹出系统拍照发图的事件推送
            case 'pic_sysphoto':break;
            // 弹出拍照或者相册发图的事件推送
            case 'pic_photo_or_album':break;
            // 弹出微信相册发图器的事件推送
            case 'pic_weixin':break;
            // 上报地理位置事件
            case 'LOCATION':
                $cls = new HandleLocation($msg);
                $ret = $cls->saveToDB($msg);
                if( $scene == 'yes')
                {
                    $text = "微信上报个人位置LOCATION\n";
                    $text .= 'Latitude:'.$msg['Latitude']."\n";
                    $text .= 'Longitude:'.$msg['Longitude']."\n";
                    $text .= 'Longitude:'.$msg['Longitude']."\n";
                    $this->response($text);
                }
            // 菜单弹出地理位置选择器的事件推送
            case 'location_select':
                $cls = new HandleLocation($msg);
                $ret = $cls->saveToDB($msg);
                if($scene == 'yes')
                {
                    $text = "上传个人位置\n";
                    $text .= 'Location_X:'.$msg['SendLocationInfo']['Location_X']."\n";
                    $text .= 'Location_Y:'.$msg['SendLocationInfo']['Location_Y']."\n";
                    $text .= 'Scale:'.$msg['SendLocationInfo']['Scale']."\n";
                    $text .= 'Label:'.$msg['SendLocationInfo']['Label']."\n";
                    $text .= 'Poiname:'.$msg['SendLocationInfo']['Poiname']."\n";
                    $this->response($text);
                }
            break;
            default:
                $this->response('这个类型事件还没开发呢！event ');
        }
        exit; //阻止DEBUG信息输出
    }
    // 保存到数据
    // 对应数据库为 msg_text
    private function _saveToDB($msg='')
    {
        $data['id'] = '';
        $data['status'] = 1;
        foreach ($msg as $key => $value) {
            if(in_array($key, ['ToUserName','FromUserName','CreateTime', 'MsgType', 
                'Event', 'EventKey'])){
                $data[$key] = $value;
                unset($msg[$key]);
            }
        }
        $data['other'] = empty($msg)? '': json_encode($msg);
        if(isset($data['Encrypt'])) unset($data['Encrypt']);
        $ret = db('we_msg_event')->insert($data);
        return $ret;
    }
}
