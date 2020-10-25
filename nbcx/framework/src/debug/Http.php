<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\debug;

use nb\Request;
use nb\Router;
use nb\Session;
use nb\Pool;

/**
 * Swoole
 *
 * @package nb\debug
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
class Http extends Php {

	private $synchronous = true;

	//是否已经中断程序运行了
	private $died = false;

    /**
     * 是否为同步进程
     * @return mixed
     */
    public function synchronous(){
        return $this->synchronous;
    }

    /**
     * 是否为异步进程，即Task进程
     * @return mixed
     */
    public function asynchronous(){
        return !$this->synchronous;
    }

	/**
	 * 统计信息，存入Bug
	 */
	public function end(){

        //如果请求的控制器是debug就算了！
        if (Router::driver()->controller == 'debug') {
            return false;
        }

        if(!Pool::object('nb\\event\\Framework')->debug()) {
            return false;
        }

		$record = $this->record;
		$log = $this->get();

		if($this->synchronous) {
            $request = Request::driver();
            if($this->ip && !in_array($request->ip,$this->ip)) {
                return;
            }
            $record['start'] = $request->requestTime;
            $record['url'] = 'http://'.$request->host.$request->uri;
            $record['get'] = $request->get;
            $record['post'] = $request->post;
            $record['file'] = $request->files;
            $record['cookie'] = $request->cookie;
            $record['method'] = $request->method;
            $record['ip'] = $request->ip;
            if(in_array('all',$this->show) || in_array('server',$this->show)){
                $record['server'] = $request->server;
            }
            $record['session'] = Session::get();
        }
        else {
            $record['url'] = 'asynchronous';
            $record['start'] = time();
            $record['method'] = 'asynchronous';
            $record['ip'] = '0.0.0.0';
        }

        $record['spend'] = round(microtime(true) - $request->requestTimeFloat,4);
        $record['mem'] = number_format((array_sum(explode(' ',memory_get_usage())) - $record['mem'])/1024).'kb';

		if(in_array('all',$this->show)||in_array('trace',$this->show)){
            $record['runfile'] = get_included_files();
		}
		if(empty($log)){
			$log[] = $record;
		}
		else{
			$n = array_unshift($log, $record);//向数组插入元素
			if($n >= $this->n) unset($log[$this->n]);
		}
		$this->put($log);
        $this->page and $this->page($record);
	}

    /**
     * 中断程序运行
     * @param $status
     */
    public function quit($status) {
        if($this->died) {
            return;
        }
        $this->died = true;
        if($status) echo $status;
        throw new \Exception('die');
    }
}
