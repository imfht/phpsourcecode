<?php

defined('IN_CART') or die;

/**
 *  
 * 备份数据库
 *
 *
 * */
class Dumpdb extends Base
{

    public $crlf = "\r\n";  //分隔符
    public $maxsize = 512000; //文件大小
    public $charset = "";  //数据库字符集
    public $dumpsql = "";  //sql语句
    public $offset = 300; //select的记录条数
    public $tablepos = 0;
    public $tableorder = 0;
    public $sqlfiledir = "";
    public $filename = "";
    public $finished = false;
    public $vol = 1;
    public $prefix = 'cart_';
    public $exclude = array();

    /**
     *  
     * 
     *  构造函数，传递文件大小，字符编码，sql行分隔符
     *
     */
    public function Dumpdb($vol = 1, $filename = "", $tablepos = 0, $tableorder = 0, $maxsize = 512000, $crlf = "\r\n")
    {

        $this->tablepos = $tablepos;

        $this->tableorder = $tableorder;

        $this->maxsize = $maxsize; //单个文件大小

        $this->crlf = $crlf; //换行

        $this->vol = $vol; //分卷

        $this->filename = $filename ? $filename : date("YmdHi"); //文件名

        $this->sqlfiledir = DATADIR . "/dbbak"; //备份文件存储

        $this->prefix = DB::getDB()->getTablePrefix(); //前缀

        $this->dbcharset = DB::getDB()->getCharset(); //编码

        $this->exclude = array($this->prefix . "session");
    }

    /**
     *  
     *  获取数据表结构
     *
     */
    public function get_Table_Structure($table)
    {
        $createsql = "/* Table Structure for table `{$table}` */" . $this->crlf . $this->crlf;
        $createsql .= "DROP TABLE IF EXISTS `$table`;" . $this->crlf;
        $ret = DB::getDB()->selectsql("SHOW CREATE TABLE `$table`", "row");
        $tmp_sql = $ret["Create Table"];

        $createsql .= substr($tmp_sql, 0, strrpos($tmp_sql, ")") + 1);
        $createsql .= " ENGINE=MyISAM DEFAULT CHARACTER SET " . $this->dbcharset . ";" . $this->crlf;
        $createsql .= $this->crlf;
        $this->crlf;
        return $createsql;
    }

    /**
     *  
     *  导入备份文件
     *
     */
    function import()
    {
        $file = $this->sqlfiledir . "/" . $this->filename . "_" . $this->vol . ".sql";
        if (!is_file($file))
            return false;

        $lines = array_filter(file($file), function($line) {
            return substr($line, 0, 2) != "/*";
        });
        $sqlstr = str_replace("\r", '', implode('', $lines));
        $ret = explode(";\n", $sqlstr);
        $ret_count = count($ret);
        for ($i = 0; $i < $ret_count; $i++) {
            $sql = rtrim($ret[$i], "\r\n;");
            if (!$sql)
                continue;

            //删除不符合条件的记录
            if (preg_match("/(?:DROP TABLE IF EXISTS|CREATE TABLE|INSERT INTO) `(.+?)`/", $sql, $matches) && !in_array($matches[1], $this->exclude)) {
                DB::getDB()->query($sql);
            }
        }
        return true;
    }

    /**
     *  
     *  处理null
     *
     */
    function null_string($str)
    {
        if (!isset($str) || is_null($str)) {
            $str = 'NULL';
        }
        return $str;
    }

