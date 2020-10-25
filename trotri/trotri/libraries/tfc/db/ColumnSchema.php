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
 * ColumnSchema class file
 * 寄存数据库列的概要描述，包含列名、是否允许为空、数据库式类型、PHP语言式类型、是否是主键、默认值等
 * <ul>
 * <li>{@link $name}</li>
 * <li>{@link $allowNull}</li>
 * <li>{@link $dbType}</li>
 * <li>{@link $type}</li>
 * <li>{@link $defaultValue}</li>
 * <li>{@link $size}</li>
 * <li>{@link $scale}</li>
 * <li>{@link $isPrimaryKey}</li>
 * <li>{@link $isForeignKey}</li>
 * <li>{@link $isAutoIncrement}</li>
 * </ul>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: ColumnSchema.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.db
 * @since 1.0
 */
class ColumnSchema
{
    /**
     * @var string 列名
     */
    public $name;

    /**
     * @var boolean 是否允许为空
     */
    public $allowNull = false;

    /**
     * @var string 列的数据库式类型
     */
    public $dbType;

    /**
     * @var string 列的PHP语言式类型
     */
    public $type;

    /**
     * @var mixed 默认的值
     */
    public $defaultValue;

    /**
     * @var integer 列的长度
     */
    public $size;

    /**
     * @var integer 精度
     */
    public $scale;

    /**
     * @var boolean 是否是主键
     */
    public $isPrimaryKey = false;

    /**
     * @var boolean 是否是外键
     */
    public $isForeignKey = false;

    /**
     * @var boolean 是否自增
     */
    public $isAutoIncrement = false;
}
