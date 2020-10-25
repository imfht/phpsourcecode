<?php

namespace Addons\Checkin;

use Common\Controller\Addon;

/**
 * 签到插件
 * @author 嘉兴想天信息科技有限公司
 */
class CheckinAddon extends Addon
{

    public $info = array(
        'name' => 'Checkin',
        'title' => '签到',
        'description' => '签到积分',
        'status' => 1,
        'author' => '嘉兴想天信息科技有限公司',
        'version' => '0.1'
    );

    public $admin_list = array(
        'model' => 'Check_info', //要查的表
        'fields' => '*', //要查的字段
        'map' => '', //查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
        'order' => 'uid desc', //排序,
        'listKey' => array( //这里定义的是除了id序号外的表格里字段显示的表头名
            'uid' => 'UID',
            'con_num' => '连续签到次数',
            'total_num' => '总签到次数',
            'ctime' => '签到时间',
        ),
    );

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    //实现的checkin钩子方法
    public function checkin($param)
    {


        /*  $getranktime= $this->getConfig();
          $set_ranktime=$getranktime['ranktime'] ;*/

        $uid = is_login();

        $data = S('check_info_'); //model('Cache')->get('check_info_' . $uid . '_' . date('Ymd'));
        if (!$data) {
            $map['uid'] = $uid;
            $map['ctime'] = array('gt', strtotime(date('Ymd')));
            $res = D('Check_info')->where($map)->find();
            //是否签到
            $data['ischeck'] = $res ? true : false;

            $checkinfo = D('Check_info')->where('uid=' . $uid)->order('ctime desc')->limit(1)->find();
            if ($checkinfo) {
                if ($checkinfo['ctime'] > (strtotime(date('Ymd')) - 86400)) {
                    $data['con_num'] = $checkinfo['con_num'];
                } else {
                    $data['con_num'] = 1;
                }
                $data['total_num'] = $checkinfo['total_num'];
            } else {
                $data['con_num'] = 1;
                $data['total_num'] = 1;
            }
            $data['day'] = date('m.d');
            S('a', 'check_info_');
        }

        $data['tpl'] = 'index';
        $week = date('w');
        switch ($week) {
            case '0':
                $week = '周日';
                break;
            case '1':
                $week = '周一';
                break;
            case '2':
                $week = '周二';
                break;
            case '3':
                $week = '周三';
                break;
            case '4':
                $week = '周四';
                break;
            case '5':
                $week = '周五';
                break;
            case '6':
                $week = '周六';
                break;
        }
        $data['week'] = $week;
        $this->assign("check", $data);


        $uid = is_login();

        $list = D('Check_info')->where('uid=' . $uid)->order('ctime desc')->count();

        $login = is_login() ? true : false;


        if (!$login) {


            $this->display('View/default');

        } elseif ($list == 0) {

            $default = 0;


            $this->assign("connum", $default);
            $this->assign("totalnum", $checkinfo['total_num']);
            $this->display('View/checkin');

        } else {

            $map['key'] = "check_connum";
            $map['uid'] = $uid;

            $checkinfo = D('Check_info')->where('uid=' . $uid)->order('ctime desc')->find();
            $this->assign("connum", $checkinfo['con_num']);
            $this->assign("totalnum", $checkinfo['total_num']);
            $this->assign("lxqd", $checkcon['0']['value']);

            $total['key'] = "check_totalnum";
            $total['uid'] = $uid;

            $this->assign("zgqd", $checktotal['0']['value']);


            /*计算超越*/
            $over_rate=S('check_in_over_rate_'.is_login());
            if(empty($over_rate)){
                $over_rate=$this->getOverRate($checkinfo);
                S('check_in_over_rate_'.is_login(),$over_rate,60);
            }
            $this->assign('over_rate',$over_rate );
            $this->display('View/checkin');
        }

    }


    public function getHtml(){

        $uid = is_login();

        $data = S('check_info_'); //model('Cache')->get('check_info_' . $uid . '_' . date('Ymd'));
        if (!$data) {
            $map['uid'] = $uid;
            $map['ctime'] = array('gt', strtotime(date('Ymd')));
            $res = D('Check_info')->where($map)->find();
            //是否签到
            $data['ischeck'] = $res ? true : false;

            $checkinfo = D('Check_info')->where('uid=' . $uid)->order('ctime desc')->limit(1)->find();
            if ($checkinfo) {
                if ($checkinfo['ctime'] > (strtotime(date('Ymd')) - 86400)) {
                    $data['con_num'] = $checkinfo['con_num'];
                } else {
                    $data['con_num'] = 1;
                }
                $data['total_num'] = $checkinfo['total_num'];
            } else {
                $data['con_num'] = 1;
                $data['total_num'] = 1;
            }
            $data['day'] = date('m.d');
            S('a', 'check_info_');
        }

        $data['tpl'] = 'index';
        $week = date('w');
        switch ($week) {
            case '0':
                $week = '周日';
                break;
            case '1':
                $week = '周一';
                break;
            case '2':
                $week = '周二';
                break;
            case '3':
                $week = '周三';
                break;
            case '4':
                $week = '周四';
                break;
            case '5':
                $week = '周五';
                break;
            case '6':
                $week = '周六';
                break;
        }
        $data['week'] = $week;
        $this->assign("check", $data);


        $uid = is_login();

        $list = D('Check_info')->where('uid=' . $uid)->order('ctime desc')->count();

        $login = is_login() ? true : false;


        if (!$login) {


            $this->display('View/default');

        } elseif ($list == 0) {

            $default = 0;


            $this->assign("connum", $default);
            $this->assign("totalnum", $checkinfo['total_num']);
            return $this->fetch('View/checkin');

        } else {

            $map['key'] = "check_connum";
            $map['uid'] = $uid;

            $checkinfo = D('Check_info')->where('uid=' . $uid)->order('ctime desc')->find();
            $this->assign("connum", $checkinfo['con_num']);
            $this->assign("totalnum", $checkinfo['total_num']);
            $this->assign("lxqd", $checkcon['0']['value']);

            $total['key'] = "check_totalnum";
            $total['uid'] = $uid;

            $this->assign("zgqd", $checktotal['0']['value']);


            /*计算超越*/
            $over_rate=S('check_in_over_rate_'.is_login());
            if(empty($over_rate)){
                $over_rate=$this->getOverRate($checkinfo);
                S('check_in_over_rate_'.is_login(),$over_rate,60);
            }
            $this->assign('over_rate',$over_rate );
           return $this->fetch('View/checkin');
        }
    }

    /**
     * @param $checkinfo
     * @auth 陈一枭
     */
    private function getOverRate($checkinfo)
    {
        $db_prefix = C('DB_PREFIX');
        $over_count = D()->query("select count(uid)  AS rank from (SELECT *,max(total_num) as total FROM `{$db_prefix}check_info`  WHERE 1 group by uid ) as checkin where total>{$checkinfo['total_num']}");

        $users_count = D('Member')->count('uid');
        $over_rate =((1- number_format($over_count[0]['rank']  / $users_count, '3'))*100)."%";
        return $over_rate;
    }

}