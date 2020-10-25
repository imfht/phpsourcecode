<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/7/26
 * Time: 上午11:23
 */
namespace LuciferP\TinyMvc\base;

interface Decorators{
    function beforeAction($action);
    function afterAction($action);
}