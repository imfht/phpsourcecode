<?php
/**
 * mysql pdo数据库操作类
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date 2013-1-19
 */
namespace framework\database;
use framework\core\Abnormal;

require_once 'Db.php';

class PdoMysql extends DB
{
    /** @var \pdo */
    private $pdo = NULL;        //pdo对象
    /** @var  resource */
    private $lastqueryid;

    /**
     * 连接数据库
     */
    public function connect()
    {
        if (is_null($this->pdo)) {
            try {
                $this->pdo = new \PDO("mysql:host={$this->dbhost};dbname={$this->dbname}", $this->dbuser, $this->dbpsw);
                $this->execute('set names ' . $this->charset);
            } catch (\PDOException $e) {
                $message = $e->getMessage();
                $encod = mb_detect_encoding($message, array('ASCII', 'GB2312', 'GBK', 'utf-8', 'iso-8859-1', 'windows-1251'));
                if ($encod != 'UTF-8') {
                    $message = iconv($encod, 'UTF-8', $message);
                }
                if (APP_DEBUG) {
                    throw new Abnormal($message, $e->getCode());
                }
            }
        }
    }

    /**
     * 执行基本的 mysql查询
     * 并返回结果集
     *
     * @param string $sql
     * @return array
     * @throws Abnormal
     */
    public function query($sql)
    {
        if (!$sql) return false;

        $this->lastqueryid = $this->pdo->query($sql);
        if ($this->lastqueryid === FALSE && APP_DEBUG) {
            $err = $this->pdo->errorInfo();
            throw new Abnormal($this->errorMsg($err[2], $sql), 500);
        }
        return $this->lastqueryid->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 获取最后一次添加数据的主键号
     */
    public function insertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * 执行mysql 语句 并返回受影响的行数
     * @param string $sql
     * @return int|mixed
     * @throws Abnormal
     */
    public function execute($sql)
    {

        $result = $this->pdo->exec($sql);
        if ($result === false) {
            $err = $this->pdo->errorInfo();
            throw new Abnormal($this->errorMsg($err[2], $sql), 500);
        }
        return $result;
    }

    /**
     * 释放资源
     * @return boolean
     */
    public function freeResult()
    {
        $this->lastqueryid = NULL;
        return true;
    }

    /**
     * 关闭数据库连接
     * @return bool
     */
    public function close()
    {
        $this->pdo = null;
        return true;
    }

    public function __destruct()
    {
        $this->freeResult();
        $this->close();
    }
}