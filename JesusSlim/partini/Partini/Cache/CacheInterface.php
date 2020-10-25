<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/29
 * Time: 下午3:29
 */

namespace Partini\Cache;


interface CacheInterface
{
    const TYPE_ARRAY = 1;
    const TYPE_STRING = 2;
    const TYPE_OBJECT = 3;

    public function get($key,$type);

    public function set($key,$v,$e);

    public function delete($key);
}