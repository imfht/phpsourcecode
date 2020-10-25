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
//use nb\Debug;
use nb\Request;
use nb\Router;
use nb\Session;
use nb\Pool;

/**
 * Php
 *
 * @package nb\debug
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
class Php extends Driver {

	protected $show = [];
    protected $key;
    protected $ip;
    protected $n = 10;
    protected $page = false;

    protected $record=[];

    public function __construct() {
        $config = Config::$o->debug;
        if(!$config || $config === true) {
            return;
        }
        if(is_string($config)) {
            $config = explode(",",$config);
        }

        foreach($config as $v){
            $ex = explode(':',$v);
            switch ($ex[0]){
                case 'show':
                    $this->show = explode('-',$ex[1]);
                    break;
                case 'key':
                    $this->key = $ex[1];
                    break;
                case 'ip':
                    $this->ip = explode('-',$ex[1]);
                    break;
                case 'n':
                    $this->n = $ex[1];
                    break;
				case 'page':
					$this->page = $ex[1];
					break;
                default:
                    break;
            }
        }
    }

    public function index(){
		$function = Router::ins()->function;//g('f')
		if($function == 'close') {
			unset($_SESSION['_key']);
            tips('Ok. close success!','你已经清除了Debug页面的访问权限！');
			return;
		}
		$sess = Session::driver();
		if($this->key && !isset($sess->get['_key'])){
            $req = Request::ins();
			if(isset($req->get['key']) && $req->get['key'] == $this->key){
                $sess->get['_key'] = $req->get['key'];
			}
			else{
                tips('No Permission!','你没有权限访问这个页面！');
				return;
			}
		}
		//修改了CXBUG值后可以及时生效
		else if($this->key && $_SESSION['_key'] != $this->key) {
            tips('No Permission!','你没有权限访问这个页面！');
			return;
		}
		//路由到具体请求
		if(intval($function) ||$function == 'index'){
			$result = $this->get();
            $result?include __DIR__.'/html/debug.tpl.php':tips('No Bug File!','暂时没有生成bug文件，你可能还没有运行过系统！');
		}
		else if($function == 'phpinfo'){
			phpinfo();
		}
		else if($function == 'p'){
			include __DIR__.'/html/p.tpl.php';
		}
        else if($function == 'server'){
            e($_SERVER);
        }
		else{
            tips('Not Found!','无效的请求！');
		}
	}

	/**
	 * @return Debug
	 */
	public function start(){
        $this->record['mem'] =  array_sum(explode(' ',memory_get_usage()));
        $this->record['sql'] = [];
        $this->record['exception'] = [];
        $this->record['log'] = [];
	}


	/**
	 * 统计信息，存入Bug
	 */
	public function end(){
        //如果请求的控制器是debug就算了！
        if (\nb\Router::ins()->controller == 'debug') {
            return false;
        }

        if(!Pool::object('nb\\event\\Framework')->debug()) {
            return false;
        }

		$record = $this->record;
		$log = $this->get();

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
			if($n >= $this->n) {
			    unset($log[$this->n]);
            }
		}

		$this->put($log);
		/*
		$bpath = Config::getx('path_temp');
		if (!is_dir($bpath) && !mkdir($bpath,0777,true)) {
			throw new \Exception('Create bug dir is fail!');
		}
		file_put_contents($bpath.'debug.log', json_encode($log));
		*/
        $this->page and $this->_page($record);
	}

	protected function page($data){
        $request = Request::driver();
		if($request->isAjax || Config::$o->sapi=='cli') {
			return;
		}
		$trace['Base'] = [
			'url'=>$data['url'],
			'method'=>$data['method'],
			'ip'=>$data['ip'],
			'spend'=>$data['endTime']
		];
		if($data['log']) {
			foreach($data['log'] as $v) {
				$trace['Log'][$v['k']] = $v['v'];
			}
		}
		if($data['sql']) {
			foreach($data['sql'] as $v) {
				$trace['Sql'][] = $v['sql'];
				$trace['Sql'][] = $v['param'];
			}
		}
		$trace['Get'] = $data['get'];
		$trace['Post'] = $data['post'];
		$trace['Cookie'] = $data['cookie'];
		isset($data['Session']) and $trace['session'] = $data['session'];
		$trace['Server'] = isset($data['server'])?$data['server']:$_SERVER;
		$trace['Trace'] = isset($data['runfile'])?$data['runfile']:get_included_files();
		if(isset($data['e'])) {
		    foreach ($data['e'] as $v) {
                $trace['exception'][] = "[{$v['type']}] {$v['message']} ({$v['file']}: {$v['line']})";
            }
        }
		include __DIR__.DS.'html'.DS.'trace.tpl.php';
	}


}
