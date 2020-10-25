<?php

namespace Redis\Db1;

/**
 * redis操作类
 */
class AbstractModel extends \Redis\AbstractModel {

    /**
     * 连接的库
     * 
     * @var int 
     */
    protected $_db = 1;

}
