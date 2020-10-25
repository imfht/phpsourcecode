<?php
namespace workerbase\classs\datalevels;

use workerbase\classs\AttachEvent;

/**
 * 关系数据库事物
 * @author fukaiyao
 */
class RdbTransaction {
    /**
     * @var RdbTransaction
     */
    private static $_instance = null;

    /**
     * 事物层级
     * @var int
     */
    private $_level = 0;

    /**
     * 当前事物
     * @var null
     */
    private $_currentTanscation = null;

    private function __construct()
    {
        $this->_level = 0;
        $this->_currentTanscation = null;
        AttachEvent::attachEventHandler('onEndRequest', [$this, 'catchException']);
    }

    /**
     * 获取事物对象
     * @return RdbTransaction
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new RdbTransaction();
        }
        return self::$_instance;
    }

    /**
     * 开始事物
     */
    public function begin()
    {
        $this->_level++;
        if ($this->_level == 1) {
            $this->_currentTanscation = Db::getInstance()->pdo;
            $this->_currentTanscation->beginTransaction();
        }
    }

    /**
     * 提交事物
     */
    public function commit()
    {
        if ($this->_level == 1 && $this->_currentTanscation != null) {
            $this->_currentTanscation->commit();
        }
        $this->_level--;
    }

    /**
     * 事物回滚
     */
    public function rollback()
    {
        if ($this->_level == 1 && $this->_currentTanscation != null) {
            $this->_currentTanscation->rollback();
        }
        $this->_level--;
    }

    /**
     * 处理异常
     * @throws DaoException
     */
    public function catchException()
    {
        if ($this->_level > 0) {
            $this->_currentTanscation != null && $this->_currentTanscation->rollback();
            throw new DaoException("loss commit/rollback transaction.");
        }
    }
}