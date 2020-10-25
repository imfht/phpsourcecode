<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/30
 * Time: 20:35
 */
return [
    '@method'=>function($method='get'){
        switch ($method){
            case 'post':
                if (Yuri2::isPost()){return true;}else{return '必须是post请求';}
            case 'get':
                if (Yuri2::isGet()){return true;}else{return '必须是get请求';}
            case 'ajax':
                if (Yuri2::isAjax()){
                    config('show_debug_btn',false);
                    return true;
                }else{
                    return '必须是ajax请求';}

            default:
                return '错误的@method注释';
        }
    },
    '@naples'=>function($param=''){
        switch ($param){
            case 'admin':
                if (session('naples.isAdmin') or md5(cookie('naplesAdminPsw'))==NAPLES_ADMIN ){
                    return true;
                }else{
                    error('需要管理员权限',url(res('login'),['jump'=>url()]));
                    return '需要管理员权限';
                }
                break;
            default:
                if (preg_match('/^token-(\w+)$/', $param, $matches)) {
                    $name = $matches[1] ? $matches[1] : 'default';
                    $token = request('naples_sys_auto_token');
                    //获取前缀
                    $prefix = preg_match('/^([\w-]+)_\w+$/', $token, $matches);
                    if ($prefix == $name) {
                        $prefix = $matches[1];
                        $serverToken = session('sysNaples.form_tokens.' . $prefix);
                        if ($token == $serverToken) {
                            session('sysNaples.form_tokens.' . $prefix, 'used');
                            if (isset($_REQUEST['naples_sys_auto_token'])) {
                                unset($_REQUEST['naples_sys_auto_token']);
                            }
                            if (isset($_GET['naples_sys_auto_token'])) {
                                unset($_GET['naples_sys_auto_token']);
                            }
                            if (isset($_POST['naples_sys_auto_token'])) {
                                unset($_POST['naples_sys_auto_token']);
                            }
                            return true;
                        }
                    }
                    error('表单令牌验证不通过', 'back');
                } else {
                    return '错误的@naples注释';
                }
        }
    },
    '@db'=>function($db_name='local'){
        initDb($db_name);
        return true;
    }
];