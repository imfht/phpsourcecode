<?php
namespace App\Http\Controllers\Main;

use App\Http\Controllers\Main\Base\BaseController;
use App\Models\UserMember;
use Endroid\QrCode\QrCode;
use Lib\Email;
use App\Common\Code;
use App\Common\Msg;
use App\Http\Auth\Auth;
use App\Models\PublicToken;
use Lib\Validate;
use App\Service\AuthService;

/**
 * 权限控制器
 * @author Chengcheng
 * @date 2016年10月21日 17:04:44
 */
class AuthController extends BaseController
{

    /**
     * 当前控制器所有方法不需要登录
     * @author Chengcheng
     * @date   2016年10月23日 20:39:25
     * @return array
     */
    protected $isNoLogin = true;

    /**
     * 获取验证码
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function getRegCode()
    {

        //1 获取输入参数,email 手机号码,passwd 密码
        $this->requestData['email'] = $this->input('email', '');

        //2.1 验证参数
        if (empty($this->requestData['email'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'email');
            return $this->ajaxReturn($result);
        }
        if (!Validate::isEmail($this->requestData['email'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_FORMAT_ERROR;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_FORMAT_ERROR, 'email');
            return $this->ajaxReturn($result);
        }

        // 发送验证码
        $emailCode                 = new PublicToken();
        $emailCode->email          = $this->requestData['email'];
        $emailCode->code           = rand(100000, 999999);
        $emailCode->available_time = date('Y-m-d H:i:s', strtotime("+5 minute"));
        $emailCode->save();
        Email::email()->send('验证码', $emailCode->email, "您的验证码为：{$emailCode->code}");

        //3 验证参数
        $result["code"] = Code::SYSTEM_OK;
        $result["msg"]  = Msg::SYSTEM_OK;
        return $this->ajaxReturn($result);
    }

    /**
     * 注册
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function register()
    {
        //1 获取输入参数,email 手机号码,passwd 密码
        $this->requestData['email']  = $this->input('email', '');
        $this->requestData['phone']  = $this->input('phone', '');
        $this->requestData['passwd'] = $this->input('passwd', '');
        $this->requestData['code']   = $this->input('code', '');
        $this->requestData['name']   = $this->input('name', '');
        $this->requestData['place']  = $this->input('place', '');

        //2.1 验证参数
        if (empty($this->requestData['email'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'email');
            return $this->ajaxReturn($result);
        }
        if (!Validate::isEmail($this->requestData['email'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_FORMAT_ERROR;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_FORMAT_ERROR, 'email');
            return $this->ajaxReturn($result);
        }

        if (!empty($this->requestData['phone']) && !Validate::isMobile($this->requestData['phone'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_FORMAT_ERROR;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_FORMAT_ERROR, 'phone');
            return $this->ajaxReturn($result);
        }
        if (empty($this->requestData['passwd'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'passwd');
            return $this->ajaxReturn($result);
        }

        $result = AuthService::register($this->requestData);

        //7 返回结果
        return $this->ajaxReturn($result);
    }

    /**
     * email登录
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function loginByEmail()
    {

        //1 获取输入参数,email 手机号码,passwd 密码
        $this->requestData['email']   = $this->input('email', '');
        $this->requestData['passwd']  = $this->input('passwd', '');
        $this->requestData['captcha'] = $this->input('captcha', '');

        //2.1 验证手机号码是否为空
        if (empty($this->requestData['email'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'email');
            return $this->ajaxReturn($result);
        }

        //2.3 验证密码
        if (empty($this->requestData['passwd'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'passwd');
            return $this->ajaxReturn($result);
        }
        if (empty($this->requestData['captcha'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, '验证码');
            return $this->ajaxReturn($result);
        }
        if (strtolower($_SESSION["captcha_code"]) != strtolower($this->requestData['captcha'])) {
            $result["code"] = Code::SYSTEM_CAPTCHA_ERROR;
            $result["msg"]  = "验证码提交不正确!";
            return $this->ajaxReturn($result);
        }

        //3 系统账号登录
        $memberLogin = AuthService::loginEmail($this->requestData);

        //4 登录失败
        if ($memberLogin['code'] != Code::SYSTEM_OK) {
            return $memberLogin;
        }

        //5 登录成功，保存信息到session
        Auth::auth()->loginMember($memberLogin['data']);

        //6 设置token
        if (!empty($this->requestData['is_get_token'])) {
            $memberLogin['data']['app_access_token'] = session_id();
        }

        $_SESSION["captcha_code"] = "";
        //7 返回结果
        return $this->ajaxReturn($memberLogin);
    }

    /**
     * 用户注销
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function logout()
    {
        //注销，清除session
        Auth::auth()->logout();
        $result["code"] = Code::SYSTEM_OK;
        $result["msg"]  = Msg::SYSTEM_OK;
        return $this->ajaxReturn($result);
    }

    /**
     * 重置密码 - 通过原来的密码
     * @author Chengcheng
     */
    public function resetPwdByCode()
    {
        //1 获取输入参数,email 手机号码,passwd 密码
        $this->requestData['email']  = $this->input('email', '');
        $this->requestData['passwd'] = $this->input('passwd', '');
        $this->requestData['code']   = $this->input('code', '');

        //2.1 验证参数
        if (empty($this->requestData['email'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'email');
            return $this->ajaxReturn($result);
        }
        if (!Validate::isEmail($this->requestData['email'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_FORMAT_ERROR;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_FORMAT_ERROR, 'email');
            return $this->ajaxReturn($result);
        }

        if (empty($this->requestData['passwd'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'passwd');
            return $this->ajaxReturn($result);
        }

        $result = AuthService::resetPasswordByCode($this->requestData);

        //7 返回结果
        return $this->ajaxReturn($result);
    }

    /**
     * 获取验证码
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function getCaptcha()
    {
        //1.创建黑色画布
        $image = imagecreatetruecolor(100, 30);

        //2.为画布定义(背景)颜色
        $bgcolor = imagecolorallocate($image, 255, 255, 255);

        //3.填充颜色
        imagefill($image, 0, 0, $bgcolor);

        // 4.设置验证码内容

        //4.1 定义验证码的内容
        $content = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        //4.1 创建一个变量存储产生的验证码数据，便于用户提交核对
        $captcha = "";
        for ($i = 0; $i < 4; $i++) {
            // 字体大小
            $fontsize = 10;
            // 字体颜色
            $fontcolor = imagecolorallocate($image, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
            // 设置字体内容
            $fontcontent = substr($content, mt_rand(0, strlen($content)), 1);
            $captcha .= $fontcontent;
            // 显示的坐标
            $x = ($i * 100 / 4) + mt_rand(5, 10);
            $y = mt_rand(5, 10);
            // 填充内容到画布中
            imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
        }

        $_SESSION["captcha_code"] = $captcha;

        //4.3 设置背景干扰元素
        for ($$i = 0; $i < 200; $i++) {
            $pointcolor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imagesetpixel($image, mt_rand(1, 99), mt_rand(1, 29), $pointcolor);
        }

        //4.4 设置干扰线
        for ($i = 0; $i < 3; $i++) {
            $linecolor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imageline($image, mt_rand(1, 99), mt_rand(1, 29), mt_rand(1, 99), mt_rand(1, 29), $linecolor);
        }

        //5.向浏览器输出图片头信息
        header('content-type:image/png');

        //6.输出图片到浏览器
        imagepng($image);

        //7.销毁图片
        imagedestroy($image);

        die;
    }

    /**
     * 微信登录 - 公众号
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function loginByWx()
    {
        //1 获取code
        $this->requestData['code'] = $this->input('code', 0);

        //2 检查code
        if (empty($this->requestData['code'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'code');
            return $this->ajaxReturn($result);
        }

        //3 获取信息
        $wxLoginResult = AuthService::wxLogin($this->requestData);

        //4 登录失败
        if ($wxLoginResult['code'] != Code::SYSTEM_OK) {
            return $wxLoginResult;
        }

        //5 登录成功，保存信息到session
        Auth::auth()->loginMember($wxLoginResult['data']);

        //5 返回结果
        return $this->ajaxReturn($wxLoginResult);
    }

    /**
     * 微信登录 - 小程序
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function loginByMiniWx()
    {
        //1 获取code
        $this->requestData['code'] = $this->input('code', 0);

        //2 检查code
        if (empty($this->requestData['code'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'code');
            return $this->ajaxReturn($result);
        }

        //3 获取信息
        $wxLoginResult = WxService::wxMiniLogin($this->requestData);

        //4 保存登录信息
        if (!empty($wxLoginResult['data']['wx'])) {
            Auth::auth()->loginWx($wxLoginResult['data']['wx']);
            $result["code"] = Code::SYSTEM_OK;
            $result["msg"]  = Msg::SYSTEM_OK;
            return $this->ajaxReturn($result);
        }

        //5 返回结果
        return $this->ajaxReturn($wxLoginResult);
    }

    /**
     * 获取验证码
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function getWxLoginQr()
    {
        $token                      = md5(rand() . uniqid(time(), true));
        $_SESSION["wx_login_token"] = $token;
        $emailCode                  = new PublicToken();
        $emailCode->token           = $token;
        $emailCode->member_id       = 0;
        $emailCode->available_time  = date('Y-m-d H:i:s', strtotime("+5 minute"));
        $emailCode->save();

        $qrCode = new QrCode('http://wn-album.tkc8.com/app/#/main/test/ocr/' . $token);
        header('Content-Type: ' . $qrCode->getContentType());
        echo $qrCode->writeString();
        die;
    }

    /**
     * 获取验证码
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function checkWxLoginQr()
    {
        if(empty($_SESSION["wx_login_token"])){
            $result = [];
            $result['code'] = Code::SYSTEM_ERROR;
            $result['msg'] = "token不存在。请重新扫面二维码";
            return $this->ajaxReturn($result);
        }
        $token = $_SESSION["wx_login_token"];
        $checkToken =  PublicToken::model()->where('token',$token)->where('available_time','>',time())->first();

        //没有检测到登录信息
        if(empty($checkToken) || empty($checkToken->member_id)){
            $result = [];
            $result['code'] = Code::SYSTEM_ERROR;
            $result['msg'] = "没有检测到登录信息";
            return $this->ajaxReturn($result);
        }

        //登录
        $memberLoginResult = UserMember::model()->login($checkToken->member_id);
        if ($memberLoginResult['code'] != Code::SYSTEM_OK) {
            return $memberLoginResult;
        }

        //5 登录成功，保存信息到session
        Auth::auth()->loginMember($memberLoginResult['data']);

        //5 返回结果
        return $this->ajaxReturn($memberLoginResult);
    }

}