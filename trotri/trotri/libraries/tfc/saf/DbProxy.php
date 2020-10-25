<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\saf;

use tfc\ap\ErrorException;
use tfc\db\Driver;
use tfc\db\Statement;
use tfc\db\Transaction;

/**
 * DbProxy class file
 * 数据库代理操作类，连接失败尝试重试、记录操作日志、主从数据库连接管理（待扩展）
 *
 * 配置 /cfg/db/cluster.php：
 * <pre>
 * return array (
 *   'service' => array (
 *     'dsn' => string,      // Data Source Name
 *     'username' => string, // 数据库用户名
 *     'password' => string, // 数据库密码
 *     'charset' => string,  // 数据库字符编码
 *     'retry' => integer,   // 连接数据库失败后，尝试重连的最大次数
 *     'tblprefix' => string // 表前缀
 *   ),
 *   'administrator' => array (
 *     'dsn' => 'mysql:host=localhost;dbname=trotri',
 *     'username' => 'root',
 *     'password' => '123456',
 *     'charset' => 'utf8',
 *     'retry' => 3,
 *     'tblprefix' => 'tr_'
 *   ),
 * );
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DbProxy.php 1 2013-04-05 01:38:06Z huan.song $
 * @package tfc.saf
 * @since 1.0
 */
class DbProxy extends Statement
{
    /**
     * @var integer 连接数据库失败后，默认尝试重连的最大次数
     */
    const MAX_RETRY_TIMES = 3;

    /**
     * @var string 寄存数据库配置名
     */
    protected $_clusterName = null;

    /**
     * @var array 寄存数据库配置信息
     */
    protected $_config = null;

    /**
     * @var instance of tfc\db\Transaction
     */
    protected $_transaction = null;

