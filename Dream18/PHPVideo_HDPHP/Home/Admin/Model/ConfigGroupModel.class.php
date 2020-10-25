<?php
/**
 * 配置组管理模型
 * Class ConfigGroupModel
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class ConfigGroupModel extends Model
{
    //配置组
    public $table = 'config_group';

    //自动验证
    public $validate = array(
        array('cname', 'nonull', '组标识不能为空', 2, 3),
        array('cname', 'IsCgname', '组标识已经存在', 2, 3),
        array('ctitle', 'nonull', '组名称不能为空', 2, 3),
        array('ctitle', 'IsCgtitle', '组名称已经存在', 2, 3)
    );

    /**
     * [$auto 自动完成]
     * @var array
     */
    public $auto = array(
        // 添加时间自动完成
        array('addtime','time','function',2,1),
    );

    /**
     * 获取组列表
     * @return mixed
     */
    public function getGroup()
    {
        return $this->where(array('isshow' => 1))->all();
    }

    /**
     * 添加配置组
     */
    public function addConfigGroup()
    {
        if ($this->create())
        {
            if ($this->add())
            {
                return true;
            }
            else
            {
                $this->error = '添加失败';
            }
        }
    }

    /**
     * 修改配置组
     */
    public function editConfigGroup()
    {
        if ($this->create())
        {
            if ($this->save())
            {
                return true;
            }
            else
            {
                $this->error = '添加失败';
            }
        }
    }

    /**
     * 删除配置组
     * @return bool
     */
    public function delConfigGroup()
    {
        $cid = Q('cid', 0, 'intval');
        $map['cid'] = array('EQ', $cid);
        if ($this->where($map)->del())
        {
            return M('config')->where(array('cid'=> $cid))->del();
        }
        else
        {
            $this->error('删除失败');
        }
    }
    /*----------------------属性定义--------------------------*/


    /**
     * 验证组名
     * @param [type] $name  [description]
     * @param [type] $value [description]
     * @param [type] $msg   [description]
     * @param [type] $arg   [description]
     */
    public function IsCgname($name, $value, $msg, $arg)
    {
        $cname = Q('cname');
        //编辑时排除当前配置组
        if ($cid = Q('cid'))
        {
            $map['cid'] = array('NEQ', $cid);
        }

        $map['cname'] = array('EQ', $cname);
        if (M('config_group')->where($map)->find())
        {
            return $msg;
        }
        else
        {
            return true;
        }
    }

    /**
     * 组标题
     * @param [type] $name  [description]
     * @param [type] $value [description]
     * @param [type] $msg   [description]
     * @param [type] $arg   [description]
     */
    public function IsCgtitle($name, $value, $msg, $arg)
    {
        $ctitle = Q('ctitle');
        //编辑时排除当前配置组
        if ($cid = Q('cid')) {
            $map['cid'] = array('NEQ', $cid);
        }

        $map['ctitle'] = array('EQ', $ctitle);
        if (M('config_group')->where($map)->find())
        {
            return $msg;
        }
        else
        {
            return true;
        }
    }
}