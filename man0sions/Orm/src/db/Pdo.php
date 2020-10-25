<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/8/26
 * Time: 上午10:44
 */

namespace LuciferP\Orm\db;

use LuciferP\Orm\base\AppException;

class Pdo implements Db
{
    private $db;
    private $errors = [];

    /**
     * @param $host
     * @param $user
     * @param $passwd
     * @param $dbname
     * @throws AppException
     */
    public function connect($host, $user, $passwd, $dbname)
    {
        try {
            $this->db = new \PDO("mysql:dbname=$dbname;host=$host", $user, $passwd);
            $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);

        } catch (\Exception $e) {
            throw new AppException("db error:{$e->getMessage()}");
        }
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param $sql
     * @param array $params
     * @return array
     */
    public function query($sql, $params = [])
    {
        $this->db->query("set names utf8");
        $ret = false;
        $prepare = $this->db->prepare($sql);
        foreach ($params as $key => $param) {
            $prepare->bindValue($key, $param, \PDO::PARAM_STR);
        }

        $ret = $prepare->execute();
        if (!$ret) {
            $this->errors[] = $prepare->errorinfo()[2];
        }


        return ['prepare' => $prepare, 'ret' => $ret];

    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function close()
    {
        unset($this->db);
    }

    /**
     * @param $query
     * @param $params
     * @return mixed
     */
    public function interpolateQuery($query, $params)
    {
        $keys = array();
        $values = $params;

        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:' . $key . '/';
            } else {
                $keys[] = '/[?]/';
            }

            if (is_string($value)) {
                $values[$key] = "'" . $value . "'";
            }

            if (is_array($value)) {
                $values[$key] = "'" . implode("','", $value) . "'";
            }

            if (is_null($value)) {
                $values[$key] = 'NULL';
            }
        }


        return preg_replace($keys, $values, $query, 1, $count);
    }

    public function __destruct()
    {
        $this->close();
    }


}