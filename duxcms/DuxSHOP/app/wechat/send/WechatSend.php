<?php
namespace app\wechat\send;
/**
 * 微信消息服务
 */
class WechatSend extends \app\base\service\BaseService {

    /**
     * 检查数据
     * @param $data
     * @return bool
     */
    public function check($data) {
        if (!$data['user_status']) {
            return $this->error('微信推送必须为用户类型');
        }
        return $this->success();
    }

    /**
     * 发送服务
     * @param $info
     * @return bool
     */
    public function send($info) {

        $target = target('wechat/Wechat', 'service');
        $target->init();

        if (empty($info['user_info'])) {
            $this->error('用户不存在！');
        }

        $openInfo = target('member/MemberConnect')->getWhereInfo([
            'type' => 'wechat',
            'user_id' => $info['receive'],
        ]);


        if (empty($openInfo)) {
            return $this->error('没有绑定微信账号');
        }

        $wechat = $target->wechat();

        if (empty($openInfo)) {
            return $this->error('没有绑定微信账号');
        }

        $url = $info['param']['url'];

        if (!empty($url)) {
            $data = new \EasyWeChat\Message\News([
                'title' => $info['title'],
                'description' => html_out($info['content']),
                'url' => $info['param']['url']
            ]);
            $data = [$data];
        } else {
            $data = new \EasyWeChat\Message\Text([
                'content' => $info['title'] . "\n" . html_out($info['content']),
            ]);
        }

        try {
            $wechat->staff->message($data)->to($openInfo['open_id'])->send();
        } catch (\Exception $err) {
            return $this->error($err->getMessage());
        }
        return $this->success();

    }

}