    /**
     *  
     *  获取表数据
     *
     */
    public function get_Table_Data($table)
    {
        //获取表数据量
        $count = DB::getDB()->selectsql("SELECT COUNT(1) FROM $table", "var");
        if ($count == 0)
            return true; //无数据直接返回


            
//读取数据
        while (true) {

            if ($this->tablepos >= $count)
                return true; //如果tablepos大于count，直接退出


                
//数据,tablepos小于count，可以取到数据
            $dumpdata = DB::getDB()->selectsql("SELECT * FROM $table LIMIT " . $this->tablepos . "," . $this->offset);
            $datacount = count($dumpdata);
            //字段
            $fields = array_keys($dumpdata[0]);
            $insertsql = "INSERT INTO `$table` (`" . implode("`,`", $fields) . "`) VALUES";


            for ($i = 0; $i < $datacount; $i++) {
                $record = array_map("caddslashes", $dumpdata[$i]);  //过滤非法字符
                $record = array_map(array($this, "null_string"), $record);     //处理null值
                $tmp_dump_sql = $insertsql . " ('" . implode("','", $record) . "');" . $this->crlf;
                $tmp_str_pos = strpos($tmp_dump_sql, 'NULL');
                if ($tmp_str_pos !== false) {
                    $tmp_dump_sql = substr($tmp_dump_sql, 0, $tmp_str_pos - 1) . 'NULL' . substr($tmp_dump_sql, $tmp_str_pos + 5);
                }

                //如果大于maxsize
                if (strlen($this->dumpsql) + strlen($tmp_dump_sql) > $this->maxsize) {
                    if (!$this->dumpsql) {
                        $this->dumpsql = $tmp_dump_sql;
                    } else {
                        $this->dumpsql .= $tmp_dump_sql;
                    }
                    $this->tablepos ++;
                    break 2;
                } else {
                    if ($this->tablepos == 0) {
                        $this->dumpsql .= "/* Data for the table `{$table}` */" . $this->crlf . $this->crlf;
                    }
                    $this->dumpsql .= $tmp_dump_sql;
                    $this->tablepos ++;
                }
            }
        }
        return $this->tablepos >= $count;
    }

    /**
     *  
     *  执行导出结构
     *
     */
    public function dump_tables()
    {
        //获取所有的tables
        $tmptables = DB::getDB()->selectsql("SHOW TABLES like '{$this->prefix}%'", "col");

        //依据tableorder删除以前导出的
        $tables = $this->tableorder ? array_splice($tmptables, $this->tableorder, count($tmptables) - $this->tableorder) : $tmptables;
        $isbreak = false;
        foreach ($tables as $key => $table) {
            if (strlen($this->dumpsql) > $this->maxsize) {//判断文件大小
                $isbreak = true;
                break;
            }
            if (!$this->tablepos) {//表结构
                $createsql = $this->get_Table_Structure($table);
                $this->dumpsql .= $createsql;
            }

            if (!in_array($table, $this->exclude)) {//exclude表，只导出表结构
                $ret = $this->get_Table_Data($table); //导出表中的数据
                if (!$ret) {
                    $isbreak = true;
                    break;
                } else {
                    $this->dumpsql .= $this->crlf . $this->crlf;
                }
                $this->tableorder ++;
                $this->tablepos = 0;
            }
        }
        $this->finished = !$isbreak;
        $this->mksqlfile();
    }

    /**
     *  
     *  生成sql文件
     *
     */
    private function mksqlfile()
    {
        @cwritefile($this->sqlfiledir . "/" . $this->filename . "_{$this->vol}.sql", $this->dumpsql);
    }

    /**
     *  
     *  获取当前需要导出的表的记录
     *
     */
    public function getTablePos()
    {
        return $this->tablepos;
    }

    /**
     *  
     *  获取当前需要导出的表
     *
     */
    public function getTableOrder()
    {
        return $this->tableorder;
    }

    /**
     *  
     *  获取当前导出的文件名
     *
     */
    public function getFileName()
    {
        return $this->filename;
    }

    /**
     *  
     *  获取下一个需要导出的分卷
     *
     */
    public function getNextVol()
    {
        return $this->vol + 1;
    }

    /**
     *  
     *  获取导出是否结束
     *
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     *  
     *  获取行分隔符
     *
     */
    public function getDeLimiter()
    {
        return $this->crlf;
    }

    /**
     *  
     *  sql文件保存路径
     *
     */
    public function getSqlFileDir()
    {
        return $this->sqlfiledir;
    }

}
