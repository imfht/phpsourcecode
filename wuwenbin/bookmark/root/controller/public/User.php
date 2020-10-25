<?php
/**
 * 用户操作
 */
class ControllerUser extends ControllerBase
{
    /**
     * @var array $types 第三方账号类型
     */
    protected $types = array("weibo", "qq", "baidu");

    /**
     * 用户首页
     */
    public function actionIndex()
    {
        $uid = (int) G("uid");
        $cid = (int) G("cid");
        $kw = trim(G("kw", ""));
        $page = (int) G("p", 1);
        $each = 11;

        $user = M("User")->get($uid, "uid");
        if (!$user) {
            alert("用户不存在", Config::get("baseUrl"));
        }

        $categoryList = M("Category")->getListByUid($uid);

        $category = null;
        $cidsPublic = array();
        foreach ($categoryList as $v) {
            if (!$v["is_private"]) {
                $cidsPublic[] = $v["id"];
            }
            if ($v["id"] == $cid) {
                $category = $v;
            }
        }

        if ($cid) {
            if (is_null($category)) {
                alert("分类不存在", Router::buildUrl("User_Index", array("uid" => $uid)));
            }

            if ($uid != $this->mid && $category["is_private"]) {
                alert("无权查看此分类", Router::buildUrl("User_Index", array("uid" => $uid)));
            }
        }

        list($linkList, $count) = M("Link")->getList($page, $each, array(
            "uid" => $uid,
            "cid" => $cid ? $cid : ($uid == $this->mid ? null : $cidsPublic),
            "title" => strlen($kw) > 0 ? $kw : null,
        ));

        Template::display("User_Index", array(
            "title" => $user["name"] . "的书签 - " . ($cid ? $category["name"] : (strlen($kw) > 0 ? "搜索 - " . $kw : "首页")),
            "mid" => $this->mid,
            "uid" => $uid,
            "user" => $user,
            "cid" => $cid,
            "kw" => $kw,
            "category" => $category,
            "categoryList" => $categoryList,
            "linkList" => $linkList,
            "pageHtml" => getPageHtml2(getPageUrl("p"), $page, $each, $count),
            "isPost" => session("link") ? true : false,
        ));
    }

    /**
     * 随机访问
     */
    public function actionRand()
    {
        $uid = M("Link")->getRandUidByCount(Config::get("randUserMinLinkCount"));
        if ($uid) {
            redirect(Router::buildUrl("User_Index", array("uid" => $uid)));
        } else {
            redirect(Config::get("baseUrl"));
        }
    }

    /**
     * 用户登录
     */
    public function actionLogin()
    {
        $app = G("app");
        if ($app == "mobile") {
            $baseUrl = BASE_URL . "/m/";
        } else {
            $baseUrl = Config::get("baseUrl");
        }

        if ($this->mid) {
            redirect(Router::buildUrl("User_Index", array("uid" => $this->mid), $baseUrl));
        }

        $oauth = getOauthInstance(G("type"));
        if (!$oauth) {
            alert("不支持的第三方账号类型", $baseUrl);
        }

        session("login_from", $app);
        redirect($oauth->getLoginUrl());
    }

    /**
     * 用户登录回调
     */
    public function actionLoginCallback()
    {
        if (session("login_from") == "mobile") {
            $baseUrl = BASE_URL . "/m/";
        } else {
            $baseUrl = Config::get("baseUrl");
        }

        if ($this->mid) {
            redirect(Router::buildUrl("User_Index", array("uid" => $this->mid), $baseUrl));
        }

        $type = G("type");
        $oauth = getOauthInstance($type);
        if (!$oauth) {
            alert("不支持的第三方账号类型", $baseUrl);
        }

        $token = $oauth->getToken();
        if (!$token) {
            alert("获取第三方账号授权信息失败", $baseUrl);
        }

        $userInfo = $oauth->getUserInfo($token);
        if (!$userInfo) {
            alert("获取第三方账号信息失败", $baseUrl);
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
                alert("保存用户信息失败", $baseUrl);
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
        }

        session("mid", $uid);
        redirect($baseUrl);
    }

    /**
     * 用户退出
     */
    public function actionLogout()
    {
        $app = G("app");
        if ($app == "mobile") {
            $baseUrl = BASE_URL . "/m/";
        } else {
            $baseUrl = Config::get("baseUrl");
        }

        session("mid", null);
        redirect($baseUrl);
    }
}
