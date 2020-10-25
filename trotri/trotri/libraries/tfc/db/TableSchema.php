<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\db;

/**
 * TableSchema class file
 * 寄存数据库表的概要描述，包含表名、主键、表的自增字段、字段名、字段默认值等
 * <ul>
 * <li>{@link $name}</li>
 * <li>{@link $primaryKey}</li>
 * <li>{@link $autoIncrement}</li>
 * <li>{@link $columnNames}</li>
 * <li>{@link $columns}</li>
 * <li>{@link $attributeDefaults}</li>
 * </ul>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: TableSchema.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.db
 * @since 1.0
 */
class TableSchema
{
    /**
     * @var string 表名
     */
    public $name;

    /**
     * @var array|string 表的主键
     */
    public $primaryKey;

    /**
     * @var string 表的自增字段
     */
    public $autoIncrement;

    /**
     * @var array 所有的列名
     */
    public $columnNames = array();

    /**
     * @var array 所有的列信息
     */
    public $columns = array();

    /**
     * @var array 所有默认的值
     */
    public $attributeDefaults = array();

    /**
     * 判断列名是否存在
     * @param string $name
     * @return boolean
     */
    public function hasColumn($name)
    {
        return in_array($name, $this->columnNames);
    }
}
