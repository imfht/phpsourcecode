<?php

/**
* Created by PhpStorm.
* User: spatra
* Date: 14-10-17
* Time: 下午8:13
*/
class AuthorityController extends BaseController
{

    /**
    * 输出单独的登入页面.
    */
    public function getLogin(){
        if (Auth::check()) {
            return Redirect::to('/ng#/project');
        } else {
            return View::make('authority.login');
        }
    }

    /**
    *处理登陆
    */
    public function postLogin()
    {
        if( $this->tryLogin() ){
            if( Session::get('tryAccessAuthUri') ){
                $redirectBackUri = Session::get('tryAccessAuthUri');
                Session::forget('tryAccessAuthUri');

                return Redirect::to($redirectBackUri);
            } else {
                return Redirect::to('/ng#/project');
            }

        } else {
            if( Request::ajax() ){
                return Response::json(['loginError' => '用户名或密码错误!'], 403, Config::get('response.jsonHeaders'), JSON_UNESCAPED_UNICODE);
            } else {
                return Redirect::back()->withInput()->withErrors(['loginFailed'=>'登陆失败，账号（电子邮件）或密码错误！']);
            }
        }
    }

    /**
     * 内部使用，用于尝试进行登陆，如果登陆成功则返回true，否则返回false
     *
     * @return bool
     */
    protected function tryLogin()
    {
        //获取账户名或邮箱、密码
        $identify = Input::get('identify');
        $password = Input::get('password');

        return
            Auth::attempt(['username' => $identify,'password' => $password,'active' => 1]) ||
            Auth::attempt(['email' => $identify,'password' => $password,'active' => 1]);
    }

    /**
    * 实现登出操作
    */
    public function getLogout()
    {
        Auth::logout();
        return Redirect::to('/');
    }

    /**
    * 输出单独的注册页面
    */
    public function getSignin()
	  {
		    if (Auth::check()) {
			      return Redirect::to('/ng#/project/list');
		    } else {
			      return View::make('authority.signin');
		    }
    }

    /**
    * 处理注册
    * @return \Illuminate\Http\RedirectResponse
    */
    public function postSignin()
    {
        //获取注册表单post的数据
        $postData = Input::all();
        $validator = $this->getSigninValidator($postData);

        if ($validator->passes()) {
            unset($postData['password_confirmation']);
            $postData['password'] = Hash::make($postData['password']);

            try {
                $addUser = User::create($postData);
                Auth::login($addUser);
                return Redirect::to('/ng#/personal/information/setting');
            } catch (Exception $err) {
                if (Request::ajax() || Request::isJson()) {
                    return Response::json(['addError' => '注册失败!']);
                } else {
                    return Redirect::back()->withErrors(['username'=>'抱歉：内部数据错误，暂时无法注册.']);
                }
            }

        } else {

            if (Request::ajax() || Request::isJson()) {
                return Response::json([
                    'errorMessages' => $validator->messages()
                ], 403);
            } else {
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }
    }

    /**
     * 内部使用，用于生成对注册数据进行校验的校验器.
     *
     * @param $postData 待校验的数据组成的关联数组
     * @return \Illuminate\Validation\Validator
     */
    protected function getSigninValidator($postData)
    {
        //对提交的注册信息制定验证规则
        $rules = array(
            'username' => 'required|unique:users',
            'password' => 'required|alpha_dash|min:6|confirmed',
            'email' => 'required|email|unique:users'
        );


        return Validator::make($postData, $rules);
    }
}
