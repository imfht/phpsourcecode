<?php

/*
 *
 * sysmanage.Auth  后台权限验证  
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */

class Auth extends Action
{

    private $cacheDir = ''; //缓存目录

    public function __construct()
    {
        $this->check_login();//检查是否登录
        $this->authorization();//检查用户动作是否在授权范围之类

        define('SYS_USER_ACC', $_SESSION["sys_user_acc"]); //当前登录帐号名
        define('SYS_USER_ID', $_SESSION["sys_user_id"]); //当前登录帐号ID
        define('SYS_CO_ID', '1'); //定义所属于公司编号

        if (isset($_SESSION["sys_user_sons"]) && !empty($_SESSION["sys_user_sons"])) {
            $sub_ids = implode(',', $_SESSION["sys_user_sons"]);
            define('SYS_USER_SUB_ID', $sub_ids);
        } else {
            define('SYS_USER_SUB_ID', '0');
        }

        if (isset($_SESSION["sys_user_self_sons"]) && !empty($_SESSION["sys_user_self_sons"])) {
            $sub_ids = implode(',', $_SESSION["sys_user_self_sons"]);
            define('SYS_USER_VIEW', $sub_ids);
        } else {
            define('SYS_USER_VIEW', '0');
        }
        //$this->initConst();
    }

    public function initConst()
    {
        $sql = "select * from fly_sys_config;";
        $list = $this->C($this->cacheDir)->findAll($sql);
        if (is_array($list)) {
            foreach ($list as $key => $row) {
                $assArr[$row["name"]] = $row["value"];
            }
        }
        return $assArr;
    }


    //检查是否有登录
    public function check_login()
    {
        if (isset($_SESSION["sys_user_id"])) {
            if (empty($_SESSION["sys_user_id"])) {
                $this->location("请登录", '/sysmanage/Login/login');
            }
        } else {
            $this->location("请登录", '/sysmanage/Login/login');
        }
    }

    //判断是有执行方法的权限
    public function authorization()
    {
        if (isset($_SESSION["sys_need_menu"])) {
            if (in_array(METHOD_NAME, $_SESSION["sys_need_menu"])) {
                if (!in_array(METHOD_NAME, $_SESSION["sys_need_method"])) {
                    $smarty = $this->setSmarty();
                    $smarty->display('404.html');
                    exit;
                }
            }
        }
    }

    //得需要验证的栏目和方法
    //返回：array("1",3,5,5) array('add',modify,del...);
    public function auth_menu_tree_arr()
    {
        $menu = $_SESSION["sys_user_menu"];
        if (!empty($menu)) {
            $sql = "select * from fly_sys_menu where visible='1' and id in ($menu)  order by sort asc,id desc;";
            $list = $this->C($this->cacheDir)->findAll($sql);
            $data = _instance('Extend/Tree')->arrToTree($list, 0);
            return $data;
        } else {
            $this->location("请登录", '/sysmanage/Login/login');
        }
    }

} //

?>