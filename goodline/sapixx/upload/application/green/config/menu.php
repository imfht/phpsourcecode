<?php
use app\green\model\GreenMember;
use app\common\event\Passport;
$user = Passport::getUser();
$menu = [];
if(!empty($user)){
    if($user->parent_id){
        $operate = GreenMember::getOperate($user->id);
        if(empty($operate)){
            $menu = [[
                'name' => '权限限制',
                'icon' => 'store_icon',
                'menu' => [['name' => '请联系客服','icon' =>'store_icon','url' => 'javascript']]
            ]
            ];
        }else{
            $menu = include_once('passport.php');
        }
    }else{
        $menu = include_once('manage.php');
    }
}
return $menu;
