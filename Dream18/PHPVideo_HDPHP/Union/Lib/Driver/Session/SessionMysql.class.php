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
 * mysql方式处理SESSION
 * @package     Session
 * @subpackage  Driver
 * @author      后盾向军 <houdunwangxj@gmail.com>
 */
class SessionMysql
{

    private $link; //Mysql数据库连接
    private $table; //SESSION表
    private $expire; //过期时间

    /**
     * 构造函数
     */
    public function __construct()
    {
    }

    //初始
    public function run()
    {
        $options = C("SESSION_OPTIONS");
        //数据表
        $this->table = C('DB_PREFIX') . $options['table'];
        $this->expire = isset($options['expire']) ? $options['expire'] : 3600; //过期时间
        $host = isset($options['host']) ? $options['host'] : C("DB_HOST");
        $port = isset($options['port']) ? $options['port'] : C("DB_PORT");
        $user = isset($options['user']) ? $options['user'] : C("DB_USER");
        $password = isset($options['password']) ? $options['password'] : C("DB_PASSWORD");
        $database = isset($options['database']) ? $options['database'] : C("DB_DATABASE");
        //连接Mysql
        $this->link = mysql_connect($host . ':' . $port, $user, $password);
        //选择数据库
        $db = mysql_select_db($database, $this->link);
        if (!$this->link || !$db) return false;
        mysql_query("SET NAMES " . str_replace("_", "", C("DB_CHARSET"))); //字符集
        session_set_save_handler(
            array(&$this, "open"),
            array(&$this, "close"),
            array(&$this, "read"),
            array(&$this, "write"),
            array(&$this, "destroy"),
            array(&$this, "gc")
        );
    }

    /**
     * session_start()时执行
     * @return boolean
     */
    public function open()
    {
        return true;
    }

    /**
     * 读取SESSION 数据
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        $sql = "SELECT data FROM " . $this->table . " WHERE sessid='$id' AND atime>" . (time() - $this->expire);
        $result = mysql_query($sql, $this->link);
        if ($result) {
            $data = mysql_fetch_assoc($result);
            return $data['data'];
        }
        return '';
    }

    /**
     * 写入SESSION
     * @param key $id key名称
     * @param mixed $data 数据
     * @return bool
     */
    public function write($id, $data)
    {
        $ip = ip_get_client();
        $sql = "REPLACE INTO " . $this->table . "(sessid,data,atime,ip) ";
        $sql .= "VALUES('$id','$data'," . time() . ",'$ip')";
        return mysql_query($sql, $this->link);
    }

    /**
     * 卸载SESSION
     * @param type $id
     * @return type
     */
    public function destroy($id)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE sessid='$id'";
        return mysql_query($sql, $this->link);
    }

    /**
     * SESSION垃圾处理
     * @return boolean
     */
    public function gc()
    {
        $sql = "DELETE FROM " . $this->table . " WHERE atime<" . (NOW - $this->expire) . " AND sessid<>'" . session_id() . "'";
        return mysql_query($sql, $this->link);
    }


    //关闭SESSION
    public function close()
    {
        if (mt_rand(1, 100) == 10){
            $this->gc();
        }
        //关闭数据库连接
        return mysql_close($this->link);
    }

    function __destruct()
    {
    }
}
