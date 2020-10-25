<?php

namespace Addons\SignIn;

use Common\Controller\Addon;
use Think\Db;
/**
 * 签到插件
 * @author 想天软件工作室
 */

class SignInAddon extends Addon
{

    public $info = array(
        'name' => 'SignIn',
        'title' => '签到',
        'description' => '签到积分',
        'status' => 1,
        'author' => '翟小斐',
        'version' => '1.0'
    );

    public $admin_list = array(
        'model' => 'SignIn', //要查的表
        'fields' => '*', //要查的字段
        'map' => '', //查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
        'order' => 'uid desc', //排序,
        'list_grid' => array( //这里定义的是除了id序号外的表格里字段显示的表头名
            'uid:UID',
            'con_num:连续签到次数',
            'total_num:总签到次数',
            'create_time:签到时间',
        ),
    );

    public function install()
    {
        $sql ="CREATE TABLE IF NOT EXISTS `ke_sign_in` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,`uid` int(11) NOT NULL,`con_num` int(11) DEFAULT '0',`total_num` int(11) DEFAULT '0',`create_time` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8";
        $orginal = C('ORIGINAL_TABLE_PREFIX');
        $prefix  = C('DB_PREFIX');
        $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);
        $db = Db::getInstance();
        $res = $db->execute($sql);
        if(false!==$res){
            return true;
        }else {
            return false;
        }
    }

    public function uninstall()
    {   $sql = "DROP TABLE IF EXISTS `ke_sign_in`";
        $orginal = C('ORIGINAL_TABLE_PREFIX');
        $prefix  = C('DB_PREFIX');
        $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);
        $db = Db::getInstance();
        $res = $db->execute($sql);
        if(false!==$res){
            return true;
        }else {
            return false;
        }
    }

    //实现的SignIn钩子方法
    public function sign_in($param)
    {
        $uid = is_login();

        $data =  s('sign_info_');
        if (!$data) {
            $map['uid'] = $uid;
            $map['create_time'] = array('gt', strtotime(date('Ymd')));
            $res = D('SignIn')->where($map)->find();  
            //是否签到
            $data['issign'] = $res ? true : false;
            $signInfo = D('SignIn')->where('uid=' . $uid)->order('create_time desc')->limit(1)->find();
          
            if ($signInfo) {
                if ($signInfo['create_time'] > (strtotime(date('Ymd')) - 86400)) {
                    $data['con_num'] = $signInfo['con_num'];
                } else {
                    $data['con_num'] =1;
                }
                $data['total_num'] = $signInfo['total_num'];
            } else {
                $data['con_num'] = 1;
                $data['total_num'] = 1;
            }
            $data['day'] = date('m.d');
            
            S('a','sign_info_');
           
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
       
       $this->assign("sign",$data);
       $uid =is_login();

       $list = D('SignIn')->where('uid='.$uid)->order('create_time desc')->count();

       $login= is_login() ? true : false;
       if(!$login) {


           $this->display('View/default');

            }
        elseif ($list==0) {

            $default=0;

            $this->assign("connum",$default);
            $this->assign("totalnum",$signInfo['total_num']);
            $this->display('View/signin');

        }

        else{
            
            $map['key'] = "sign_connum";
            $map['uid'] = $uid;

            $signInfo = D('SignIn')->where('uid='.$uid)->order('create_time desc')->find();
            $this->assign("connum",$signInfo['con_num']);
            $this->assign("totalnum",$signInfo['total_num']);
            $total['key'] = "sign_totalnum";
            $total['uid'] = $uid;
            $this->display('View/signin');



        }

    }
    //实现的Rank钩子方法
    public function rank($param)
    {
    
        $getranktime = $this->getConfig();
        $set_ranktime = $getranktime['ranktime'];
    
        $y = date("Y", time());
        $m = date("m", time());
        $d = date("d", time());
    
    
        $start_time = mktime($set_ranktime, 0, 0, $m, $d, $y);
        $this->assign("ss", $start_time);
        $rank=S('check_rank');
        if(empty($rank)){
            $rank = D('SignIn')->where('create_time>' . $start_time)->order('create_time asc')->limit(5)->select();
            S('check_rank',$rank,60);
        }
    
        if (time() <= $start_time) {
            $this->assign("time", $set_ranktime);
            $this->display('default');
        } else {
            foreach ($rank as &$v) {
                $v['userInfo'] = D('Member')->info($v['uid']);
    
            }
            $this->assign("rank", $rank);
            $this->display('View/rank');
        }
    
    
    }

}








