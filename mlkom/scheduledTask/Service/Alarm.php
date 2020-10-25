<?php

namespace Service;

/**
 * 报警系统
 * 通知方式：短信通知、RTX通知、邮件通知
 *
 * @author: moxiaobai
 * @since : 2015/5/11 15:15
 */

use Library\Db\Mysql;

class Alarm {

    /**
     * 报警：通知程序负责人处理
     *
     * @param $uid     程序员用户ID
     * @todo 通知方式自己实现
     * @return mixed
     */
    public static function noticeProgrammer($params) {
        $url     = 'http://sc.ftqq.com/SCU60T29db0e2a2c17d9ed4ec3e77d5a41a2055eee817beab5.send';

        $content = '';
        foreach($params as $val) {
            $content .= "* {$val} <br />";
        }
        $data  = array('text' => '定时程序执行失败', 'desp' => $content);
        return self::httpRequest($url, $data);
    }


    /**
     * 报警：通知运维人员处理
     *
     * @todo   通知方式自己实现
     * @return mixed
     */
    public static function noticeOperational() {
        $url = 'http://sc.ftqq.com/SCU60T29db0e2a2c17d9e3d4ec3e77da41a2055eee817beab5.send';
        $data = array('text' =>'定时任务系统关闭');

        return self::httpRequest($url, $data);

    }

    /**
     * 获取程序员用户信息
     *
     * @param $uid
     * @return mixed
     * @throws Exception
     */
    private function getProgrammerInfo($uid) {
        $db  = Mysql::instance('cron');

        $sql = $db->select('u_phone')->from('t_user')->where('u_id', $uid);

        $row = $db->fetchValue($sql);

        return $row;
    }

    private function httpRequest($url, $data) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }
}