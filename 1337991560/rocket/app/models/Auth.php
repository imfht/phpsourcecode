<?php

namespace app\model;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use \MadphpDao\Rbac\Auth as AuthDao;

/**
 * 模型类
 * @author 徐亚坤 hdyakun@sina.com
 */

class Auth extends Base
{

    public function __construct()
    {

    }

    public static function lists()
    {
        return AuthDao::Dao()->getAll();
    }

}