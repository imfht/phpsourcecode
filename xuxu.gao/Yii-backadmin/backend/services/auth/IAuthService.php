<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/17
 * Time: 12:14
 */

namespace backend\services\auth;


interface IAuthService {


    /**
     * 用户登录验证
     * @param null $params
     * @return mixed
     */
    public function Login($params   = null);

}