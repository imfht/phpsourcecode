<?php

namespace App\Component;

/**
 * 基础模型类.
 */
class BaseModel extends \Swoole\Model
{
    /**
     * 获取单个数据.
     *
     * @param $params
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getone($params)
    {
        if (empty($params)) {
            throw new \Exception('no params.');
        }
        $selectdb = new \Swoole\SelectDB($this->db);
        $selectdb->from($this->table);
        $selectdb->primary = $this->primary;
        $selectdb->select($this->select);

        if (!isset($params['order'])) {
            $params['order'] = "`{$this->table}`.{$this->primary} desc";
        }
        $selectdb->put($params);

        return $selectdb->getone();
    }

    /**
     * 开启事务
     */
    public function start()
    {
        $this->db->start();
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        $this->db->commit();
    }

    /**
     * 事务回滚.
     */
    public function rollback()
    {
        $this->db->rollback();
    }

    /**
     * 查询某个字段最大值
     * @param $field
     * @param array $params
     * @return int
     */
    public function getMax($field, $params = [])
    {
        $params['select'] = 'max(`'.$field.'`) as num';
        $maxData = $this->getone($params);
        return isset($maxData['num']) ? (int)$maxData['num'] : 0;
    }

    /**
     * 查询某个字段最小值
     * @param $field
     * @param array $params
     * @return int
     */
    public function getMin($field, $params = [])
    {
        $params['select'] = 'min(`'.$field.'`) as num';
        $maxData = $this->getone($params);
        return isset($maxData['num']) ? (int)$maxData['num'] : 0;
    }
}
