<?php

class RemindersController extends BaseController {

    public function __construct() {
        parent::__construct();
        //已经登录的用户无法进行如下操作
        if (Auth::check()) {
            $template = Theme::message();
            $html = View::make($template, array('message' => '对不起，你当前有处于登陆的账户，请退出后重新操作！', 'type' => 'success', 'url' => ''))->render();
            echo $html;
            exit;
        }
    }

    /**
     * 找回密码页面
     */
    public function getRemind() {
        //设置标题、描述等SEO信息
        View::share('title', '找回密码');

        $template = Theme::template('password.remind');
        return View::make($template);
    }

    /**
     * 接受找回密码的邮箱
     */
    public function postRemind() {
        //设置标题、描述等SEO信息
        View::share('title', '找回密码');

        $email = Input::only('email');

        try {
            $response = Password::remind($email, function($message) {
                        $message->subject('密码重置--' . $this->siteName);
                    });
            switch ($response) {
                case Password::INVALID_USER:
                    return Redirect::back()->with('error', '该邮箱没有在该网站注册'); //Lang::get($response)

                case Password::REMINDER_SENT:
                    $template = Theme::message();
                    return View::make($template, array('message' => '邮件已经成功发送，请登陆邮箱查看并进行重置密码！', 'type' => 'success', 'url' => ''));
            }
        } catch (Exception $e) {
            $template = Theme::message();
            return View::make($template, array('message' => '邮件发送失败，请联系管理员！', 'type' => 'error', 'url' => ''));
        }
    }

    /**
     * 重置密码FORM表单
     */
    public function getReset($token = null) {
        //设置标题、描述等SEO信息
        View::share('title', '重置密码');

        if (is_null($token)) {
            App::abort(404);
        }
        $remind = DB::table('password_reminders')->where('token', $token)->first();
        if (!$remind) {
            $template = Theme::message();
            return View::make($template, array('message' => '错误的请求，不存在的会话！', 'type' => 'error', 'url' => ''));
        }
        $template = Theme::template('password.reset');
        return View::make($template, array('token' => $token));
    }

    /**
     * Handle a POST request to reset a user's password.
     *
     * @return Response
     */
    public function postReset() {
        //设置标题、描述等SEO信息
        View::share('title', '重置密码');

        $credentials = Input::only(
                        'email', 'password', 'password_confirmation', 'token'
        );
        //根据token获取email
        $remind = DB::table('password_reminders')->where('token', Input::only('token'))->first();
        $credentials['email'] = $remind->email;

        $response = Password::reset($credentials, function($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                });

        switch ($response) {
            case Password::INVALID_PASSWORD:
            case Password::INVALID_TOKEN:
            case Password::INVALID_USER:
                //Lang::get($response);
                return Redirect::back()->with('error', '请确保两次输入的密码一致，密码最少为6位');

            case Password::PASSWORD_RESET:
                //return Redirect::to('/');
                $template = Theme::message();
                return View::make($template, array('message' => '密码重置成功，即将为你跳转到登陆页面！', 'type' => 'success', 'url' => '/login'));
        }
    }

}
