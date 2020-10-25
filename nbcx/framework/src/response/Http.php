<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\response;


use nb\Pool;
use nb\Server;

/**
 * Swoole
 *
 * @package nb\response
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 *
 * @method  \swoole\http\Response end()
 */
class Http extends Driver {

    /**
     * @var \swoole\http\Response
     */
    protected $res;


    public function __construct() {
        $this->res  = Pool::value('\swoole\http\Response');//\nb\driver\Swoole::$o->response;

    }

    public function header($key, $value=null,$http_response_code=null) {
        ob_clean();
        if($value === null) {
            $f = explode(':',$key);
            list($key,$value) = explode(':',$key);
        }
        $this->res->header($key,$value);
        if($http_response_code) {
            $this->res->status($http_response_code);
        }
    }

    public function __call($name, $arguments) {
        if(method_exists($this->res,$name)) {
            return call_user_func_array([$this->res,$name],$arguments);
        }
        // TODO: Implement __call() method.
        return null;
    }

}