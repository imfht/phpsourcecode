<?php
/**
 * 网址管理
 */
class ControllerLink extends ControllerBase
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
            $data = Template::display("Link_Add", array(
                "link" => $link,
                "categoryList" => M("Category")->getListByUid($this->mid),
                "isAdded" => $link ? M("Link")->hasExisted($link["url"], $this->mid) : false,
                "mid" => $this->mid,
            ), true);
            returnJson(1, "", $data);
        }
    }

    /**
     * 修改网址
     */
    public function actionEdit()
    {
        if (isPost()) {
            $id = (int) P("id");
            $cid = (int) P("cid");
            $url = trim(P("url", ""));
            $title = trim(P("title", ""));

            $link = M("Link")->get($id);
            if (!$link) {
                returnJson(0, "网址不存在");
            }

            if ($link["uid"] != $this->mid) {
                returnJson(0, "无权修改此网址");
            }

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

            $res = M("Link")->set($id, array(
                "title" => addslashes($title),
                "url" => addslashes($url),
                "cid" => $cid,
            ));

            if ($res) {
                returnJson(1, "修改网址成功");
            } else {
                returnJson(0, "修改网址失败");
            }

        } else {
            $id = (int) G("id");

            $link = M("Link")->get($id);
            if (!$link) {
                returnJson(0, "网址不存在");
            }

            if ($link["uid"] != $this->mid) {
                returnJson(0, "无权修改此网址");
            }

            $data = Template::display("Link_Edit", array(
                "link" => $link,
                "categoryList" => M("Category")->getListByUid($this->mid),
                "mid" => $this->mid,
            ), true);
            returnJson(1, "", $data);
        }
    }

    /**
     * 删除网址
     */
    public function actionDelete()
    {
        if (isPost()) {
            $id = (int) P("id");

            $link = M("Link")->get($id);
            if (!$link) {
                returnJson(0, "网址不存在");
            }

            if ($link["uid"] != $this->mid) {
                returnJson(0, "无权删除此网址");
            }

            $res = M("link")->del($id);
            if ($res) {
                returnJson(1, "删除网址成功");
            } else {
                returnJson(0, "删除网址失败");
            }
        }
    }

    /**
     * 导出网址
     */
    public function actionExport()
    {
        $cid = G("cid");
        if (is_null($cid)) {
            $data = Template::display("Link_Export", array(
                "categoryList" => M("Category")->getListByUid($this->mid),
            ), true);
            returnJson(1, "", $data);
        } else {
            $cid = (int) $cid;
            $res = M("Category")->getListByUid($this->mid);
            $cids = array();
            $categoryList = array();
            foreach ($res as $v) {
                if ($cid) {
                    if ($cid == $v["id"]) {
                        $cids[] = $v["id"];
                        $categoryList[$v["id"]] = $v;
                        $categoryList[$v["id"]]["links"] = array();
                    }
                } else {
                    $cids[] = $v["id"];
                    $categoryList[$v["id"]] = $v;
                    $categoryList[$v["id"]]["links"] = array();
                }
            }

            if (empty($cids)) {
                exitUtf8("分类不存在或无权操作");
            }

            list($linkList, $count) = M("Link")->getList(1, 10000, array(
                "cid" => $cid ?: $cids,
            ));

            foreach ($linkList as $v) {
                $cid = $v["cid"];
                if (isset($categoryList[$cid])) {
                    $categoryList[$cid]["links"][] = $v;
                }
            }

            header("content-type: text/html");
            header("content-disposition: attachment; filename=\"bookmark-" . date("Y-m-d", time()) . ".html\"");

            Template::display("Link_Export_Data", array(
                "list" => $categoryList,
            ));
        }
    }

    /**
     * 导入网址
     */
    public function actionImport()
    {
        if (isPost()) {
            $file = F("file");
            if (!$file) {
                returnJson(0, "未上传文件");
            }
            if ($file["error"] != UPLOAD_ERR_OK) {
                returnJson(0, "上传文件失败");
            }
            if (filesize($file["tmp_name"]) > 1024 * 1024) {
                returnJson(0, "上传文件大小不能超过1M");
            }

            // NETSCAPE-Bookmark-file-1协议解析
            $fp = fopen($file["tmp_name"], "r");
            $dlp = 0;
            $c = $_c = "未分类";
            $list = array(
                $c => array(),
            );

            while (!feof($fp)) {
                $line = trim(fgets($fp, 1024 * 1024));
                if (strtoupper($line) == "<DL><P>") {
                    $dlp++;
                } elseif (strtoupper($line) == "</DL><P>") {
                    $dlp--;
                    if ($dlp == 1) {
                        $c = $_c;
                    }
                } elseif (preg_match("/^<DT><H3[^>]*>(.*)<\/H3>$/i", $line, $m)) {
                    if ($dlp == 1) {
                        $c = trim($m[1]);
                        if (!isset($list[$c])) {
                            $list[$c] = array();
                        }
                    }
                } elseif (preg_match("/^<DT><A\s+[^>]*HREF=\"([^>\"]+)\"[^>]*>(.*)<\/A>$/i", $line, $m)) {
                    $url = trim($m[1]);
                    $title = trim($m[2]);
                    if (!isset($list[$c][$url])) {
                        $list[$c][$url] = $title;
                    }
                }
            }

            $success = 0;
            foreach ($list as $k => $v) {
                if (empty($v)) {
                    continue;
                }

                // 创建分类
                $cname = trim($k);
                $category = M("Category")->getByName($this->mid, $cname);
                if ($category) {
                    $cid = $category["id"];
                } else {
                    $cid = M("Category")->add(array(
                        "name" => addslashes($cname),
                        "ctime" => time(),
                        "uid" => $this->mid,
                    ));
                    if (!$cid) {
                        continue;
                    }

                    M("Category")->set($cid, array(
                        "sort" => $cid,
                    ));
                }

                // 导入书签
                foreach ($v as $_k => $_v) {
                    $url = trim($_k);
                    $title = trim($_v);

                    if (M("Link")->hasExisted($url, $this->mid)) {
                        continue;
                    }

                    if (!preg_match("/^https?:\/\//i", $url)) {
                        $url = "http://" . $url;
                    }

                    $id = M("Link")->add(array(
                        "title" => addslashes($title),
                        "url" => addslashes($url),
                        "ctime" => time(),
                        "cid" => $cid,
                        "uid" => $this->mid,
                    ));

                    if ($id) {
                        $success++;
                    }
                }
            }

            returnJson(1, "成功导入书签{$success}个");

        } else {
            $data = Template::display("Link_Import", array(
                "categoryList" => M("Category")->getListByUid($this->mid),
                "mid" => $this->mid,
            ), true);
            returnJson(1, "", $data);
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
            alert('请先登录', Config::get("baseUrl"));
        }
    }
}
