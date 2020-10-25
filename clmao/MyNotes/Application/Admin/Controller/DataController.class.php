<?php

namespace Admin\Controller;

use Think\Controller;

class DataController extends CommonController {

    /**
     * 备份数据库
     */
    public function backupDB() {
        $host = C('DB_HOST') . (C('DB_PORT') ? ":" . C('DB_PORT') : '');
        $user = C('DB_USER');
        $password = C('DB_PWD');
        $dbname = C('DB_NAME');
        $prefix = C('DB_PREFIX');
        //连接mysql数据库
        if (!mysql_connect($host, $user, $password)) {
            $this->error('数据库连接失败');
        }
        //是否存在该数据库
        if (!mysql_select_db($dbname)) {
            $this->error('不存在数据库:' . $dbname);
        }
        set_time_limit(0);
        mysql_query("SET interactive_timeout=3600, wait_timeout=3600 ;");
        mysql_query("set names 'utf8'");
        $mysql = "set charset utf8;\r\n";
        $q1 = mysql_query("show tables like '$prefix%'");
        while ($t = mysql_fetch_array($q1)) {
            $table = $t [0];
            $q2 = mysql_query("show create table `$table`");
            $sql = mysql_fetch_array($q2);
            $mysql .= $sql ['Create Table'] . ";\r\n";
            $q3 = mysql_query("select * from `$table`");
            while ($data = mysql_fetch_assoc($q3)) {
                $keys = array_keys($data);
                $keys = array_map('addslashes', $keys);
                $keys = join('`,`', $keys);
                $keys = "`" . $keys . "`";
                $vals = array_values($data);
                $vals = array_map('mysql_real_escape_string', $vals);
                $vals = join("','", $vals);
                $vals = "'" . $vals . "'";
                $vals = str_replace("''", "null", $vals);
                $mysql .= "insert into `$table`($keys) values($vals);\r\n";
            }
        }
        $filename = $dbname . ' ' . date('Y/m/d', time()) . '.sql';
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $filename);
        echo $mysql;
    }

    /*
     * sql命令
     */

    public function sql() {
        $this->title='SQL命令';
        $returnStr = '';
        if (isset($_POST['execute'])) {
            $sqlquery = $_POST['sqlquery'];
            $sqlquery = trim(stripslashes($sqlquery));
            if (preg_match("#drop(.*)table#i", $sqlquery) || preg_match("#drop(.*)database#", $sqlquery)) {
                $returnStr = "<span style='font-size:10pt'>删除'数据表'或'数据库'的语句不允许在这里执行。</span>";
                $this->returnStr = $returnStr;
                $this->display();
                die;
            }
            //运行查询语句

            $host = C('DB_HOST') . (C('DB_PORT') ? ":" . C('DB_PORT') : '');
            $user = C('DB_USER');
            $password = C('DB_PWD');
            $dbname = C('DB_NAME');
            $prefix = C('DB_PREFIX');
            mysql_connect($host, $user, $password);
            mysql_select_db($dbname);
            if (preg_match("#^select #i", $sqlquery)) {

                $res = mysql_query($sqlquery);

                if (empty($res)) {
                    $returnStr = "运行SQL：{$sqlquery}，无返回记录！";
                } else {
                    $returnStr = "运行SQL：{$sqlquery}，共有" . mysql_num_rows($res) . "条记录，最大返回100条！";
                }
                $j = 0;
                while ($row = mysql_fetch_assoc($res)) {
                    $j++;
                    if ($j > 100) {
                        break;
                    }
                    $returnStr.="<hr size=1 width='100%'/>";
                    $returnStr.="记录：$j";
                    $returnStr.="<hr size=1 width='100%'/>";
                    foreach ($row as $k => $v) {
                        $v = strip_tags($v);
                        $returnStr.="<font color='red'>{$k}：</font>{$v}<br/>\r\n";
                    }
                }
            } else {
                //普通的SQL语句
                $sqlquery = str_replace("\r", "", $sqlquery);
                $sqls = preg_split("#;[ \t]{0,}\n#", $sqlquery);
                $nerrCode = "";
                $i = 0;
                foreach ($sqls as $q) {
                    $q = trim($q);
                    if ($q == "") {
                        continue;
                    }
                    mysql_query($q);
                    $errCode = trim(mysql_error());
                    if ($errCode == "") {
                        $i++;
                    } else {
                        $nerrCode .= "执行： <font color='blue'>$q</font> 出错，错误提示：<font color='red'>" . $errCode . "</font><br>";
                    }
                }
                $returnStr .= "成功执行{$i}个SQL语句！<br><br>";
                $returnStr .= $nerrCode;
            }
        }
        $this->returnStr = $returnStr;
        $this->display();
    }

    /*
     * 优化/修复/表结构
     */

    public function opimize() {
        $this->title='表的优化修复';
        $returnStr = '';
        $host = C('DB_HOST') . (C('DB_PORT') ? ":" . C('DB_PORT') : '');
        $user = C('DB_USER');
        $password = C('DB_PWD');
        $dbname = C('DB_NAME');
        $prefix = C('DB_PREFIX');
        mysql_connect($host, $user, $password);
        mysql_select_db($dbname);
        $res = mysql_query("show tables like '$prefix%'");
        $selStr = '<select name="tablename" class="select-3 form-control chosen">';
        while ($t = mysql_fetch_array($res)) {
            $table = $t [0];
            $selStr.= '<option value="' . $table . '">' . $table . '</option>';
        }
        $selStr .= '</select>';
        if (!empty($_POST['execute'])) {
            $dopost = I('post.dopost');
            $tablename = I('post.tablename');
            //优化表
            if ($dopost == 'opimize') {
                if (empty($tablename)) {
                    $returnStr .= "没有指定表名！";
                } else {
                    $rs = mysql_query("OPTIMIZE TABLE `$tablename`");
                    if ($rs)
                        $returnStr .= "执行优化表： $tablename  OK！";
                    else
                        $returnStr .= "执行优化表： $tablename  失败，原因是：" . mysql_error();
                }
            }
            //优化所有表
            else if ($dopost == "opimizeAll") {
                $res = mysql_query("show tables like '$prefix%'");
                while ($t = mysql_fetch_array($res)) {
                    $table = $t [0];
                    $rs = mysql_query("OPTIMIZE TABLE `$table`");
                    if ($rs) {
                        $returnStr .= "优化表: {$table} ok!<br />\r\n";
                    } else {
                        $returnStr .= "优化表: {$table} 失败! 原因是: " . mysql_error() . "<br />\r\n";
                    }
                }
            }
            //修复表
            else if ($dopost == "repair") {
                if (empty($tablename)) {
                    $returnStr .= "没有指定表名！";
                } else {
                    $rs = mysql_query("REPAIR TABLE `$tablename` ");
                    if ($rs)
                        $returnStr .= "修复表： $tablename  OK！";
                    else
                        $returnStr .= "修复表： $tablename  失败，原因是：" . mysql_error();
                }
            }
            //修复全部表
            else if ($dopost == "repairAll") {
                $res = mysql_query("show tables like '$prefix%'");
                while ($t = mysql_fetch_array($res)) {
                    $table = $t [0];
                    $rs = mysql_query("REPAIR TABLE `$table`");
                    if ($rs) {
                        $returnStr .= "修复表: {$table} ok!<br />\r\n";
                    } else {
                        $returnStr .= "修复表: {$table} 失败! 原因是: " . mysql_error() . "<br />\r\n";
                    }
                }
            }
            //查看表结构
            else if ($dopost == "viewinfo") {
                if (empty($tablename)) {
                    echo "没有指定表名！";
                } else {
                    $rs = mysql_query("SHOW CREATE TABLE " . $dbname . "." . $tablename);
                    $row = mysql_fetch_array($rs);
                    $ctinfo = $row[1];
                    $returnStr .= "<xmp>" . trim($ctinfo) . "</xmp>";
                }
            }
        }

        $this->selStr = $selStr;
        $this->returnStr = $returnStr;
        $this->display();
    }

}
