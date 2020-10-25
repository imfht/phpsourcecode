<?php
class LogModel extends Db
{
    protected $_log = 'w_log';
    /**
     * 增加日志记录
     * @param unknown_type $params
     */
    public function addLog ($params)
    {
        return $this->add($this->_log, $params);
    }
    public function delLog ($time)
    {
        $sql = "DELETE FROM $this->_log WHERE action_time<'$time' AND level=0";
        return $this->exec($sql);
    }
    /**
     * 统计最近三十天的登录人数
     * Enter description here ...
     */
    public function tjLogin ($days, $format = "%Y%m%d", $event)
    {
        $sql = "select count(DISTINCT(username)) num,DATE_FORMAT(action_time,'$format') m ,action_time from $this->_log WHERE event='$event' GROUP by m order by m DESC  limit $days";
        return $this->fetchAll($sql);
    }
    /**
     * 获取日志列表
     * Enter description here ...
     * @param unknown_type $start
     * @param unknown_type $num
     */
    public function getLogPage ($start, $num)
    {
        return $this->getPage($start, $num, $this->_log, null, null, 'id desc');
    }
    public function getYesterdayEvent ($yesterday)
    {
        $sql = "select count(DISTINCT(username)) num,event from w_log where date_format(action_time,\"%Y-%m-%d\")='$yesterday' group by event order by num desc limit 10; ";
        return $this->fetchAll($sql);
    }
    public function countLogNum ()
    {
        $sql = "SELECT id  FROM $this->_log";
        return $this->rowCount($sql);
    }
    /**
     * 获取指定记录
     * Enter description here ...
     * @param unknown_type $where
     */
    public function getLogByWhere ($where, $limit)
    {
        return $this->getAll($this->_log, $where, NULL, "id DESC", $limit);
    }
    /**
     * 返回log
     * @return LogModel
     */
    public static function instance ()
    {
        return parent::_instance(__CLASS__);
    }
}