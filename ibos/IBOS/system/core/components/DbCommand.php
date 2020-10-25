<?php

namespace application\core\components;

use CLogger;
use PDOException;
use Yii;

class DbCommand extends CDbCommand
{
    public function prepare()
    {
        if ($this->_statement == null) {
            try {
                $rawSql = $this->getText();

                $forRead = true;
                /** @var DbConnection $connection */
                $connection = $this->getConnection();
                if ($connection->getCurrentTransaction()) {
                    $forRead = false;
                }
                $forRead = $forRead && $this->isReadOperation($rawSql);

                if ($forRead) {
                    $pdo = $connection->getSlavePdo();
                } else {
                    $pdo = $connection->getMasterPdo();
                }

                $this->_statement = $pdo->prepare($rawSql);
                $this->_paramLog = array();
            } catch (\Exception $e) {
                Yii::log('Error in preparing SQL: ' . $this->getText(), CLogger::LEVEL_ERROR, 'system.db.CDbCommand');
                $errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
                throw new \CDbException(Yii::t('yii', 'CDbCommand failed to prepare the SQL statement: {error}',
                    array('{error}' => $e->getMessage())), (int)$e->getCode(), $errorInfo);
            }
        }
    }

    /**
     * 检查 SQL 语句是否仅是只读（主要包括如下几种操作：SELECT、SHOW、DESCRIBE、PRAGMA）
     *
     * @param string $sql
     * @return bool
     */
    protected function isReadOperation($sql)
    {
        $sql = substr(ltrim($sql), 0, 10);
        // ^O^,magic smile
        $sql = str_ireplace(array('SELECT', 'SHOW', 'DESCRIBE', 'PRAGMA'), '^O^', $sql);
        return strpos($sql, '^O^') === 0;
    }
}