<?php
/**
 * Created by Wenlong Li
 * User: wenlong11
 * Date: 2018/9/21
 * Time: 下午12:22
 */

namespace Component\Orm\Connection;


trait HashCode
{
    protected $hash = '';
    public function HashCode() : string
    {
        if($this->hash == '') {
            $this->hash = spl_object_hash($this);
        }
        return $this->hash;
    }
}