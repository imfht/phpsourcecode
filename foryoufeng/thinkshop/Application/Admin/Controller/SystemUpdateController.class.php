<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Controller;
/**
 * 系统升级控制器
 * @author jry <598821125@qq.com>
 */
class SystemUpdateController extends AdminController{
    /**
     * 初始化方法
     * @author jry <598821125@qq.com>
     */
    protected function _initialize(){
        //只有ID为1的超级管理员才有权限系统更新
        if(session('user_auth.uid') !== '1'){
            $this->success('');
        }
    }

    /**
     * 检查新版本
     * @author jry <598821125@qq.com>
     */
    public function checkVersion(){
        if(extension_loaded('curl')){
            //远程更新地址
            $url = C('WEBSITE_DOMAIN').C('UPDATE_URL').'.html?action=check';

            //参数设置
            $params = array(
                //系统信息
                'product_name'    => C('PRODUCT_NAME'),
                'current_version' => C('CURRENT_VERSION'),
                'company_name'    => C('COMPANY_NAME'),

                //用户信息
                'data_auth_key'   => sha1(C('DATA_AUTH_KEY')),
                'website_domain'  => $_SERVER['HTTP_HOST'],
                'server_software' => php_uname().'_'.$_SERVER['SERVER_SOFTWARE'],
                'website_title'   => C('WEB_SITE_TITLE'),
            );
            $vars = http_build_query($params);

            //获取版本数据
            $result = json_decode($this->get_data_from_url($url, 'post', $vars), true);

            if($result['status'] == 1){
                $this->success($result['info']);
            }else{
                $this->error('连接服务器失败');
            }
        }else{
            $this->error('请配置支持curl');
        }
    }

    /**
     * 获取远程数据
     * @author jry <598821125@qq.com>
     */
    public function get_data_from_url($url = '', $method = '', $param = ''){
        $opts = array(
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
        );
        if($method === 'post'){
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $param;
        }

        //初始化并执行curl请求
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        return $data;
    }
}
