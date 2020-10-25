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

use nb\Config;
use nb\Debug;

/**
 * Driver
 *
 * @package nb\debug
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
abstract class Driver {

    protected $record;

	/**
	 * @return Debug
	 */
	abstract public function start();

    /**
     *
     * @param $type
     * @param $key
     * @param $val
     */
    public function record($type,$parama,$paramb=null){
        switch($type) {
            case 1:
                if(is_object($paramb)) {
                    $paramb = '<pre>'.print_r($paramb,true).'<pre/>';
                }
                $this->record['log'][] = ['k'=>$parama,'v'=>$paramb];
                break;
            case 2:
                $parama = Debug::e2Array($parama);
                $this->record['e'][] = $parama;
                break;
            case 3:
                $this->record['sql'][] = ['sql'=>$parama,'param'=>$paramb];
                break;
        }
    }

	/**
	 * 统计信息，存入Bug
	 */
    abstract public function end();


    /**
     * 获取已经存在的Debug日志
     * @return object
     */
    protected function get(){
        $bpath = Config::getx('path_temp');
        if(is_file($bpath.'debug.log')){
            return json_decode(file_get_contents($bpath.'debug.log'),true);
        }
        return null;
    }

    /**
     * 记录Debug日志
     * @param $log
     * @throws \Exception
     */
    protected function put($log) {
        $bpath = Config::getx('path_temp');
        if (!is_dir($bpath) && !mkdir($bpath,0777,true)) {
            throw new \Exception('Create bug dir is fail!');
        }
        file_put_contents($bpath.'debug.log', json_encode($log));
    }


    /**
     * 对终端友好的变量输出
     * @access public
     * @param  mixed         $var 变量
     * @param  boolean       $detailed 是否详细输出 默认为true 如果为false 则使用print_r输出
     * @param  string        $label 标签 默认为空
     * @param  integer       $flags htmlspecialchars flags
     * @return void|string
     */
    public static function ex($var, $detailed = false) {
        if (is_object($var)) { //$var instanceof \nb\Collection
            $detailed = false;
        }
        ob_start();
        $detailed?var_dump($var):print_r($var);
        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        $output = '<pre>'  . $output . '</pre>';
        echo $output;
    }

    /**
     * 中断程序运行
     * @param $status
     */
    public function quit($status) {
        die($status);
    }

}
