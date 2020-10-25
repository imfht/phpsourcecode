<?php
namespace backend\services;

use backend\models\AdminUser;

class AdminUserService extends AdminUser{
    public static $STATUS_USABLE = 10;//通过
    public static $STATUS_CODE = array(
        '0'=>'未审核',
        '10'=>'通过',
        '-10'=>'禁用',
    );
   
}
