<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-4-17
 * Time: 下午8:51
 */
namespace Helper;

use Auth;
use Validator;
use App\User;
use Illuminate\Http\Request;


trait PersonalHelper
{
    /**
     * 用户基本资料的数据验证
     *
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     */
    public function getInfoValidator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email',
        ]);

        return $validator;
    }

    /**
     * 修改用户的基本资料
     *
     * @param $mixed 可以传入用户的id或直接传入Model实例
     * @param $infoData
     * @return bool|int
     */
    public function updateInfo($mixed, $infoData)
    {
        if( ! is_object($mixed) ){
            $mixed = User::find($mixed);
        }

        return $mixed->update($infoData);
    }

    /**
     * 头像数据验证
     *
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     */
    public function getHeadImageValidator(Request $request)
    {
        if ( $request->hasFile('head_image') ) {
            $validator = Validator::make($request->only('head_image'),[
                'head_image' => 'max:2048|image'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'head_image' => 'required'
            ]);
        }

        return $validator;
    }

    /**
     * 修改用户头像
     *
     * @param Request $request
     * @return array|mixed|null 修改成功时，返回新的头像文件名；失败时返回null
     */
    public function updateHeadImage(Request $request)
    {
        $newImageName = Auth::user()['_id'] . '.png';
        if ( !$request->hasFile('head_image') ) {
            $imageData = str_replace(' ', '+', $request->get('head_image'));
            $imageData = base64_decode($imageData);
            file_put_contents(public_path(). '/image/header/'. $newImageName, $imageData);
        } else {
            $imageData = $request->file('head_image');
            $imageData->move(public_path(). '/image/header', $newImageName);
        }
        Auth::user()->update(['head_image' => $newImageName]);
        return $imageData;
    }

    /**
     * 对头像文件进行一些检查
     *
     * @param Request $request
     * @return bool 检查通过返回true，否则返回false
     */
    protected function checkFile(Request $request)
    {
        // 检查该字段是否存在
        if ( !$request->has('head_image') ) {
            return false;
        }

        // 检查是不是有效的上传文件
        $file = $request->file('head_image');
        if ( !$file->isValid() ) {
            return false;
        }

        return true;
    }

    /**
     * 用户密码修改的数据验证
     *
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     */
    public function getPasswordValidator(Request $request)
    {
        // 检查原密码是否正确
        Validator::extend('check_password', function($attr, $value){
            return Auth::validate(['_id' => Auth::user()['_id'], 'password' => $value]);
        });

        $validator = Validator::make($request->all(),[
            'password' => 'required|check_password|min:6',
            'newPassword' => 'required|min:6|confirmed',
        ]);

        return $validator;
    }

    /**
     * 修改用户密码
     *
     * @param $mixed 可以传入用户id或对应的Model实例
     * @param $postData
     * @return bool|int
     */
    public function updatePassword($mixed, $postData)
    {
        if( ! is_object($mixed) ){
            $mixed = User::find($mixed);
        }
        return $mixed->update(['password' => bcrypt($postData['newPassword'])]);
    }
}