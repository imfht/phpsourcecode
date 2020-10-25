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

use tfc\ap\ErrorException;
use tfc\ap\Singleton;
use tfc\db\TableSchema;
use tfc\saf\DbProxy;

/**
 * EntityBuilder class file
 * 生成表的实体类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: EntityBuilder.php 1 2013-05-18 14:58:59Z huan.song $
 * @package tdo
 * @since 1.0
 */
class EntityBuilder
{
    /**
     * @var instance of tfc\saf\DbProxy
     */
    protected $_dbProxy = null;

    /**
     * @var instance of tdo\Metadata
     */
    protected $_metadata = null;

    /**
     * @var array instances of tdo\EntityBuilder
     */
    protected static $_instances = array();

    /**
     * 构造方法：初始化数据库操作类和MySQL表结构处理类
     * @param \tfc\saf\DbProxy $dbProxy
     */
    protected function __construct(DbProxy $dbProxy)
    {
        $this->_dbProxy = $dbProxy;
        $this->_metadata = new Metadata($dbProxy);
    }

    /**
     * 单例模式：获取本类的实例
     * @param \tfc\saf\DbProxy $dbProxy
     * @return \tdo\EntityBuilder
     */
    public static function getInstance(DbProxy $dbProxy)
    {
        $clusterName = $dbProxy->getClusterName();
        if (!isset(self::$_instances[$clusterName])) {
            self::$_instances[$clusterName] = new self($dbProxy);
        }

        return self::$_instances[$clusterName];
    }

    /**
     * 通过表的实体类，获取表的概要描述，包括：表名、主键、自增字段、字段、默认值
     * 应该根据不同的数据库类型创建对应的TableSchema类：$dbType = $this->getDriver(false)->getDbType();
     * 这里只用到MySQL数据库，暂时不做多数据库类型
     * @param string $tableName
     * @return \tfc\db\TableSchema
     */
    public function getTableSchema($tableName)
    {
        $className = 'tfc\\db\\TableSchema::' . strtolower($tableName);
        if (Singleton::has($className)) {
            return Singleton::get($className);
        }

        $ref = $this->getRefClass($tableName);
        $attributes = $ref->getDefaultProperties();

        $tableSchema = new TableSchema();
        $tableSchema->name = $ref->hasConstant('TABLE_NAME') ? $ref->getConstant('TABLE_NAME') : $ref->getShortName();
        $tableSchema->autoIncrement = $ref->hasConstant('AUTO_INCREMENT') ? $ref->getConstant('AUTO_INCREMENT') : null;
        if (isset($attributes['primaryKey'])) {
            $tableSchema->primaryKey = $attributes['primaryKey'];
            unset($attributes['primaryKey']);
        }

        $tableSchema->columnNames = array_keys($attributes);
        if ($tableSchema->primaryKey === null) {
            $tableSchema->primaryKey = $tableSchema->columnNames[0];
        }

        foreach ($attributes as $key => $value) {
            if ($value === null) {
                unset($attributes[$key]);
            }
        }
        $tableSchema->attributeDefaults = $attributes;

        Singleton::set($className, $tableSchema);
        return $tableSchema;
    }

    /**
     * 获取实体类的反射对象
     * @param string $tableName
     * @return class reports
     */
    public function getRefClass($tableName)
    {
        require_once $this->getEntityFile($tableName);
        return new \ReflectionClass('\\' . ucfirst(strtolower($tableName)));
    }

    /**
     * 获取实体类所在的文件名，如果文件不存在，会自动创建文件
     * @param string $tableName
     * @return string
     * @throws ErrorException 如果没有创建文件的权限，抛出异常
     */
    public function getEntityFile($tableName)
    {
        $tableName = strtolower($tableName);
        $className = ucfirst($tableName);
        $filePath = $this->getEntityDir() . DS . $className . '.php';
        if (is_file($filePath)) {
            return $filePath;
        }

        if (!($stream = @fopen($filePath, 'w', false))) {
            throw new ErrorException(sprintf(
                'EntityBuilder file "%s" cannot be opened with mode "w"', $filePath
            ));
        }

        $tableSchema = $this->getMetadata()->getTableSchema($tableName);

        fwrite($stream, "<?php\n");
        fwrite($stream, "/**\n");
        fwrite($stream, " * {$className} class file\n");
        fwrite($stream, " * {$tableName} 表实体\n");
        fwrite($stream, " * @author Create by tdo\\EntityBuilder\n");
        fwrite($stream, " * @version \$Id: {$className}.php 1 " . date('Y-m-d H:i:s') . "Z tdo\\EntityBuilder $\n");
        fwrite($stream, " * @package \n");
        fwrite($stream, " * @since 1.0\n");
        fwrite($stream, " */\n");

        fwrite($stream, "class {$className}\n");
        fwrite($stream, "{\n");
        fwrite($stream, "    /**\n");
        fwrite($stream, "     * @var string 表名\n");
        fwrite($stream, "     */\n");
        fwrite($stream, "    const TABLE_NAME = '" . $tableSchema->name . "';\n\n");

        fwrite($stream, "    /**\n");
        fwrite($stream, "     * @var string 自增字段名\n");
        fwrite($stream, "     */\n");
        fwrite($stream, "    const AUTO_INCREMENT = '" . $tableSchema->autoIncrement . "';\n\n");

        fwrite($stream, "    /**\n");
        fwrite($stream, "     * @var array|string 主键名\n");
        fwrite($stream, "     */\n");
        fwrite($stream, "    public static \$primaryKey = " . (is_array($tableSchema->primaryKey) ? "array ('" . implode("', '", $tableSchema->primaryKey) . "')" : "'" . $tableSchema->primaryKey . "'") . ";\n\n");

        $comments = $this->getMetadata()->getComments($tableName);
        foreach ($tableSchema->columnNames as $columnName) {
            $comment = isset($comments[$columnName]) ? $comments[$columnName] : '';
            $type = $tableSchema->columns[$columnName]->type;
            $defaultValue = isset($tableSchema->attributeDefaults[$columnName]) ? $tableSchema->attributeDefaults[$columnName] : '';
            if ($defaultValue !== '' && $type === 'string') {
                $defaultValue = '\'' . $defaultValue . '\'';
            }

            fwrite($stream, "    /**\n");
            fwrite($stream, "     * @var $type $comment\n");
            fwrite($stream, "     */\n");
            fwrite($stream, "    public \${$columnName}" . (($defaultValue !== '') ? " = $defaultValue" : '') . ";\n\n");
        }

        fwrite($stream, "}\n");
        return $filePath;
    }

    /**
     * 获取实体类所在的目录名，如果目录不存在，会自动创建目录
     * @return string
     * @throws ErrorException 如果创建目录失败，抛出异常
     */
    public function getEntityDir()
    {
        $dir = DIR_DATA_RUNTIME_ENTITIES . DS . $this->getDbProxy()->getClusterName();
        $mode = 0664;
        if (!is_dir($dir)) {
            mkdir($dir, $mode, true);
        }

        if (!is_dir($dir)) {
            throw new ErrorException(sprintf(
                'EntityBuilder dir "%s" cannot be created with mode "%o" ', $dir, $mode
            ));
        }

        return $dir;
    }

    /**
     * 获取数据库操作类
     * @return \tfc\saf\DbProxy
     */
    public function getDbProxy()
    {
        return $this->_dbProxy;
    }

    /**
     * 获取MySQL表结构处理类
     * @return \tdo\Metadata
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }
}
