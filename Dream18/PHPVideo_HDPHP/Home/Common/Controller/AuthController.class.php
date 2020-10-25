<?php
/**
 * 「PHP联盟」
 * 认证控制器
 * 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class AuthController extends Controller
{
	public function __construct()
	{
        parent::__construct();
        //设置此页面的过期时间(用格林威治时间表示)，只要是已经过去的日期即可。
        header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");
        //设置此页面的最后更新日期(用格林威治时间表示)为当天，可以强制浏览器获取最新资料
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        //告诉客户端浏览器不使用缓存，HTTP 1.1 协议
        header("Cache-Control: no-cache, must-revalidate");
        //告诉客户端浏览器不使用缓存，兼容HTTP 1.0 协议
        header("Pragma: no-cache");
    }

    /**
     * [__init 构造函数]
     * @return [type] [description]
     */
	public function __init()
	{
		header('Content-Type:text/html;charset=utf-8');
		if(!session('uid'))
		{
			$this->error('你没有权限，请重新登录。','Admin/Login/index');
		}
	}
}