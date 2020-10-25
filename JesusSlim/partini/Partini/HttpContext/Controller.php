<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/8/3
 * Time: 下午10:22
 */

namespace Partini\HttpContext;


class Controller
{

    protected $context;

    public function __construct(Context $ctx)
    {
        $this->context = $ctx;
    }

    public function ctx(){
        return $this->context;
    }

    public function json($data){
        $this->context->output()->json($data);
        return $this->context->output();
    }
}