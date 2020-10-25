<?php
/**
 * 会员等级组模型
 * Class RoleModel
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class GroupModel extends Model
{
    //数据主表
    public $table = 'role';
    public $auto = array(
        array('creditslower', 'intval', 'function', 2, 3)
    );
    // 自动验证
    public $validate = array(
        array('rname', 'null', '组名不能为空', 2, 3),
        array('rname', 'IsRname', '会员组已经存在', 2, 3),
        array('creditslower', 'nonull', '积分不能为空', 2, 3),
    );

    /**
     * 添加会员组
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
     * 编辑会员组
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
     * 删除等级组
     * @return [type] [description]
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

    /*------------------------------------属性定义--------------------------------------------*/

    /**
     * 验证会员组
     * @param [type] $name  [description]
     * @param [type] $value [description]
     * @param [type] $msg   [description]
     * @param [type] $arg   [description]
     */
    public function IsRname($name, $value, $msg, $arg)
    {
        $rid = Q('rid', 0, 'intval');
        if ($rid) {
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
