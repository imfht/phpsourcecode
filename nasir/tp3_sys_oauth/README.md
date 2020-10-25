# sys_auth
thinkphp 3.x 第三方登录扩展包

# 目前支持
- 腾讯QQ登录
- 微信扫码登录
- 新浪微博登录

# 安装（扩展包）
> 确保已经安装composer 否则无法执行 composer中文网:http://www.phpcomposer.com/
```php
composer require cocolait/sys_oauth
```

# thinkphp 3.x的版本不会自动加载composer 需要手动操作一下
> 找到index.php入口文件
> 在框架引入之前加载composer即可
```php
// 加载composer
require './vendor/autoload.php';
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';
```

# 配置Config信息
```php
<?php
return [
    //腾讯QQ登录配置
    'SYA_AUTH_QQ'      => [
        'APP_KEY'       => '',  //应用注册成功后分配的 APP ID
        'APP_SECRET'    => '',  //应用注册成功后分配的KEY
        'CALLBACK'      => '',  // 应用回调地址
    ],
    //新浪微博配置
    'SYA_AUTH_SINA'    => [
        'APP_KEY'       => '', //应用注册成功后分配的 APP ID
        'APP_SECRET'    => '', //应用注册成功后分配的KEY
        'CALLBACK'      => '', // 应用回调地址
    ],
    //微信登录
    'SYA_AUTH_WEIXIN' => [
        'APP_KEY' => '',     //应用注册成功后分配的 APP ID
        'APP_SECRET' => '',  //应用注册成功后分配的KEY
        'CALLBACK' => "",    //应用回调地址
    ]
);
```

# 控制器操作示列
>thinkPHP 3.2.3 为例 php >= 5.3 或者 thinkphp 3.x都支持
```php
namespace Home\Controller;
use Think\Controller;
class OauthController extends Controller {
	//登录地址
	//目前type参数 只支持 [qq,sina,weixin]
	public function login($type = null){
		empty($type) && $this->error('参数错误');
		$_SESSION['login_http_referer']=$_SERVER["HTTP_REFERER"];
		$sns  = \Cp\Sys\Oauth::getInstance($type);
		//跳转到授权页面
		redirect($sns->getRequestCodeURL());
	}

	//授权回调地址
	public function callback($type = null, $code = null){
		(empty($type)) && $this->error('参数错误');

		if(empty($code)){
			redirect(__ROOT__."/");
		}

		$sns  = \Cp\Sys\Oauth::getInstance($type);
		$extend = null;
        // 获取TOKEN
		$token = $sns->getAccessToken($code , $extend);

		//获取当前第三方登录用户信息
		if(is_array($token)){
			  $user_info = \Cp\Sys\GetInfo::getInstance($type,$token);
			  var_dump($user_info);
		}else{
              echo "获取基本信息失败";
		}
	}
}
```

# DEMO介绍
>thinkPHP 3.2.3 为例
- 环境要求 php >= 5.3
- 控制器处理 =》 /demo/Application/Api/Controller
- 配置文件配置 =》 /demo/Application/Common/Conf/config.php
>运行demo项目 http://127.0.0.1/demo