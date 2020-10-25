<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/26
 * Time: 上午11:30
 */

namespace Partini;


use Exception;
class ExceptionHandler
{

    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
        set_exception_handler([$this,'handleExceptions']);
    }

    public function handleExceptions(Exception $e){
        var_dump($e->getMessage());
    }
}