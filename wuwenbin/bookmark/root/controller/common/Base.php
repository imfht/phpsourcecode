<?php
/**
 *  控制器基类
 */
class ControllerBase
{
    const ERROR_BAD_REQUEST = "非法请求";

    /**
     * @var int $mid 登录用户ID
     */
    protected $mid = 0;

    /**
     * @var mixed $user 登录用户信息
     */
    protected $user = null;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->checkPost();
        $this->checkLogin();
        $baseUrl = (isHttps() ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . rtrim(dirname($_SERVER["SCRIPT_NAME"]), "/") . "/";
        if (!Config::get("baseUrl")) {
            Config::set("baseUrl", $baseUrl);
        }
        Router::setBaseUrl(Config::get("baseUrl"));
        defined("BASE_URL") || define("BASE_URL", rtrim($baseUrl, "/"));
    }

    /**
     * 检查POST数据合法性
     */
    protected function checkPost()
    {
        if (!isPost()) {
            return;
        }

        $pToken = P("_token", "");
        $cToken = C("_token", "");
        setcookie("_token", "", time() - 3600, "/");

        if ($pToken != $cToken || !$pToken) {
            if (isAjax()) {
                returnJson(0, "非法请求");
            } else {
                exitUtf8("非法请求");
            }
        }
    }

    /**
     * 检查登录状态
     */
    protected function checkLogin()
    {
        $mid = session("mid");
        $user = M("User")->get($mid, "uid");
        $this->mid = $user ? $user["uid"] : 0;
        $this->user = $user ?: null;
    }
}
