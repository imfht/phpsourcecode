<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/5
 * Time: 13:41
 */

namespace naples\lib\base;


interface IGetSet
{
    function get($key);
    function set($key,$value,$expiry=99999999);
    function has($key);
}