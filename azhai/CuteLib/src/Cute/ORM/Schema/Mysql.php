<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Schema;

use \PDO;
use \Cute\ORM\Database;
use \Cute\ORM\Query\ResultSet;


/**
 * MySQL数据库
 */
class Mysql extends Database
{
    protected $lower_table_name = null; //表明区分大小写时，是否强制小写处理

    public function needTableToLower()
    {
        if (is_null($this->lower_table_name)) {
            $sql = "SHOW Variables LIKE 'lower_case_table_names'";
            $name_case = $this->queryCol($sql);
            $this->lower_table_name = intval($name_case) === 1;
        }
        return $this->lower_table_name;
    }

    public function doExchange($dbname, $create = false)
    {
        if ($create) {
            $this->pdo->exec(sprintf("CREATE DATABASE IF NOT EXIST `%s`", $dbname));
        }
        $this->pdo->exec(sprintf("USE `%s`", $dbname));
    }

    public function getDBName()
    {
        if (empty($this->dbname)) {
            $this->dbname = $this->queryCol("SELECT DATABASE()", [], 0);
        }
        return $this->dbname;
    }

    public function getTableName($table, $quote = false)
    {
        if (strpos($table, '.') === false) {
            $table = $this->tblpre . $table;
        }
        if ($this->needTableToLower()) {
            $table = strtolower($table);
        }
        if ($quote) {
            $table = str_replace('.', '`.`', '`' . $table . '`');
        }
        return $table;
    }

    public function getLimit($length, $offset = 0)
    {
        if ($offset > 0) {
            $limit = sprintf(" LIMIT %d, %d", $offset, $length);
        } else {
            $limit = sprintf(" LIMIT %d", $length);
        }
        return ["", $limit];
    }

    public function getColumns($table)
    {
        $query = $this->queryModel(__NAMESPACE__ . '\\Column');
        $table_name = $this->getTableName($table, false);
        $query->findBy('TABLE_SCHEMA', $this->getDBName());
        $query->findBy('TABLE_NAME', $table_name);
        $query->orderBy('ORDINAL_POSITION');
        $columns = [
            'COLUMN_NAME', 'COLUMN_DEFAULT', 'COLUMN_KEY', 'IS_NULLABLE',
            'COLUMN_TYPE', 'DATA_TYPE', 'CHARACTER_MAXIMUM_LENGTH',
            'NUMERIC_PRECISION', 'NUMERIC_SCALE', 'DATETIME_PRECISION',
        ];
        array_unshift($columns, 'COLUMN_NAME');
        return $query->all($columns, PDO::FETCH_UNIQUE);
    }

    public function getPKey($table)
    {
        $table_name = $this->getTableName($table);
        $sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS"
            . " WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND COLUMN_KEY='PRI'";
        $params = [$this->dbname, $table_name];
        return $this->queryCol($sql, $params);
    }

    public function isExists($table)
    {
        $table_name = $this->getTableName($table);
        $sql = "SELECT TABLE_NAME FROM information_schema.TABLES"
            . " WHERE TABLE_NAME=? AND (TABLE_SCHEMA=? OR TABLE_CATALOG=?)";
        $params = [$table_name, $this->dbname, $this->dbname];
        $table = $this->queryCol($sql, $params);
        return $table_name === $table;
    }

    public function listTables()
    {
        $sql = sprintf("SHOW TABLES FROM `%s`", $this->getDBName());
        $params = [];
        if (!empty($this->tblpre)) {
            $sql .= " LIKE ?";
            $params[] = str_replace('_', '\_', $this->tblpre) . '%';
        }
        return $this->queryPairs($sql, $params);
    }

