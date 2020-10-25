<?php
/**
 * Date: 2018\3\19 0019 10:57
 */

if (!function_exists('import')) {
    /**
     * 导入所需的类库 同java的Import 本函数有缓存功能
     * @param string $class 类库命名空间字符串
     * @param string $baseUrl 起始路径
     * @param string $ext 导入的文件扩展名
     * @return boolean
     */
    function import($class, $baseUrl = 'function', $ext = '.php')
    {
        return Yaf_Loader::import($baseUrl . '/' . $class . $ext);
    }
}
if (!function_exists('vendor')) {
    /**
     * 快速导入第三方框架类库 所有第三方框架的类库文件统一放到 系统的Vendor目录下面
     * @param string $class 类库
     * @param string $ext 类库后缀
     * @return boolean
     */
    function vendor($class, $ext = '.php')
    {
        return import($class, 'vendor', $ext);
    }
}
if (!function_exists('session')) {
    /**
     * Session管理
     * @param string|array $name session名称，如果为数组表示进行session设置
     * @param mixed $value session值
     * @param string $prefix 前缀
     * @return mixed
     */
    function session($name, $value = '', $prefix = null)
    {
        /**
         * Session管理
         * @param string|array $name session名称，如果为数组表示进行session设置
         * @param mixed $value session值
         * @param string $prefix 前缀
         * @return mixed
         */
        function session($name, $value = '', $prefix = null)
        {
            if (is_array($name)) {
                // 初始化
                Session::init($name);
            } elseif (is_null($name)) {
                // 清除
                Session::clear('' === $value ? null : $value);
            } elseif ('' === $value) {
                // 判断或获取
                return 0 === strpos($name, '?') ? Session::has(substr($name, 1), $prefix) : Session::get($name, $prefix);
            } elseif (is_null($value)) {
                // 删除
                return Session::delete($name, $prefix);
            } else {
                // 设置
                return Session::set($name, $value, $prefix);
            }
        }
    }
}

if (!function_exists('cache')) {
    /**
     * 缓存管理
     * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
     * @param mixed $value 缓存值
     * @param mixed $options 缓存参数
     * @param string $tag 缓存标签
     * @return mixed
     */
    function cache($name, $value = '', $options = null, $tag = null)
    {
        if (is_array($options)) {
            // 缓存操作的同时初始化
            $cache = \think\Cache::connect($options);
        } elseif (is_array($name)) {
            // 缓存初始化
            return \think\Cache::connect($name);
        } else {
            $cache = Yaf_Registry::get('cache');
        }

        if (is_null($name)) {
            return $cache->clear($value);
        } elseif ('' === $value) {
            // 获取缓存
            return 0 === strpos($name, '?') ? $cache->has(substr($name, 1)) : $cache->get($name);
        } elseif (is_null($value)) {
            // 删除缓存
            return $cache->rm($name);
        } elseif (0 === strpos($name, '?') && '' !== $value) {
            $expire = is_numeric($options) ? $options : null;
            return $cache->remember(substr($name, 1), $value, $expire);
        } else {
            // 缓存数据
            if (is_array($options)) {
                $expire = isset($options['expire']) ? $options['expire'] : null; //修复查询缓存无法设置过期时间
            } else {
                $expire = is_numeric($options) ? $options : null; //默认快捷缓存设置过期时间
            }
            if (is_null($tag)) {
                return $cache->set($name, $value, $expire);
            } else {
                return $cache->tag($tag)->set($name, $value, $expire);
            }
        }
    }
}

