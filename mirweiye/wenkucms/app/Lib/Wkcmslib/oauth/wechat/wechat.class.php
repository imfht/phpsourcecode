<?php
/**
 * wechat
 */
class Wechat {
    public $appid = '';
    public $appkey = '';
    public $scope = 'snsapi_userinfo';
    private $_authorize_url = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    function __construct($appid, $appkey) {
        $this->appid = $appid;
        $this->appkey = $appkey;
    }
    function getAuthorizeURL($callback) {
        $state = md5(uniqid(rand(), TRUE));
        $url = $this->_authorize_url . "?response_type=code&appid="
          . $this->appid . "&redirect_uri=" . urlencode($callback)
          . "&state=" . $state
          . "&scope=".$this->scope . '#wechat_redirect';
        cookie('wechat_state', $state);
      	return $url;
    }
    function getAccessToken($keys) {
        $wechat_state = cookie('wechat_state');
        if ($keys['state']) {

            $data = $_GET['data'];
            if (!$data) {
                $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code&"
              . "appid=" . $this->appid . "&secret=" . $this->appkey . "&code=" . $keys["code"];

                $response = $this->get_url_contents($token_url);
                if (!$response) {
                    exit('system error');
                }
            } else {
                $response = $data;
            }
            

            $response = json_decode($response, true);
            if (isset($response['errcode']) && $response['errcode']) {
                print_r($response);die;
            }
            return $response;
        } else {
            echo("The state does not match. You may be a victim of CSRF.");
        }
    }
    function getOpenid($access_token) {
        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $access_token;
        $str  = file_get_contents($graph_url);
        if (strpos($str, "callback") !== false) {
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
        }
        $user = json_decode($str);
        if (isset($user->error)) {
            echo "<h3>error:</h3>" . $user->error;
            echo "<h3>msg  :</h3>" . $user->error_description;
            exit;
        }
        return $user->openid;
    }
    function getUserInfo($access_token, $openid) {
        $get_user_info = "https://api.weixin.qq.com/sns/userinfo?"
            . "access_token=".$access_token
            . "&openid=".$openid
            . "&lang=zh_CN";

        $info = $this->get_url_contents($get_user_info, true);
        $arr = json_decode($info, true);
        return $arr;
    }

    function do_post($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }

    function get_url_contents($url, $state = false) {
//        if (ini_get("allow_url_fopen") == "1")
//            return file_get_contents($url);

        # 本地测试用了代理...
        $state = true;
        if (!$state) {
            $refer = 'http://fenxiao.1209.com.cn/system/index.php?m=oauth&a=callback&mod=wechat&code=081ouaWb2sTuWO0SBCWb27M7Wb2ouaW7&state=d52a83825e847b39e601ae2e988caf49';
            $url = 'http://data.dever.cc/curl.php?url=' . base64_encode($url) . '&refer=' . base64_encode($refer);
            header('location:' . $url);
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $result =  curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}