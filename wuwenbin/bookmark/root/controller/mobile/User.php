<?php
/**
 * 用户操作
 */
class ControllerUser extends ControllerBaseMobile
{
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
            "pageHtml" => getPageHtml3(getPageUrl("p"), $page, $each, $count),
            "isPost" => session("link") ? true : false,
        ));
    }
}