if (!function_exists('cookie')) {
    /**
     * Cookie管理
     * @param string|array $name cookie名称，如果为数组表示进行cookie设置
     * @param mixed $value cookie值
     * @param mixed $option 参数
     * @return mixed
     */
    function cookie($name, $value = '', $option = null)
    {
        if (is_array($name)) {
            // 初始化
            Cookie::init($name);
        } elseif (is_null($name)) {
            // 清除
            Cookie::clear($value);
        } elseif ('' === $value) {
            // 获取
            return 0 === strpos($name, '?') ? Cookie::has(substr($name, 1), $option) : Cookie::get($name, $option);
        } elseif (is_null($value)) {
            // 删除
            return Cookie::delete($name);
        } else {
            // 设置
            return Cookie::set($name, $value, $option);
        }
    }
}
if (!function_exists('input')) {
    /**
     * 获取输入参数 支持过滤和默认值
     * 使用方法:
     * <code>
     * I('id',0); 获取id参数 自动判断get或者post
     * I('post.name','','htmlspecialchars'); 获取$_POST['name']
     * I('get.'); 获取$_GET
     * </code>
     * @param string $name 变量的名称 支持指定类型
     * @param mixed $default 不存在的时候默认值
     * @param mixed $filter 参数过滤方法
     * @param mixed $datas 要获取的额外数据源
     * @return mixed
     */
    function input($name, $default = '', $filter = null, $datas = null)
    {
        static $_PUT = null;
        if (strpos($name, '/')) { // 指定修饰符
            list($name, $type) = explode('/', $name, 2);
        }
        if (strpos($name, '.')) { // 指定参数来源
            list($method, $name) = explode('.', $name, 2);
        } else { // 默认为自动判断
            $method = 'param';
        }
        switch (strtolower($method)) {
            case 'get'     :
                $input =& $_GET;
                break;
            case 'post'    :
                $input =& $_POST;
                break;
            case 'put':
            case "delete":
                if (is_null($_PUT)) {
                    parse_str(file_get_contents('php://input'), $_PUT);
                }
                $input = $_PUT;
                break;
            case 'param'   :
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'POST':
                        $input = $_POST;
                        break;
                    case 'PUT':
                    case "DELETE":
                        if (is_null($_PUT)) {
                            parse_str(file_get_contents('php://input'), $_PUT);
                        }
                        $input = $_PUT;
                        break;
                    default:
                        $input = $_GET;
                }
                break;
            case 'path'    :
                $input = array();
                if (!empty($_SERVER['PATH_INFO'])) {
                    $depr = '/';
                    $input = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
                }
                break;
            case 'request' :
                $input =& $_REQUEST;
                break;
            case 'session' :
                $input =& $_SESSION;
                break;
            case 'cookie'  :
                $input =& $_COOKIE;
                break;
            case 'server'  :
                $input =& $_SERVER;
                break;
            case 'globals' :
                $input =& $GLOBALS;
                break;
            case 'data'    :
                $input =& $datas;
                break;
            default:
                return null;
        }
        if ('' == $name) { // 获取全部变量
            $data = $input;
            $filters = isset($filter) ? $filter : 'safe';
            if ($filters) {
                if (is_string($filters)) {
                    $filters = explode(',', $filters);
                }
                foreach ($filters as $filter) {
                    $data = array_map_recursive($filter, $data); // 参数过滤
                }
            }
        } elseif (isset($input[$name])) { // 取值操作
            $data = $input[$name];
            $filters = isset($filter) ? $filter : 'safe';
            if ($filters) {
                if (is_string($filters)) {
                    if (0 === strpos($filters, '/')) {
                        if (1 !== preg_match($filters, (string)$data)) {
                            // 支持正则验证
                            return isset($default) ? $default : null;
                        }
                    } else {
                        $filters = explode(',', $filters);
                    }
                } elseif (is_int($filters)) {
                    $filters = array($filters);
                }

                if (is_array($filters)) {
                    foreach ($filters as $filter) {
                        if (function_exists($filter)) {
                            $data = is_array($data) ? array_map_recursive($filter, $data) : $filter($data); // 参数过滤
                        } else {
                            $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                            if (false === $data) {
                                return isset($default) ? $default : null;
                            }
                        }
                    }
                }
            }
            if (!empty($type)) {
                switch (strtolower($type)) {
                    case 'a':    // 数组
                        $data = (array)$data;
                        break;
                    case 'd':    // 数字
                        $data = (int)$data;
                        break;
                    case 'f':    // 浮点
                        $data = (float)$data;
                        break;
                    case 'b':    // 布尔
                        $data = (boolean)$data;
                        break;
                    case 's':   // 字符串
                    default:
                        $data = (string)$data;
                }
            }
        } else { // 变量默认值
            $data = isset($default) ? $default : null;
        }
        is_array($data) && array_walk_recursive($data, 'think_filter');
        return $data;
    }

    function array_map_recursive($filter, $data)
    {
        $result = array();
        foreach ($data as $key => $val) {
            $result[$key] = is_array($val)
                ? array_map_recursive($filter, $val)
                : call_user_func($filter, $val);
        }
        return $result;
    }

    function think_filter(&$value)
    {
        // TODO 其他安全过滤

        // 过滤查询特殊字符
        if (preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i', $value)) {
            $value .= ' ';
        }
    }
}
if (!function_exists('safe')) {
    /**
     * safe 函数用于过滤html标签，输出允许的安全的html
     * @param string $text 待过滤的字符串
     * @param string $type 保留的标签格式
     * @return string 处理后内容
     */
    function safe($text, $type = 'html')
    {
        // 无标签格式
        $text_tags = '';
        //只保留链接
        $link_tags = '';
        //只保留图片
        $image_tags = '<img>';
        //只存在字体样式
        $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
        //标题摘要基本格式
        $base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
        //兼容Form格式
        $form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
        //内容等允许HTML的格式
        $html_tags = $base_tags . '<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
        //专题等全HTML格式
        $all_tags = $form_tags . $html_tags . '<!DOCTYPE><meta><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
        //过滤标签
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = strip_tags($text, ${$type . '_tags'});
        //过滤超链接
        // 过滤攻击代码
        if ($type != 'all') {
            // 过滤危险的属性，如：过滤on事件lang js
            while (preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat)) {
                $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
            }
            while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
                $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
            }
        }
        $text = preg_replace('/\\\(.*)\"/', "", $text);
        return $text;
    }
}
if (!function_exists('jump')) {
    function dump($var, $echo = true, $label = null, $flags = ENT_SUBSTITUTE)
    {
        $label = (null === $label) ? '' : rtrim($label) . ':';
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        if (Yaf_Dispatcher::getInstance()->getRequest()->isCli()) {
            $output = PHP_EOL . $label . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, $flags);
            }

            $output = '<pre>' . $label . $output . '</pre>';
        }
        $output = '<pre>' . $label . $output . '</pre>';
        if ($echo) {
            echo($output);
            return;
        } else {
            return $output;
        }
    }
}

if (!function_exists('authcode')) {
    /**
     * Discuz 加密/解密
     *
     * @param [type]  $string    [description]
     * @param string $operation [description]
     * @param string $key [description]
     * @param integer $expiry [description]
     * @return [type]             [description]
     */
    function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $ckey_length = 4;
        $key = md5($key != '' ? $key : Config::get('auth_code'));
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }
}