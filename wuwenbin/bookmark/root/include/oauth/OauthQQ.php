<?php
/**
 * QQ账号登录
 */
class OauthQQ
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
        $baseUrl = "https://graph.qq.com/oauth2.0/authorize";
        $query = "response_type=code"
        . "&client_id=" . $this->clientId
        . "&redirect_uri=" . $this->redirectUri
        . "&state=" . md5(time());
        return $baseUrl . "?" . $query;
    }

    /**
     * 获取授权信息
     *
     * @return mixed
     */
    public function getToken()
    {
        $baseUrl = "https://graph.qq.com/oauth2.0/token";
        $code = isset($_GET["code"]) ? $_GET["code"] : "";
        $query = "grant_type=authorization_code"
        . "&client_id=" . $this->clientId
        . "&client_secret=" . $this->clientSecret
        . "&code=" . $code
        . "&redirect_uri=" . $this->redirectUri;
        $res = http($baseUrl . "?" . $query, null, 10);
        parse_str($res, $token);
        if (!is_array($token) || !isset($token["access_token"])) {
            return false;
        }

        $baseUrl = "https://graph.qq.com/oauth2.0/me";
        $query = "access_token=" . $token["access_token"];
        $res = http($baseUrl . "?" . $query, null, 10);
        if (preg_match("/\{.+\}/", $res, $match)) {
            $temp = json_decode($match[0], true);
        } else {
            parse_str($res, $temp);
        }
        if (!isset($temp["openid"])) {
            return false;
        }
        $token["openid"] = $temp["openid"];

        return $token;
    }

    /**
     * 获取用户信息
     *
     * @param array $token 授权信息
     * @return mixed
     */
    public function getUserInfo(array $token)
    {
        $baseUrl = "https://graph.qq.com/user/get_user_info";
        $query = "access_token=" . $token["access_token"]
        . "&oauth_consumer_key=" . $this->clientId
        . "&openid=" . $token["openid"];
        $res = http($baseUrl . "?" . $query, null, 10);
        $res = json_decode($res, true);
        if (!isset($res["ret"]) || $res["ret"] != 0) {
            return false;
        }

        $baseUrl = " https://graph.qq.com/user/get_info";
        $query = "access_token=" . $token["access_token"]
        . "&oauth_consumer_key=" . $this->clientId
        . "&openid=" . $token["openid"]
        . "&format=json";
        $res2 = http($baseUrl . "?" . $query, null, 10);
        $res2 = json_decode($res2, true);

        return array(
            "id" => $token["openid"],
            "name" => $res["nickname"],
            "avatar" => $res["figureurl_qq_2"] ?: $res["figureurl_2"],
            "url" => isset($res2["data"]["homepage"]) ? $res2["data"]["homepage"] : "",
        );
    }
}
