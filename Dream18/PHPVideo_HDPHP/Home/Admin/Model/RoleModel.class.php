<?php
/**
 * RBAC角色管理模型
 * Class RoleModel
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class RoleModel extends Model
{
    // 角色表
    public $table = 'role';
    //验证
    public $validate = array(
        array('rname', 'null', '角色名不能为空', 2, 3),
        array('rname', 'IsRname', '角色已经存在', 2, 3),
    );

    /**
     * 添加角色
     */
    public function addRole()
    {
        if ($this->create())
        {
            if ($this->add())
            {
                return true;
            }
            $this->error = '添加失败';
        }
    }

    /**
     * 编辑角色
     * @return [type] [description]
     */
    public function editRole()
    {
        if ($this->create())
        {
            if ($this->save())
            {
                return true;
            }
            $this->error = '修改失败';
        }
    }

    /**
     * 删除角色
     * @param  [type] $rid [description]
     * @return [type]      [description]
     */
    public function delRole()
    {
        $rid = Q('rid', 0, 'intval');
        if ($this->del($rid))
        {
            M("user")->where(array('rid' => $rid))->save(array('rid' => 4));
            return true;
        }
        $this->error = '删除失败';
    }

    /*------------------------------属性定义---------------------------------*/

    /**
     * 角色名验证
     * @param [type] $name  [description]
     * @param [type] $value [description]
     * @param [type] $msg   [description]
     * @param [type] $arg   [description]
     */
    public function IsRname($name, $value, $msg, $arg)
    {
        if ($rid = Q('rid', 0, 'intval'))
        {
            $map['rid'] = array('NEQ', $rid);
        }
        $map['rname'] = $value;
        if (M('role')->where($map)->find())
        {
            return $msg;
        }
        return true;
    }
}
