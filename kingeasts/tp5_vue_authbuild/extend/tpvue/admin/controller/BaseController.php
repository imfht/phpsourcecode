<?php
// 公有控制器
// +----------------------------------------------------------------------
// | PHP version 5.3+                
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\controller;


use think\App;
use think\Container;
use think\facade\Config;
use think\facade\Env;
use think\Request;
use think\View;
use tpvue\admin\ConfigApi;
use traits\controller\Jump;


class BaseController
{
    use Jump;

    /**
     * @var App $app
     */
    protected $app;

    /**
     * @var View $view
     */
    protected $view;

    protected $url;

    /**
     * @var Request $request
     */
    protected $request;
    protected $module;
    protected $controller;
    protected $action;
    protected $config;
    public static $logIndex = 1;

	public function __construct() {

        $this->app     = Container::get('app');
        $this->request = $this->app['request'];
        $this->view    = \tpvue\admin\App::$view;
		// header("Content-Type: Text/Html;Charset=UTF-8");

		//读取数据库中的配置
        $configObj = new ConfigApi();
        $config = $configObj->lists();

        Config::set($config);
	}

    protected function assign($name, $value = [])
    {
        $this->view->assign($name, $value);
    }

    protected function fetch($name, $vars = [], $config = [])
    {
        return $this->view->fetch($name, $vars, $config);
    }



    /**
     * 快捷调用验证器
     * @param array $data 验证数据
     * @param mixed $rule 验证规则
     * @param string $scene 验证场景
     * @param array $msg 错误消息
     * @return array|bool
     */
    protected function validate($data, $rule, $scene = '', $msg = [])
    {
        if (is_array($rule)) {
            $valid = $this->app->validate();
            $valid->rule($rule);
        } else {
            $valid = $this->app->validate($rule);
        }
        if ($scene) {
            $valid->scene($scene);
        }
        $valid->message($msg);

        if ($valid->check($data)) {
            return true;
        } else {
            return $valid->getError();
        }
    }

	/**
	 * request信息
	 * @return [type] [description]
	 */
	protected function requestInfo() {
		$this->param =  $this->request->param();

		defined('MODULE_NAME') or define('MODULE_NAME', $this->request->module());
		defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $this->request->controller());
		defined('ACTION_NAME') or define('ACTION_NAME', $this->request->action());
		defined('IS_POST') or define('IS_POST', $this->request->isPost());
		defined('IS_AJAX') or define('IS_AJAX', $this->request->isAjax());
		defined('IS_GET') or define('IS_GET', $this->request->isGet());
//		//获取当前地址
//        $this->assign('full_url', $this->request->url());//完整url
//		$this->assign('request', $this->request);
//		$this->assign('param', $this->param);

		$this->url = strtolower($this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
        $this->ip=$this->request->ip();
        //获取当前地址
        return $this->full_url=$this->request->url(true);//完整url
	}

	/**
	 * 获取单个参数的数组形式
	 */
	protected function getArrayParam($param) {
		if (isset($this->param['id'])) {
			return array_unique((array) $this->param[$param]);
		} else {
			return array();
		}
	}

	/**
	 * [log 日志打印]
	 */
    public function log($text) {
    	//是否打印日志
        if(!Config::get('app_debug')){
            return ;
        }

        //分割线
        if (self::$logIndex == 1) {
            $log = '*************************打印数据分割线*************************' . PHP_EOL;
            $log .= date("Y-m-d H:i:s", time()) . PHP_EOL;
        }

        //日志路径目录
        $logPath = Env::get('RUNTIME_PATH') . 'debug/';

        //创建日志目录
        if (!is_dir($logPath)) { self::mkdirs($logPath);}

        //日志名字文件生成
        $logFile = $logPath . 'log'.date("Y-m-d", time()).'.log';
        if (is_array($text)) {
            $log = self::$logIndex . ', array==>';
            $text = var_export($text, true);
        } else {
            $log .= self::$logIndex . ', str==>';
        }

        //写入文件
        $log .= $text . PHP_EOL;
        file_put_contents($logFile, $log, FILE_APPEND);
        self::$logIndex++;
    }

    /**
     * 创建文件夹
     * @param type $dir
     * @return boolean
     */
    public static function mkdirs($dir) {
        if (!is_dir($dir)) {
            if (!self::mkdirs(dirname($dir))) {
                return false;
            }
            if (!mkdir($dir, 0777)) {
                return false;
            }
        }
        return true;
    }
}
