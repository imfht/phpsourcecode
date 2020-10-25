<?php

class PersonalController extends \BaseController
{
    public function __construct()
    {
        $this->currUser = Auth::user();

        /**
         * 对个人信息相关post或put请求进行数据过滤
         */
        \Libraries\MarkDownPurifier::purify(['description']);
    }

    /**
     * 返回个人信息
     * @return \Illuminate\Http\JsonResponse
     *
     * 返回的JSON格式：
     * {
     *   "id": "用户id",
     *   "username": "用户姓名"，
     *   "email": "用户电子邮件",
     *   "description":"个人简介"，
     *   "head_image"："用户头像",
     *   "created_at": "用户注册时间"
     * }
     *
     */
    public function getInfo()
    {
        return Response::json($this->currUser);
    }

    /**
     * 更改个人信息
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function putInfo()
    {
        $userDescription = Input::get('description');
        $userHeadImage = $this->uploadImage();

        $this->currUser->description = $userDescription;
        if( $userHeadImage ){
            $this->currUser->head_image = $userHeadImage;
        }

        $this->currUser->update();

        return Response::make('', 200);
    }

    /**
     * 类内部使用，如果成功上传文件则返回上传后文件的文件名， 否则返回null
     *
     * @return mixed
     */
    private function uploadImage()
    {
        $imgData = Input::get('head_image');


        if ($imgData) {
            $imgData = str_replace(' ', '+', $imgData);

            $newImgName = $this->currUser['id'] . '.png';
            file_put_contents(public_path() . '/img/userHeadImage/' . $newImgName, base64_decode($imgData));

            return $newImgName;
        } else {

            return null;
        }
    }

    /**
     * 实现密码修改， 提交的数据如下：
     * 	password: 原密码
     *  newPassword: 新密码
     *  newPassword_confirmation: 新密码的确认
     *
     * 返回JSON的参数(仅当操作失败时)：
     * errorMessage: 当密码不成功修改时，返回表示错误信息的对象，示范如下：
     *  {
     *    "errorMessages": {
     *       "password": ["第一个校验规则不同的错误信息", "第二个校验规则不通过的错误信息"],
     * 	     "newPassword": ["第一个校验规则不同的错误信息", "第二个校验规则不通过的错误信息"],
     *       "newPassword_confirmation": ["第一个校验规则不同的错误信息", "第二个校验规则不通过的错误信息"]
     *    	}
     *   }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function putPassword()
    {
        $postData = Input::all();
        $validator = $this->getChangePasswordValidator($postData);

        $res = [];
        $statusCode = 200;


        if ($validator->passes()) {
            User::where('id', Auth::user()['id'])->update(['password'=> Hash::make( $postData['newPassword'])]);
        } else {
            $res['errorMessages'] = $validator->messages();
            $statusCode = 403;
        }
        return Response::json($res, $statusCode);
    }

    /**
     * 内部使用，用于生成更改密码时使用的数据校验器.
     *
     * @param $postData
     * @return \Illuminate\Validation\Validator
     */
    protected function getChangePasswordValidator($postData)
    {
        $verifiedRules = [
            'password' => 'required|check_password',
            'newPassword'=> 'required|confirmed'
        ];

        Validator::extend('check_password', function($attr, $value){
            return Auth::validate(['username'=>Auth::user()->username, 'password'=>$value] );
        });

        $errorMessage = [
            'check_password' => '原密码输入错误，请重新输入原密码',
            'confirmed' => '两次输入不一致，请重新输入'
        ];

        return Validator::make($postData, $verifiedRules, $errorMessage);
    }

    private $currUser;  //引用当前登陆的用户模型实例
}
