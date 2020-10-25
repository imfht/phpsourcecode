<?php
/**
 * 菜单挂件
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/25
 * Time: 13:39
 */

namespace backend\components;


use yii\base\Widget;
use Yii;
class NavWidget extends Widget{


    //查询菜单数据
    public function run()
    {

        $server = Yii::createObject('menuservice');
        $list        = $server->queryMenus(['parent_id'=>0]);
        $menuList    = $server->menuGroup($list);
        //查询登录者信息
        $uid         = Yii::$app->user->getId();
        $service     = Yii::createObject('userservice');
        $user        = $service->getUserById($uid);
        return $this->render('@app/views/layouts/mylayouts/menus',['menus'=>$menuList,'user'=>$user]);
    }

}