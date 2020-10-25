<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/10
 * Time: 15:49
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\model\SystemAuthMap;
use app\common\model\SystemRole;

/**
 * @title 角色管理
 * Class Role
 * @package app\admin\controller
 */
class Role extends BaseAdmin
{
    /**
     * @title 列表页
     * @return mixed
     */
    public function index()
    {
        $db = new SystemRole();
        return $this->_list($db);
    }

    /**
     * @title 添加
     * @return array|mixed
     */
    public function add()
    {
        return $this->_form(new SystemRole(), 'form');
    }

    /**
     * @title 编辑
     * @return array|mixed
     */
    public function edit()
    {
        return $this->_form(new SystemRole(), 'form');
    }

    /**
     * @title 授权
     * @return array|mixed
     */
    public function access()
    {
        return $this->_form(new SystemRole());
    }

    protected function _access_form_before(&$data)
    {
        if ($this->request->isGet()) {
            $data['node'] = explode(',', $data['access_list']);
            $nodes = \LiteAdmin\Node::getNodesData();
            $this->assign('nodes', $nodes);
        } else {
            $data['access_list'] = empty($data['node'])?'':implode(',', $data['node']);
            unset($data['node']);
        }
    }

    /**
     * @title 删除及批量删除
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $ids = $this->request->post('ids');
        $this->_del(new SystemRole(),$ids);
    }
}