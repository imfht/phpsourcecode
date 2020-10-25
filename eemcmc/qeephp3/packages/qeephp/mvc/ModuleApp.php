<?php namespace qeephp\mvc;

use qeephp\Config;

/**
 * ModuleApp 支持多模块的App类
 *
 */
class ModuleApp extends App
{
	/**
     * URL 中用于指示请求模块的参数名称
     *
     * @var string
     */
    public $module_accessor;

    /**
     * 路由设置
     *
     * @var array
     */
    public static $_routes = array();

//    {{{ 子类继承 ModuleApp 可以直接复制下面的代码
//
//    /**
//     * 构造函数
//     */
//    function __construct()
//    {
//        $module_name = $this->parse_udi();
//        # 模块的物理路径
//        $app_dir     = ($module_name == 'app')
//            ? __DIR__
//            : MYAPP_SRC_PATH.'/modules/'.$module_name;
//
//        # 如果有指定模块名称
//        if($module_name != 'app')
//        {
//            # 以模块的物理路径来判断模块是否存在
//            if( ! is_dir($app_dir))
//            {
//                return $this->_process_result($this->_on_module_not_found($module_name));
//            }
//        }
//
//        # 模块对应的名字空间
//        $namespace   = ($module_name == 'app')
//            ? __NAMESPACE__
//            : '{你应用的命名空间}\\modules\\'.$module_name;
//
//        parent::__construct($namespace, $app_dir, true);
//    }
//
//    /**
//     * 加载路由规则
//     *
//     */
//    protected static function require_routes()
//    {
//        require dirname(__DIR__) . '/config/routes.php';
//    }
//
//     样例代码结束 }}}

    /**
     * 设置路由规则
     *
     * @return array $routes
     */
    static function set_routes($routes)
    {
        self::$_routes = $routes;
    }

    /**
     * 解析 UDI, 匹配对应的 路由规则
     *
     * @return string $module_name
     */
    function parse_udi()
    {
    	$url_mode = Config::get('app.url_mode');
        $this->module_accessor = Config::get(array('app.module_accessor'));
        
        if($url_mode == 'rewrite')
        {
            static::require_routes();
	    	$module_name = $this->_parse_rewrite_udi(get_request_pathinfo());
		}
		else
		{
			$module_name = $this->_parse_normal_udi();
		}

	    return $module_name;
    }

    /**
     * 解析 Normal 模式的 UDI
     *
     * @return string $module_name
     */
    protected function _parse_normal_udi()
    {
    	$module_name = request($this->module_accessor);
    	$module_name = empty($module_name) ? 'app' : self::_format_module_name($module_name);

    	return $module_name;
    }

    /**
     * 解析 Rewrite 模式的 UDI
     *
     * @return string $module_name
     */
    protected function _parse_rewrite_udi($pathinfo)
    {
        if ( empty($pathinfo) ) $pathinfo = '/';

        foreach(self::$_routes AS $key => $route)
        {
            # 将路由的配置参数添加到 正则规则中.
            foreach($route['config'] AS $ck => $cval)
            {
                $route['pattern'] = str_replace('{'.$ck.'}', '('.$cval.')', $route['pattern']);
            }

            if (preg_match('#^'.$route['pattern'].'/?$#i', $pathinfo, $match_result))
            {
                # 处理默认项
                if ( !empty($route['default']) )
                {
                    foreach ($route['default'] as $ck => $cval)
                    {
                        $_GET[$ck] = $_REQUEST[$ck] = $cval;
                    }
                }

                # offset 为0 是 原字符串
                $offset = 1;
                foreach($route['config'] as $ck => $cval)
                {
                    if(isset($match_result[$offset]))
                    {
                        $_GET[$ck] = $_REQUEST[$ck] = $match_result[$offset++];
                    }
                }
                break;
            }
        }

        return empty($_GET[$this->module_accessor]) ? 'app' : $_GET[$this->module_accessor];
    }

    /**
     * 生成 URL
     *
     * @param string $action_name
     * @param array $params
     * @param string $anchor 锚点
     *
     * @return string
     */
    function url($route_key, $params = null, $anchor=null)
    {
    	$url_mode = Config::get('app.url_mode');
        if($url_mode == 'rewrite')
        {
	        $url = $this->_rewrite_url($route_key, $params);
        }
        else
        {
	        $url = $this->_normal_url($route_key, $params);
        }

        if (!empty($anchor)) $url .= '#' . trim($anchor);
        return $url;
    }

    /**
     * 生成 URL Normal 模式的网址
     *
     * @param string $route_key
     * @param array $params
     *
     * @return string
     */
    protected function _normal_url($route_key, $params)
    {
        $url = get_request_baseuri().'?';
        $module_name = 'app';
        
        # url 中 moudle 部分的处理
        $module_name = empty($params[$this->module_accessor]) ?
                'app' : $params[$this->module_accessor];
        $url .= "{$this->module_accessor}={$module_name}&";

        # url 中 action 部分的处理
        $action = empty($params[$this->action_accessor]) ?
                Config::get(array('app.default_action', 'defaults.default_action')) : $params[$this->action_accessor];
        $url .= "{$this->action_accessor}={$action}";

        unset($params[$this->module_accessor]);
        unset($params[$this->action_accessor]);

        if (!empty($params))
        {
            $url .= '&' . http_build_query($params);
        }
        return $url;
    }

    /**
     * 生成 URL Rewrite 模式的网址
     *
     * @param string $action_name
     * @param array $params
     *
     * @return string
     */
    protected function _rewrite_url($route_key, $params)
    {
        $route = empty(self::$_routes[$route_key]) ? self::$_routes['default'] : self::$_routes[$route_key];
        
        # 找出要参数化的变量(将参数数组同default合并起来)
        $kv = array();
        foreach($route['config'] as $ck => $cval)
        {
            $kv[$ck] = $cval;
        }
        foreach($route['default'] as $ck => $cval)
        {
            $kv[$ck] = $cval;
        }
        
        # 填充必须的参数
        if ( empty($kv[$this->module_accessor]) )
        {
            $kv[$this->module_accessor] = 'app';
        }
        if ( empty($kv[$this->action_accessor]) )
        {
            $kv[$this->action_accessor] = Config::get(array('app.default_action', 'defaults.default_action'));
        }

        foreach($kv as $ck => $cval)
        {
            if ( ! empty($params[$ck]) )
            {
                $cval = $params[$ck];
                unset($params[$ck]);
            }
            $route['pattern'] = str_replace('{'.$ck.'}', $cval, $route['pattern']);
        }

        $url = rtrim(get_request_baseuri(), '\/') . '/' . ltrim($route['pattern'], '\/');

        if (!empty($params))
        {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * 指定的模块没有找到
     *
     * @param string $module_name
     */
    protected function _on_module_not_found($module_name)
    {
        throw ModuleActionError::module_not_found_error($module_name);
    }

    /**
     * 指定的控制器或动作没有找到
     *
     * PS: 如有需要可以重写找不到action的提示
     *
     * @param string $action_name
     */
    protected function _on_action_not_found($action_name)
    {
        throw ModuleActionError::action_not_found_error($action_name);
    }

    /**
     * 格式化模块名称
     *
     * @param string $action_name
     *
     * @return string
     */
    protected static function _format_module_name($module_name)
    {
    	$module_name = trim(strtolower($module_name), ". \t\r\n\0\x0B");
        return preg_replace('/[^a-z]/', '', $module_name);
    }
}
