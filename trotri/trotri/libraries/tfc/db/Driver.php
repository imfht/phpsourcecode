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

use tfc\ap\Application;
use tfc\ap\PDOException;
use tfc\ap\ErrorException;

/**
 * Driver class file
 * PDO方式连接数据库
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Driver.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.db
 * @since 1.0
 */
class Driver extends Application
{
    /**
     * @var \PDO|null PDO类实例
     */
    protected $_pdo = null;

    /**
     * @var string Data Source Name
     */
    protected $_dsn = '';

    /**
     * @var string 数据库类型
     */
    protected $_dbType = '';

    /**
     * @var string 数据库用户名
     */
    protected $_username = '';

    /**
     * @var string 数据库密码
     */
    protected $_password = '';

    /**
     * @var string 数据库字符编码
     */
    protected $_charset = '';

    /**
     * @var integer 通过列名获取数据时，列名大小写规则
     * Options
     * PDO::CASE_NATURAL (default) 默认方式
     * PDO::CASE_LOWER 列名小写
     * PDO::CASE_UPPER 列名大写
     */
    protected $_caseFolding = \PDO::CASE_NATURAL;

    /**
     * 构造方法：初始化DSN、数据库用户名、密码和字符编码
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param string $charset
     */
    public function __construct($dsn, $username, $password, $charset = 'UTF8')
    {
        $this->_dsn = $dsn;
        $this->_username = $username;
        $this->_password = $password;
        $this->_charset = $charset;
        $this->_dbType = strtolower(substr($this->_dsn, 0, strpos($this->_dsn, ':')));
    }

    /**
     * 初始化PDO，连接数据库
     * @return \tfc\db\Driver
     * @throws ErrorException 如果实例化PDO对象失败，抛出异常
     */
    public function open()
    {
        if ($this->getIsConnected()) {
            return $this;
        }

        try {
            $this->_pdo = new \PDO($this->getDsn(), $this->getUsername(), $this->getPassword());
            $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->_pdo->setAttribute(\PDO::ATTR_CASE, $this->getCaseFolding());
            if ($this->getCharset() != '') {
                $driver = strtolower($this->_pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
                if (in_array($driver, array('pgsql', 'mysql', 'mysqli'))) {
                    $this->_pdo->exec('SET NAMES ' . $this->_pdo->quote($this->getCharset()));
                }
            }
        }
        catch (\PDOException $e) {
            $e = new PDOException($e);
            throw new ErrorException(sprintf(
                'Driver PDO connect db failed, %s', $e->getMessage()
            ), (int) $e->getCode());
        }

        return $this;
    }

    /**
     * 关闭数据库连接
     * @return \tfc\db\Driver
     */
    public function close()
    {
        $this->_pdo = null;
        return $this;
    }

    /**
     * 获取连接数据库的状态
     * @return boolean
     */
    public function getIsConnected()
    {
        return ($this->_pdo instanceof \PDO);
    }

    /**
     * 获取最后一次插入记录的ID
     * @return integer
     * @throws ErrorException 如果获取ID失败，抛出异常
     */
    public function getLastInsertId()
    {
        try {
            return (int) $this->getPdo()->lastInsertId();
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Driver PDO get last insert id failed, %s', $e->getMessage()
            ), (int) $e->getCode());
        }
    }

    /**
     * 防止SQL注入，对字符串进行处理
     *
     * <pre>
     * $driver = new Driver();
     *
     * $value = $driver->quoteValue('"\a');
     * 结果：'\"\\\a'
     *
     * $value = $driver->quoteValue("'\a");
     * 结果：'''\\\a'
     * </pre>
     * @param string $value
     * @return string
     */
    public function quoteValue($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }

        if ($this->getIsConnected() && (($value = $this->getPdo()->quote($value)) !== false)) {
            return $value;
        }
        // the driver doesn't support quote (e.g. oci)
        else {
            return "'" . addcslashes(str_replace("'", "''", $value), "\000\n\r\\\032") . "'";
        }
    }

    /**
     * 将普通的字符类型转化成PDO的字符类型
     *
     * <pre>
     * $driver = new Driver();
     *
     * $type = $driver->getPDOType(getType(2));
     * 结果：PDO::PARAM_INT (1)
     *
     * $type = $driver->getPDOType(getType('a'));
     * 结果：PDO::PARAM_STR (2)
     *
     * $type = $driver->getPDOType(getType(true));
     * 结果：PDO::PARAM_BOOL (5)
     *
     * $type = $driver->getPDOType(getType(NULL));
     * 结果：PDO::PARAM_NULL (0)
     * </pre>
     * @param string $type
     * @return integer
     */
    public function getPDOType($type)
    {
        static $maps = array(
            'boolean' => \PDO::PARAM_BOOL,
            'integer' => \PDO::PARAM_INT,
            'string'  => \PDO::PARAM_STR,
            'NULL'    => \PDO::PARAM_NULL
        );
        return isset($maps[$type]) ? $maps[$type] : \PDO::PARAM_STR;
    }

    /**
     * 通过属性名，获取属性值
     * @param integer $attribute
     * @return mixed
     * @throws ErrorException 如果参数不是有效的属性名，抛出异常
     */
    public function getAttribute($attribute)
    {
        try {
            return $this->getPdo()->getAttribute($attribute);
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Driver PDO get attribute "%d" failed, %s', $attribute, $e->getMessage()
            ), (int) $e->getCode());
        }
    }

    /**
     * 通过属性名，设置属性值
     * @param integer $attribute
     * @param mixed $value
     * @return boolean
     * @throws ErrorException 如果参数不是有效的属性名或者设置属性值失败，抛出异常
     */
    public function setAttribute($attribute, $value)
    {
        try {
            return $this->getPdo()->setAttribute($attribute, $value);
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Driver PDO set attribute "%d => %s" failed, %s', $attribute, $value, $e->getMessage()
            ), (int) $e->getCode());
        }
    }

    /**
     * 获取数据库配置信息
     * @return array
     */
    public function getConfig()
    {
        return array(
            'dsn' => $this->getDsn(),
            'dbType' => $this->getDbType(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'charset' => $this->getCharset(),
            'caseFolding' => $this->getCaseFolding(),
        );
    }

    /**
     * 获取列名大小写规则
     * @return integer
     */
    public function getCaseFolding()
    {
        return $this->_caseFolding;
    }

    /**
     * 设置列名大小写规则
     * @param integer $caseFolding
     * @return \tfc\db\Driver
     * @throws ErrorException 如果参数不是有效的规则值，抛出异常
     */
    public function setCaseFolding($caseFolding = \PDO::CASE_NATURAL)
    {
        if (in_array($caseFolding, array(\PDO::CASE_NATURAL, \PDO::CASE_LOWER, \PDO::CASE_UPPER), true)) {
            $this->_caseFolding = $caseFolding;
            return $this;
        }

        throw new ErrorException(sprintf(
            'Driver Invalid PDO case folding "%d"', $caseFolding
        ));
    }

    /**
     * 获取PDO对象
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->_pdo;
    }

    /**
     * 获取Data Source Name
     * @return string
     */
    public function getDsn()
    {
        return $this->_dsn;
    }

    /**
     * 获取数据库类型
     * @return string
     */
    public function getDbType()
    {
        return $this->_dbType;
    }

    /**
     * 获取数据库用户名
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * 获取数据库密码
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * 获取数据库字符编码
     * @return string
     */
    public function getCharset()
    {
        return $this->_charset;
    }
}
