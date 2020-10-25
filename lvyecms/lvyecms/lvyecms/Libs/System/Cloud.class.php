<?php

// +----------------------------------------------------------------------
// | LvyeCMS 云平台
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.lvyecms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 旅烨集团 <web@alvye.cn>
// +----------------------------------------------------------------------

namespace Libs\System;

// =====Api 说明======
// get.license 获取授权信息，参数 domain=网站域名
// get.competence 判断帐号权限是否正常
// get.latestversion 获取最新版本号，无参数
// get.notice 远程通知
// 模块
// get.module.list 获取在线模块列表信息，参数 page=当前第几页，默认1，paging=每页显示数量
// get.module.info 获取某个模块的信息，参数 sign=模块签名
// get.module.latestversion 获取某个模块的最新版本号，参数 sign=模块签名，sign为数组时，返回多个
// get.module.install.package.url 获取某个模块的安装包地址，参数 sign=模块签名
// get.module.upgrade.package.url 获取某个模块的升级包地址，参数 sign=模块签名，version=安装版本
// get.module.explanation 获取模块的使用/安装说明，参数 sign=模块签名
// 插件
// get.addons.list 获取在线插件列表信息，参数 page=当前第几页，默认1，paging=每页显示数量
// get.addons.info 获取某个插件的信息，参数 name=插件标识
// get.addons.install.package.url 获取某个插件的安装包地址，参数 name=插件标识
// get.addons.upgrade.package.url 获取某个模块的升级包地址，参数 name=插件标识，version=安装版本
// get.addons.explanation 获取插件的使用/安装说明，参数 name=插件标识

class Cloud {

    //错误信息
    private $error = '出现未知错误 Cloud ！';
    //需要发送的数据
    private $data = array();
    //接口
    private $act = NULL;
    private $token = NULL;

    //服务器地址
    const serverHot = 'http://api.lvyecms.com/index.php';

    /**
     * 连接云平台系统
     * @access public
     * @return void
     */
    static public function getInstance() {
        static $systemHandier;
        if (empty($systemHandier)) {
            $systemHandier = new Cloud();
        }
        return $systemHandier;
    }

    /**
     * 获取错误信息
     * @return type
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 需要发送的数据
     * @param type $data
     * @return \Libs\System\Cloud
     */
    public function data($data) {
        $this->data = $data;
        return $this;
    }

    /**
     * 执行对应命令
     * @param type $act 例如 version.detection
     * @return type
     */
    public function act($act) {
        if (empty($this->data)) {
            $data = null;
        } else {
            $data = $this->data;
            //重置，以便下一次服务请求
            $this->data = array();
        }
        $this->act = $act;
        return $this->run($data);
    }

    /**
     * 检测当前站点云端帐号密码是否正常
     * @return boolean
     */
    public function competence() {
        $key = $this->getTokenKey();
        $token = S($key);
        if (empty($token)) {
            $this->act('get.token');
            $token = S($key);
        }
        $this->token = $token;
        return true;
    }

    /**
     * 请求
     * @param type $data
     * @return type
     */
    private function run($data) {
        $curl = new \Curl();
        $fields = array(
            'data' => json_encode($data),
            'version' => SHUIPF_VERSION,
            'act' => $this->act,
            'identity' => $this->getIdentity(),
            'token' => $this->token,
        );
        //curl支持 检测
        if ($curl->create() == false) {
            $this->error = '服务器不支持Curl扩展！';
            return false;
        }
        //请求
        $status = $curl->post(self::serverHot, $fields);
        if (false == $status) {
            $this->error = '无法联系服务器，请稍后再试！';
            return false;
        }
        return $this->returnResolve($status);
    }

    /**
     * 解析服务器返回的数据
     * @param type $data
     * @return type
     */
    private function returnResolve($data) {
        if (empty($data)) {
            return array();
        }
        $data = json_decode(base64_decode($data), true);
        if (!is_array($data) || !isset($data['status'])) {
            $this->error = '服务器返回信息错误！';
            return false;
        }
        if (!$data['status']) {
            $this->error = $data['error'];
            return false;
        }
        return $data['data'];
    }

    /**
     * 获取token Key
     * @return type
     */
    public function getTokenKey() {
        return md5(date('Y-m-d H') . 'cloud_token');
    }

    /**
     * LvyeCMS官网会员帐号信息
     * @return type
     */
    private function getIdentity() {
        return json_encode(array(
            'username' => C('CLOUD_USERNAME'),
            'password' => C('CLOUD_PASSWORD'),
            'domain' => urlDomain(get_url()),
        ));
    }

}
