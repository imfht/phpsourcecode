<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/8/26
 * Time: 上午11:03
 */

namespace LuciferP\Orm\db;


interface Db
{
    function connect($host,$user,$passwd,$dbname);
    function query($sql,$params=[]);
    function close();

}