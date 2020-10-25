<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/25
 * Time: 9:45
 */

namespace console\models;


use backend\models\AuthAssignment\AuthAssignment;
use common\models\User;
use Yii;
class UserSeeder{


    public static function initUser(){

        $user = new User();
        $user->username = 'admin';
        $user->email = 'admin@admin.com';
        $user->setPassword('123456');
        $user->generateAuthKey();
        $user->save();
        //分配角色
        $connection = Yii::$app->getDb();
        $connection->createCommand("INSERT INTO auth_assignment(item_name,user_id,created_at) VALUES ('admin','".$user->id."','".time()."')")->execute();

    }

}