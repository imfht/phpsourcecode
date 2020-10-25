<?php

namespace application\core\components;

use application\core\utils\Ibos;

/**
 * 重写 CDbConnection，支持读写分离
 *
 * 使用方法：
 * ```
 * 'components' => array(
 *     'db' => array(
 *          'class' => 'application\core\components\DbConnection',
 *          'connectionString' => 'mysql:host=localhost;dbname=ibos',
 *          'username' => 'root',
 *          'password' => 'root',
 *          'charset' => 'utf8',
 *          'enableSlave' => true,
 *          'shareSlaveConfig' => array(
 *              'username' => 'root',
 *              'password' => 'root',
 *          ),
 *          'maxConnSlaveErrCnt' => 3,
 *          'slaves' => array(
 *              array(
 *                  'connectionString' => 'mysql:host=localhost2;dbname=ibos2',
 *              ),
 *              array(
 *                  'connectionString' => 'mysql:host=localhost3;dbname=ibos3',
 *              ),
 *          ),
 *     ),
 * )
 * ```
 *
 * 如果是在 IBOS 中使用，需要改写对应 Engine（比如：Local、Saas） 下的 initConfig 方法。
 *
 * 参考了：http://www.yiiframework.com/extension/dbreadwritesplitting/
 *
 * DbConnectionMan（上面那个 yii 扩展）有一个问题：
 * 只能在使用到 AR 的情况下才支持读写分离，而手动创建 command 的方式是不能的。
 * （如：`Yii::app->db->createCommand()->select('id')->from('{{user}}->query()')`）
 *
 * @package application\core\components
 */
class DbConnection extends \CDbConnection
{
    /**
     * @var array
     */
    public $slaves = array();

    /**
     * @var array
     */
    public $shareSlaveConfig = array();

    /**
     * @var bool
     */
    public $enableSlave = true;

    /**
     * 连接 slave 最大的错误次数，超过这个，则直接使用 master
     *
     * @var int
     */
    public $maxConnSlaveErrCnt = 3;

    /**
     * @var int
     */
    protected $currentMaxConnSlaveErrCnt = 0;

    /**
     * @var \CDbConnection
     */
    protected $activeSlave;

    /**
     * @param string $query
     * @return DbCommand
     */
    public function createCommand($query = null)
    {
        $command = new DbCommand($this, $query);
        return $command;
    }

    /**
     * @return \PDO
     */
    public function getMasterPdo()
    {
        $this->setActive(true);
        return $this->getPdoInstance();
    }

    /**
     * @return \PDO
     */
    public function getSlavePdo()
    {
        $slave = $this->getSlave();
        if (empty($slave)) {
            return $this->getMasterPdo();
        } else {
            $slave->setActive(true);
            return $slave->getPdoInstance();
        }
    }

    /**
     * @return bool|\CDbConnection
     */
    protected function getSlave()
    {
        if ($this->enableSlave === false) {
            return false;
        }

        if (!empty($this->activeSlave)) {
            return $this->activeSlave;
        }

        if ($this->currentMaxConnSlaveErrCnt >= $this->maxConnSlaveErrCnt) {
            return false;
        }

        if (!empty($this->slaves)) {
            $engineMainConfig = Ibos::engine()->getMainConfig();

            // 打乱 $this->slaves 后，取出第一个 slaveConfig
            shuffle($this->slaves);
            $slaves = array_values($this->slaves);
            $slaveConfig = $slaves[0];

            if (!isset($slaveConfig['class'])) {
                $slaveConfig['class'] = 'CDbConnection';
            }
            if (!isset($slaveConfig['tablePrefix'])) {
                $slaveConfig['tablePrefix'] = $engineMainConfig['db']['tableprefix'];
            }
            if (!isset($slaveConfig['charset'])) {
                $slaveConfig['charset'] = $engineMainConfig['db']['charset'];
            }
            $slaveConfig['autoConnect'] = false;
            $slaveConfig = array_merge($this->shareSlaveConfig, $slaveConfig);

            try {
                /** @var \CDbConnection $slave */
                $slave = \Yii::createComponent($slaveConfig);
                $slave->setActive(true);
            } catch (\CException $e) {
                \Yii::log(array(
                    'msg' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ), \CLogger::LEVEL_WARNING, 'db');
                $this->currentMaxConnSlaveErrCnt += 1;
                return false;
            }

            $this->activeSlave = $slave;
            return $slave;
        } else {
            return false;
        }
    }

    /**
     * 重写 close 方法，同时关闭 master 和 slave
     */
    protected function close()
    {
        parent::close();

        if (empty($this->activeSlave)) {
            $this->activeSlave->setActive(false);
        }
    }


}
