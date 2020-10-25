<?php

class tableTool extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->view('dbtool');
    }

    public function go()
    {
        $host = $this->input->post('host');
        $user = $this->input->post('user');
        $pass = $this->input->post('pass');
        $dbNew = $this->input->post('db_new');
        $dbOld = $this->input->post('db_old');
        if ($host == '' || $user == '' || $pass == '' || $dbNew == '' || $dbOld == '') {
            exit('ERROR:empty params');
        }

        //开始连接数据库
        $DB_NEW = $this->_conn($host, $user, $pass, $dbNew);
        $DB_OLD = $this->_conn($host, $user, $pass, $dbOld);
        //测试连接状态
        if (!$this->_testConn($DB_NEW) || !$this->_testConn($DB_OLD)) {
            exit('ERROR:database connect fail');
        }

        //读取所有表
        $DB_NEW_TABLE = $DB_NEW->list_tables();
        $DB_OLD_TABLE = $DB_OLD->list_tables();
        //测试是否有空库
        if (empty($DB_NEW_TABLE) || empty($DB_OLD_TABLE)) {
            exit('WARNING:check database,keep not empty');
        }
        //比较生成差异表结果
        //$tableDiff2 = array_diff($DB_OLD_TABLE, $DB_NEW_TABLE);//删除表用，暂时不用
        $tableDiff = array_diff($DB_NEW_TABLE, $DB_OLD_TABLE);

        //生成创建表的语句
        $tableCreate = '';
        if (!empty($tableDiff)) {
            foreach ($tableDiff as $tD) {
                $tableCreate .=$this->_createSQL($DB_NEW, $tD);
                $tableCreate.="\r\n\r\n";
            }
            unset($tD);
        }
        echo $tableCreate;
        //比较生成差异表结构结果
        //1.获取相同的表
        $tableSame = array_intersect($DB_NEW_TABLE, $DB_OLD_TABLE);
        $tableAlter = '';
        if (!empty($tableSame)) {
            foreach ($tableSame as $tS) {
                $tableAlter.=$this->_alterSQL($DB_NEW, $DB_OLD, $tS);
                $tableAlter.="\r\n\r\n";
            }
        }
        echo $tableAlter;
    }

    /**
     * 连接数据库
     *
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $db
     * @return resource
     */
    private function _conn($host, $user, $pass, $db)
    {
        $dsn = "mysqli://{$user}:{$pass}@{$host}/{$db}";
        $dbs = $this->load->database($dsn, TRUE);
        return $dbs;
    }

    /**
     * 测试数据库连通性
     *
     * @param resource $db
     * @return bool
     */
    private function _testConn($db)
    {
        $data = $db->version();
        $result = is_string($data) && strlen($data) > 0 ? true : false;
        return $result;
    }

    /**
     * 获取创建表的语句
     *
     * @param resource $db
     * @param string $tableName
     * @return string
     */
    private function _createSQL($db, $tableName)
    {
        $result = $db->query("SHOW CREATE TABLE " . $tableName)->row_array();
        if (empty($result)) {
            return '';
        }
        return $result['Create Table'] . ";";
    }

    /**
     * 获取修改表的语句
     *
     * @param resource $dbNew
     * @param resource $dbOld
     * @param string $tableName
     * @return string
     */
    private function _alterSQL($dbNew, $dbOld, $tableName)
    {
        //获取新旧库同表的字段
        $fieldNew = $this->_arrayValueToKey($dbNew->query("SHOW FULL COLUMNS FROM " . $tableName)->result_array());
        $fieldOld = $this->_arrayValueToKey($dbOld->query("SHOW FULL COLUMNS FROM " . $tableName)->result_array());
        //测试为空的情况
        if (empty($fieldNew) || empty($fieldOld)) {
            return '';
        }

        //获取列差异
        $fieldDiff = array_diff_key($fieldNew, $fieldOld);
        //测试差异为空的情况
        if (empty($fieldDiff)) {
            return '';
        }
        //生成ALTER TABLE语句
        $alterSql = "ALTER TABLE `{$tableName}`" . "\r\n";
        foreach ($fieldDiff as $fD) {
            $alterSql.="ADD COLUMN `{$fD['Field']}`  ";
            $alterSql.="{$fD['Type']} ";
            if ($fD['Collation'] != null) {
                $charachter = explode('_', $fD['Collation']);
                $alterSql.='CHARACTER SET ' . $charachter[0] . ' COLLATE ' . $fD['Collation'] . ' ';
            }
            if (strtolower($fD['Null']) == 'no') {
                $alterSql.='NOT NULL ';
            }
            if ($fD['Default'] != null) {
                if (strpos($fD['Default'], 'int') > 0 || strpos($fD['Default'], 'float') > 0 || strpos($fD['Default'], 'double') > 0 || strpos($fD['Default'], 'decimal') > 0) {
                    $alterSql.='DEFAULT ' . $fD['Default'] . ' ';
                } else {
                    $alterSql.="DEFAULT '" . $fD['Default'] . "' ";
                }
            }
            if ($fD['Upkey'] != '') {
                $alterSql.="COMMENT '" . $fD['Comment'] . "' AFTER `{$fD['Upkey']}`,";
            } else {
                $alterSql.="COMMENT '" . $fD['Comment'] . "',";
            }
            $alterSql.="\r\n";
        }
        $result = substr_replace($alterSql, ";\r\n", -3, 3);

        return $result;
    }

    /**
     * 工具：转数组为关联数组
     * @param type $array
     * @return type
     */
    private function _arrayValueToKey($array)
    {
        $upKey = '';
        foreach ($array as $a) {
            $result[$a['Field']] = $a;
            $result[$a['Field']]['Upkey'] = $upKey;
            $upKey = $a['Field'];
        }
        return $result;
    }

}
