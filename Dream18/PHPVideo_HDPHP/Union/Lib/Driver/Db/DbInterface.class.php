<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------

/**
 * 数据库操作接口
 * @package     Db
 * @author      后盾向军 <houdunwangxj@gmail.com>
 */
interface DbInterface
{

    public function connect(); //获得连接   参数为表名

    public function close(); //关闭数据库

    public function exe($sql); //发送没有返回值的sql

    public function query($sql); //有返回值的sql

    public function getInsertId(); //获得最后插入的id

    public function getAffectedRows(); //受影响的行数

    public function getVersion(); //获得版本

    public function beginTrans(); //自动提交模式true开启false关闭

    public function commit(); //提供一个事务

    public function rollback(); //回滚事务

    public function escapeString($str); //数据安全处理

    public function link($table, $full);//获得链接

    /**
     * 设置表
     * @param $table
     * @param $full
     * @return mixed
     */
    public function table($table, $full = false);//设置表
}
