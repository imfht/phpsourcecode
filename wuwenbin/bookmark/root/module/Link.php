<?php
/**
 * 网址模块
 */
class ModuleLink extends ModuleBase
{
    /**
     * @var string $table 表名
     */
    protected $table = "link";

    /**
     * 获取网址列表
     *
     * @param int $page 第几页
     * @param int $each 每页显示数量
     * @param array $filter 过滤条件
     *
     * @return array
     */
    public function getList($page, $each, array $filter = array())
    {
        $page = (int) $page;
        $each = (int) $each;
        $offset = ($page - 1) * $each;

        $where = "";
        if (isset($filter["uid"])) {
            $uid = (int) $filter["uid"];
            $where .= " and `uid` = $uid";
        }

        if (isset($filter["cid"])) {
            if (is_array($filter["cid"])) {
                $cids = array_unique(array_map("intval", $filter["cid"]));
                if (!empty($cids)) {
                    $where .= " and `cid` in (" . implode(",", $cids) . ")";
                }
            } else {
                $cid = (int) $filter["cid"];
                $where .= " and `cid` = $cid";
            }
        }

        if (isset($filter["title"])) {
            $title = addslashes($filter["title"]);
            $where .= " and `title` like '%$title%'";
        }

        if ($where) {
            $where = "where " . substr($where, 5);
        }

        $sql = "select * from `{$this->table}` $where order by `id` desc limit $offset,$each";
        $res = Mysql::query($sql);
        $list = $res;

        $sql = "select count(*) as `count` from `{$this->table}` $where";
        $res = Mysql::query($sql);
        $count = empty($res) ? 0 : $res[0]["count"];
        return array($list, $count);
    }

    /**
     * 检查网址是否存在
     *
     * @param string $url 网址
     * @param int $uid 用户ID
     *
     * @return bool
     */
    public function hasExisted($url, $uid)
    {
        $url = addslashes($url);
        $uid = (int) $uid;

        $sql = "select * from `{$this->table}` where `uid` = $uid and `url` = '$url' limit 1";
        $res = Mysql::query($sql);
        return empty($res) ? false : true;
    }

    /**
     * 根据分类ID删除网址
     *
     * @param int $cid 分类ID
     *
     * @return bool
     */
    public function delByCid($cid)
    {
        $cid = (int) $cid;

        $sql = "delete from `{$this->table}` where `cid` = $cid";
        $res = Mysql::query($sql);
        return (bool) $res;
    }

    /**
     * 获取最近时间添加的网址数
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
     * 根据网址数获取随机用户ID
     *
     * @param int $count 网址数
     *
     * @return int
     */
    public function getRandUidByCount($count)
    {
        $count = (int) $count;

        $sql = "select `uid` from `{$this->table}` group by `uid` having count(*) >= $count order by rand() limit 1";
        $res = Mysql::query($sql);
        return empty($res) ? 0 : $res[0]["uid"];
    }
}
