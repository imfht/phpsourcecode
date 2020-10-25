<?php
/**
 * 用户接口
 */
class ControllerUser extends ControllerBaseApi
{
    /**
     * 用户授权
     */
    public function actionAuth()
    {
        $appId = (int) $this->getParam('app_id');
        $appSecret = $this->getParam('app_secret');
        $type = $this->getParam('oauth_type');
        $token = (array) $this->getParam('oauth_token');

        if (!$this->checkAppId($appId)) {
            $this->send(0, "应用ID不合法");
        }

        if (!$this->checkAppSecret($appSecret)) {
            $this->send(0, "应用密钥不合法");
        }

        $oauth = getOauthInstance($type);
        if (!$oauth) {
            $this->send(0, "不支持的第三方账号类型");
        }

        $userInfo = $oauth->getUserInfo($token);
        if (!$userInfo) {
            $this->send(0, "获取第三方账号信息失败");
        }

        $user = M("User")->getByType($type, $userInfo["id"]);
        if ($user) {
            M("User")->set($user["uid"], array(
                "name" => addslashes($userInfo["name"]),
                "avatar" => addslashes($userInfo["avatar"]),
                "url" => addslashes($userInfo["url"]),
                "login_ip" => getClientIp(),
                "login_time" => time(),
            ), "uid");
            $uid = $user["uid"];
        } else {
            $uid = M("User")->add(array(
                "type" => $type,
                "id" => addslashes($userInfo["id"]),
                "name" => addslashes($userInfo["name"]),
                "avatar" => addslashes($userInfo["avatar"]),
                "url" => addslashes($userInfo["url"]),
                "login_ip" => getClientIp(),
                "login_time" => time(),
            ));

            if (!$uid) {
                $this->send(0, "保存用户信息失败");
            }

            $cid = M("Category")->add(array(
                "name" => "默认分类",
                "is_default" => 1,
                "ctime" => time(),
                "uid" => $uid,
            ));

            if ($cid) {
                M("Category")->set($cid, array(
                    "sort" => $cid,
                ));
            }

            $user = M("User")->get($uid);
        }

        $id = uuid($user["uid"]);
        $create_time = time();
        $expire_time = $create_time + 3600 * 24 * 30;
        $this->token = array(
            "id" => $id,
            "app_id" => $appId,
            "app_secret" => $appSecret,
            "uid" => $user["uid"],
            "expire_time" => $expire_time,
            "create_time" => $create_time,
        );

        $res = M("Token")->add($this->token);

        $this->sendEncrypted(1, "用户授权成功", array(
            "user" => array(
                "uid" => (int) $user["uid"],
                "name" => $user["name"],
                "avatar" => $user["avatar"],
            ),
            "token" => $id,
            "expire_time" => $expire_time,
            "create_time" => $create_time,
        ));
    }

    /**
     * 检查应用ID是否合法
     *
     * @param int $id 应用ID
     *
     * @return bool
     */
    protected function checkAppId($id)
    {
        if ($id <= 0) {
            return false;
        }
    }

    /**
     * 检查应用密钥是否合法
     *
     * @param string $secret 密钥
     *
     * @return bool
     */
    protected function checkAppSecret($secret)
    {
        return strlen($secret) == 32 ? true : false;
    }
}
