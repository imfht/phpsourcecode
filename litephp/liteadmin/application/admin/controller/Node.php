<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/8
 * Time: 15:59
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;

/**
 * @title 权限节点
 * Class Node
 * @package app\admin\controller
 */
class Node extends BaseAdmin
{
    /**
     * @title 列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $list = \LiteAdmin\Node::getNodesData();
        $this->assign("list", $list);
        return $this->fetch();
    }

    /**
     * @title 刷新节点
     * @throws \ReflectionException
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function clear()
    {
        set_time_limit(0);
        session_write_close();
        \LiteAdmin\Node::reload();
        $this->success('操作成功', '');
    }
}