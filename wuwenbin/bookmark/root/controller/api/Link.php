<?php
/**
 * 网址接口
 */
class ControllerLink extends ControllerBaseApi
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();

        if (!$this->mid) {
            $this->send(0, '用户未登录');
        }
    }

    /**
     * 获取网址列表
     */
    public function actionGetList()
    {
        $uid = $this->mid;
        $cid = (int) $this->getParam("cid");
        $kw = trim($this->getParam("kw", ""));
        $page = (int) $this->getParam("page", 1);
        $each = (int) $this->getParam("each", 20);

        list($linkList, $count) = M("Link")->getList($page, $each, array(
            "uid" => $uid,
            "cid" => $cid ? $cid : null,
            "title" => strlen($kw) > 0 ? $kw : null,
        ));

        $list = array();
        foreach ($linkList as $v) {
            $list[] = array(
                'id' => (int) $v['id'],
                'title' => $v['title'],
                'url' => $v['url'],
                'ctime' => (int) $v['ctime'],
                'cid' => (int) $v['cid'],
            );
        }

        $this->sendEncrypted(1, '获取网址列表成功', array(
            'list' => $list,
            'count' => $count,
            'page' => $page,
            'each' => $each,
        ));
    }

    /**
     * 添加网址
     */
    public function actionAdd()
    {
        $cid = (int) $this->getParam("cid");
        $url = trim($this->getParam("url", ""));
        $title = trim($this->getParam("title", ""));

        $category = M("Category")->get($cid);
        if (!$category) {
            $this->sendEncrypted(0, "分类不存在");
        }
        if ($category["uid"] != $this->mid) {
            $this->sendEncrypted(0, "无权发布到此分类");
        }
        if (strlen($url) < 1) {
            $this->sendEncrypted(0, "网址不能为空");
        }
        if (!preg_match("/^https?:\/\//i", $url)) {
            $url = "http://" . $url;
        }
        if (strlen($title) < 1) {
            $title = getHtmlTitle($url);
        }
        if (strlen($title) < 1) {
            $this->sendEncrypted(0, "标题不能为空");
        }
        if (M("Link")->getCountByTime($this->mid, 3600) > Config::get("linkCountPerHour")) {
            $this->sendEncrypted(0, "超过每小时添加限额");
        }

        $id = M("Link")->add(array(
            "title" => addslashes($title),
            "url" => addslashes($url),
            "ctime" => time(),
            "cid" => $cid,
            "uid" => $this->mid,
        ));

        if ($id) {
            $this->sendEncrypted(1, "网址添加成功", $id);
        } else {
            $this->sendEncrypted(0, "网址添加失败");
        }
    }

    /**
     * 修改网址
     */
    public function actionEdit()
    {
        $id = (int) $this->getParam("id");
        $cid = (int) $this->getParam("cid");
        $url = trim($this->getParam("url", ""));
        $title = trim($this->getParam("title", ""));

        $link = M("Link")->get($id);
        if (!$link) {
            $this->sendEncrypted(0, "网址不存在");
        }

        if ($link["uid"] != $this->mid) {
            $this->sendEncrypted(0, "无权修改此网址");
        }

        $category = M("Category")->get($cid);
        if (!$category) {
            $this->sendEncrypted(0, "分类不存在");
        }
        if ($category["uid"] != $this->mid) {
            $this->sendEncrypted(0, "无权发布到此分类");
        }
        if (strlen($url) < 1) {
            $this->sendEncrypted(0, "网址不能为空");
        }
        if (!preg_match("/^https?:\/\//i", $url)) {
            $url = "http://" . $url;
        }
        if (strlen($title) < 1) {
            $title = getHtmlTitle($url);
        }
        if (strlen($title) < 1) {
            $this->sendEncrypted(0, "标题不能为空");
        }

        $res = M("Link")->set($id, array(
            "title" => addslashes($title),
            "url" => addslashes($url),
            "cid" => $cid,
        ));

        if ($res) {
            $this->sendEncrypted(1, "修改网址成功");
        } else {
            $this->sendEncrypted(0, "修改网址失败");
        }
    }

    /**
     * 删除网址
     */
    public function actionDelete()
    {
        $id = (int) $this->getParam("id");

        $link = M("Link")->get($id);
        if (!$link) {
            $this->sendEncrypted(0, "网址不存在");
        }

        if ($link["uid"] != $this->mid) {
            $this->sendEncrypted(0, "无权删除此网址");
        }

        $res = M("link")->del($id);
        if ($res) {
            $this->sendEncrypted(1, "删除网址成功");
        } else {
            $this->sendEncrypted(0, "删除网址失败");
        }
    }
}
