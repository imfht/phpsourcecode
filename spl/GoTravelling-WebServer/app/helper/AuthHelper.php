<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-11
 * Time: 下午8:48
 */

namespace Helper;

use App\User;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;

trait AuthHelper
{
    use AuthenticatesAndRegistersUsers;

    /**
     * 处理一个登录请求.
     *
     * @param Request $request
     * @return $this|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function postLogin(Request $request)
    {

        $this->validate($request, [
           'identify' => 'required',
            'password' => 'required|min:6'
        ]);

        if( $this->tryLogin($request) ){
            return ResponseHelper::chooseResponse($request, [
                'json'  =>  response()->json(User::makeUserInfo(false), 200),
                'base' =>  redirect()->intended(url('/'))
            ]);

        } else {
            return ResponseHelper::chooseResponse($request, [
                'json' =>  response()->json('', 400),
                'base' => redirect($this->loginPath())
                    ->withInput($request->only('identify', 'remember'))
                    ->withErrors([
                        'error' => $this->getFailedLoginMessage()
                    ])
            ]);
        }
    }

    /**
     * 处理一个注册请求.
     * 
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\Response
     */
    public function postRegister(Request $request)
    {
        $validator = $this->registrar->validator($request->all());

        if( $validator->fails() ){
            $errorMessages = ValidateHelper::changeValidatorMessageToArray($validator->getMessageBag());

            return ResponseHelper::chooseResponse($request, [
                'base' =>redirect()->intended(url('auth/register'))->withInput($request->all())->withErrors($errorMessages),
                'json' => response()->json($errorMessages, 400)
            ]);
        }

        $this->auth->login($this->registrar->create($request->all()));

        return ResponseHelper::chooseResponse($request, [
            'json' => response()->json(User::makeUserInfo(false), 201, ['location' => url('api/personal/info')]),
            'base' => redirect($this->redirectPath())
        ]);
    }

    /**
     * 返回登录失败时候的错误提示信息.
     *
     * @return string
     */
    protected function getFailedLoginMessage()
    {
        return '登录失败，账号或密码有误';
    }

    /**
     * 基于用户的用户名或电话号码，来进行登录尝试
     *
     * @param Request $request
     * @return bool
     */
    protected function tryLogin(Request $request){
        $data = $request->only('identify', 'password');

        $useUsername = [
            'username' => $data['identify'],
            'password' => $data['password']
        ];

        $userCellphone = [
            'cellphone_number' => $data['identify'],
            'password' => $data['password']
        ];

        return
            $this->auth->attempt($useUsername, $request->has('remember') )
            ||
            $this->auth->attempt($userCellphone, $request->has('remember') );
    }
}


