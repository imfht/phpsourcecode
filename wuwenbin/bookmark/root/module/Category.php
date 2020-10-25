<?php
/**
 * 分类模块
 */
class ModuleCategory extends ModuleBase
{
    /**
     * @var string $table 表名
     */
    protected $table = "category";

    /**
     * 获取用户所有分类
     *
     * @param int $uid 用户ID
     *
     * @return array
     */
    public function getListByUid($uid)
    {
        $uid = (int) $uid;

        $sql = "select * from `{$this->table}` where `uid` = $uid order by `sort` asc,`id` asc";
        $res = Mysql::query($sql);
        return $res;
    }

    /**
     * 获取最近时间添加的分类数
     *
     * @param int $uid 用户ID
     * @param int $time 时间区间
     *
     * @return int
     */
    public function getCountByTime($uid, $time)
    {
        $uid = (int) $uid;
        $time = (int) $time;
        $now = time();

        $sql = "select count(*) as `count` from `{$this->table}` where `uid` = $uid and $now - `ctime` < $time";
        $res = Mysql::query($sql);
        return empty($res) ? 0 : (int) $res[0]["count"];
    }

    /**
     * 根据名称获取分类
     *
     * @param int $uid 用户ID
     * @param string $name 分类名称
     *
     * @return mixed
     */
    public function getByName($uid, $name)
    {
        $uid = (int) $uid;
        $name = addslashes(trim($name));

        $sql = "select * from `{$this->table}` where `uid` = $uid and `name` = '$name' limit 1";
        $res = Mysql::query($sql);
        return empty($res) ? null : $res[0];
    }
}
