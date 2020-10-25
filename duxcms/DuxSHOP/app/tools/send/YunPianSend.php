<?php
namespace app\tools\send;
/**
 * 短信发送服务
 */
class YunPianSend extends \app\base\service\BaseService {

    /**
     * 检查数据
     * @param $data
     * @return bool
     */
    public function check($data) {
        if($data['user_status']) {
            return $this->success();
        }
        if (!preg_match("/(^1[3|4|5|7|8][0-9]{9}$)/", $data['receive'])) {
            return $this->error('手机号码不正确');
        }
        return $this->success();
    }

    /**
     * 发送接口
     * @param $info
     * @return bool
     */
    public function send($info) {
        $config = target('tools/ToolsSendConfig')->getConfig('yunpian');
        if(empty($config)){
            return $this->error('配置不存在!');
        }
        $receive = $info['receive'];
        if($info['user_info']) {
            $receive = $info['user_info']['tel'];
        }
        $data = array();
        $data['apikey'] = $config['apikey'];
        $data['mobile'] = $receive;
        $data['text'] = '【' . $config['label'] . '】' . html_clear(html_out($info['content']));
        $return = \dux\lib\Http::doPost('https://sms.yunpian.com/v1/sms/send.json', $data, 10);
        $return = json_decode($return, true);
        if($return['msg'] == 'OK'){
            return $this->success();
        }else{
            return $this->error($return['msg'] . $return['detail']);
        }
    }

}
