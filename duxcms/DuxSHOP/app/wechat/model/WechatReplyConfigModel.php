<?php

/**
 * 回复设置
 */
namespace app\wechat\model;

use app\system\model\SystemModel;

class WechatReplyConfigModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'config_id',
        'into' => '',
        'out' => '',
    ];

    public function getConfig() {
        $list = $this->loadList();
        $data = array();
        foreach($list as $vo) {
            $data[$vo['name']] = $vo['content'];
        }
        return $data;
    }

    public function saveInfo() {
        $post = request('post');
        foreach ($post as $key => $value) {
            $where = array();
            $where['name'] = $key;
            $data = array();
            $content = str_replace('&nbsp;', '', $value);
            $content = str_replace('<br>', "\n", $content);
            $xss = new \dux\vendor\HtmlCleaner(['a'], ['href'], 0, 0);
            $data['content'] = $xss->remove($content);
            if(!$this->data($data)->where($where)->update()){
                return false;
            }
        }
        return true;
    }


}