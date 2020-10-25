<?php

/**
 * PESCMS for PHP 5.4+
 *
 * Copyright (c) 2014 PESCMS (http://www.pescms.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace App\Api\POST;

class Check extends \App\Api\Common {

    /**
     * 验证物理地址
     */
    public function mac() {
        $mac = $this->isP('mac', '请提交物理地址');
        $user = $this->isP('user', '请提用户名称');
        $version = $this->isP('version', '请提交软件版本');
        
        //验证版本
        $checkVersion = $this->db('version')->order('version_id DESC')->find();
        if($version != $checkVersion['version_no']){
            $this->error('<p>当前版本过于陈旧，请点击下方链接升级软件：</p><p><a href="'.$checkVersion['version_link'].'">获取新版</a></p>');
        }
        
        $verify = \Core\Func\CoreFunc::generatePwd($mac . $user, \Core\Func\CoreFunc::loadConfig('PRIVATE_KEY'));

        $checkResult = \Model\Content::findContent('mac', $verify, 'mac_verify');
        if (empty($checkResult)) {
            $this->db('mac')->insert(array('mac_address' => $mac, 'mac_createtime' => time(), 'mac_verify' => $verify));
            $this->error("首次运行本软件,请将下方编码发给管理员:<br/>{$mac}");
        } elseif ($checkResult['mac_status'] == '0') {
            $this->error("您的电脑还没有通过验证,请将下方编码发给管理员:<br/>{$mac}");
        } else {
            //验证成功,输出数据,减小请求
            $result = $this->db('api')->where('api_status = 1')->order("api_listsort asc, api_id desc")->select();
            $this->returnMsg('获取数据成功', $result, '200');
        }
    }
    
    /**
     * 执行登录
     */
    public function login() {
        /**
         * 此处处理登陆器发送过来的account 和 password 两个参数
         */
        $data['account'] = $this->isP('account', '请提交帐号');
        $data['password'] = \Core\Func\CoreFunc::generatePwd($data['account'] . $this->isP('password', '请提交密码'), 'PRIVATE_KEY');
        //验证用户密码
        $checkAccount = $this->db('user')->where('user_account = :account AND user_password = :password AND user_status = 1')->find($data);
        if (empty($checkAccount)) {
            $this->error('帐号或者密码错误');
        }
        
        unset($checkAccount['user_password']);
        
        /**
         * 当登录成功后，我们需要生成一个全球唯一的匹配值（具体如何生成，自行编写）
         * 该匹配值用于验证用户是否在登陆器触发登录动作
         */
        $sec = explode(' ', microtime());
        $mark = crypt(round(time() * $sec['0'], 0));
        
        /**
         * 生成完唯一的匹配值后，我们要将匹配值连同用户的帐号记录至数据库
         */
        $this->db('dologin')->insert(array('dologin_createtime' => time(), 'dologin_account' => $data['account'], 'dologin_mark' => $mark, 'dologin_session' => json_encode($checkAccount)));
        
        //上面动作都完成后，即用户的登录动作已经准备就绪了，我们返回指定的信息给登陆器，让他触发浏览器访问登录地址。
        $this->returnMsg('登录成功', "http://login.pescms.com/?g=Admin&m=Login&a=doLogin&mark={$mark}", '200');
    }

}
