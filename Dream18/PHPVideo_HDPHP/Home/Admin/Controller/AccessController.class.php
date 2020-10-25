<?php

/**
 * 后台rbac权限管理
 * Class AccessControl
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class AccessController extends AuthController
{
    //模型
    private $db;
    //角色id
    private $rid;

    //构造函数
    public function __init()
    {
        parent::__init();
        $this->db = K('Access');
        $this->rid = Q('rid', 0, 'intval');
        /*if (!IS_WEBMASTER)
        {
            $this->error('没有操作权限');
        }*/
    }

    /**
     * 设置权限
     * @return [type] [description]
     */
    public function edit()
    {
        if (IS_POST)
        {
            if ($this->db->editAccess())
            {
                $this->success('修改成功');
            }
            $this->error('修改失败');
        }
        else
        {
            $sql = "SELECT n.nid,n.title,n.pid,n.type,a.rid as access_rid FROM " . C('DB_PREFIX') . "node AS n LEFT JOIN (SELECT * FROM " . C('DB_PREFIX') . "access WHERE rid={$this->rid}) AS a ON n.nid = a.nid ORDER BY list_order ASC";
            $result = $this->db->query($sql);
            foreach ($result as $n => $r)
            {
                // 当前角色已经有权限或不需要验证的节点
                $checked = $r['access_rid'] || $r['type'] == 2 ? " checked=''" : '';

                // 不需要验证的节点，关闭选择（因为所有管理员都有权限）
                $disabled = $r['type'] == 2 ? 'disabled=""' : '';

                //表单
                $result[$n]['checkbox'] = "<label>
                        <input type='checkbox' name='nid[]' value='{$r['nid']}' $checked $disabled/> {$r['title']}
                        </label>";
            }
            $this->assign('access', Data::channelLevel($result, 0, '-', 'nid'));
            $this->assign('rid', $this->rid);
            $this->display();
        }
    }
}