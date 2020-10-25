<?php
/**
 * iPHP - i PHP Framework
 * Copyright (c) iiiPHP.com. All rights reserved.
 *
 * @author iPHPDev <master@iiiphp.com>
 * @website http://www.iiiphp.com
 * @license http://www.iiiphp.com/license
 * @version 2.1.0
 */
class iView {
    public static $handle   = NULL;
    public static $app      = null;
    public static $gateway  = null;
    public static $config   = array();
    public static $template = array();
    const TPL_FUNC_NAME   = 'FuncClass';
    const TPL_FUNC_Method = 'FuncMethod';

    public static function init($config = array()) {
        self::$config = $config;
        self::$handle = self::Template();
        self::$handle->assign('_GET', $_GET);
        self::$handle->assign('_POST', $_POST);

        iPHP_TPL_DEBUG && self::$handle->clear_compiled_tpl();
    }
    public static function Template() {
        $tpl = new iTemplateLite();
        $tpl->debugging    = iPHP_TPL_DEBUGGING;
        $tpl->template_dir = iPHP_TPL_DIR;
        $tpl->compile_dir  = iPHP_TPL_CACHE;
        $tpl->reserved_template_varname = iPHP_TPL_VAR;
        $tpl->reserved_func_name        = self::TPL_FUNC_NAME;
        $tpl->error_reporting_header    = "<?php defined('iPHP') OR exit('What are you doing?');error_reporting(iPHP_TPL_DEBUG?E_ALL & ~E_NOTICE:0);?>\n";
        $tpl->left_delimiter  = '<!--{';
        $tpl->right_delimiter = '}-->';
        $tpl->register_modifier("date", "get_date");
        $tpl->register_modifier("cut", "csubstr");
        $tpl->register_modifier("htmlcut", "htmlcut");
        $tpl->register_modifier("cnlen", "cstrlen");
        $tpl->register_modifier("html2txt", "html2text");
        $tpl->register_modifier("key2num", "key2num");
        $tpl->register_modifier("unicode", "get_unicode");
        $tpl->register_modifier("random", "random");
        $tpl->register_modifier("fields", "select_fields");
        $tpl->register_modifier("htmldecode",'htmlspecialchars_decode');
        $tpl->register_modifier("pinyin",array("iPinyin", "get"));
        $tpl->register_modifier("thumb", array("files", "thumb"));
        $tpl->register_block("cache", array(__CLASS__, "block_cache"));
        $tpl->template_callback = array(
            "compile"  => array(__CLASS__,"callback_compile"),
            "resource" => array(__CLASS__,"callback_resource"),
            "func"     => array(__CLASS__,"callback_func"),
            "plugin"   => array(__CLASS__,"callback_plugin"),
            "block"    => array(__CLASS__,"callback_block"),
            "register" => array(__CLASS__,"callback_register"),
            "output"   => array(__CLASS__,"callback_output"),
        );
        return $tpl;
    }
    public static function set_vars($key,$value) {
        self::$handle->$key = $value;
    }
    public static function set_template_dir($dir) {
        self::$handle->template_dir = $dir;
    }
    /**
     * [callback_register 模板方法注册]
     * @param  [type] $func [方法]
     * @param  [type] $type [类型]
     * @return [type]       [description]
     */
    public static function callback_register($func,$type) {
        list($app,$method) = explode(':', $func);
        //app:method => testTmpl::block_method
        //payment:cut => paymentTmpl::block_cut
        $typeMap  = array('compiler','block','function','output');
        $class    = in_array($type, $typeMap)?'Tmpl':'Func';
        if(self::check_file($app,strtolower($class))){
            $callback = array($app.$class,$type.($method?'_'.$method:''));
            if(class_exists($callback[0]) && method_exists($callback[0], $callback[1])){
                return implode('::', $callback);
            }
        }
    }
    public static function check_file($app,$type='func') {
        $path = iPHP_APP_DIR . '/' . $app . '/' . $app . '.'.$type.'.php';
        return is_file($path);
    }
    public static function callback_output(&$content) {
        if(self::$config['callback']['output']){
            iPHP::callback(self::$config['callback']['output'],array(&$content));
        }
    }
    public static function unvars(&$args)
    {
        unset($args[self::TPL_FUNC_NAME], $args[self::TPL_FUNC_Method]);
    }
    /**
     * iPHP:test:method
     * iPHP:func
     * iPHP:testApp:method
     * iPHP:testClass:method
     */
    public static function callback_func($args,$tpl) {
        // isset($args['debug']) && $args['debug_vars'] = $args['debug'];
        isset($args['debug_vars']) && var_dump($args);
        $TFN = $args[self::TPL_FUNC_NAME];
        $TFM = $args[self::TPL_FUNC_Method];
        (is_array($TFN) && $TFN['app']) && $TFN = $TFN['app'];
        $keys = $TFN.($TFM?'_'.$TFM:'');
        isset($args['as']) && $keys = $args['as'];
        $isMultiArgs = false;
        //模板标签 对应>> 类::静态方法
        if($TFM){
            if(substr($TFN, -3,3)==='App'){
                //app/test/test.app.php
                //iPHP:testApp:method >> testApp::method
                //$testApp_method
                $callback = array($TFN,$TFM);
                $isMultiArgs = true;
            }else if(substr($TFN, -5,5)==='Class'){
                //app/test/test.class.php
                //iPHP:testClass:method >> test::method
                ////$testClass_method
                $callback = array(substr($TFN, 0,-5),$TFM);
                $isMultiArgs = true;
            }else{
                //iPHP:test:method app="aaa" method="bbb" >> aaaFunc::aaa_bbb
                // $args['app']     && $TFN = $args['app'];
                // $args['method']  && $TFM = $args['method'];
                //
                //app/test/test.func.php
                //iPHP:test:method >> testFunc::test_method
                $callback = array($TFN.'Func',$TFN.'_'.$TFM);
                //自定义APP模板调用
                //app/content/content.func.php
                //iPHP:content:list app="test" >> contentFunc::content_list
                //iPHP:test:list >> contentFunc::content_list
                if(self::$config['define']){
                    $apps = self::$config['define']['apps'];
                    $func = self::$config['define']['func'];
                    // 判断自定义APP app/test/test.func.php 程序是否存在
                    if(!self::check_file($TFN) && $apps[$TFN]){
                        // 程序不存在调用 contentFunc::content_list
                        $args['app'] = $TFN; //参数必需设置
                        $callback = array($func.'Func',$func.'_'.$TFM);
                    }
                }
                //app/test/MY_test.func.php
                //用户重写 iPHP:test:method 调用 MY_testFunc::test_method
                self::callback_func_custom($callback,'MY');
            }
            //是否多参数
            if(isset($args['isMA'])){
                $isMultiArgs = $args['isMA'];
                ksort($args);
                unset($args['isMA']);
            }
            if(!method_exists($callback[0],$callback[1]) && strpos($callback[1], '__')===false){
                iPHP::error_throw("Unable to find method '{$callback[0]}::{$callback[1]}'");
            }
        }else{
            //app/func/iPHP/iPHP.test.php
            //iPHP:test
            $callback = self::callback_func_system($TFN,$args['run']);

        }
        //合并 参数
        if(isset($args['vars'])){
            $vars = $args['vars'];
            unset($args['vars'],$vars['loop'],$vars['page']);
            $args = array_merge($args,$vars);
        }
        parse_bracket($args);//解析[]字符
        $isnew = isset($args['new'])?true:false;
        isset($args['debug_func']) && var_dump($callback);
        isset($args['args']) && $args = $args['args'];//设置参数
        $tpl->assign($keys.'_vars',$args);
        if(is_array($callback)){
            // iPHP:app:_method >> testFunc::method
            strpos($callback[1], '__')!==false && $callback[1] = substr($callback[1], strpos($callback[1], '__')+2);
            $isnew && $callback[0] = new $callback[0](); //动态方法 iPHP:app:method >> new test() ->method($args);
            $tpl->assign($keys,call_user_func_array($callback, $isMultiArgs?(array)$args:array($args)));
        }else{
            $callback && $tpl->assign($keys,$callback($args));
        }
    }
    public static function callback_func_system($func,$run=false,$vars=array()){
        //iPHP:test >> iPHP_test
        //app/func/iPHP/iPHP.test.php
        $func_path = iPHP_TPL_FUN."/".iPHP_APP."/".iPHP_APP.".".$func.".php";
        $callback  = iPHP_APP.'_' . $func;
        function_exists($callback) OR require_once($func_path);
        return $run?
                call_user_func_array($callback, array($vars)):
                $callback;
    }
    public static function callback_func_custom(&$callback=null,$prefix='MY',$type='func'){
        //用户重写 iPHP:test:method 调用 MY_testFunc::test_method
        //app/test/MY_test.func.php
        if($callback){
            $my    = $callback;
            $my[0] = $prefix.'_'.$my[0];
            $pos   = strlen($type);
            $app   = substr($callback[0],0,-$pos);
            $file  = $prefix.'_'.$app.'.'.$type;
            $path  = iPHP_APP_DIR . '/' . $app . '/' . $file . '.php';
            if(is_file($path)){
                if(method_exists($my[0],$my[1]) && strpos($callback[1], '__')===false){
                    $callback = $my;
                }
            }
        }
    }
    public static function callback_plugin($name,$tpl) {
        $path = iPHP_TPL_FUN."/template/tpl.".$name;
        if (is_file($path)) {
            return $path;
        }
        return false;
    }
    public static function block_cache($vars, &$content, $tpl) {
        $vars['id'] OR iUI::warning('cache 标签出错! 缺少"id"属性或"id"值为空.');
        $cache_time = isset($vars['time']) ? (int) $vars['time'] : -1;
        $cache_name = self::$config['template']['device'] . '/block_cache/' . $vars['id'];
        $_content   = iCache::get($cache_name);

        if ($_content===false) {
            iCache::set($cache_name, $content, $cache_time);
        }else{
            $content = $_content;
        }
        if ($vars['assign']) {
            $tpl->assign($vars['assign'], $content);
            return false;
        }
        return true;
    }
    //防模板下载
    public static function callback_compile($content,$file,$obj){
        return str_replace("<?php defined('iPHP') OR exit('What are you doing?');?>", '', $content);
    }
    /**
     * 模板路径
     * @param  [type] $tpl [description]
     * @return [type]      [description]
     */
    public static function callback_resource($tpl,$obj){
        $tpl = ltrim($tpl,'/');
        strpos($tpl,'..') && iPHP::error_404("The template path contains'..'");

        if(strpos($tpl, 'file::')!==false){
            list($_dir,$tpl)   = explode('||',str_replace('file::','',$tpl));
            $obj->template_dir = $_dir;
            return $tpl;
        }

        strpos($tpl,'./') !==false && $tpl = str_replace('./',dirname($obj->_file).'/',$tpl);

        $rtpl = self::tpl_exists($tpl,$_tpl);
        $rtpl === false && iPHP::error_404('Unable to find the template file <b>'.self::$handle->template_dir.'/' . $_tpl . '</b>', '002', 'TPL');
        return $rtpl;
    }
    public static function tpl_exists($tpl,&$_tpl=null) {
        $flag = iPHP_APP . ':/';
        $_tpl = $tpl;
        if (strpos($tpl, $flag) !== false) {
            // 模板名/$tpl
            if ($_tpl = self::check_tpl($tpl, self::$config['template']['dir'])){
                return $_tpl;
            }
            // testApp/$tpl
            if(self::$app){
                if ($_tpl = self::check_tpl($tpl, self::$app.'App')) {
                    return $_tpl;
                }
            }
            // iPHP/设备名/$tpl
            if ($_tpl = self::check_tpl($tpl, iPHP_APP.'/'.self::$config['template']['device'])) {
                return $_tpl;
            }
            // iPHP/$tpl
            if ($_tpl = self::check_tpl($tpl, iPHP_APP)) {
                return $_tpl;
            }
            // // 其它移动设备$tpl
            // if(iPHP_MOBILE){
            //     // iPHP/mobile/$tpl
            //     if ($_tpl = self::check_tpl($tpl, iPHP_APP.'/mobile')) {
            //         return $_tpl;
            //     }
            // }
            $_tpl = str_replace($flag, self::$config['template']['dir'], $tpl);
            // return self::check_tpl($tpl, self::$config['template']['dir']);
        } elseif (strpos($tpl, '{iTPL}') !== false) {
            $flag = '{iTPL}';
            // 模板名/$tpl
            if ($_tpl = self::check_tpl($tpl, self::$config['template']['dir'],$flag)){
                return $_tpl;
            }
            if(self::$app){
            // testApp/$tpl
                if ($_tpl = self::check_tpl($tpl, self::$app.'App',$flag)) {
                    return $_tpl;
                }
            }
            $_tpl = str_replace($flag, self::$config['template']['dir'], $tpl);
        }

        $_tpl = str_replace('{DEVICE}', self::$config['template']['device'], $_tpl);

        if (is_file(self::$handle->template_dir . "/" . $_tpl)) {
            return $_tpl;
        } else {
            return false;
        }
    }
    public static function check_tpl($tpl, $dir=null,$flag=null) {
        $flag===null && $flag = iPHP_APP.':/';
        $dir && $tpl = str_replace($flag, $dir, $tpl);
        $tpl  = ltrim($tpl,'/');
        $tdir = rtrim(self::$handle->template_dir,'/');
        if (is_file($tdir . "/" . $tpl)) {
            return $tpl;
        }
        return false;
    }
    public static function check_dir($name) {
        $dir = self::$handle->template_dir . "/" . $name;
        if (is_dir($dir)) {
            return $dir;
        }
        return false;
    }
    public static function unfunc_vars(&$vars) {
        unset($vars[iView::TPL_FUNC_NAME]);
    }
    public static function parse_vars(&$vars) {
        return parse_bracket($vars);
    }
    public static function app_vars($app_name = true, $out = false) {
        $app_name === true && $app_name = iPHP::$app_name;
        $rs = self::get_vars($app_name);
        return $rs['param'];
    }
    public static function get_vars($key = null) {
        return self::$handle->get_template_vars($key);
    }
    public static function set_iVARS($value = null,$key=null,$append=false) {
        if(is_array($value) && $key===null){
            self::$handle->_iVARS = array_merge(self::$handle->_iVARS,$value);
        }else{
            $vars = &self::$handle->_iVARS[$key];
            if($append){
                if(is_array($value)){
                    $vars = array_merge($vars,$value);
                }else{
                    $vars.= $value;
                }
            }else{
                $vars = $value;
            }
        }
    }

