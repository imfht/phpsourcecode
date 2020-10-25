<?php
/**
 * 视图处理类
 *
 * @package     View
 * @author      lajox <lajox@19www.com>
 */
namespace Took;
class View
{

    public $vars = array(); //模板变量
    public $const = array(); //系统常量如__WEB__$const['WEB'];
    public $tplFile = null; //模版文件
    public $compileFile = null; //编译文件
    public $layoutFile = null; //布局文件
    public $content; //模板编译内容
    public $driver = null; //驱动引擎
    public static $factory = null; //静态工厂实例
    public $_g = 'took'; //全局变量定义 模板使用类似{$took.get.xx}方式调用
    public $config;

    /**
     * 构造函数
     */
    public function __construct(){
        $this->config['template_suffix'] = C('TPL_EXT');
        $this->config['layout_replace'] = C('LAYOUT_REPLACE') ? C('LAYOUT_REPLACE') : '{__CONTENT__}';
    }

    protected function __init(){}

    /**
     * 工厂实例
     */
    public static function factory()
    {
        //只实例化一个对象
        if (is_null(self::$factory)) {
            self::$factory = new View();
            self::$factory->driver = \Took\View\ViewFactory::factory();
        }
        return self::$factory;
    }

    /**
     * 验证缓存是否过期
     *
     * @param string $cachePath 缓存目录
     * @return bool
     */
    public function isCache($cachePath = null)
    {
        $cachePath = $cachePath ? $cachePath : TEMP_CACHE_PATH;
        $cacheName = md5($_SERVER['REQUEST_URI']);
        return S($cacheName, false, null, array("dir" => $cachePath, "driver" => "file")) ? true : false;
    }

    /**
     * 模板显示
     *
     * @param string $tplFile 模板文件
     * @param int $cacheTime 缓存时间
     * @param string $cachePath 缓存目录
     * @param string $contentType 文件类型
     * @param bool $show 是否显示
     * @return bool|string
     */
    public function display($tplFile = null, $cacheTime = -1, $cachePath = null, $contentType = "text/html", $show = true)
    {
        //获得模板文件
        $this->tplFile = $this->getTemplateFile($tplFile);
        //缓存文件名
        $cacheName = md5($_SERVER['REQUEST_URI'].md5($this->tplFile));
        //缓存时间
        $cacheTime = is_numeric($cacheTime) ? $cacheTime  : intval( C("TPL_CACHE_TIME") );
        //缓存路径
        $cachePath = $cachePath ? $cachePath : TEMP_CACHE_PATH;
        //内容
        $content = null;
        if ($cacheTime >= 0) {
            $content = S($cacheName, false, $cacheTime, array("dir" => $cachePath, 'zip' => false, "driver" => "file"));
        }
        //缓存失效
        if (!$content) {
            $this->vars[$this->_g] = self::getGlobal();
            if (!$this->tplFile) {
                return;
            }
            //编译模板文件内容
            $content = $this->compiler();
            //写入缓存
            if ($cacheTime >= 0) {
                S($cacheName, $content, $cacheTime, array("dir" => $cachePath, 'zip' => false, "driver" => "file"));
            }
        }
        if ($show) {
            $charset = C('TPL_CHARSET') ? C('TPL_CHARSET') : "UTF-8";
            if (!headers_sent()) {
                header("Content-type:" . $contentType . ';charset=' . $charset);
            }
            echo $content;
        } else {
            return $content;
        }
    }

    /**
     * 获得全局变量数据
     */
    public static function getGlobal() {
        static $global;
        if (!$global) {
            $global = array(
                'get'=>&$_GET,
                'post'=>&$_POST,
                'request'=>&$_REQUEST,
                'cookie'=>&$_COOKIE,
                'session'=>&$_SESSION,
                'server'=>&$_SERVER,
                'env'=>&$_ENV,
                'config'=>C(),
                'language'=>L(),
                'const'=>get_defined_constants(),
            );
        }
        return $global;
    }

