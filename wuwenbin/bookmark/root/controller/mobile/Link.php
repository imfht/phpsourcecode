<?php
/**
 * 网址管理
 */
class ControllerLink extends ControllerBaseMobile
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        if (in_array(G("do"), array("Link_Save"))) {
            return;
        }
        if (!$this->mid) {
            returnJson(0, "用户未登录");
        }
    }

    /**
     * 添加网址
     */
    public function actionAdd()
    {
        if (isPost()) {
            $cid = (int) P("cid");
            $url = trim(P("url", ""));
            $title = trim(P("title", ""));

            $category = M("Category")->get($cid);
            if (!$category) {
                returnJson(0, "分类不存在");
            }
            if ($category["uid"] != $this->mid) {
                returnJson(0, "无权发布到此分类");
            }
            if (strlen($url) < 1) {
                returnJson(0, "网址不能为空");
            }
            if (!preg_match("/^https?:\/\//i", $url)) {
                $url = "http://" . $url;
            }
            if (strlen($title) < 1) {
                $title = getHtmlTitle($url);
            }
            if (strlen($title) < 1) {
                returnJson(0, "标题不能为空");
            }
            if (M("Link")->getCountByTime($this->mid, 3600) > Config::get("linkCountPerHour")) {
                returnJson(0, "超过每小时添加限额");
            }

            $id = M("Link")->add(array(
                "title" => addslashes($title),
                "url" => addslashes($url),
                "ctime" => time(),
                "cid" => $cid,
                "uid" => $this->mid,
            ));

            if ($id) {
                returnJson(1, "网址添加成功");
            } else {
                returnJson(0, "网址添加失败");
            }

        } else {
            $link = session("link");
            session("link", null);
            Template::display("Link_Add", array(
                "link" => $link,
                "categoryList" => M("Category")->getListByUid($this->mid),
                "isAdded" => $link ? M("Link")->hasExisted($link["url"], $this->mid) : false,
                "mid" => $this->mid,
            ));
        }
    }

    /**
     * 浏览器工具保存
     */
    public function actionSave()
    {
        $url = trim(G("url", ""));
        if (!preg_match("/^https?:\/\//i", $url)) {
            $url = "http://" . $url;
        }

        $title = getHtmlTitle($url);
        session("link", array(
            "url" => $url,
            "title" => $title,
        ));

        if ($this->mid) {
            redirect(Router::buildUrl("User_Index", array("uid" => $this->mid)));
        } else {
            redirect(Config::get("baseUrl"));
        }
    }
}
