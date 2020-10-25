<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM5:41
 */




namespace Addons\Mail\Controller;
use Home\Controller\AddonsController;
use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;

/**
 * 邮件订阅模块
 * Class MailController
 * @package Addons\Mail\Controller
 * @author:xjw129xjt xjt@ourstu.com
 */
class MailController extends AddonsController
{

    /**
     * 后台首页-邮件配置
     * @param string $id
     * autor:xjw129xjt
     */
    public function saveConfig()
    {

        if($_POST['config'] && is_array($_POST['config'])){
            $Config = M('Config');
            foreach ($_POST['config'] as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA',null);
            $this->success('编辑成功。');
    }

    /**
     * 发送测试邮件
     * autor:xjw129xjt
     */
    public function sendTestMail()
    {
        //发送邮件
        $res = send_mail();
        if ($res) {
            $this->success('发送测试邮件成功');
        } else {
            $this->error('发送失败');
        }
    }

    /**
     * 邮箱列表
     * @param string $address
     * autor:xjw129xjt
     */
    public function mailList()
    {
        $address = op_t(I('address'));
        $map = array('status' => 1);
        if ($address != '')
            $map['address'] = array('like', '%' . $address . '%');
        $mailList = D('MailList')->where($map)->select();
        $this->assign('mailList', $mailList);
        $this->assign('current','list');

        $this->display(T('Addons://Mail@Mail/mailList'));
    }

    /**
     * 添加邮箱
     * @param string $address
     * autor:xjw129xjt
     */
    public function addEmail()
    {
        $address = op_t(I('address'));
        if (IS_POST) {
            $check =  D('MailList')->where(array('address'=>$address))->find();
            if($check){
                if($check['status']){
                    $this->error('该邮箱已经存在');
                }
                else{
                   $res = D('MailList')->where(array('address'=>$address))->setField('status', 1);
                }
            }else{
                $res = D('MailList')->add(array('address' => op_h($address), 'status' => 1, 'create_time' => time()));
           }
            if ($res) {
                $this->success('添加成功。');
            } else {
                $this->error('添加失败。');
            }


        } else {

            $this->display(T('Addons://Mail@Mail/addEmail'));

        }
    }

    /**删除邮箱
     * @param $ids
     * autor:xjw129xjt
     */
    public function delEmail()
    {
        $ids = I('ids');
        $res = D('MailList')->where(array('id' => array('in', $ids)))->setField('status', 0);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


    /**
     * 发送邮件页面
     * @param string $ids
     * autor:xjw129xjt
     */
    public function sendEmail()
    {
        $ids = I('ids');
        $list = D('MailList')->where(array('id' => array('in', $ids)))->select();
        foreach ($list as $k => $v) {
            $address[$v['id']] = $v['address'];
        }
        $data['address'] = implode('; ', $address);
        $this->assign($data);
        $this->display(T('Addons://Mail@Mail/sendEmail'));
    }

    /**
     * 执行发送邮件操作
     * @param $address 地址列表
     * @param string $title 邮件标题
     * @param string $body 邮件正文
     * autor:xjw129xjt
     */
    public function doSendEmail()
    {
        $address = op_h(I('address'));
        $title =op_h(I('title'));
        $body = op_h(I('body'));

        $server_host = "http://" . $_SERVER ['HTTP_HOST'];
        if ($title == '' || $body == '') {
            $this->error('请填写完整！');
        }
        //获取邮件配置信息

        $address = explode('; ', $address);
        //将邮件内容写入数据库
        $data = D('MailHistory')->create();
        $data['create_time'] = time();
        $data['status'] = 1;
        $data['from'] = C('WEB_SITE');
        $history = D('MailHistory')->add($data);
        //匹配图片地址
        preg_match_all('/src="([^http].*?)"/', $body, $out);
        $body = str_replace($out[1][0], $server_host . $out[1][0], $body);

        if ($address[0] == '') {
            $address = D('MailList')->where(array('status' => 1))->field('address')->select();
            foreach ($address as $k => &$v) {
                $v = $v['address'];
            }
        }

        foreach ($address as $k => $v) {
            if ($token_data = D('MailToken')->where(array('email' => $v))->select()) {
                $token = $token_data[0]['token'];
            } else {
                $token = $this->create_rand(10);
                $data_token['token'] = $token;
                $data_token['email'] = $v;
                D('MailToken')->add($data_token);
            }
            $url = $server_host .addons_url('Mail://Mail/unsubscribe', array('token' => $token));
            //发送邮件

            $body1 = $body . '<hr/><div style="float:right;margin-right: 20px;"><a href="' . $url . '">取消订阅</a></div>';
            $status = send_mail($v, $title, $body1);
            //将发送情况和状态写入数据库
            $data_link['status'] = $status;
            $data_link['mail_id'] = $history;
            $data_link['to'] = $v;
            $link = D('MailHistoryLink')->add($data_link);
        }
        $this->success('邮件发送成功。', addons_url('Mail://Mail/mailList'));
    }

    public function unsubscribe()
    {
        $token = I('token');
        if ($token) {
            $arr = D('MailToken')->where(array('token' => $token))->find();
            $res = D('MailList')->where(array('address' => $arr['email']))->setField('status', 0);
            D('MailToken')->where(array('token' => $token))->delete();
            if ($res) {
                $this->success('取消订阅成功', U('Home/Index/index'));
            } else {
                $this->error('取消订阅失败', U('Home/Index/index'));
            }
        }
    }
    public function subscribe()
    {
        $email_address = I('email_address');
        $match = preg_match("/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/", $email_address);

        if( $email_address =='' || !$match){
            $this->error('邮箱格式不正确');
        }

        $check = D('MailList')->where(array('address' => $email_address))->find();
        if ($check) {
             if($check['status']){
                    $this->error('该邮箱已经存在');
                }
                else{
                    $res = D('MailList')->where(array('address'=>$email_address))->setField('status', 1);
                }

        } else {
            $res = D('MailList')->add(array('address' => $email_address, 'status' => 1, 'create_time' => time()));

        }
        if ($res) {
            $this->success('订阅成功.');
        } else {
            $this->error(' 订阅失败');
        }
    }

    /**
     * 邮件发送历史
     * @param string $title
     * autor:xjw129xjt
     */
    public function history()
    {
        $title = I('title');
        $map = array('status' => 1);
        if ($title != '')
            $map['title'] = array('like', '%' . $title . '%');
        $mailList = D('MailHistory')->where($map)->order('create_time desc')->select();
        foreach ($mailList as $k => &$v) {
            $v['title'] = getShortSp(op_h($v['title'], 'text'), 20);
            $v['body'] = getShortSp(op_h($v['body'], 'text'), 50);
        }


        $this->assign('mailList',$mailList);

        $this->display(T('Addons://Mail@Mail/history'));
    }

    public function setStatus()
    {
        $ids = I('ids');
        $status = I('get.status');
        $builder = new AdminListBuilder();
        $builder->doSetStatus('mail_history', $ids, $status);

    }

    /**
     * 邮件详情
     * @param string $id
     * autor:xjw129xjt
     */
    public function mailDetail()
    {
        $id = I('id');
        $history = D('MailHistory')->where(array('id' => $id))->find();
        $link = D('MailHistoryLink')->where(array('mail_id' => $id))->select();
        $this->assign('history', $history);
        $this->assign('link', $link);
        $this->display(T('Addons://Mail@Mail/mailDetail'));
    }

    /**
     * 随机生成字符串
     * @param int $length
     * @return string
     * autor:xjw129xjt
     */
    private function create_rand($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }
}
