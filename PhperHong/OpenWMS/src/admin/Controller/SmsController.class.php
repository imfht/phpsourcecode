<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Controller;
use Think\Controller;
use Think\Exception;
class SmsController extends BaseController {
    /**
     +----------------------------------------------------------
     * 手机号列表
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function phone_list(){
        $pagenum = I('get.p');
        $pagelen = I('get.pagelen');
        $sortkey = I('get.sortkey');
        $reverse = I('get.reverse');

        
        $where = array(
            'username'     => I('get.username'),
        );
        $return_data    = array();
        try {
            $client         = D('Client');
            $list = $client->get_client_phone_list_by_mid($pagenum, $pagelen, $sortkey, $reverse, $where);
            $cop = C('COPYRIGHT');
            $where['pagelen'] = $pagelen;
            $where['sortkey'] = $sortkey;
            $where['reverse'] = $reverse;

            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-手机号列表',
                'rows'              => $list['list'],
                'count'             => $list['count'],
                'pagelen'           => $pagelen,
                'param'             => $where,
                'breadcrumb'        => '&gt;短信日志&gt;手机用户管理'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
    }
    /**
     +----------------------------------------------------------
     * 发送短信
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function send_sms(){
        $phone_list = I('post.phone_list');
        $send_msg = I('post.send_msg');

        $return_data    = array();
        try {
            $sms_log         = D('SmsLog');
            $sms_log->send_sms($phone_list, $send_msg);
            $return_data = array(
                'ret'           => 1,
                'msg'           => '发送成功',
            );
        } catch (Exception $e) {
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
                'info'  => false,
            );
        }
        exit(json_encode($return_data));
    }
    /**
     +----------------------------------------------------------
     * 短信发送日志列表
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function sms_log_list(){

        $pagenum = I('get.p');
        $pagelen = I('get.pagelen');
        $sortkey = I('get.sortkey');
        $reverse = I('get.reverse');

        
        $where = array(
            'phonenumber'     => I('get.phonenumber'),
        );
        $return_data    = array();
        try {
            $sms_log         = D('SmsLog');
            $list = $sms_log->get_sms_log_by_userid($pagenum, $pagelen, $sortkey, $reverse, $where);
            $cop = C('COPYRIGHT');
            $where['pagelen'] = $pagelen;
            $where['sortkey'] = $sortkey;
            $where['reverse'] = $reverse;
            
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-手机号列表',
                'rows'              => $list['list'],
                'count'             => $list['count'],
                'pagelen'           => $pagelen,
                'param'             => $where,
                'breadcrumb'        => '&gt;短信日志&gt;手机号列表'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
    }
}