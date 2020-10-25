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
use tfc\ap\ErrorException;

/**
 * Transaction class file
 * PDO事务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Transaction.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.db
 * @since 1.0
 */
class Transaction extends Application
{
    /**
     * @var instance of tfc\db\Driver
     */
    protected $_driver = null;

    /**
     * 构造方法：初始化PDO方式连接数据库对象
     * @param \tfc\db\Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * 获取自动提交开启状态
     * @return integer 1：开启；0：关闭
     */
    public function getAutoCommit()
    {
        return $this->getDriver()->getAttribute(\PDO::ATTR_AUTOCOMMIT);
    }

    /**
     * 设置自动提交开启状态
     * @param integer $attribute 1：开启；0：关闭
     * @return \tfc\db\Transaction
     */
    public function setAutoCommit($attribute = 0)
    {
        $this->getDriver()->setAttribute(\PDO::ATTR_AUTOCOMMIT, (int) $attribute);
        return $this;
    }

    /**
     * 开启PDO事务
     * @return boolean
     * @throws ErrorException 如果开启PDO事务失败，抛出异常
     */
    public function beginTransaction()
    {
        try {
            return $this->getDriver()->open()->getPdo()->beginTransaction();
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Transaction PDO begin transaction failed, %s', $e->getMessage()
            ), (int) $e->getCode());
        }
    }

    /**
     * 回滚PDO事务
     * @return boolean
     * @throws ErrorException 如果回滚PDO事务失败，抛出异常
     */
    public function rollBack()
    {
        try {
            return $this->getDriver()->open()->getPdo()->rollBack();
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Transaction PDO roll back failed, %s', $e->getMessage()
            ), (int) $e->getCode());
        }
    }

    /**
     * 提交PDO事务
     * @return boolean
     * @throws ErrorException 如果提交PDO事务失败，抛出异常
     */
    public function commit()
    {
        try {
            return $this->getDriver()->open()->getPdo()->commit();
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Transaction PDO commit failed, %s', $e->getMessage()
            ), (int) $e->getCode());
        }
    }

    /**
     * 获取PDO方式连接数据库对象
     * @return \tfc\db\Driver
     */
    public function getDriver()
    {
        return $this->_driver;
    }
}
