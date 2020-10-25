<?php
/**
 * Created by zhouzhongyuan.
 * User: zhou
 * Date: 2015/11/27
 * Time: 11:46
 */

namespace shiwolang\db;


interface ObjectContainerInterface
{
    public function getObject();

    public function setObject($value);
}