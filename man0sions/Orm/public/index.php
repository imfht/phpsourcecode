<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/10/11
 * Time: 上午11:22
 */
define("BASE_PATH", __DIR__);
require BASE_PATH . '/../vendor/autoload.php';

$db_conf = [
    'master' => [  //配置主数据库
        'host' => '192.168.10.10',
        'user' => 'mysqluser',
        'passwd' => 'mysqlpasswd',
        'dbname' => 'wxbdb',
    ],
    'slave' => [   //配置从数据库,可以无限多个

        [
            'host' => '192.168.10.10',
            'user' => 'mysqluser',
            'passwd' => 'mysqlpasswd',
            'dbname' => 'wxbdb',
            'slave' => '1',
        ],
        [
            'host' => '192.168.10.10',
            'user' => 'mysqluser',
            'passwd' => 'mysqlpasswd',
            'dbname' => 'wxbdb',
            'slave' => '2',
        ]
    ]


];
\LuciferP\Orm\base\Registry::set('db_conf', $db_conf);

/**
 * create
 */

$user = new \LuciferP\Orm\models\Users();
$user->name = 'zhangsan';
$user->password = password_hash('passwd', PASSWORD_DEFAULT, ['cost' => 10]);
if ($user->create()) {
    var_dump($user->getAttributes());
} else {
    var_dump($user->getErrors()); //sql操作失败用getErrors()方法获取错误信息
}

/**
 * 获取数据
 * getAttributes
 */

/**
 * find
 */

$user = \LuciferP\Orm\models\Users::model()
    ->fields(['*'])//fields(['id','name'])
    ->where(['name' => 'zhangsan'])
    ->find();

var_dump($user->getAttributes());

/**
 * find all
 * findall 方法返回的是一个数组对象,数组中的每一个对象都可以进行update,delete,操作
 */

$users = \LuciferP\Orm\models\Users::model()
    ->fields(['*'])
    ->where(['name' => 'zhangsan'])
    ->limit(5)
    ->order(['id' => 'desc'])
    ->findAll();

foreach ($users as $item) {
    var_dump($item->getAttributes());
}

/**
 * update
 */
$user->name = 'lisi' . microtime();
if ($user->update()) {
    var_dump($user->getAttributes());
} else {
    var_dump($user->getErrors());
}


/**
 * delete
 */
if (!$user->delete()) {
    var_dump($user->getErrors());

}