    /**
     * 获得模版文件
     *
     * @param $file 模板文件
     * @return bool|string
     */
    private function getTemplateFile($file)
    {
        $file = get_view_file($file);
        //模板文件检测
        if (is_file($file)) {
            return $file;
        } else {
            DEBUG && halt("模板不存在:$file");
            return false;
        }
    }

    /**
     * 向模板中传入变量
     *
     * @param string|array $var 变量名
     * @param mixed $value 变量值
     * @return bool
     */
    public function assign($var, $value)
    {
        if (is_array($var)) {
            foreach ($var as $k => $v) {
                if (is_string($k)) {
                    $this->vars[$k] = $v;
                }
            }
        } else {
            $this->vars[$var] = $value;
        }
    }

    // 模板变量获取
    public function get($name) {
        if(isset($this->vars[$name]))
            return $this->vars[$name];
        else
            return false;
    }

    // 模板变量设置
    public function set($name, $value) {
        $this->assign($name, $value);
    }

    /**
     * 获得视图内容
     *
     * @param null $tplFile 模板文件
     * @param null $cacheTime 缓存时间
     * @param null $cachePath 缓存路径
     * @param string $contentType 文档类型
     * @return bool|string
     */
    public function fetch($tplFile = null, $cacheTime = null, $cachePath = null, $contentType = "text/html")
    {
        return $this->display($tplFile, $cacheTime, $cachePath, $contentType, false);
    }

    //执行编译
    public function compiler() {
        //编译文件
        $this->compileFile = $this->getCompileFile($this->tplFile);
        //记录模板编译文件
        if (DEBUG) {
            \Took\Debug::$tpl[] = array(basename($this->tplFile), $this->compileFile);
        }
        //模板内容
        $this->content = file_get_contents($this->tplFile);
        $layoutFile = '';
        /*
         * 1.判断是否启用布局
         * 2.读取模板中的布局标签, 解析Layout标签
         */
        $find = preg_match('/'.C("TPL_TAG_LEFT").'layout\s(.+?)\s*?\/'.C("TPL_TAG_RIGHT").'/is', $this->content, $match);
        if(C('LAYOUT_ON') || $find) {
            if(false !== strpos($this->content,'{__NOLAYOUT__}')) { // 可以单独定义不使用布局
                $this->content = str_replace('<!--{__NOLAYOUT__}-->','',$this->content);
                $this->content = str_replace('{__NOLAYOUT__}','',$this->content);
            }else{ // 替换布局的主体内容
                $layerName = C('LAYOUT_NAME');
                $layoutFile = MODULE_VIEW_PATH. $layerName . $this->config['template_suffix'];
                $layerReplace = $this->config['layout_replace'];
                if($find) {
                    // 属性解析
                    $attr = $this->parseTagAttr($match[1]);
                    // 读取布局模板
                    $layerName = isset($attr['name']) ? $attr['name'] : C('LAYOUT_NAME');
                    $layoutFile = MODULE_VIEW_PATH. $layerName . $this->config['template_suffix'];
                    $layerReplace = isset($attr['replace']) ? $attr['replace'] : $layerReplace;
                    $this->content = str_replace($match[0],'',$this->content);
                }
                // 检查布局文件
                if(!is_file($layoutFile)) {
                    halt('模板文件不存在'.':'.$layoutFile);
                }
                // 替换布局的主体内容
                $this->content = str_replace($layerReplace, $this->content,file_get_contents($layoutFile));
            }
        }
        //编译是否失效（不存在或过期）
        if ( $this->compileInvalid($this->tplFile) || ( $layoutFile && $this->compileInvalid($layoutFile)) ) {
            if(is_array(self::$factory->driver)) {
                $i = 0;
                foreach(self::$factory->driver as $driver) {
                    $content = $i>0 ? $this->getContent() : $this->content;
                    $driver->run($this, $content, $this->compileFile);
                    $i++;
                }
            }
            else {
                self::$factory->driver->run($this, $this->content, $this->compileFile);
            }
        }
        $content = $this->getContent();
        return $content;
    }

