<?php
/**
 * Trotri Data Objects
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tdo;

use tfc\db\TableSchema;
use tfc\db\ColumnSchema;
use tfc\saf\DbProxy;

/**
 * Metadata class file
 * 分析MySQL表结构类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Metadata.php 1 2013-05-18 14:58:59Z huan.song $
 * @package tdo
 * @since 1.0
 */
class Metadata
{
    /**
     * @var instance of tfc\saf\DbProxy
     */
    protected $_dbProxy = null;

    /**
     * 构造方法：初始化数据库操作类
     * @param \tfc\saf\DbProxy $dbProxy
     */
    public function __construct(DbProxy $dbProxy)
    {
        $this->_dbProxy = $dbProxy;
    }

    /**
     * 获取MySQL版本号
     * @return string
     */
    public function getVersion()
    {
        return $this->getDbProxy()->fetchColumn('SELECT VERSION()');
    }

    /**
     * 获取数据库中所有表名，如果$value不为null，通过LIKE匹配$value指定的表名
     * @param string|null $value
     * @return array
     */
    public function getTableNames($value = null)
    {
        $sql = 'SHOW TABLES';
        if ($value !== null) {
            $sql .= ' LIKE \'%' . $value . '%\'';
        }

        $tables = $this->getDbProxy()->fetchAll($sql);
        $tableNames = array();
        foreach ($tables as $table) {
            $tableNames[] = array_shift($table);
        }

        return $tableNames;
    }

    /**
     * 获取创建表的SQL语句
     * @param string $tableName
     * @return string
     */
    public function getCreateTable($tableName)
    {
        return $this->getDbProxy()->fetchColumn('SHOW CREATE TABLE ' . $tableName, null, 1);
    }

    /**
     * 获取表的所有字段信息
     * @param string $tableName
     * @return array
     */
    public function getColumns($tableName)
    {
        return $this->getDbProxy()->fetchAll('SHOW COLUMNS FROM ' . $tableName);
    }

    /**
     * 获取指定表的概要描述
     * @param string $tableName
     * @return \tfc\db\TableSchema
     */
    public function getTableSchema($tableName)
    {
        $tableSchema = new TableSchema();
        $tableSchema->name = $tableName;

        $columns = $this->getColumns($tableName);
        foreach ($columns as $values) {
            $columnSchema = $this->getColumnSchema($values);
            if ($columnSchema->isPrimaryKey) {
                if ($tableSchema->primaryKey === null) {
                    $tableSchema->primaryKey = $columnSchema->name;
                }
                else {
                    $tableSchema->primaryKey = (array) $tableSchema->primaryKey;
                    $tableSchema->primaryKey[] = $columnSchema->name;
                }
            }

            if ($columnSchema->isAutoIncrement) {
                $tableSchema->autoIncrement = $columnSchema->name;
            }

            if ($columnSchema->defaultValue !== null) {
                $tableSchema->attributeDefaults[$columnSchema->name] = $this->typecast($columnSchema->defaultValue, $columnSchema->type);
            }

            $tableSchema->columnNames[] = $columnSchema->name;
            $tableSchema->columns[$columnSchema->name] = $columnSchema;
        }

        if (is_array($tableSchema->primaryKey)) {
            $tableSchema->primaryKey = $this->getPrimary($tableName);
        }

        return $this->_tableSchemas[$tableName] = $tableSchema;
    }

    /**
     * 获取列的概要描述
     * @param array $columns
     * @return \tfc\db\ColumnSchema
     */
    public function getColumnSchema($columns)
    {
        $columnSchema = new ColumnSchema();

        $columnSchema->name = $columns['Field'];
        $columnSchema->allowNull = ($columns['Null'] === 'YES');
        $columnSchema->isPrimaryKey = (strpos($columns['Key'], 'PRI') !== false);
        $columnSchema->dbType = $columns['Type'];
        $columnSchema->type = $this->extractType($columnSchema->dbType);
        $columnSchema->size = $this->extractSize($columnSchema->dbType);
        $columnSchema->scale = $this->extractScale($columnSchema->dbType);
        $columnSchema->defaultValue = $this->typecast($columns['Default'], $columnSchema->type);
        $columnSchema->isAutoIncrement = (strpos(strtolower($columns['Extra']), 'auto_increment') !== false);

        return $columnSchema;
    }

