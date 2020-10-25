<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Admin\Entity;

use Zend\Hydrator\Reflection;

/**
 * 基础实体
 * @package Admin\Entity
 */
class BaseEntity
{
    /**
     * 将对象数值转换为数组
     * @return array
     */
    public function valuesArray()
    {
        $reflect = new Reflection();
        return $reflect->extract($this);
    }

    /**
     * 设置属性赋值
     * @param array $data
     * @return $this
     */
    public function valuesSet(array $data)
    {
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                $setFun = 'set'.ucfirst($key);
                if(is_callable([$this, $setFun])) {
                    $this->$setFun($value);
                }
            }
        }
        return $this;
    }
}