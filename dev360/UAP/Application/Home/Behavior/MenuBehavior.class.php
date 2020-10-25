<?php

/**
 * Created by PhpStorm.
 * User: kira
 * Date: 2015/8/22
 * Time: 16:37
 */
namespace Home\Behavior;

class MenuBehavior
{

    // 行为扩展的执行入口必须是run
    public function run(&$params)
    {
        $rs = M('sys_keyvalue')->select();
        for ($i = 0; $i < count($rs); $i++) {
            $_SESSION[$rs[$i]['bussiness']] = $rs[$i]['dict'];
        }

        if (empty($_SESSION['_user']) && empty($_GET['redirect'])) {
            if (empty(strstr($_SERVER['PHP_SELF'], 'home'))) {
                redirect('home/login?redirect=1');
            } else {
                redirect('login?redirect=1');
            }
            return;
        }
        if (!empty($_SESSION['_user'])) {
            $con['b.userid'] = $_SESSION['_user']['id'];
            $con['sys_menu.display'] = 1;
            $arr = M('sys_menu')
                ->field('distinct sys_menu.*')
                ->join('sys_menu_role as a on a.menuid=sys_menu.id')
                ->join('sys_role_user as b on b.roleid=a.roleid')
                ->where($con)->order('sys_menu.orders')->select();
            $_SESSION['menus'] = $arr;
        }


    }


}


