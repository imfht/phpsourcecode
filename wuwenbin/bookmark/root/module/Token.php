<?php
/**
 * 会话模块
 */
class ModuleToken extends ModuleBase
{
    /**
     * @var string $table 表名
     */
    protected $table = 'token';

    /**
     * 删除过期会话
     *
     * @return int
     */
    public function delExpired()
    {
        $sql = "delete from `token` where `expire_time` < unix_timestamp()";
        $res = Mysql::query($sql);
        return $res;
    }
}
