<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-4-22
 * Time: 下午12:36
 */
namespace App\Http\Controllers;

use App\User;

class UserController extends Controller
{
    /**
     * 测试某一用户名或电话号码是否已经被使用（存在），用于注册时的重复校验.
     *
     * @param string $identify  电话号码或用户名
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getExist($identify)
    {
        $target = User::where('username', $identify)->orWhere('cellphone_number', $identify)->first();

        if( is_null($target) ){
            return response(null, 404);
        } else {
            return response(null, 200);
        }

    }
}