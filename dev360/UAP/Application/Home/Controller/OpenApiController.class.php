<?php
namespace Home\Controller;

use Think\Controller;
use Think\Exception;

class OpenApiController extends Controller
{

    public function loadMenu($userid,$appkey)
    {
        $con['b.userid'] = $userid;
        $con['sys_menu.display'] = 1;
        $con['c.appkey'] = $appkey;
        $arr = M('sys_menu')
            ->field('distinct sys_menu.*')
            ->join('sys_menu_role as a on a.menuid=sys_menu.id')
            ->join('sys_role_user as b on b.roleid=a.roleid')
            ->join('sys_app as c on c.id=sys_menu.appid')
            ->where($con)->order('sys_menu.orders')->select();
        iconvEcho(json_encode($arr));
    }

}