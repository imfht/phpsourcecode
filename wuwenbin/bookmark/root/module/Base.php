<?php
/**
 * 模块基类
 */
class ModuleBase
{
    /**
     * @var string $table 表名
     */
    protected $table = "";

    /**
     * @var string $error 错误信息
     */
    protected $error = "";

    /**
     * 获取单一纪录
     *
     * @param int $id ID
     * @param string $idLabel ID字段名
     *
     * @return mixed
     */
    public function get($id, $idLabel = "id")
    {
        $id = (int) $id;

        $sql = "select * from `{$this->table}` where `$idLabel` = $id";
        $res = Mysql::query($sql);
        return empty($res) ? null : $res[0];
    }

    /**
     * 添加纪录
     *
     * @param array $data 数据
     *
     * @return int
     */
    public function add(array $data)
    {
        $sql = buildInsertSql($this->table, $data);
        $res = Mysql::query($sql);
        return $res;
    }

    /**
     * 删除纪录
     *
     * @param int $id ID
     * @param string $idLabel ID字段名
     *
     * @return int
     */
    public function del($id, $idLabel = "id")
    {
        $id = (int) $id;

        $sql = "delete from `{$this->table}` where `$idLabel` = $id";
        $res = Mysql::query($sql);
        return $res;
    }

    /**
     * 更新纪录
     *
     * @param int $id ID
     * @param array $data 更新内容
     * @param string $idLabel ID字段名
     *
     * @return int
     */
    public function set($id, array $data, $idLabel = "id")
    {
        $id = (int) $id;

        $sql = buildUpdateSql($this->table, $data, "`$idLabel` = $id");
        $res = Mysql::query($sql);
        return $res;
    }

    /**
     * 获取错误信息
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}
