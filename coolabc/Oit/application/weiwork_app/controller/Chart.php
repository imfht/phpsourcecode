<?php
namespace app\weiwork_app\controller;

use app\common\controller\WeiWorkBase;
use think\Log;
use think\WeChat;

/**
 * Class Chart
 * 企业微信打卡
 * @package app\weiwork_app\controller
 */
class Chart extends WeiWorkBase {
    /**
     * @return string
     */
    public function index() {

        return $this->fetch();
    }

    // 获取打卡数据
    // 需要设置一个对接密码
    public function get_wei_timer() {
        $work = WeChat::agent('chart');
        $corp = $work->corp;

        $data = [
            'opencheckindatatype' => 3,
            'starttime'           => 1492617600,
            'endtime'             => 1492790400,
            'useridlist'          => ['wb'],
        ];
        //var_dump($corp->getCheckInData($data));
    }

}