    /**
     * 构造方法：初始化数据库配置名
     * @param string $clusterName
     */
    public function __construct($clusterName)
    {
        $this->_clusterName = $clusterName;
        parent::__construct($this->getDriver(false));
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\db\Statement::query()
     */
    public function query($sql = null, $params = null)
    {
        $start = gettimeofday();
        try {
            $result = parent::query($sql, $params);
            $message = 'PDO Exec Sql Successfully!';
            $code = 0;
        }
        catch (ErrorException $e) {
            $result = false;
            $message = 'PDO Exec Sql Failed! ' . $e->getMessage();
            $code = $e->getCode();
        }
        $end = gettimeofday();

        $event = array(
            'msg' => $message,
            'sql' => $sql,
            'params' => serialize($params),
            'cost' => ($end['sec'] - $start['sec']) * 1000000 + ($end['usec'] - $start['usec']),
            'config' => serialize($this->getConfig())
        );

        if ($result) {
            Log::notice($event, __METHOD__);
        }
        else {
            Log::warning($event, $code, __METHOD__);
        }

        return $result;
    }

    /**
     * 执行数据库事务操作
     * @param array $commands
     * @return boolean
     */
    public function doTransaction(array $commands = array())
    {
        if ($commands === array()) {
            return false;
        }

        $transaction = $this->getTransaction();
        $transaction->setAutoCommit(0);
        $transaction->beginTransaction();

        foreach ($commands as $row) {
            $sql = $params = null;
            if (is_array($row)) {
                $sql = isset($row['sql']) ? $row['sql'] : null;
                $params = isset($row['params']) ? $row['params'] : null;
            }

            if ($sql) {
                if ($this->query($sql, $params)) {
                    continue;
                }
            }

            $transaction->rollBack();
            $transaction->setAutoCommit(1);
            return false;
        }

        $transaction->commit();
        $transaction->setAutoCommit(1);
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\db\Statement::getDriver()
     */
    public function getDriver($autoOpen = true)
    {
        if ($this->_driver === null) {
            $this->_driver = new Driver($this->getDsn(), $this->getUsername(), $this->getPassword(), $this->getCharset());
            if (($caseFolding = $this->getCaseFolding()) !== null) {
                $this->_driver->setCaseFolding($caseFolding);
            }
        }

        if (!$autoOpen) {
            return $this->_driver;
        }

        if ($this->_driver->getIsConnected()) {
            return $this->_driver;
        }

        $maxRetry = $this->getRetry();
        for ($retry = 0; $retry < $maxRetry; $retry++) {
            try {
                $this->_driver->open();
                $message = 'PDO Connect DB Successfully!';
                $code = 0;
            }
            catch (ErrorException $e) {
                $message = 'PDO Connect DB Failed! ' . $e->getMessage();
                $code = $e->getCode();
            }

            $event = array(
                'msg' => $message,
                'retry' => $retry,
                'config' => serialize($this->getConfig())
            );

            if ($code === 0) {
                Log::notice($event, __METHOD__);
                return $this->_driver;
            }

            Log::warning($event, $code, __METHOD__);
        }

        return null;
    }

    /**
     * 获取最后一次插入记录的ID
     * @return integer
     */
    public function getLastInsertId()
    {
        try {
            return $this->getDriver(false)->getLastInsertId();
        }
        catch (ErrorException $e) {
            Log::warning($e->getMessage(), $e->getCode(), __METHOD__);
        }

        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\db\Statement::getRowCount()
     */
    public function getRowCount()
    {
        try {
            return parent::getRowCount();
        }
        catch (ErrorException $e) {
            Log::warning($e->getMessage(), $e->getCode(), __METHOD__);
        }

        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\db\Statement::getColumnCount()
     */
    public function getColumnCount()
    {
        try {
            return parent::getColumnCount();
        }
        catch (ErrorException $e) {
            Log::warning($e->getMessage(), $e->getCode(), __METHOD__);
        }

        return 0;
    }

    /**
     * 获取PDO事务处理类
     * @return \tfc\db\Transaction
     */
    public function getTransaction()
    {
        if ($this->_transaction === null) {
            $this->_transaction = new Transaction($this->getDriver());
        }

        return $this->_transaction;
    }

    /**
     * 获取数据库类型
     * @return string
     */
    public function getDbType()
    {
        return $this->getDriver(false)->getDbType();
    }

    /**
     * 获取Data Source Name
     * @return string
     */
    public function getDsn()
    {
        return $this->getConfig('dsn');
    }

    /**
     * 获取数据库用户名
     * @return string
     */
    public function getUsername()
    {
        return $this->getConfig('username');
    }

    /**
     * 获取数据库密码
     * @return string
     */
    public function getPassword()
    {
        return $this->getConfig('password');
    }

    /**
     * 获取数据库字符编码
     * @return string
     */
    public function getCharset()
    {
        return $this->getConfig('charset');
    }

    /**
     * 获取通过列名获取数据时，列名大小写规则
     * @return integer
     */
    public function getCaseFolding()
    {
        return $this->getConfig('case_folding');
    }

    /**
     * 获取连接数据库失败尝试重连次数
     * @return integer
     */
    public function getRetry()
    {
        return $this->getConfig('retry');
    }

    /**
     * 获取表前缀
     * @return string
     */
    public function getTblprefix()
    {
        return (string) $this->getConfig('tblprefix');
    }

    /**
     * 获取数据库配置信息，如果配置信息中没有指定连接数据库失败尝试重连次数，则由MAX_RETRY_TIMES常量指定次数
     * @param mixed $key
     * @return mixed
     * @throws ErrorException 如果配置信息中没有指定DSN、用户名、密码或编码格式，抛出异常
     */
    public function getConfig($key = null)
    {
        if ($this->_config === null) {
            $config = Cfg::getDb($this->getClusterName());
            if (!isset($config['dsn']) || !isset($config['username']) || !isset($config['password']) || !isset($config['charset'])) {
                throw new ErrorException(sprintf(
                    'DbProxy no entry is registered for key: dsn|username|password|charset in db config "%s"', serialize($config)
                ));
            }
            $config['retry'] = isset($config['retry']) ? (int) $config['retry'] : self::MAX_RETRY_TIMES;
            $this->_config = $config;
        }

        if ($key === null) {
            return $this->_config;
        }

        return isset($this->_config[$key]) ? $this->_config[$key] : null;
    }

    /**
     * 获取数据库配置名
     * @return string
     */
    public function getClusterName()
    {
        return $this->_clusterName;
    }
}
