<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/9
 * Time: 上午11:14
 */

namespace App\Libs\Elasticsearch;

/**
 * es公共
 * Class Sms
 * @package App\Libs
 */
class EsBase
{
    /**
     * 获取总条数
     * @param $result
     * 获取返回总数
     */
    public function getTotal(array $result)
    {
        $total = isset($result['hits']['total']['value']) ? $result['hits']['total']['value'] : 0;
        return $total;
    }

    /**
     * 获取结果
     * @param $result
     * 获取返回总数
     */
    public function getResult(array $result)
    {
        $return = array();
        if ($this->getTotal($result) > 0 && isset($result['hits']['hits'])) {
            foreach ($result['hits']['hits'] as $hit) {
                $return[] = $hit['_source'];
            }
        }
        return $return;
    }
}