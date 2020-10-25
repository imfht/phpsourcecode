<?php
/**
 * 数据库异常处理类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\db;

use herosphp\exception\HeroException;

class DBException extends HeroException {

    protected $query;       /* 查询语句 */

    public function __contruct( $message ) {
        parent::__contruct($message);
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param $code
     */
    public function setCode($code) {
        $this->code = $code;
    }

}