    public function getContent($compileFile = null, &$vars = null) {
        //加载全局变量
        static $loaded = false;
        if(!$loaded) {
            $vars = !empty($vars) ? $vars : $this->vars;
            if (!empty($vars)) {
                extract($vars);
            }
            $loaded = true;
        }
        $compileFile = $compileFile ? $compileFile : $this->compileFile;
        if(!is_file($compileFile)) {
            $this->savefile($this->content, $compileFile);
        }
        ob_start();
        include($compileFile);
        $content = ob_get_clean();
        return $content;
    }

    /**
     * 获取模板的编译文件路径
     * @return string
     */
    public function getCompileFile($tplFile) {
        return TEMP_COMPILE_PATH . MODULE . '/' . CONTROLLER . '/' . ACTION . '_' . substr(md5($tplFile), 0, 8) . '.php';
    }

    /**
     * 编译是否失效
     * @return bool true 失效
     */
    private function compileInvalid($tplFile = null, $compileFile = null)
    {
        $tplFile = $tplFile ? $tplFile : $this->tplFile;
        $compileFile = $compileFile ? $compileFile : $this->compileFile;
        return DEBUG || !file_exists($compileFile) || (filemtime($tplFile) > filemtime($compileFile));
    }

    /**
     * 保存编译文件内容
     * @return string 文件内容
     */
    public function savefile($content = null, $file = null) {
        $content = is_null($content) ? $this->content : $content;
        $file = is_null($file) ? $this->compileFile : $file;
        //编译内容
        $head = "<?php if(!defined('TOOK_PATH'))exit;C('SHOW_NOTICE',FALSE);?>";
        if(strpos($content, $head)!==false) {
            $content = $head."\n".$content;
        }
        //创建编译目录与安全文件
        \Tool\Dir::create(dirname($file));
        \Tool\Dir::safeFile(dirname($file));
        //储存编译文件
        file_put_contents($file, $content);
        return $content;
    }

    /**
     * 解析标签属性
     *
     * @param string $attrStr 标签字符串
     * @return array
     */
    protected function parseTagAttr($attrStr)
    {
        $pregAttr = '#' . '([a-z_]+)=(["\'])(.*)\2#iU'; //属性正则
        //$info说明 0 完整内容, 1 引号, 3 属性值
        $status = preg_match_all($pregAttr, $attrStr, $info, PREG_SET_ORDER);
        if ($status) {
            $attr = array();
            foreach ($info as $k) {
                //解析属性值
                $attr[$k[1]] = $this->parseAttrValue($k[3]);
            }
            return $attr;
        } else {
            return array();
        }
    }

    /**
     * 解析属性值
     *
     * @param $attrValue 属性值
     * @return mixed
     */
    protected function parseAttrValue($attrValue)
    {
        //替换常量值
        $const = get_defined_constants(true);
        foreach ($const['user'] as $name => $value) {
            //替换以__开始的常量
            if (substr($name, 0, 2) == '__') {
                $attrValue = str_ireplace($name, $value, $attrValue);
            }
        }
        //解析变量为PHP可识别状态
        $preg   = '@\$([\w\.]+)@i';
        $status = preg_match_all($preg, $attrValue, $info, PREG_SET_ORDER);
        if ($status) {
            foreach ($info as $i => $d) {
                $var  = '';
                $data = explode('.', $d[1]);
                foreach ($data as $n => $m) {
                    if ($n == 0) {
                        $var .= $m;
                    } else {
                        $var .= '[\'' . $m . '\']';
                    }
                }
                $attrValue = str_replace($d[1], $var, $attrValue);
            }
        }
        return $attrValue;
    }

}