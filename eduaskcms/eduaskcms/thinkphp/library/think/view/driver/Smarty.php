<?php
namespace think\view\driver;

use Smarty as BasicSmarty;
use think\App;
use think\exception\TemplateNotFoundException;
use think\Loader;
use Log;
use Env; 

class Smarty {

    private $template = null;
    private $config = [];
    protected $storage;

    public function __construct($config = []) {
        $default = [
            'debug'        => config('app_debug'),
            'tpl_begin'    => '{',
            'tpl_end'      => '}',
            'view_path'    => '',
            'cache_path'   => Env::get('runtime_path') . 'temp' . DS, // 模板缓存目录
            'cache_prefix' => '',
            'cache_suffix' => '.php',
            'tpl_dir'      => [Env::get('app_path') . 'public' . DS . 'view'],
        ];
        $this->config = array_merge($default, $config);
        if (empty($this->config['view_path'])) {
            $this->config['view_path'] = app()->getModulePath() . 'view' . DS;
        }
        $this->config['tpl_dir'][] = $this->config['view_path'];
        if (empty($this->config['cache_path'])) {
            $this->config['cache_path'] = Env::get('runtime_path') . 'temp' . DS;
        }
                
        include_once Env::get('smarty_path') . 'Smarty.class.php';
        $this->template = new BasicSmarty();
        $this->template->setLeftDelimiter($this->config['tpl_begin']);
        $this->template->setRightDelimiter($this->config['tpl_end']);
        $this->template->setCaching(!$this->config['debug']);
        $this->template->setForceCompile(!$this->config['debug']); #是否强制编译
        $this->template->setTemplateDir($this->config['tpl_dir']); #设置模板目录
        $this->template->merge_compiled_includes = true; #合并编译导入
        $this->template->setCacheDir($this->config['cache_path']); #设置缓存目录
        $this->template->setCompileDir($this->config['cache_path']); #设置编译目录
    }

    /**
     * 渲染模板文件
     * @access public
     * @param string    $template 模板文件
     * @param array     $data 模板变量
     * @param array     $config 模板参数
     * @return void
     */
    public function fetch($template, $data = [], $config = []) {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }
        // 模板不存在 抛出异常
        if (!is_file($template)) {
            throw new TemplateNotFoundException('template not exists:' . $template, $template);
        }
        // 记录视图信息
        //App::$debug && Log::record('[ VIEW ] ' . $template . ' [ ' . var_export(array_keys($data), true) . ' ]', 'info');
        // 定义模板常量
        $request = request();
        $default = [
            '__ROOT__' => pathinfo($request->baseFile(true), PATHINFO_DIRNAME),
            '__SELF__' => $request->url(TRUE),
            '__APP__'  => $request->baseFile(TRUE)
        ];
        $default['__LIB__'] = $default['__ROOT__'] . '/static/plugs';
        $default['__STATIC__'] = $default['__ROOT__'] . '/static';
        $default['__UPLOAD__'] = $default['__ROOT__'] . '/static/upload';
        // 赋值模板变量
        !empty($template) && $this->template->assign($data);
        echo str_replace(array_keys($default), array_values($default), $this->template->fetch($template));
    }

    /**
     * 渲染模板内容
     * @access public
     * @param string    $template 模板内容
     * @param array     $data 模板变量
     * @param array     $config 模板参数
     * @return void
     */
    public function display($template, $data = [], $config = []) {
        $this->fetch($template, $data, $config);
    }

    /**
     * 自动定位模板文件
     * @access private
     * @param string $template 模板文件规则
     * @return string
     */
    private function parseTemplate($template) {
        // 获取视图根目录
        if (strpos($template, '@')) {
            // 跨模块调用
            list($module, $template) = explode('@', $template);
            $path = Env::get('app_path') . $module . DS . 'view' . DS;
        } else {
            // 当前视图目录
            $path = $this->config['view_path'];          
        }
        // 分析模板文件规则
        $request = request();
        $controller = Loader::parseName($request->controller());
        if ($controller && 0 !== strpos($template, '/')) {
            $depr = config('template.view_depr');
            $template = str_replace(['/', ':'], $depr, $template);
            if ('' == $template) {
                // 如果模板文件名为空 按照默认规则定位
                $template = str_replace('.', DS, $controller) . $depr . $request->action();
            } elseif (false === strpos($template, $depr)) {
                $template = str_replace('.', DS, $controller) . $depr . $template;
            }
        }
        return $path . ltrim($template, '/') . '.' . ltrim(config('template.view_suffix'), '.');
    }

    public function __call($method, $params) {
        return call_user_func_array([$this->template, $method], $params);
    }

}
