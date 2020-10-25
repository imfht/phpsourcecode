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

/**
 * CommandBuilder class file
 * 创建简单的MySQL执行命令类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: CommandBuilder.php 1 2013-05-18 14:58:59Z huan.song $
 * @package tdo
 * @since 1.0
 */
class CommandBuilder
{
    /**
     * @var string 填充SQL字符
     */
    const PLACE_HOLDERS = '?';

    /**
     * 创建查询数据的命令
     * @param string $table
     * @param array $columnNames
     * @param string $condition
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @param string $option
     * @return string
     */
    public function createFind($table, array $columnNames = array(), $condition = '', $order = '', $limit = 0, $offset = 0, $option = '')
    {
        $option !== '' && $option = strtoupper($option) . ' ';
        $command = 'SELECT ' . $option . implode(', ', $this->quoteColumnNames($columnNames)) . ' FROM ' . $this->quoteTableName($table);
        $command = $this->applyCondition($command, $condition);
        $command = $this->applyOrder($command, $order);
        $command = $this->applyLimit($command, $limit, $offset);
        return $command;
    }

    /**
     * 创建查询记录数的命令
     * @param string $table
     * @param string $condition
     * @return string
     */
    public function createCount($table, $condition = '')
    {
        $command = 'SELECT COUNT(*) AS total FROM ' . $this->quoteTableName($table);
        return $this->applyCondition($command, $condition);
    }

    /**
     * 创建新增数据的命令
     * @param string $table
     * @param array $columnNames
     * @param boolean $ignore
     * @return string
     */
    public function createInsert($table, array $columnNames = array(), $ignore = false)
    {
        $command = 'INSERT ' . ($ignore ? 'IGNORE INTO ' : 'INTO ') .  $this->quoteTableName($table);
        $command .= ' (' . implode(', ', $this->quoteColumnNames($columnNames)) . ') VALUES';
        $command .= ' (' . rtrim(str_repeat(self::PLACE_HOLDERS . ', ', count($columnNames)), ', ') . ')';
        return $command;
    }

    /**
     * 创建新增数据的命令，如果主键或唯一键存在则执行编辑命令
     * @param string $table
     * @param array $columnNames
     * @param string $onDup
     * @param boolean $ignore
     * @return string
     */
    public function createInsertOnDup($table, array $columnNames = array(), $onDup = '', $ignore = false)
    {
        return $this->createInsert($table, $columnNames, $ignore) . ' ON DUPLICATE KEY UPDATE ' . $onDup;
    }

    /**
     * 创建编辑数据的命令
     * @param string $table
     * @param array $columnNames
     * @param string $condition
     * @return string
     */
    public function createUpdate($table, array $columnNames = array(), $condition = '')
    {
        $command = 'UPDATE ' . $this->quoteTableName($table) . ' SET ';
        $command .= implode(' = ' . self::PLACE_HOLDERS . ', ', $this->quoteColumnNames($columnNames)) . ' = ' . self::PLACE_HOLDERS;
        return $this->applyCondition($command, $condition);
    }

    /**
     * 创建编辑数据的命令，如果数据存在则编辑，如果数据不存在则新增
     * @param string $table
     * @param array $columnNames
     * @return string
     */
    public function createReplace($table, array $columnNames = array())
    {
        $command = 'REPLACE INTO ' .  $this->quoteTableName($table);
        $command .= ' (' . implode(', ', $this->quoteColumnNames($columnNames)) . ') VALUES';
        $command .= ' (' . rtrim(str_repeat(self::PLACE_HOLDERS . ', ', count($columnNames)), ', ') . ')';
        return $command;
    }

    /**
     * 创建删除数据的命令
     * @param string $table
     * @param string $condition
     * @return string
     */
    public function createDelete($table, $condition)
    {
        $command = 'DELETE FROM ' . $this->quoteTableName($table);
        return $this->applyCondition($command, $condition);
    }

    /**
     * 根据键名创建条件“与”命令
     * @param array $columnNames
     * @return string
     */
    public function createAndCondition(array $columnNames = array())
    {
        if ($columnNames) {
            return implode(' = ' . self::PLACE_HOLDERS . ' AND ', $this->quoteColumnNames($columnNames)) . ' = ' . self::PLACE_HOLDERS;
        }

        return '';
    }

    /**
     * 向命令中追加条件命令
     * @param string $command
     * @param string $condition
     * @return string
     */
    public function applyCondition($command, $condition)
    {
        if ($condition != '') {
            return $command . ' WHERE ' . $condition;
        }

        return $command;
    }

    /**
     * 向命令中追加条件“与”命令
     * @param string $command
     * @param string $condition
     * @return string
     */
    public function applyAndCondition($command, $condition)
    {
        if ($condition != '') {
            return $command . ' AND ' . $condition;
        }

        return $command;
    }

    /**
     * 向命令中追加条件“或”命令
     * @param string $command
     * @param string $condition
     * @return string
     */
    public function applyOrCondition($command, $condition)
    {
        if ($condition != '') {
            return $command . ' OR ' . $condition;
        }

        return $command;
    }

    /**
     * 向命令中追加排序命令
     * @param string $command
     * @param string $order
     * @return string
     */
    public function applyOrder($command, $order)
    {
        if ($order != '') {
            $command .= ' ORDER BY ' . $order;
        }

        return $command;
    }

    /**
     * 向命令中追加限制查询条数命令
     * @param string $command
     * @param integer $limit 读取的条数
     * @param integer $offset 从哪条开始读取
     * @return string
     */
    public function applyLimit($command, $limit, $offset = 0)
    {
        if (($limit = (int) $limit) > 0 && ($offset = (int) $offset) >= 0) {
            $command .= ' LIMIT ' . $offset . ', ' . $limit;
        }

        return $command;
    }

    /**
     * 引用一个表名，被引用的表名可放在SQL命令中执行
     * @param string $name
     * @return string
     */
    public function quoteTableName($name)
    {
        return '`' . $name . '`';
    }

    /**
     * 引用多个列名，被引用的列名可放在SQL命令中执行
     * @param array $names
     * @return array
     */
    public function quoteColumnNames(array $names)
    {
        return array_map(array($this, 'quoteColumnName'), $names);
    }

    /**
     * 引用一个列名，被引用的列名可放在SQL命令中执行
     * @param string $name
     * @return string
     */
    public function quoteColumnName($name)
    {
        return '`' . $name . '`';
    }
}
