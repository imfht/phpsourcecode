<?php
/**
 * 供模板使用的数组对象，避免某些错误
 *
 * @author chengxuan <i@chengxuan.li>
 */
namespace Entity;
class Tarr extends \ArrayObject {
    
    /**
     * 获取一个数组中的值，不存在返回NULL
     * {@inheritDoc}
     * @see ArrayObject::offsetGet()
     */
    public function offsetGet($index) {
        if($this->offsetExists($index)) {
            return parent::offsetGet($index);
        } else {
            return null;
        }
    }
}