    /**
     * 获取表的主键信息
     * @param string $tableName
     * @return array|string
     */
    public function getPrimary($tableName)
    {
        $sql = str_replace('`', '', $this->getCreateTable($tableName));
        $regs = array();
        $preg = '/PRIMARY KEY\s*\(([^\)]+)\)\s*/mi';
        preg_match_all($preg, $sql, $regs, PREG_SET_ORDER);
        if ($regs === array()) {
            return null;
        }

        $primaryKey = str_replace(' ', '', $regs[0][1]);
        if (strpos($primaryKey, ',') === false) {
            return $primaryKey;
        }

        return explode(',', $primaryKey);
    }

    /**
     * 获取表的外键信息
     * @param string $tableName
     * @return array
     */
    public function getConstraints($tableName)
    {
        $sql = str_replace('`', '', $this->getCreateTable($tableName));
        $regs = array();
        $preg = '/FOREIGN KEY\s+\(([^\)]+)\)\s+REFERENCES\s+([^\(^\s]+)\s*\(([^\)]+)\)/mi';
        preg_match_all($preg, $sql, $regs, PREG_SET_ORDER);

        $constraints = array();
        foreach ($regs as $reg) {
            $keys = array_map('trim', explode(',', $reg[1]));
            $fks = array_map('trim', explode(',', $reg[3]));
            foreach ($keys as $key => $name) {
                $constraints[$name] = array($reg[2], $fks[$key]);
            }
        }

        return $constraints;
    }

    /**
     * 获取表中所有字段的描述
     * @param string $tableName
     * @return array
     */
    public function getComments($tableName)
    {
        $sql = str_replace('`', '', $this->getCreateTable($tableName));
        $lines = explode("\n", $sql);
        array_shift($lines);
        array_pop($lines);
        $comments = array();
        foreach ($lines as $line) {
            $line = rtrim(trim($line), ' ,');
            if (($pos = stripos($line, ' COMMENT \'')) === false) {
                continue;
            }

            $key = strstr($line, ' ', true);
            $comments[$key] = trim(substr($line, $pos + 9), ' \'');
        }

        if (($pos = stripos($sql, ' COMMENT=\'')) !== false) {
            $comments['__table__'] = trim(substr($sql, $pos + 10), ' \'');
        }

        return $comments;
    }

    /**
     * 通过列的PHP语言式类型，转换值的类型
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    public function typecast($value, $type)
    {
        if ($value === null || $value === '' || gettype($value) === $type) {
            return $value;
        }

        switch ($type) {
            case 'string': return (string) $value;
            case 'integer': return (integer) $value;
            case 'boolean': return (boolean) $value;
            case 'double':
            default: return $value;
        }
    }

    /**
     * 通过列的数据库式类型获取列的PHP语言式类型
     * @param string $dbType
     * @return string
     */
    public function extractType($dbType)
    {
        if ((stripos($dbType, 'int') !== false) && (stripos($dbType, 'unsigned int') === false)) {
            return 'integer';
        }
        elseif (stripos($dbType, 'bool') !== false) {
            return 'boolean';
        }
        elseif (preg_match('/(real|floa|doub)/i', $dbType)) {
            return 'double';
        }

        return 'string';
    }

    /**
     * 通过列的数据库式类型获取字段长度
     * @param string $dbType
     * @return string
     */
    public function extractSize($dbType)
    {
        if (strpos($dbType, '(') && preg_match('/\((.*)\)/', $dbType, $regs)) {
            $values = explode(',', $regs[1]);
            return (int) $values[0];
        }

        return null;
    }

    /**
     * 通过列的数据库式类型获取字段精度
     * @param string $dbType
     * @return string
     */
    public function extractScale($dbType)
    {
        if (strpos($dbType, '(') && preg_match('/\((.*)\)/', $dbType, $regs)) {
            $values = explode(',', $regs[1]);
            if (isset($values[1])) {
                return (int) $values[1];
            }
        }

        return null;
    }

    /**
     * 获取数据库操作类
     * @return \tfc\saf\DbProxy
     */
    public function getDbProxy()
    {
        return $this->_dbProxy;
    }
}