    public function getCreateSQL($table, $new_table, $same_db = false, $same_type = false)
    {
        $table_name = $this->getTableName($table);
        if ($same_db) {
            $sql = "CREATE TABLE `%s` LIKE `%s`";
            return sprintf($sql, $new_table, $table_name);
        } else if ($same_type) {
            $create = sprintf("CREATE TABLE `%s`", $table_name);
            $sql = $this->queryCol("SHOW $create", [], 1);
            $sql = preg_replace('/(AUTO_INCREMENT=\d+)/', 'AUTO_INCREMENT=1', $sql, 1);
            $create_if_not = sprintf("CREATE TABLE IF NOT EXISTS `%s`", $new_table);
            return str_replace($create, $create_if_not, $sql);
        } else {
            $pk_field = "";
            $pk_state = "";
            $other_fields = "";
            $columns = $this->getColumns($table);
            foreach ($columns as $column) {
                $name = $column->name;
                $default = trim($column->default, "()");
                if ($column->getCategory() === 'char') {
                    $length = intval($column->length);
                    $type = ($length > 255 || $length < 0) ? "text" : "varchar($length)";
                } else if ($column->getCategory() === 'int') {
                    $precision = intval($column->precision);
                    if ($default === '') {
                        $default = "0";
                    }
                    $type = $column->type . "($precision)";
                } else if ($column->getCategory() === 'float') {
                    $precision = intval($column->precision);
                    $scale = intval($column->scale);
                    if ($default === '') {
                        $default = "0.0";
                    }
                    $type = $column->type;
                    if ($column->type === 'real') {
                        $type = 'float';
                    } else if ($column->type === 'money') {
                        $type = 'numeric';
                    }
                    $type .= "($precision,$scale)";
                } else if ($column->getCategory() === 'datetime') {
                    $type = 'datetime';
                } else {
                    $type = $column->type;
                }
                if ($column->isPrimaryKey()) {//主键
                    $pk_field = "    `$name` int(10) unsigned NOT NULL AUTO_INCREMENT,";
                    $pk_state = "PRIMARY KEY (`$name`)";
                } else if (starts_with($type, 'date') || ends_with($type, 'text')) {
                    $other_fields .= "    `$name` $type NULL,\n";
                } else if ($column->isNullable()) {
                    $other_fields .= "    `$name` $type NULL,\n";
                } else {
                    $other_fields .= "    `$name` $type NOT NULL DEFAULT '$default',\n";
                }
            }
            $tpl = <<<EOD
CREATE TABLE IF NOT EXISTS `%s` (
%s
%s
    %s
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT
EOD;
            if (empty($pk_field)) {
                $other_fields = rtrim($other_fields, ",\n");
            }
            $sql = sprintf($tpl, $new_table, $pk_field, $other_fields, $pk_state);
            return $sql;
        }
    }

    /**
     * 将当前范围的数据输出到文件，默认格式为TSV文本
     * @param string $fname 输出文件路径
     * @param string $ftb 字段值分隔符
     * @param string $ltb 行数据分隔符
     * @param string $oeb 字段值定界符
     * @param string $nrb NULL的替代符号
     * @return int 输出的数据行数
     */
    public function sqlToFile($sql, $fname, $ftb = "\t", $ltb = PHP_EOL, $oeb = '"', $feb = '"')
    {
        @mkdir(dirname($fname), 0664, true);
        $addition = "FIELDS TERMINATED BY '" . addslashes($ftb) . "'";
        $addition .= " LINES TERMINATED BY '" . addslashes($ltb) . "'";
        if ($oeb) {
            $addition .= " OPTIONALLY ENCLOSED BY '" . addslashes($oeb) . "'";
        }
        if ($feb) {
            $addition .= " FIELDS ESCAPED BY '" . addslashes($feb) . "'";
        }
        $tmp_fname = sys_get_temp_dir() . DIRECTORY_SEPARATOR . basename($fname);
        $outsql = "$sql INTO OUTFILE '$tmp_fname' $addition";

        try {
            if (file_exists($tmp_fname)) {
                unlink($tmp_fname);
            }
            $this->execute($outsql);
            if (file_exists($tmp_fname)) {
                rename($tmp_fname, $fname);
                $lines = shell_exec('wc -l ' . $fname . ' | cut -d" " -f1');
                $lines = trim($lines); //后面带有换行符
                return is_numeric($lines) ? intval($lines) : 0;
            }
        } catch (\Exception $e) {
            //数据库权限或文件系统权限不足，继续下面的传统方法
        }
    }
}
