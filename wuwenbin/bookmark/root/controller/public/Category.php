<?php
/**
 * 分类管理
 */
class ControllerCategory extends ControllerBase
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        if (!$this->mid) {
            returnJson(0, "用户未登录");
        }
    }

    /**
     * 添加分类
     */
    public function actionAdd()
    {
        if (isPost()) {
            $name = trim(P("name", ""));
            $isPrivate = (int) P("is_private") ? 1 : 0;

            if (strlen($name) < 1) {
                returnJson(0, "分类名称不能为空");
            }

            if (M("Category")->getCountByTime($this->mid, 3600) > Config::get("categoryCountPerHour")) {
                returnJson(0, "超过每小时添加限额");
            }

            $id = M("Category")->add(array(
                "name" => addslashes($name),
                "is_private" => $isPrivate,
                "ctime" => time(),
                "uid" => $this->mid,
            ));

            if ($id) {
                M("Category")->set($id, array(
                    "sort" => $id,
                ));
                returnJson(1, "添加分类成功", $id);
            } else {
                returnJson(0, "添加分类失败");
            }

        } else {
            $data = Template::display("Category_Add", array(
                "mid" => $this->mid,
            ), true);
            returnJson(1, "", $data);
        }
    }

    /**
     * 修改分类
     */
    public function actionEdit()
    {
        if (isPost()) {
            $id = (int) P("id");
            $name = trim(P("name", ""));
            $isPrivate = (int) P("is_private") ? 1 : 0;

            $category = M("Category")->get($id);
            if (!$category) {
                returnJson(0, "分类不存在");
            }
            if ($category["uid"] != $this->mid) {
                returnJson(0, "无权修改此分类");
            }

            if (strlen($name) < 1) {
                returnJson(0, "分类名称不能为空");
            }

            $res = M("Category")->set($id, array(
                "name" => addslashes($name),
                "is_private" => $isPrivate,
            ));

            if ($res) {
                returnJson(1, "修改分类成功");
            } else {
                returnJson(0, "修改分类失败");
            }

        } else {
            $id = (int) G("id");

            $category = M("Category")->get($id);
            if (!$category) {
                returnJson(0, "分类不存在");
            }
            if ($category["uid"] != $this->mid) {
                returnJson(0, "无权修改此分类");
            }

            $data = Template::display("Category_Edit", array(
                "cid" => $id,
                "category" => $category,
                "mid" => $this->mid,
            ), true);
            returnJson(1, "", $data);
        }
    }

    /**
     * 删除分类
     */
    public function actionDelete()
    {
        if (isPost()) {
            $id = (int) P("id");

            $category = M("Category")->get($id);
            if (!$category) {
                returnJson(0, "分类不存在");
            }
            if ($category["uid"] != $this->mid) {
                returnJson(0, "无权修改此分类");
            }
            if ($category["is_default"]) {
                returnJson(0, "禁止删除默认分类");
            }

            $res = M("Category")->del($id);
            if ($res) {
                M("Link")->delByCid($id);
                returnJson(1, "删除分类成功");
            } else {
                returnJson(0, "删除分类失败");
            }
        }
    }

    /**
     * 交换分类排序
     */
    public function actionExchange()
    {
        if (isPost()) {
            $id1 = (int) P("id1");
            $id2 = (int) P("id2");

            if ($id1 == $id2) {
                returnJson(0, "交换分类不能为相同分类");
            }

            $category1 = M("Category")->get($id1);
            if (!$category1) {
                returnJson(0, "分类不存在");
            }
            if ($category1["uid"] != $this->mid) {
                returnJson(0, "无权修改此分类");
            }

            $category2 = M("Category")->get($id2);
            if (!$category2) {
                returnJson(0, "分类不存在");
            }
            if ($category2["uid"] != $this->mid) {
                returnJson(0, "无权修改此分类");
            }

            $res1 = M("Category")->set($id1, array(
                "sort" => $category2["sort"],
            ));
            $res2 = M("Category")->set($id2, array(
                "sort" => $category1["sort"],
            ));

            if ($res1 && $res2) {
                returnJson(1, "分类移动成功");
            } else {
                returnJson(0, "分类移动失败");
            }
        }
    }
}
