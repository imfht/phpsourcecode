<?php
/**
 * 用户模块
 */
class ModuleUser extends ModuleBase
{
    /**
     * @var string $table 表名
     */
    protected $table = "user";

    /**
     * 根据第三方账号类型获取用户信息
     *
     * @param string $type 第三方账号类型
     * @param int $id 第三方账号ID
     *
     * @return mixed
     */
    public function getByType($type, $id)
    {
        $type = addslashes($type);
        $id = addslashes($id);

        $sql = "select * from `{$this->table}` where `type` = '$type' and `id` = '$id'";
        $res = Mysql::query($sql);
        return empty($res) ? null : $res[0];
    }
}
