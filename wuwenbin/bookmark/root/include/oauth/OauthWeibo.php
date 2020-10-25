<?php
/**
 * 微博账号登录
 */
class OauthWeibo
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
        $baseUrl = "https://api.weibo.com/oauth2/authorize";
        $query = "client_id=" . $this->clientId
        . "&redirect_uri=" . $this->redirectUri
        . "&response_type=code";
        return $baseUrl . "?" . $query;
    }

    /**
     * 获取授权信息
     *
     * @return mixed
     */
    public function getToken()
    {
        $baseUrl = "https://api.weibo.com/oauth2/access_token";
        $code = isset($_GET["code"]) ? $_GET["code"] : "";
        $query = "client_id=" . $this->clientId
        . "&client_secret=" . $this->clientSecret
        . "&grant_type=authorization_code"
        . "&code=" . $code
        . "&redirect_uri=" . $this->redirectUri;
        $res = http($baseUrl, $query, 10);
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
        $baseUrl = "https://api.weibo.com/2/users/show.json";
        $query = "access_token=" . $token["access_token"]
        . "&uid=" . $token["uid"];
        $res = http($baseUrl . "?" . $query);
        $res = json_decode($res, true);
        if (!isset($res["id"])) {
            return false;
        }

        return array(
            "id" => sprintf("%d", $res["id"]),
            "name" => $res["name"],
            "avatar" => $res["avatar_large"],
            "url" => "http://weibo.com/" . $res["profile_url"],
        );
    }
}
