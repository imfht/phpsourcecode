<?php

/**
 * 微信响应接口
 */

namespace app\wechat\api;

class AppApi {

    public $wechat = null;
    public $config = [];

    public function __construct() {
        $target = target('wechat/Wechat', 'service');
        $target->init();
        $this->wechat = $target->wechat();
        $this->config = $target->config();
    }

    /**
     * 统一接口处理
     */
    public function index() {
        dux_log('访问微信接口');
        $server = $this->wechat->server;
        $server->setMessageHandler(function ($message) {
            $config = target('wechat/WechatReplyConfig')->getConfig();
            $reply = '';
            switch ($message->MsgType) {
                case 'text':
                    $reply = $this->msgText($message->Content);
                    break;
                case 'event':
                    if ($message->Event == 'subscribe') {
                        $reply = $config['msg_welcome'];
                    }
                    if ($message->Event == 'CLICK') {
                        $reply = $this->msgText($message->EventKey);
                    }
                    break;
            }
            if (empty($reply)) {
                $reply = $config['msg_default'];
            }
            return $reply;
        });
        $response = $server->serve();
        $response->send();
    }

    private function msgText($message) {
        $list = target('wechat/WechatReply')->loadList([], 0, 'priority desc, reply_id desc');
        $exactList = [];
        $fuzzyList = [];
        $type = '';
        $replyId = 0;
        foreach ($list as $vo) {
            if ($vo['match']) {
                $exactList[] = $vo;
            } else {
                $fuzzyList[] = $vo;
            }
        }
        foreach ($exactList as $vo) {
            $keywords = explode(',', $vo['keywords']);
            if (in_array($message, $keywords)) {
                $type = $vo['type'];
                $replyId = $vo['reply_id'];
                break;
            }
        }
        foreach ($fuzzyList as $vo) {
            $keywords = explode(',', $vo['keywords']);
            foreach ($keywords as $v) {
                if (strpos($message, $v) !== false) {
                    $type = $vo['type'];
                    $replyId = $vo['reply_id'];
                    break 2;
                }
            }
        }

        if (!$replyId) {
            return '';
        }
        $mediaArray = ['image', 'video', 'voice', 'news', 'article'];
        if (in_array($type, $mediaArray)) {
            $type = 'media';
        }

        $info = target('wechat/WechatReply' . ucfirst($type))->getReply($replyId);
        if (empty($info)) {
            return '';
        }

        $data = null;
        switch ($info['type']) {
            case 'text':
                $data = new \EasyWeChat\Message\Text([
                    'content' => $info['content']
                ]);
                break;
            case 'image':
                $data = new \EasyWeChat\Message\Image([
                    'media_id' => $info['media_id']
                ]);
                break;
            case 'video':
                $data = new \EasyWeChat\Message\Video([
                    'media_id' => $info['media_id'],
                    'title' => $info['title'],
                    'description' => $info['description']
                ]);
                break;
            case 'voice':
                $data = new \EasyWeChat\Message\Voice([
                    'media_id' => $info['media_id']
                ]);
                break;
            case 'news':
                $data = [];
                foreach ($info['data'] as $vo) {
                    $data[] = new \EasyWeChat\Message\News($vo);
                }
                break;
            case 'article':
                $data = new \EasyWeChat\Message\Article($info['data']);
                break;
        }


        return $data;

    }

}