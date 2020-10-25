<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-11-11
 * Time: 下午11:23
 */

use \Illuminate\Database\Eloquent\Model;

/**
 * Class UserController
 *
 * 此类用于查询用户的基本信息
 */
class UserController extends \BaseController
{

    /**
     * 通过id查询用户的基本信息
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInfo($id)
    {
        $targetUser = User::findOrFail($id)->first();
        $keys = ['username', 'email', 'head_image', 'description'];

        return Response::json($this->getSectionalValuesFromModel($targetUser, $keys));

    }

    /**
     * 查询指定的用户名或电子邮件地址是否有对应的用户
     *
     * @param $mixed
     * @return \Illuminate\Http\Response
     *
     * 如果用户存在，则还返回下列信息：
     * {
     *  "id": "用户id",
     *  "username": "用户名"
     *  "head_image": "头像",
     *  "description": "个人简介"
     * }
     */
    public function getExist($mixed)
    {
        $targetUser = $this->getUserByMixed($mixed);

        if (is_object($targetUser)) {
            $keys = ['id', 'username', 'head_image', 'description'];
            return Response::json($this->getSectionalValuesFromModel($targetUser, $keys));
        } else {
            return Response::make('Not Found!', 404);
        }
    }

    /**
     * 查询指定的用户名或电子邮件地址是否已经注册了
     *
     * @param $mixed
     * @return \Illuminate\Http\Response
     *
     * 如果已经注册了，则返回：
     *  'repeated'
     * 否则返回：
     *  'ok'
     */
    public function checkRepeat($mixed)
    {
        $targetUser = $this->getUserByMixed($mixed);

        if( is_object($targetUser) ){
            return Response::make('repeated', 200);
        } else {
            return Response::make('ok', 200);
        }
    }

    /**
     * 内部使用，通过用户名或电子邮件地址来查找对象
     * @param $mixed
     * @return Model|null|static
     */
    private function getUserByMixed($mixed)
    {
        return $targetUser = User::where('username', $mixed)
            ->orWhere('email', $mixed)
            ->first();
    }

}
