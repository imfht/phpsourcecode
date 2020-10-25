<?php

/**
 * Created by koko
 * Email: kokostudio@qq.com
 * Date: 2015/5/18
 * Time: 11:47
 */

namespace backend\rules;

use yii\rbac\Rule;

/*
 *  用户访问规则：已登录用户 user
 *  已登录用户返回真
 *  未登录返回假
 *
 * */

class User extends Rule
{
    public function execute($user, $item, $params)
    {

        /*        print 'user';
                var_dump($user);
                print 'item';
                var_dump($item);
                print 'params';
                var_dump($params);
                exit;*/

        if (!\yii::$app->user->isGuest) {
            return true;
        }
        return false;
    }
}
