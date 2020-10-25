<?php

/**
 * PESCMS for PHP 5.4+
 *
 * Copyright (c) 2014 PESCMS (http://www.pescms.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace App\Admin\GET;

class Login extends \App\Admin\Common {

    public function __init() {
        parent::__init();
        $this->assign('sitetile', \Model\Option::findOption('sitetitle')['value']);
        $this->assign('signup', \Model\Option::findOption('signup')['value']);
    }

    public function index() {
        $login = $this->checkLogin();
        if ($login) {
            $this->jump($this->url(GROUP . '-Index-index'));
        }
        $this->display();
    }

    /**
     * 注册帐号
     */
    public function signup() {
        $this->display();
    }
    
    public function dologin(){
        $mark = $this->isG('mark', '非法请求'); //登陆器生成的URL中，mark是之前登录验证接口协定的参数。大家可以按照自己的实际情况更改。
        
        $check = \Model\Content::findContent('dologin', $mark, 'dologin_mark');//验证mark参数是否与之前登录验证接口生成并记录数据库的信息一致
        
        if(empty($check)){
            $this->error('不存在的记录');
        }
        
        /**
         * 请求过来的mark如果已经使用了，那么数据库中的dologin_status将会设置为1.
         * 考虑到mark是弱验证(HTTP短链接的特性)，当登陆器接口触发浏览器访问时，聪明点的人可以将该地址发给别人，让他人访问。
         * 我们加上了30秒的验证，可以防止大部分的小白用户和上面别有用心的人。让他们的做法成功几率降低。
         * PS：如果需要真正的强验证功能，请自行研究TCP之类的网络协议实现。也可以联系PESCMS开发人员，选择定制计划。
         */
        if($check['dologin_createtime'] <= time() - 30 && $check['dologin_status'] == '0'){
            $this->error('登录超时，请重新运行软件');
        }
        
        //一切都验证通过，则处理登录，赋上对应的登录信息。
        $this->db('dologin')->where('dologin_id = :dologin_id')->update(array('dologin_status' => '1', 'noset' => array('dologin_id' => $check['dologin_id'])));
        
        $_SESSION['admin'] = json_decode($check['dologin_session'], true); 
        
        $this->success('登录成功!', $this->url(GROUP . '-Index-index'));
        
    }

}
