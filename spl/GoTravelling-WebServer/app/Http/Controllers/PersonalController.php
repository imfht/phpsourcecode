<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-4-17
 * Time: 上午9:37
 */
namespace App\Http\Controllers;

use App\User;
use Auth;
use Helper\ResponseHelper;
use Illuminate\Http\Request;
use Helper\PersonalHelper;

class PersonalController extends Controller
{
    use PersonalHelper;

    /**
     * 显示修改基本资料的页面
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getInfo(Request $request)
    {
        $userData = $this->getUserInfo();

        return ResponseHelper::responseViewOrJson($request, $userData, 'personal.info');
    }

    /**
     * 处理基本资料修改的数据
     *
     * @param Request $request
     * @return $this|\Symfony\Component\HttpFoundation\Response
     */
    public function putInfo(Request $request)
    {
        $validator = $this->getInfoValidator($request);
        if ( $validator->fails() ) {
            return ResponseHelper::responseErrorMessagesOnViewOrJson($request, $validator->getMessageBag());
        }

        $this->updateInfo(\Auth::user(), $request->all());
        return ResponseHelper::responseViewOrJson($request, $this->getUserInfo(), 'personal.info');
    }

    /**
     * 显示头像上传页面
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getHeadImage(Request $request)
    {
        $headImage['head_image'] = $this->getUserInfo()['head_image'];
        return ResponseHelper::responseViewOrJson($request, $headImage, 'personal.headImage');
    }

    /**
     * 处理头像上传数据
     *
     * @param Request $request
     * @return $this|\Symfony\Component\HttpFoundation\Response
     */
    public function postHeadImage(Request $request)
    {
        $validator = $this->getHeadImageValidator($request);
        if ( $validator->fails() ) {
            return ResponseHelper::responseErrorMessagesOnViewOrJson($request, $validator->getMessageBag());
        }

        $this->updateHeadImage($request);
        return $this->getHeadImage($request);
    }

    /**
     * 显示密码修改页面
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getPassword(Request $request)
    {
        if( $request->wantsJson() ){
            return response()->json(null, 404);
        } else {
            return response()->view('personal.password');
        }
    }

    /**
     * 处理密码修改的数据
     *
     * @param Request $request
     * @return $this|\Symfony\Component\HttpFoundation\Response
     */
    public function putPassword(Request $request)
    {
        $validator = $this->getPasswordValidator($request);
        if ( $validator->fails() ) {
            return ResponseHelper::responseErrorMessagesOnViewOrJson($request, $validator->getMessageBag());
        }

        $this->updatePassword(Auth::user(), $request->only('newPassword'));

        return ResponseHelper::responseViewOrJson($request, [], 'personal.password');
    }

    /**
     * @return array
     */
    protected function getUserInfo()
    {
        if( \Request::wantsJson() ){
            return User::makeUserInfo(false);
        } else{
            return User::makeUserInfo(true);
        }
    }
}