    public static function clear_tpl($file = null) {
        self::$handle OR self::init();
        self::$handle->clear_compiled_tpl($file);
    }
    public static function value($key, $value) {
        self::$handle->assign($key, $value);
    }
    public static function assign($key, $value) {
        self::$handle->assign($key, $value);
    }
    public static function append($key, $value = null, $merge = false) {
        self::$handle->append($key, $value, $merge);
    }
    public static function clear($key) {
        self::$handle->clear_assign($key);
    }
    public static function display($tpl,$app=null) {
        self::$handle OR self::init();
        $app && self::$app = $app;
        return self::$handle->fetch($tpl,true);
    }
    public static function fetch($tpl,$app=null){
        self::$handle OR self::init();
        $app && self::$app = $app;
        return self::$handle->fetch($tpl);
    }
    public static function render($tpl, $app = 'index') {
        $tpl OR iPHP::error_404('Please set the template file', '001', 'TPL');
        $app && self::$app = $app;
        self::receive_tpl($tpl);
        if (self::$gateway == 'html') {
            return self::$handle->fetch($tpl);
        } else {
            self::$handle->fetch($tpl,true);
            iPHP::debug_info($tpl);
        }
    }
    public static function receive_tpl(&$iTPL,$tpl=null){
        $tpl===null && $tpl = iSecurity::escapeStr($_GET['tpl']);
        if($tpl){
            $tpl.= '.htm';
            $tpl = iFS::escape_dir(ltrim($tpl,'/'));
            if(iFS::check($tpl)){
                $tplpath = self::$handle->template_dir . '/' .self::$config['template']['dir'].'/'.$tpl;
                if (is_file($tplpath)) {
                    $iTPL = '{iTPL}/'.$tpl;
                }
            }
        }
    }
}
