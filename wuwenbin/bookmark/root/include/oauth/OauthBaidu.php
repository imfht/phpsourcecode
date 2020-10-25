<?php
/**
 * 百度账号登录
 */
class OauthBaidu
{
    /**
     * @var string $clientId 应用ID
     */
    protected $clientId = "";

    /**
     * @var string $clientSecret 应用密钥
     */
    protected $clientSecret = "";

    /**
     * @var string $redirectUri 回调地址
     */
    protected $redirectUri = "";

    /**
     * 构造函数
     *
     * @param array $options 配置信息
     */
    public function __construct(array $options = array())
    {
        foreach ($options as $name => $value) {
            switch ($name) {
                case "clientId":
                    $this->clientId = $value;
                    break;
                case "clientSecret":
                    $this->clientSecret = $value;
                    break;
                case "redirectUri":
                    $this->redirectUri = $value;
            }
        }
    }

    /**
     * 获取登录地址
     *
     * @return string
     */
    public function getLoginUrl()
    {
        $baseUrl = "http://openapi.baidu.com/oauth/2.0/authorize";
        $query = "response_type=code"
        . "&client_id=" . $this->clientId
        . "&redirect_uri=" . $this->redirectUri
        . "&display=popup";
        return $baseUrl . "?" . $query;
    }

    /**
     * 获取授权信息
     *
     * @return mixed
     */
    public function getToken()
    {
        $baseUrl = "https://openapi.baidu.com/oauth/2.0/token";
        $code = isset($_GET["code"]) ? $_GET["code"] : "";
        $query = "grant_type=authorization_code"
        . "&code=" . $code
        . "&client_id=" . $this->clientId
        . "&client_secret=" . $this->clientSecret
        . "&redirect_uri=" . $this->redirectUri;
        $res = http($baseUrl . "?" . $query, null, 10);
        $res = json_decode($res, true);
        if (!isset($res["access_token"])) {
            return false;
        }

        return $res;
    }

    /**
     * 获取用户信息
     *
     * @param array $token 授权信息
     * @return mixed
     */
    public function getUserInfo(array $token)
    {
        $baseUrl = "https://openapi.baidu.com/rest/2.0/passport/users/getInfo";
        $query = "access_token=" . $token["access_token"];
        $res = http($baseUrl . "?" . $query, null, 10);
        $res = json_decode($res, true);
        if (!isset($res["userid"])) {
            return false;
        }

        return array(
            "id" => $res["userid"],
            "name" => $res["username"],
            "avatar" => "http://tb.himg.baidu.com/sys/portrait/item/" . $res["portrait"],
            "url" => "http://www.baidu.com/p/" . $res["username"],
        );
    }
}
