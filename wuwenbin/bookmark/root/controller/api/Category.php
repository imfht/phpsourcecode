<?php
/**
 * 分类接口
 */
class ControllerCategory extends ControllerBaseApi
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
     * 获取分类列表
     */
    public function actionGetList()
    {
        $categoryList = M('Category')->getListByUid($this->mid);
        $data = array();
        foreach ($categoryList as $v) {
            $data[] = array(
                'id' => (int) $v['id'],
                'name' => $v['name'],
                'sort' => (int) $v['sort'],
                'is_default' => (int) $v['is_default'],
                'is_private' => (int) $v['is_private'],
                'ctime' => (int) $v['ctime'],
            );
        }

        $this->sendEncrypted(1, '获取分类列表成功', $data);
    }

    /**
     * 添加分类
     */
    public function actionAdd()
    {
        $name = trim($this->getParam('name', ''));
        $isPrivate = $this->getParam('is_private') ? 1 : 0;

        if (strlen($name) < 1) {
            $this->sendEncrypted(0, '分类名称不能为空');
        }

        if (M('Category')->getCountByTime($this->mid, 3600) > Config::get('categoryCountPerHour')) {
            $this->sendEncrypted(0, '超过每小时添加限额');
        }

        $id = M('Category')->add(array(
            'name' => addslashes($name),
            'is_private' => $isPrivate,
            'ctime' => time(),
            'uid' => $this->mid,
        ));

        if ($id) {
            M('Category')->set($id, array(
                'sort' => $id,
            ));
            $this->sendEncrypted(1, '添加分类成功', $id);
        } else {
            $this->sendEncrypted(0, '添加分类失败');
        }
    }

    /**
     * 修改分类
     */
    public function actionEdit()
    {
        $id = (int) $this->getParam("id");
        $name = trim($this->getParam("name", ""));
        $isPrivate = $this->getParam("is_private") ? 1 : 0;

        $category = M("Category")->get($id);
        if (!$category) {
            $this->sendEncrypted(0, "分类不存在");
        }
        if ($category["uid"] != $this->mid) {
            $this->sendEncrypted(0, "无权修改此分类");
        }

        if (strlen($name) < 1) {
            $this->sendEncrypted(0, "分类名称不能为空");
        }

        $res = M("Category")->set($id, array(
            "name" => addslashes($name),
            "is_private" => $isPrivate,
        ));

        if ($res) {
            $this->sendEncrypted(1, "修改分类成功");
        } else {
            $this->sendEncrypted(0, "修改分类失败");
        }
    }

    /**
     * 删除分类
     */
    public function actionDelete()
    {
        $id = (int) $this->getParam('id');

        $category = M("Category")->get($id);
        if (!$category) {
            $this->sendEncrypted(0, "分类不存在");
        }
        if ($category["uid"] != $this->mid) {
            $this->sendEncrypted(0, "无权修改此分类");
        }
        if ($category["is_default"]) {
            $this->sendEncrypted(0, "禁止删除默认分类");
        }

        $res = M("Category")->del($id);
        if ($res) {
            M("Link")->delByCid($id);
            $this->sendEncrypted(1, "删除分类成功");
        } else {
            $this->sendEncrypted(0, "删除分类失败");
        }
    }

    /**
     * 交换分类
     */
    public function actionExchange()
    {
        $id1 = (int) $this->getParam("id1");
        $id2 = (int) $this->getParam("id2");

        if ($id1 == $id2) {
            $this->sendEncrypted(0, "交换分类不能为相同分类");
        }

        $category1 = M("Category")->get($id1);
        if (!$category1) {
            $this->sendEncrypted(0, "分类不存在");
        }
        if ($category1["uid"] != $this->mid) {
            $this->sendEncrypted(0, "无权修改此分类");
        }

        $category2 = M("Category")->get($id2);
        if (!$category2) {
            $this->sendEncrypted(0, "分类不存在");
        }
        if ($category2["uid"] != $this->mid) {
            $this->sendEncrypted(0, "无权修改此分类");
        }

        $res1 = M("Category")->set($id1, array(
            "sort" => $category2["sort"],
        ));
        $res2 = M("Category")->set($id2, array(
            "sort" => $category1["sort"],
        ));

        if ($res1 && $res2) {
            $this->sendEncrypted(1, "移动分类成功");
        } else {
            $this->sendEncrypted(0, "移动分类失败");
        }
    }
}
