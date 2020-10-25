<?php

defined("IN_CART") or die;
define('PHP_OPEN', "<?php ");
define('PHP_CLOSE', "?>");

function rewrite_url($pre, $para, $page = '')
{
    $para = str_replace(array('&', '='), array('-', '-'), $para);
    if ($page) {
        return '<a href="' . $pre . $para . '-' . $page . '.html"';
    } else {
        return '<a href="' . $pre . $para . '.html"';
    }
}

function rewrite_form($pre, $para, $page = '')
{
    $para = str_replace(array('&', '='), array('-', '-'), $para);
    if ($page) {
        
    } else {
        return '<form action="' . $pre . $para . '.html"';
    }
}

function rewrite_member($para, $type = '')
{
    $para == "index" && $para = "member";
    return $type == "form" ? rewrite_form('', $para) : rewrite_url('', $para);
}

function rewrite_url2($p1, $p2, $page = '')
{
    $arr = array(
        "mytrade_index" => "mytrade",
        "favor_myfavor" => "myfavor",
        "coupon_mycoupon" => "mycoupon",
        "link_index" => "link",
        "front_agree" => "agree",
        "user_forgetpwd" => "forgetpwd",
        "user_login" => "login",
        "user_reg" => "reg",
        "user_logout" => "logout",
        "cart_index" => "cart"
    );
    $key = $p1 . "_" . $p2;
    if (isset($arr[$key]))
        return rewrite_url('', $arr[$key], $page);
}

function rewrite_aftersale($para, $page)
{
    if ($para == "trade") {
        return rewrite_url('', "aftersale", $page);
    } else if ($para == "my") {
        return rewrite_url("my", "aftersale", $page);
    }
}

function rewrite_cat($para1, $para2 = null, $type = 'link')
{
    if ($type == 'link') {
        if (!$para2) {
            return rewrite_url('cat-', $para1);
        } else {
            return '<a href="cat-' . $para1 . '.html?' . trim(trim($para2, "&amp;"), "&") . '"';
        }
    } else if ($type == 'form') {
        if (!$para2) {
            return rewrite_form('cat-', $para1);
        } else {
            return '<form action="cat-' . $para1 . '.html?' . trim(trim($para2, "&amp;"), "&") . '"';
        }
    }
}

function rewrite_search($para = null, $type = '')
{
    if ($type == '') {
        if (!$para) {
            return rewrite_url('', 'search');
        } else {
            return '<a href="search.html?' . trim(trim($para, "&amp;"), "&") . '"';
        }
    } else if ($type == 'form') {
        if (!$para) {
            return rewrite_form('', 'search');
        } else {
            return '<form action="search.html?' . trim(trim($para, "&amp;"), "&") . '"';
        }
    }
}

/**
 *
 * Dwoo_File对象
 * 
 */
class Dwoo_File
{

    //文件路径
    protected $file;
    //文件路径
    protected $name;
    protected $compileId;
    protected $cacheId;
    //强制重新编译
    protected $compilationEnforced;
    protected static $cache = array('cached' => array(), 'compiled' => array());
    //文件权限
    protected $chmod = 0777;

    /**
     *
     * 构造函数
     * 
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->name = basename($file);
    }

    /**
     *
     * 判断编译文件是否正确
     * 
     */
    protected function isValidCompiledFile($file)
    {
        return file_exists($file) && (int) $this->getUid() <= filemtime($file);
    }

    /**
     *
     * 返回内容
     * 
     */
    public function getSource()
    {
        return file_get_contents($this->getResourceIdentifier());
    }

    /**
     *
     * 返回资源名
     * 
     */
    public function getResourceName()
    {
        return 'file';
    }

    /**
     *
     * 获取模版名称
     * 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * cache文件
     * 
     */
    public function cache(Dwoo $dwoo, $output)
    {
        $cacheDir = $dwoo->getCacheDir();
        $cachedFile = $this->getCacheFilename($dwoo);

        //在cacheDir目录生成临时文件
        $temp = tempnam($cacheDir, 'temp');
        if (!($file = @fopen($temp, 'wb'))) {
            $temp = $cacheDir . uniqid('temp');
            if (!($file = @fopen($temp, 'wb'))) {
                trigger_error('Error writing temporary file \'' . $temp . '\'', E_USER_WARNING);
                return false;
            }
        }

        fwrite($file, $output);
        fclose($file);

        remkdir(dirname($cachedFile));

        //将临时文件命名为cacheFile
        if (!@rename($temp, $cachedFile)) {
            @unlink($cachedFile);
            @rename($temp, $cachedFile);
        }

        if ($this->chmod !== null) {
            @chmod($cachedFile, $this->chmod);
        }

        self::$cache['cached'][$this->cacheId] = true;

        return $cachedFile;
    }

    /**
     *
     * 强制重新编译
     * 
     */
    public function forceCompilation()
    {
        $this->compilationEnforced = true;
    }

    /**
     *
     * 获取缓存的文件
     * 
     */
    public function getCachedTemplate(Dwoo $dwoo)
    {

        //cache时间
        $cacheLength = $dwoo->getCacheTime();

        //文件未被cache
        if ($cacheLength === 0) {
            return false;
        }

        $cachedFile = $this->getCacheFilename($dwoo);

        if (isset(self::$cache['cached'][$this->cacheId]) === true && file_exists($cachedFile)) { //如果cache文件存在
            return $cachedFile;
        } elseif ($this->compilationEnforced !== true && file_exists($cachedFile) && ($cacheLength === -1 || filemtime($cachedFile) > ($_SERVER['REQUEST_TIME'] - $cacheLength)) && $this->isValidCompiledFile($this->getCompiledFilename($dwoo))) {
            // cache is still valid and can be loaded
            self::$cache['cached'][$this->cacheId] = true;
            return $cachedFile;
        } else { //cache文件不存在或者已经过期
            return true;
        }
    }

    /**
     *
     * 获取资源文件ID
     * 
     */
    public function getResourceIdentifier()
    {
        return $this->file;
    }

    /**
     *
     * 返回文件修改时间
     * 
     */
    public function getUid()
    {
        return (string) filemtime($this->getResourceIdentifier());
    }

    /**
     *
     * 返回模版文件的Dwoo_File类
     * 
     */
    public static function templateFactory(Dwoo $dwoo, $resourceId)
    {
        $resourceId = str_replace(array("\t", "\n", "\r", "\f", "\v"), array('\\t', '\\n', '\\r', '\\f', '\\v'), $resourceId);
        $resourceId = strtr($resourceId, '\\', '/');

        if (file_exists($resourceId) === false) {

            $parentTemplate = $dwoo->getTemplate();

            $resourceId = dirname($parentTemplate->getResourceIdentifier()) . "/" . $resourceId;
            if (file_exists($resourceId) === false) {
                return null;
            }
        }
        return new Dwoo_File($resourceId);
    }

    /**
     *
     * 获取编译的文件
     * 
     */
    public function getCompiledTemplate(Dwoo $dwoo, $compiler = null)
    {
        $compiledFile = $this->getCompiledFilename($dwoo);

        if ($this->compilationEnforced !== true && isset(self::$cache['compiled'][$this->compileId]) === true) {
            // already checked, return compiled file
        } elseif ($this->compilationEnforced !== true && $this->isValidCompiledFile($compiledFile)) { //模版文件存在
            self::$cache['compiled'][$this->compileId] = true;
        } else { //编译
            $this->compilationEnforced = false;

            //compiler
            if ($compiler === null) {
                if (class_exists('Dwoo_Compiler', false) === false) {
                    include DWOO_DIRECTORY . 'Dwoo/Compiler.php';
                }
                $compiler = Dwoo_Compiler::compilerFactory();
            }

            $this->compiler = $compiler;

            //生成编译路径
            remkdir(dirname($compiledFile));

            file_put_contents($compiledFile, $compiler->compile($dwoo, $this));

            @chmod($compiledFile, 0777);

            self::$cache['compiled'][$this->compileId] = true;
        }
        return $compiledFile;
    }

    /**
     *
     * 获取compile的文件名
     * 
     */
    protected function getCompiledFilename(Dwoo $dwoo)
    {
        if ($this->compileId === null) {
            $this->compileId = str_replace('../', '__', strtr(basename($this->getResourceIdentifier()), '\\:', '/-'));
        }
        return $dwoo->getCompileDir() . $this->compileId . '.d' . C_RELEASE . '.php';
    }

    /**
     *
     * 获取cache的文件名
     * 
     */
    protected function getCacheFilename(Dwoo $dwoo)
    {
        //cacheId不存在时，利用REQUEST_URI
        if ($this->cacheId === null) {

            if (isset($_SERVER['REQUEST_URI']) === true) {
                $cacheId = $_SERVER['REQUEST_URI'];
            } elseif (isset($_SERVER['SCRIPT_FILENAME']) && isset($_SERVER['argv'])) {
                $cacheId = $_SERVER['SCRIPT_FILENAME'] . '_' . implode('_', $_SERVER['argv']);
            } else {
                $cacheId = '';
            }

            //强制生成compile_id
            $this->getCompiledFilename($dwoo);

            $this->cacheId = str_replace('../', '__', $this->compileId . preg_replace("#[/\\%?=!:;&]#", "_", $cacheId));
        }
        return $dwoo->getCacheDir() . $this->cacheId . '.html';
    }

}

/**
 *
 * Dwoo插件抽象类
 * 
 */
abstract class Dwoo_Plugin
{

    protected $buffer = '';
    protected $dwoo;

    public function __construct(Dwoo $dwoo)
    {
        $this->dwoo = $dwoo;
    }

    public function buffer($input)
    {
        $this->buffer .= $input;
    }

    public function end()
    {
        
    }

    public function process()
    {
        return $this->buffer;
    }

    public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
    {
        return PHP_OPEN . $prepend . '$this->addStack("' . $type . '", array(' . Dwoo_Compiler::implode_r($compiler->getCompiledParams($params)) . '));' . $append . PHP_CLOSE;
    }

    public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
    {
        return $content . PHP_OPEN . $prepend . '$this->delStack();' . $append . PHP_CLOSE;
    }

    public static function paramsToAttributes(array $params, $delim = '\'')
    {
        if (isset($params['*'])) {
            $params = array_merge($params, $params['*']);
            unset($params['*']);
        }

        $out = '';
        foreach ($params as $attr => $val) {
            $out .= ' ' . $attr . '=';
            if (trim($val, '"\'') == '' || $val == 'null') {
                $out .= str_replace($delim, '\\' . $delim, '""');
            } elseif (substr($val, 0, 1) === $delim && substr($val, -1) === $delim) {
                $out .= str_replace($delim, '\\' . $delim, '"' . substr($val, 1, -1) . '"');
            } else {
                $out .= str_replace($delim, '\\' . $delim, '"') . $delim . '.' . $val . '.' . $delim . str_replace($delim, '\\' . $delim, '"');
            }
        }

        return ltrim($out);
    }

}

/**
 *
 * Dwoo插件 if
 * 
 */
class Dwoo_Plugin_if extends Dwoo_Plugin
{

    public function init(array $rest)
    {
        
    }

    public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
    {
        return '';
    }

    public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
    {

        $params = $compiler->getCompiledParams($params);

        $pre = PHP_OPEN . 'if (' . implode(' ', $params['*']) . ") {\n" . PHP_CLOSE;

        $post = PHP_OPEN . "\n}" . PHP_CLOSE;

        if (isset($params['hasElse'])) {
            $post .= $params['hasElse'];
        }

        return $pre . $content . $post;
    }

}

/**
 *
 * Dwoo插件 else
 * 
 */
class Dwoo_Plugin_else extends Dwoo_Plugin
{

    public function init()
    {
        
    }

    public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
    {
        $preContent = '';
        $preContent .= $compiler->removeTopBlock();
        $block = & $compiler->getCurrentBlock();
        $params['initialized'] = true;
        $compiler->injectBlock($type, $params);
        return $preContent;
    }

    public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
    {
        if (!isset($params['initialized'])) {
            return '';
        }

        $block = & $compiler->getCurrentBlock();
        $block['params']['hasElse'] = PHP_OPEN . "else {\n" . PHP_CLOSE . $content . PHP_OPEN . "\n}" . PHP_CLOSE;
        return '';
    }

}

/**
 *
 * Dwoo插件 elseif
 * 
 */
class Dwoo_Plugin_elseif extends Dwoo_Plugin
{

    public function init(array $rest)
    {
        
    }

    public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
    {
        $preContent = '';
        $preContent .= $compiler->removeTopBlock();
        $block = & $compiler->getCurrentBlock();
        $params['initialized'] = true;
        $compiler->injectBlock($type, $params);
        return $preContent;
    }

    public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
    {
        if (!isset($params['initialized'])) {
            return '';
        }

        $params = $compiler->getCompiledParams($params);

        $pre = PHP_OPEN . "elseif (" . implode(' ', $params['*']) . ") {\n" . PHP_CLOSE;
        $post = PHP_OPEN . "\n}" . PHP_CLOSE;

        if (isset($params['hasElse'])) {
            $post .= $params['hasElse'];
        }

        $block = & $compiler->getCurrentBlock();
        $block['params']['hasElse'] = $pre . $content . $post;
        return '';
    }

}

/**
 *
 * Dwoo插件 foreach
 * 
 */
class Dwoo_Plugin_foreach extends Dwoo_Plugin
{

    public static $cnt = 0;

    public function init($from, $key = null, $item = null, $name = 'default', $implode = null)
    {
        
    }

    public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
    {
        // get block params and save the current template pointer to use it in the postProcessing method
        $currentBlock = & $compiler->getCurrentBlock();
        $currentBlock['params']['tplPointer'] = $compiler->getPointer();

        return '';
    }

    public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
    {
        $params = $compiler->getCompiledParams($params);
        $tpl = $compiler->getTemplateSource($params['tplPointer']);

        // assigns params
        $src = $params['from'];

        if ($params['item'] !== 'null') {
            if ($params['key'] !== 'null') {
                $key = $params['key'];
            }
            $val = $params['item'];
        } elseif ($params['key'] !== 'null') {
            $val = $params['key'];
        } else {
            hint('Foreach <em>item</em> ' . __("parameter_missing"));
        }
        $name = $params['name'];

        if (substr($val, 0, 1) !== '"' && substr($val, 0, 1) !== '\'') {
            hint('Foreach <em>item</em> ' . __("parameter_mustbe_string"));
        }
        if (isset($key) && substr($val, 0, 1) !== '"' && substr($val, 0, 1) !== '\'') {
            hint('Foreach <em>key</em> ' . __("parameter_mustbe_string"));
        }

        // evaluates which global variables have to be computed
        $varName = '$dwoo.foreach.' . trim($name, '"\'') . '.';
        $shortVarName = '$.foreach.' . trim($name, '"\'') . '.';
        $usesAny = strpos($tpl, $varName) !== false || strpos($tpl, $shortVarName) !== false;
        $usesFirst = strpos($tpl, $varName . 'first') !== false || strpos($tpl, $shortVarName . 'first') !== false;
        $usesLast = strpos($tpl, $varName . 'last') !== false || strpos($tpl, $shortVarName . 'last') !== false;
        $usesIndex = $usesFirst || strpos($tpl, $varName . 'index') !== false || strpos($tpl, $shortVarName . 'index') !== false;
        $usesIteration = $usesLast || strpos($tpl, $varName . 'iteration') !== false || strpos($tpl, $shortVarName . 'iteration') !== false;
        $usesShow = strpos($tpl, $varName . 'show') !== false || strpos($tpl, $shortVarName . 'show') !== false;
        $usesTotal = $usesLast || strpos($tpl, $varName . 'total') !== false || strpos($tpl, $shortVarName . 'total') !== false;

        if (strpos($name, '$this->scope[') !== false) {
            $usesAny = $usesFirst = $usesLast = $usesIndex = $usesIteration = $usesShow = $usesTotal = true;
        }

        // override globals vars if implode is used
        if ($params['implode'] !== 'null') {
            $implode = $params['implode'];
            $usesAny = true;
            $usesLast = true;
            $usesIteration = true;
            $usesTotal = true;
        }

        // gets foreach id
        $cnt = self::$cnt++;

        // build pre content output
        $pre = PHP_OPEN . "\n" . '$_fh' . $cnt . '_data = ' . $src . ';';
        // adds foreach properties
        if ($usesAny) {
            $pre .= "\n" . '$this->globals["foreach"][' . $name . '] = array' . "\n(";
            if ($usesIndex)
                $pre .= "\n\t" . '"index"		=> 0,';
            if ($usesIteration)
                $pre .= "\n\t" . '"iteration"		=> 1,';
            if ($usesFirst)
                $pre .= "\n\t" . '"first"		=> null,';
            if ($usesLast)
                $pre .= "\n\t" . '"last"		=> null,';
            if ($usesShow)
                $pre .= "\n\t" . '"show"		=> $this->isArray($_fh' . $cnt . '_data, true),';
            if ($usesTotal)
                $pre .= "\n\t" . '"total"		=> $this->isArray($_fh' . $cnt . '_data) ? count($_fh' . $cnt . '_data) : 0,';
            $pre .= "\n);\n" . '$_fh' . $cnt . '_glob =& $this->globals["foreach"][' . $name . '];';
        }
        // checks if foreach must be looped
        $pre .= "\n" . 'if ($this->isArray($_fh' . $cnt . '_data' . (isset($params['hasElse']) ? ', true' : '') . ') === true)' . "\n{";
        // iterates over keys
        $pre .= "\n\t" . 'foreach ($_fh' . $cnt . '_data as ' . (isset($key) ? '$this->scope[' . $key . ']=>' : '') . '$this->scope[' . $val . '])' . "\n\t{";
        // updates properties
        if ($usesFirst) {
            $pre .= "\n\t\t" . '$_fh' . $cnt . '_glob["first"] = (string) ($_fh' . $cnt . '_glob["index"] === 0);';
        }
        if ($usesLast) {
            $pre .= "\n\t\t" . '$_fh' . $cnt . '_glob["last"] = (string) ($_fh' . $cnt . '_glob["iteration"] === $_fh' . $cnt . '_glob["total"]);';
        }
        $pre .= "\n/* -- foreach start output */\n" . PHP_CLOSE;

        // build post content output
        $post = PHP_OPEN . "\n";

        if (isset($implode)) {
            $post .= '/* -- implode */' . "\n" . 'if (!$_fh' . $cnt . '_glob["last"]) {' .
                    "\n\t" . 'echo ' . $implode . ";\n}\n";
        }
        $post .= '/* -- foreach end output */';
        // update properties
        if ($usesIndex) {
            $post .= "\n\t\t" . '$_fh' . $cnt . '_glob["index"]+=1;';
        }
        if ($usesIteration) {
            $post .= "\n\t\t" . '$_fh' . $cnt . '_glob["iteration"]+=1;';
        }
        // end loop
        $post .= "\n\t}\n}" . PHP_CLOSE;
        if (isset($params['hasElse'])) {
            $post .= $params['hasElse'];
        }

        return $pre . $content . $post;
    }

}

/**
 *
 * 顶级block类
 * 
 */
final class Dwoo_Plugin_topLevelBlock extends Dwoo_Plugin
{

    public function init()
    {
        
    }

    public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
    {
        return '/* end template head */ ob_start(); /* template body */ ' . PHP_CLOSE;
    }

    public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
    {
        return $content . PHP_OPEN . ' /* end template body */' . "\n" . 'return $this->buffer . ob_get_clean();';
    }

}

/**
 *
 * 加载分页文件
 * 
 */
function Dwoo_Plugin_include_page(Dwoo $dwoo, $file, $pagetype)
{
    return Dwoo_Plugin_include($dwoo, $file, array("pagetype" => $pagetype));
}

/**
 *
 * 加载文件
 * 
 */
function Dwoo_Plugin_include(Dwoo $dwoo, $file, $rest = array())
{
    if ($file === '')
        return;
    try {
        $include = $dwoo->templateFactory($file);
    } catch (Exception $e) {
        cerror("Include : " . $e->getMessage());
    }
    if (!$include) {
        cerror(__("include_file_not_found", $file));
    }

    $vars = $dwoo->getData();
    if (count($rest)) {
        $vars = $rest + $vars;
    }
    $out = $dwoo->get($include, $vars);
    return $out;
}

class Dwoo
{

    const FUNC_PLUGIN = 2; //函数插件
    const NATIVE_PLUGIN = 4;
    const BLOCK_PLUGIN = 8;

    protected $charset = 'utf-8';
    public $globals;
    protected $compileDir;
    protected $cacheDir;
    protected $cacheTime = 0;
    protected $plugins = array();
    protected $template = null;
    protected $runtimePlugins;
    public $data;
    public $scope;
    protected $scopeTree;
    protected $stack;
    protected $curBlock;
    protected $buffer;

    /**
     *
     * 构造函数
     * 
     */
    public function __construct($compileDir, $cacheDir)
    {
        $this->setCompileDir($compileDir);
        $this->setCacheDir($cacheDir);
        $this->initGlobals();
    }

    public function __clone()
    {
        $this->template = null;
        unset($this->data);
    }

    /**
     *
     * 输出template
     * 
     */
    public function output($tpl, $data = array(), $compiler = null, $redirect = false)
    {
        return $this->get($tpl, $data, $compiler, true, $redirect);
    }

    /**
     *
     * 获取template
     * 
     */
    public function get($_tpl, $data = array(), $_compiler = null, $_output = false, $redirect = false)
    {
        if ($this->template instanceof Dwoo_File) {
            $proxy = clone $this;
            return $proxy->get($_tpl, $data, $_compiler, $_output);
        }

        //data比如为数组
        if (!is_array($data))
            return false;
        $this->data = $data;

        //tpl
        if (is_string($_tpl) && file_exists($_tpl)) {
            $_tpl = new Dwoo_File($_tpl);
        }

        if (!($_tpl instanceof Dwoo_File)) {
            halt(__('template_not_exists', basename(strval($_tpl))));
        }

        $this->template = $_tpl;

        $this->globals['template'] = $_tpl->getName();
        $this->initRuntimeVars($_tpl);

        //获取cache的文件
        $file = $_tpl->getCachedTemplate($this);
        $doCache = $file === true;

        //加载cache文件
        $cacheLoaded = is_string($file);
        if ($cacheLoaded === true) { //文件已经cache
            if ($_output === true) { //直接输出	
                include $file;
                $this->template = null;
            } else {     //返回文件
                ob_start();
                include $file;
                $this->template = null;
                return ob_get_clean();
            }
        } else {      //文件未cache
            if ($doCache === true) {
                $dynamicId = uniqid();
            }

            //编译后的文件
            $compiledTemplate = $_tpl->getCompiledTemplate($this, $_compiler);

            $out = include $compiledTemplate;

            //如果include返回false,重新编译
            if ($out === false) {
                $_tpl->forceCompilation();
                $compiledTemplate = $_tpl->getCompiledTemplate($this, $_compiler);
                $out = include $compiledTemplate;
            }

            if ($redirect) {
                $preg_searchs = $preg_replaces = array();

                //item
                $preg_searchs[] = "/\<a href\=\"index\.php\?model=item(?:&|&amp;)action=index(?:&|&amp;)itemid=([0-9]+)\"/ieU";
                $preg_searchs[] = "/\<a href\=\"index\.php\?([0-9]+)\"/ieU";

                //item
                $preg_replaces[] = "rewrite_url('item-','$1')";
                $preg_replaces[] = "rewrite_url('item-','$1')";

                //cat
                $preg_searchs[] = "/\<a href\=\"index\.php\?model=listing(?:&|&amp;)action=index(?:&|&amp;)catid=([0-9]+)(&.+)?\"/ieU";
                $preg_searchs[] = "/\<form action\=\"index\.php\?model=listing(?:&|&amp;)action=index(?:&|&amp;)catid=([0-9]+)(&.+)?\"/ieU";
                //cat
                $preg_replaces[] = "rewrite_cat('$1','$2','link')";
                $preg_replaces[] = "rewrite_cat('$1','$2','form')";

                //content
                $preg_searchs[] = "/\<a href\=\"index\.php\?model=content(?:&|&amp;)action=view(?:&|&amp;)contentid=([0-9]+)\"/ieU";
                $preg_searchs[] = "/\<a href\=\"index\.php\?model=content(?:&|&amp;)action=page(?:&|&amp;)pageid=([0-9]+)\"/ieU";
                $preg_replaces[] = "rewrite_url('content-','$1')";
                $preg_replaces[] = "rewrite_url('page-','$1')";

                //search
                $preg_searchs[] = "/\<a href\=\"index\.php\?model=search(?:&|&amp;)action=index(&.+)?\"/ieU";
                $preg_searchs[] = "/\<form action\=\"index\.php\?model=search(?:&|&amp;)action=index(&.+)?\"/ieU";

                //search
                $preg_replaces[] = "rewrite_search('$1')";
                $preg_replaces[] = "rewrite_search('$1','form')";

                //member
                $preg_searchs[] = "/\<a href\=\"index\.php\?model=member(?:&|&amp;)action=(index|info|pass|address)?\"/ieU";
                $preg_searchs[] = "/\<form action\=\"index\.php\?model=member(?:&|&amp;)action=(info|pass|address)?\"/ieU";
                //member
                $preg_replaces[] = "rewrite_member('$1')";
                $preg_replaces[] = "rewrite_member('$1','form')";

                //myservice
                $preg_searchs[] = "/\<a href\=\"index\.php\?model=myservice(?:&|&amp;)action=(myletter|nostock|downprice|mycomprice|myqa|mycomment)(?:&|&amp;)?(?:page=(.+))?\"/ieU";
                $preg_replaces[] = "rewrite_url('','$1','$2')";

                //mytrade,myfavor,mycoupon
                $preg_searchs[] = "/\<a href\=\"index\.php\?model=(mytrade|favor|coupon|link|front|user|cart)(?:&|&amp;)action=(index|login|reg|myfavor|mycoupon|forgetpwd|logout|agree)?(?:&|&amp;)?(?:page=(.+))?\"/ieU";
                $preg_replaces[] = "rewrite_url2('$1','$2','$3')";

                //aftersale
                $preg_searchs[] = "/\<a href\=\"index\.php\?model=aftersale(?:&|&amp;)action=(trade|my)?\"/ieU";
                $preg_replaces[] = "rewrite_aftersale('$1','$2')";

                $out = preg_replace($preg_searchs, $preg_replaces, $out);
            }

            //如果文件需要cache
            if ($doCache === true) {
                $file = $_tpl->cache($this, $out);

                if ($_output === true) {
                    include $file;
                    $this->template = null;
                } else {
                    ob_start();
                    include $file;
                    $this->template = null;
                    return ob_get_clean();
                }
            } else { //文件不需要cache
                $this->template = null;
                if ($_output === true) {
                    echo $out;
                }
                return $out;
            }
        }
    }

    /**
     *
     * 设置全局变量
     * 
     */
    protected function initGlobals()
    {
        $this->globals = array(
            'now' => $_SERVER['REQUEST_TIME'],
            'charset' => $this->charset,
        );
    }

    /**
     *
     * 初始化运行变量
     * 
     */
    protected function initRuntimeVars($tpl)
    {
        $this->runtimePlugins = array();
        $this->scope = &$this->data;
        $this->scopeTree = array();
        $this->stack = array();
        $this->curBlock = null;
        $this->buffer = '';
    }

    /**
     *
     * 获取cache文件保存路径
     * 
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     *
     * 设置cache文件保存路径
     * 
     */
    public function setCacheDir($dir)
    {
        $this->cacheDir = rtrim($dir, '/\\') . "/";
    }

    /**
     *
     * 获取编译文件保存路径
     * 
     */
    public function getCompileDir()
    {
        return $this->compileDir;
    }

    /**
     *
     * 设置编译文件保存路径
     * 
     */
    public function setCompileDir($dir)
    {
        $this->compileDir = rtrim($dir, '/\\') . "/";
    }

    /**
     *
     * 获取cache时间
     * 
     */
    public function getCacheTime()
    {
        return $this->cacheTime;
    }

    /**
     *
     * 设置cache时间
     * 
     */
    public function setCacheTime($seconds)
    {
        $this->cacheTime = (int) $seconds;
    }

    /**
     *
     * 获取字符集
     * 
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     *
     * 设置字符集
     * 
     */
    public function setCharset($charset)
    {
        $this->charset = strtolower($charset);
    }

    /**
     *
     * 获取模版
     * 
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     *
     * 返回模版文件的Dwoo_File类
     * 
     */
    public function templateFactory($resourceId)
    {
        return call_user_func(array("Dwoo_File", 'templateFactory'), $this, $resourceId);
    }

    /**
     *
     * 判断是否为数组，检查数组是否为空
     * 
     */
    public function isArray($value, $checkIsEmpty = false)
    {
        if (is_array($value) === true) {
            if ($checkIsEmpty === false) {
                return true;
            } else {
                return count($value) > 0;
            }
        } elseif ($value instanceof Iterator) {
            if ($checkIsEmpty === false) {
                return true;
            } elseif ($value instanceof Countable) {
                return count($value) > 0;
            } else {
                $value->rewind();
                return $value->valid();
            }
        } elseif ($value instanceof ArrayAccess) {
            if ($checkIsEmpty === false) {
                return true;
            } elseif ($value instanceof Countable) {
                return count($value) > 0;
            } else {
                return $value->offsetExists(0);
            }
        }
        return false;
    }

    public function addStack($blockName, array $args = array())
    {
        if (isset($this->plugins[$blockName])) {
            $class = $this->plugins[$blockName]['class'];
        } else {
            $class = 'Dwoo_Plugin_' . $blockName;
        }

        if ($this->curBlock !== null) {
            $this->curBlock->buffer(ob_get_contents());
            ob_clean();
        } else {
            $this->buffer .= ob_get_contents();
            ob_clean();
        }

        $block = new $class($this);

        $cnt = count($args);
        if ($cnt === 0) {
            $block->init();
        } elseif ($cnt === 1) {
            $block->init($args[0]);
        } elseif ($cnt === 2) {
            $block->init($args[0], $args[1]);
        } elseif ($cnt === 3) {
            $block->init($args[0], $args[1], $args[2]);
        } elseif ($cnt === 4) {
            $block->init($args[0], $args[1], $args[2], $args[3]);
        } else {
            call_user_func_array(array($block, 'init'), $args);
        }

        $this->stack[] = $this->curBlock = $block;
        return $block;
    }

    public function delStack()
    {
        $args = func_get_args();

        $this->curBlock->buffer(ob_get_contents());
        ob_clean();

        $cnt = count($args);
        if ($cnt === 0) {
            $this->curBlock->end();
        } elseif ($cnt === 1) {
            $this->curBlock->end($args[0]);
        } elseif ($cnt === 2) {
            $this->curBlock->end($args[0], $args[1]);
        } elseif ($cnt === 3) {
            $this->curBlock->end($args[0], $args[1], $args[2]);
        } elseif ($cnt === 4) {
            $this->curBlock->end($args[0], $args[1], $args[2], $args[3]);
        } else {
            call_user_func_array(array($this->curBlock, 'end'), $args);
        }

        $tmp = array_pop($this->stack);

        if (count($this->stack) > 0) {
            $this->curBlock = end($this->stack);
            $this->curBlock->buffer($tmp->process());
        } else {
            $this->curBlock = null;
            echo $tmp->process();
        }

        unset($tmp);
    }

    public function findBlock($type)
    {
        if (isset($this->plugins[$type])) {
            $type = $this->plugins[$type]['class'];
        } else {
            $type = 'Dwoo_Plugin_' . str_replace('Dwoo_Plugin_', '', $type);
        }

        $keys = array_keys($this->stack);
        while (($key = array_pop($keys)) !== false) {
            if ($this->stack[$key] instanceof $type) {
                return $this->stack[$key];
            }
        }
        return false;
    }

    public function getObjectPlugin($class)
    {
        if (isset($this->runtimePlugins[$class])) {
            return $this->runtimePlugins[$class];
        }
        return $this->runtimePlugins[$class] = new $class($this);
    }

    public function classCall($plugName, array $params = array())
    {
        $class = 'Dwoo_Plugin_' . $plugName;

        $plugin = $this->getObjectPlugin($class);

        $cnt = count($params);
        if ($cnt === 0) {
            return $plugin->process();
        } elseif ($cnt === 1) {
            return $plugin->process($params[0]);
        } elseif ($cnt === 2) {
            return $plugin->process($params[0], $params[1]);
        } elseif ($cnt === 3) {
            return $plugin->process($params[0], $params[1], $params[2]);
        } elseif ($cnt === 4) {
            return $plugin->process($params[0], $params[1], $params[2], $params[3]);
        } else {
            return call_user_func_array(array($plugin, 'process'), $params);
        }
    }

    public function arrayMap($callback, array $params)
    {
        if ($params[0] === $this) {
            $addThis = true;
            array_shift($params);
        }
        if ((is_array($params[0]) || ($params[0] instanceof Iterator && $params[0] instanceof ArrayAccess))) {
            if (empty($params[0])) {
                return $params[0];
            }

            // array map
            $out = array();
            $cnt = count($params);

            if (isset($addThis)) {
                array_unshift($params, $this);
                $items = $params[1];
                $keys = array_keys($items);

                if (is_string($callback) === false) {
                    while (($i = array_shift($keys)) !== null) {
                        $out[] = call_user_func_array($callback, array(1 => $items[$i]) + $params);
                    }
                } elseif ($cnt === 1) {
                    while (($i = array_shift($keys)) !== null) {
                        $out[] = $callback($this, $items[$i]);
                    }
                } elseif ($cnt === 2) {
                    while (($i = array_shift($keys)) !== null) {
                        $out[] = $callback($this, $items[$i], $params[2]);
                    }
                } elseif ($cnt === 3) {
                    while (($i = array_shift($keys)) !== null) {
                        $out[] = $callback($this, $items[$i], $params[2], $params[3]);
                    }
                } else {
                    while (($i = array_shift($keys)) !== null) {
                        $out[] = call_user_func_array($callback, array(1 => $items[$i]) + $params);
                    }
                }
            } else {
                $items = $params[0];
                $keys = array_keys($items);

                if (is_string($callback) === false) {
                    while (($i = array_shift($keys)) !== null) {
                        $out[] = call_user_func_array($callback, array($items[$i]) + $params);
                    }
                } elseif ($cnt === 1) {
                    while (($i = array_shift($keys)) !== null) {
                        $out[] = $callback($items[$i]);
                    }
                } elseif ($cnt === 2) {
                    while (($i = array_shift($keys)) !== null) {
                        $out[] = $callback($items[$i], $params[1]);
                    }
                } elseif ($cnt === 3) {
                    while (($i = array_shift($keys)) !== null) {
                        $out[] = $callback($items[$i], $params[1], $params[2]);
                    }
                } elseif ($cnt === 4) {
                    while (($i = array_shift($keys)) !== null) {
                        $out[] = $callback($items[$i], $params[1], $params[2], $params[3]);
                    }
                } else {
                    while (($i = array_shift($keys)) !== null) {
                        $out[] = call_user_func_array($callback, array($items[$i]) + $params);
                    }
                }
            }
            return $out;
        } else {
            return $params[0];
        }
    }

    public function readVarInto($varstr, $data, $safeRead = false)
    {
        if ($data === null) {
            return null;
        }

        if (is_array($varstr) === false) {
            preg_match_all('#(\[|->|\.)?((?:[^.[\]-]|-(?!>))+)\]?#i', $varstr, $m);
        } else {
            $m = $varstr;
        }
        unset($varstr);

        while (list($k, $sep) = each($m[1])) {
            if ($sep === '.' || $sep === '[' || $sep === '') {
                if ((is_array($data) || $data instanceof ArrayAccess) && ($safeRead === false || isset($data[$m[2][$k]]))) {
                    $data = $data[$m[2][$k]];
                } else {
                    return null;
                }
            } else {
                if (is_object($data) && ($safeRead === false || isset($data->$m[2][$k]) || is_callable(array($data, '__get')))) {
                    $data = $data->$m[2][$k];
                } else {
                    return null;
                }
            }
        }

        return $data;
    }

    public function readParentVar($parentLevels, $varstr = null)
    {
        $tree = $this->scopeTree;
        $cur = $this->data;

        while ($parentLevels-- !== 0) {
            array_pop($tree);
        }

        while (($i = array_shift($tree)) !== null) {
            if (is_object($cur)) {
                $cur = $cur->$i;
            } else {
                $cur = $cur[$i];
            }
        }

        if ($varstr !== null) {
            return $this->readVarInto($varstr, $cur);
        } else {
            return $cur;
        }
    }

    public function readVar($varstr)
    {
        if (is_array($varstr) === true) {
            $m = $varstr;
            unset($varstr);
        } else {
            if (strstr($varstr, '.') === false && strstr($varstr, '[') === false && strstr($varstr, '->') === false) {
                if ($varstr === 'dwoo') {
                    return $this->globals;
                } elseif ($varstr === '__' || $varstr === '_root') {
                    return $this->data;
                    $varstr = substr($varstr, 6);
                } elseif ($varstr === '_' || $varstr === '_parent') {
                    $varstr = '.' . $varstr;
                    $tree = $this->scopeTree;
                    $cur = $this->data;
                    array_pop($tree);

                    while (($i = array_shift($tree)) !== null) {
                        if (is_object($cur)) {
                            $cur = $cur->$i;
                        } else {
                            $cur = $cur[$i];
                        }
                    }

                    return $cur;
                }

                $cur = $this->scope;

                if (isset($cur[$varstr])) {
                    return $cur[$varstr];
                } else {
                    return null;
                }
            }

            if (substr($varstr, 0, 1) === '.') {
                $varstr = 'dwoo' . $varstr;
            }

            preg_match_all('#(\[|->|\.)?((?:[^.[\]-]|-(?!>))+)\]?#i', $varstr, $m);
        }



        $i = $m[2][0];
        if ($i === 'dwoo') {
            $cur = $this->globals;
            array_shift($m[2]);
            array_shift($m[1]);
            switch ($m[2][0]) {

                case 'get':
                    $cur = $_GET;
                    break;
                case 'post':
                    $cur = $_POST;
                    break;
                case 'session':
                    $cur = $_SESSION;
                    break;
                case 'cookies':
                case 'cookie':
                    $cur = $_COOKIE;
                    break;
                case 'server':
                    $cur = $_SERVER;
                    break;
                case 'env':
                    $cur = $_ENV;
                    break;
                case 'request':
                    $cur = $_REQUEST;
                    break;
                case 'const':
                    array_shift($m[2]);
                    if (defined($m[2][0])) {
                        return constant($m[2][0]);
                    } else {
                        return null;
                    }
            }
            if ($cur !== $this->globals) {
                array_shift($m[2]);
                array_shift($m[1]);
            }
        } elseif ($i === '__' || $i === '_root') {
            $cur = $this->data;
            array_shift($m[2]);
            array_shift($m[1]);
        } elseif ($i === '_' || $i === '_parent') {
            $tree = $this->scopeTree;
            $cur = $this->data;

            while (true) {
                array_pop($tree);
                array_shift($m[2]);
                array_shift($m[1]);
                if (current($m[2]) === '_' || current($m[2]) === '_parent') {
                    continue;
                }

                while (($i = array_shift($tree)) !== null) {
                    if (is_object($cur)) {
                        $cur = $cur->$i;
                    } else {
                        $cur = $cur[$i];
                    }
                }
                break;
            }
        } else {
            $cur = $this->scope;
        }

        while (list($k, $sep) = each($m[1])) {
            if ($sep === '.' || $sep === '[' || $sep === '') {
                if ((is_array($cur) || $cur instanceof ArrayAccess) && isset($cur[$m[2][$k]])) {
                    $cur = $cur[$m[2][$k]];
                } else {
                    return null;
                }
            } elseif ($sep === '->') {
                if (is_object($cur)) {
                    $cur = $cur->$m[2][$k];
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }

        return $cur;
    }

    public function getData()
    {
        return $this->data;
    }

    public function &getScope()
    {
        return $this->scope;
    }

}

class Dwoo_Compiler
{

    protected $ld = '{';
    protected $ldr = '\\{';
    protected $rd = '}';
    protected $rdr = '\\}';
    protected $allowNestedComments = false;
    protected $securityPolicy;
    protected $templatePlugins = array();
    protected $processors = array('pre' => array(), 'post' => array());
    protected $usedPlugins;
    protected $template;
    protected $pointer;
    protected $line;
    protected $templateSource;
    protected $data;
    protected $scope;
    protected $scopeTree;
    protected $stack = array();
    protected $curBlock;
    protected $dwoo;
    protected static $instance; //编译实例

    /**
     *
     * 构造函数
     * 
     */

    public function __construct()
    {
        self::$instance = $this;
    }

    /**
     *
     * 设置分隔符
     * 
     */
    public function setDelimiters($left, $right)
    {
        $this->ld = $left;
        $this->rd = $right;
        $this->ldr = preg_quote($left, '/');
        $this->rdr = preg_quote($right, '/');
    }

    /**
     *
     * 获取分隔符
     * 
     */
    public function getDelimiters()
    {
        return array($this->ld, $this->rd);
    }

    public function setPointer($position, $isOffset = false)
    {
        if ($isOffset) {
            $this->pointer += $position;
        } else {
            $this->pointer = $position;
        }
    }

    public function getPointer()
    {
        return $this->pointer;
    }

    public function setLine($number, $isOffset = false)
    {
        if ($isOffset) {
            $this->line += $number;
        } else {
            $this->line = $number;
        }
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getDwoo()
    {
        return $this->dwoo;
    }

    public function setTemplateSource($newSource, $fromPointer = false)
    {
        if ($fromPointer === true) {
            $this->templateSource = substr($this->templateSource, 0, $this->pointer) . $newSource;
        } else {
            $this->templateSource = $newSource;
        }
    }

    public function getTemplateSource($fromPointer = false)
    {
        if ($fromPointer === true) {
            return substr($this->templateSource, $this->pointer);
        } elseif (is_numeric($fromPointer)) {
            return substr($this->templateSource, $fromPointer);
        } else {
            return $this->templateSource;
        }
    }

    /**
     *
     * 编译
     * 
     */
    public function compile(Dwoo $dwoo, $template)
    {
        $tpl = $template->getSource();
        $ptr = 0;
        $this->dwoo = $dwoo;
        $this->template = $template;
        $this->templateSource = &$tpl;

        $this->pointer = &$ptr;

        while (true) {
            if ($ptr === 0) {

                // 重置变量
                $this->usedPlugins = array();
                $this->data = array();
                $this->scope = & $this->data;
                $this->scopeTree = array();
                $this->stack = array();
                $this->line = 1;
                $this->templatePlugins = array();

                // 添加顶级block
                $compiled = $this->addBlock('topLevelBlock', array(), 0);
                $this->stack[0]['buffer'] = '';
            }

            //以ptr为offset，查找下一个开始符
            $pos = strpos($tpl, $this->ld, $ptr);

            if ($pos === false) { //如果没有找到，跳出循环
                $this->push(substr($tpl, $ptr), 0);
                break;
            } elseif (substr($tpl, $pos - 1, 1) === '\\' && substr($tpl, $pos - 2, 1) !== '\\') { //todo
                $this->push(substr($tpl, $ptr, $pos - $ptr - 1) . $this->ld);
                $ptr = $pos + strlen($this->ld);
            } else {
                if (substr($tpl, $pos - 2, 1) === '\\' && substr($tpl, $pos - 1, 1) === '\\') {
                    $this->push(substr($tpl, $ptr, $pos - $ptr - 1));
                    $ptr = $pos;
                }

                $this->push(substr($tpl, $ptr, $pos - $ptr));
                $ptr = $pos;

                $pos += strlen($this->ld);

                //处理换行，制表符
                if (substr($tpl, $pos, 1) === ' ' || substr($tpl, $pos, 1) === "\r" || substr($tpl, $pos, 1) === "\n" || substr($tpl, $pos, 1) === "\t") {
                    $ptr = $pos;
                    $this->push($this->ld);
                    continue;
                }

                //如果没有找到 结束符
                if (strpos($tpl, $this->rd, $pos) === false) {
                    cerror(__('template_tag_not_closed', substr($tpl, $ptr, 30)));
                }

                $ptr += strlen($this->ld);

                $subptr = $ptr;

                while (true) {
                    $parsed = $this->parse($tpl, $subptr, null, false, 'root', $subptr);
                    if ($ptr === 0) {
                        continue 2;
                    }

                    $len = $subptr - $ptr;
                    $this->push($parsed, substr_count(substr($tpl, $ptr, $len), "\n"));
                    $ptr += $len;

                    if ($parsed === false) {
                        break;
                    }
                }

                // adds additional line breaks between php closing and opening tags because the php parser removes those if there is just a single line break
                if (substr($this->curBlock['buffer'], -2) === '?>' && preg_match('{^(([\r\n])([\r\n]?))}', substr($tpl, $ptr, 3), $m)) {
                    if ($m[3] === '') {
                        $ptr += 1;
                        $this->push($m[1] . $m[1], 1);
                    } else {
                        $ptr += 2;
                        $this->push($m[1] . "\n", 2);
                    }
                }
            }
        }

        $compiled .= $this->removeBlock('topLevelBlock');

        //output处理

        $output = "<?php\n/* template head */\n";
        $output .= $compiled . "\n?>";

        $output = preg_replace('/(?<!;|\}|\*\/|\n|\{)(\s*' . preg_quote(PHP_CLOSE, '/') . preg_quote(PHP_OPEN, '/') . ')/', ";\n", $output);
        $output = str_replace(PHP_CLOSE . PHP_OPEN, "\n", $output);
        $output = preg_replace('#(/\* template body \*/ \?>\s*)<\?xml#is', '$1<?php echo \'<?xml\'; ?>', $output);

        $this->template = $this->dwoo = null;
        $tpl = null;

        return $output;
    }

    public function push($content, $lineCount = null)
    {
        if ($lineCount === null) {
            $lineCount = substr_count($content, "\n");
        }

        if ($this->curBlock['buffer'] === null && count($this->stack) > 1) {
            // buffer is not initialized yet (the block has just been created)
            $this->stack[count($this->stack) - 2]['buffer'] .= (string) $content;
            $this->curBlock['buffer'] = '';
        } else {
            if (!isset($this->curBlock['buffer'])) {
                cerror(__('template_tag_closed_early'));
            }
            // append current content to current block's buffer
            $this->curBlock['buffer'] .= (string) $content;
        }
        $this->line += $lineCount;
    }

    public function setScope($scope, $absolute = false)
    {
        $old = $this->scopeTree;

        if ($scope === null) {
            unset($this->scope);
            $this->scope = null;
        }

        if (is_array($scope) === false) {
            $scope = explode('.', $scope);
        }

        if ($absolute === true) {
            $this->scope = & $this->data;
            $this->scopeTree = array();
        }

        while (($bit = array_shift($scope)) !== null) {
            if ($bit === '_parent' || $bit === '_') {
                array_pop($this->scopeTree);
                reset($this->scopeTree);
                $this->scope = & $this->data;
                $cnt = count($this->scopeTree);
                for ($i = 0; $i < $cnt; $i++)
                    $this->scope = & $this->scope[$this->scopeTree[$i]];
            } elseif ($bit === '_root' || $bit === '__') {
                $this->scope = & $this->data;
                $this->scopeTree = array();
            } elseif (isset($this->scope[$bit])) {
                $this->scope = & $this->scope[$bit];
                $this->scopeTree[] = $bit;
            } else {
                $this->scope[$bit] = array();
                $this->scope = & $this->scope[$bit];
                $this->scopeTree[] = $bit;
            }
        }

        return $old;
    }

    /**
     *
     * 添加顶级block
     * 
     */
    public function addBlock($type, array $params, $paramtype)
    {

        $class = 'Dwoo_Plugin_' . $type;

        $params = $this->mapParams($params, array($class, 'init'));

        $this->stack[] = array('type' => $type, 'params' => $params, 'custom' => false, 'class' => $class, 'buffer' => null);

        $this->curBlock = & $this->stack[count($this->stack) - 1];

        return call_user_func(array($class, 'preProcessing'), $this, $params, '', '', $type);
    }

    /**
     *
     * 插入一个block
     * 
     */
    public function injectBlock($type, array $params)
    {
        $class = 'Dwoo_Plugin_' . $type;
        $this->stack[] = array('type' => $type, 'params' => $params, 'custom' => false, 'class' => $class, 'buffer' => null);
        $this->curBlock = & $this->stack[count($this->stack) - 1];
    }

    /**
     *
     * 移除Block
     * 
     */
    public function removeBlock($type)
    {

        $output = '';
        $pluginType = $this->getPluginType($type);

        //移除block时执行stack所有的block
        while (true) {
            while ($top = array_pop($this->stack)) {
                if ($top['custom']) {
                    $class = $top['class'];
                } else {
                    $class = 'Dwoo_Plugin_' . $top['type'];
                }

                if (count($this->stack)) {
                    $this->curBlock = & $this->stack[count($this->stack) - 1];
                    $this->push(call_user_func(array($class, 'postProcessing'), $this, $top['params'], '', '', $top['buffer']), 0);
                } else {
                    $null = null;
                    $this->curBlock = & $null;
                    $output = call_user_func(array($class, 'postProcessing'), $this, $top['params'], '', '', $top['buffer']);
                }

                if ($top['type'] === $type) {
                    break 2;
                }
            }
            cerror(__('block_closed_not_open', $type));
            break;
        }
        return $output;
    }

    public function &getCurrentBlock()
    {
        return $this->curBlock;
    }

    public function removeTopBlock()
    {
        $o = array_pop($this->stack);
        if ($o === null) {
            cerror(__('block_closed_not_open', ""));
        }
        if ($o['custom']) {
            $class = $o['class'];
        } else {
            $class = 'Dwoo_Plugin_' . $o['type'];
        }

        $this->curBlock = & $this->stack[count($this->stack) - 1];

        return call_user_func(array($class, 'postProcessing'), $this, $o['params'], '', '', $o['buffer']);
    }

    /**
     *
     * 获取编译后的参数
     * 
     */
    public function getCompiledParams(array $params)
    {
        foreach ($params as $k => $p) {
            if (is_array($p)) {
                $params[$k] = $p[0];
            }
        }
        return $params;
    }

    protected function parse($in, $from, $to, $parsingParams = false, $curBlock = '', &$pointer = null)
    {

        if ($to === null) {
            $to = strlen($in);
        }
        $parsed = "";
        $first = substr($in, $from, 1);
        $substr = substr($in, $from, $to - $from);

        //变量
        if ($first === '$') {
            $out = $this->parseVar($in, $from, $to, $parsingParams, $curBlock, $pointer);
            $parsed = 'var';
        } elseif ($first === '%' && preg_match('#^%[a-z]#i', $substr)) {
            // const
            $out = $this->parseConst($in, $from, $to, $parsingParams, $curBlock, $pointer);
        } elseif ($first === '"' || $first === "'") {
            // string
            $out = $this->parseString($in, $from, $to, $parsingParams, $curBlock, $pointer);
        } elseif (preg_match('/^[a-z_][a-z_]*(?:::[a-z][a-z0-9_]*)?(' . (is_array($parsingParams) || $curBlock != 'root' ? '' : '\s+[^(]|') . '\s*\(|\s*' . $this->rdr . '|\s*;)/i', $substr)) {
            //函数
            $out = $this->parseFunction($in, $from, $to, $parsingParams, $curBlock, $pointer);
            $parsed = 'func';
        } elseif ($first === ';') {
            // instruction end

            if ($pointer !== null) {
                $pointer++;
            }
            return $this->parse($in, $from + 1, $to, false, 'root', $pointer);
        } elseif ($curBlock === 'root' && preg_match('#^/([a-z][a-z0-9_]*)?#i', $substr, $match)) {

            // close block
            if (!empty($match[1]) && $match[1] == 'else') {
                cerror(__('block_mustnot_be_close', "Else"));
            }
            if (!empty($match[1]) && $match[1] == 'elseif') {
                cerror(__('block_mustnot_be_close', "Elseif"));
            }
            if ($pointer !== null) {
                $pointer += strlen($match[0]);
            }
            if (empty($match[1])) {
                if ($this->curBlock['type'] == 'else' || $this->curBlock['type'] == 'elseif') {
                    $pointer -= strlen($match[0]);
                }
                return $this->removeTopBlock();
            } else {

                return $this->removeBlock($match[1]);
            }
        } elseif ($curBlock === 'root' && substr($substr, 0, strlen($this->rd)) === $this->rd) {
            $pointer += strlen($this->rd);
            return false;
        } elseif (is_array($parsingParams) && preg_match('#^([a-z0-9_]+\s*=)(?:\s+|[^=]).*#i', $substr, $match)) {
            // named parameter
            $len = strlen($match[1]);
            while (substr($in, $from + $len, 1) === ' ') {
                $len++;
            }
            if ($pointer !== null) {
                $pointer += $len;
            }

            $output = array(trim(substr(trim($match[1]), 0, -1)), $this->parse($in, $from + $len, $to, false, 'namedparam', $pointer));

            $parsingParams[] = $output;
            return $parsingParams;
        } elseif (preg_match('#^([a-z0-9_]+::\$[a-z0-9_]+)#i', $substr, $match)) {
            // static member access
            $parsed = 'var';
            if (is_array($parsingParams)) {
                $parsingParams[] = array($match[1], $match[1]);
                $out = $parsingParams;
            } else {
                $out = $match[1];
            }
            $pointer += strlen($match[1]);
        } elseif ($substr !== '' && (is_array($parsingParams) || $curBlock === 'namedparam' || $curBlock === 'condition' || $curBlock === 'expression')) {
            // unquoted string, bool or number
            $out = $this->parseOthers($in, $from, $to, $parsingParams, $curBlock, $pointer);
        } else {
            // parse error
            cerror(__("parse_error", substr($in, $from, $to - $from)));
        }

        if (empty($out)) {
            return '';
        }
        $substr = substr($in, $pointer, $to - $pointer);

        // var parsed, check if any var-extension applies
        if ($parsed === 'var') {
            if (preg_match('#^\s*([/%+*-])\s*([a-z0-9]|\$)#i', $substr, $match)) {

                // parse expressions
                $pointer += strlen($match[0]) - 1;
                if (is_array($parsingParams)) {
                    if ($match[2] == '$') {
                        $expr = $this->parseVar($in, $pointer, $to, array(), $curBlock, $pointer);
                    } else {
                        $expr = $this->parse($in, $pointer, $to, array(), 'expression', $pointer);
                    }
                    $out[count($out) - 1][0] .= $match[1] . $expr[0][0];
                    $out[count($out) - 1][1] .= $match[1] . $expr[0][1];
                } else {
                    if ($match[2] == '$') {
                        $expr = $this->parseVar($in, $pointer, $to, false, $curBlock, $pointer);
                    } else {
                        $expr = $this->parse($in, $pointer, $to, false, 'expression', $pointer);
                    }
                    if (is_array($out) && is_array($expr)) {
                        $out[0] .= $match[1] . $expr[0];
                        $out[1] .= $match[1] . $expr[1];
                    } elseif (is_array($out)) {
                        $out[0] .= $match[1] . $expr;
                        $out[1] .= $match[1] . $expr;
                    } elseif (is_array($expr)) {
                        $out .= $match[1] . $expr[0];
                    } else {
                        $out .= $match[1] . $expr;
                    }
                }
            } else if ($curBlock === 'root' && preg_match('#^(\s*(?:[+/*%-.]=|=|\+\+|--)\s*)(.*)#s', $substr, $match)) {

                $value = $match[2];
                $operator = trim($match[1]);
                if (substr($value, 0, 1) == '=') {
                    cerror(__("=_error", $substr));
                }

                if ($pointer !== null) {
                    $pointer += strlen($match[1]);
                }

                if ($operator !== '++' && $operator !== '--') {
                    $parts = array();
                    $ptr = 0;
                    $parts = $this->parse($value, 0, strlen($value), $parts, 'condition', $ptr);
                    $pointer += $ptr;

                    $parts = $this->mapParams($parts, array('Dwoo_Plugin_if', 'init'));
                    $parts = $this->getCompiledParams($parts);

                    $value = $parts['*'];
                    $echo = '';
                } else {
                    $value = array();
                    $echo = 'echo ';
                }

                $out = PHP_OPEN . $echo . $out . $operator . implode(' ', $value) . PHP_CLOSE;
            }
        }

        if ($curBlock !== 'modifier' && ($parsed === 'func' || $parsed === 'var') && preg_match('#^\|@?[a-z0-9_]+(:.*)?#i', $substr, $match)) {
            // parse modifier on funcs or vars
            $srcPointer = $pointer;
            if (is_array($parsingParams)) {
                $tmp = $this->replaceModifiers(array(null, null, $out[count($out) - 1][0], $match[0]), 'var', $pointer);
                $out[count($out) - 1][0] = $tmp;
                $out[count($out) - 1][1] .= substr($substr, $srcPointer, $srcPointer - $pointer);
            } else {
                $out = $this->replaceModifiers(array(null, null, $out, $match[0]), 'var', $pointer);
            }
        }

        if ($parsed === 'func' && preg_match('#^->[a-z0-9_]+(\s*\(.+|->[a-z].*)?#is', $substr, $match)) {
            $ptr = 0;
            if (is_array($parsingParams)) {
                $output = $this->parseMethodCall($out[count($out) - 1][1], $match[0], $curBlock, $ptr);
                $out[count($out) - 1][0] = $output;
                $out[count($out) - 1][1] .= substr($match[0], 0, $ptr);
            } else {
                $out = $this->parseMethodCall($out, $match[0], $curBlock, $ptr);
            }
            $pointer += $ptr;
        }

        if ($curBlock === 'root' && substr($out, 0, strlen(PHP_OPEN)) !== PHP_OPEN) {
            return PHP_OPEN . 'echo ' . $out . ';' . PHP_CLOSE;
        } else {
            return $out;
        }
    }

    /**
     *
     * 解析函数
     * 
     */
    protected function parseFunction($in, $from, $to, $parsingParams = false, $curBlock = '', &$pointer = null)
    {

        //匹配出函数名
        $cmdstr = substr($in, $from, $to - $from);
        preg_match('/^([a-z_][a-z0-9_]*(?:::[a-z][a-z0-9_]*)?)(\s*' . $this->rdr . '|\s*;)?/i', $cmdstr, $match);

        if (empty($match[1])) {
            cerror(__("parse_error", substr($cmdstr, 0, 15)));
        }
        $func = $match[1];
        if (!empty($match[2])) {
            $cmdstr = $match[1];
        }

        $paramsep = '';

        if (is_array($parsingParams) || $curBlock != 'root') {
            $paramspos = strpos($cmdstr, '(');
            $paramsep = ')';
        } elseif (preg_match_all('#[a-z0-9_]+(\s*\(|\s+[^(])#i', $cmdstr, $match, PREG_OFFSET_CAPTURE)) {
            $paramspos = $match[1][0][1];
            $paramsep = substr($match[1][0][0], -1) === '(' ? ')' : '';
            if ($paramsep === ')') {
                $paramspos += strlen($match[1][0][0]) - 1;
                if (substr($cmdstr, 0, 2) === 'if' || substr($cmdstr, 0, 6) === 'elseif') {
                    $paramsep = '';
                    if (strlen($match[1][0][0]) > 1) {
                        $paramspos--;
                    }
                }
            }
        } else {
            $paramspos = false;
        }
        $state = 0;

        if ($paramspos === false) {
            $params = array();
            if ($curBlock !== 'root') {
                return $this->parseOthers($in, $from, $to, $parsingParams, $curBlock, $pointer);
            }
        } else {
            $whitespace = strlen(substr($cmdstr, strlen($func), $paramspos - strlen($func)));
            $paramstr = substr($cmdstr, $paramspos + 1);
            if (substr($paramstr, -1, 1) === $paramsep) {
                $paramstr = substr($paramstr, 0, -1);
            }

            if (strlen($paramstr) === 0) {
                $params = array();
                $paramstr = '';
            } else {
                $ptr = 0;
                $params = array();
                if ($func === 'empty') {
                    $params = $this->parseVar($paramstr, $ptr, strlen($paramstr), $params, 'root', $ptr);
                } else {
                    while ($ptr < strlen($paramstr)) {
                        while (true) {
                            if ($ptr >= strlen($paramstr)) {
                                break 2;
                            }

                            if ($func !== 'if' && $func !== 'elseif' && $paramstr[$ptr] === ')') {
                                break 2;
                            } elseif ($paramstr[$ptr] === ';') {
                                $ptr++;
                                break 2;
                            } elseif ($func !== 'if' && $func !== 'elseif' && $paramstr[$ptr] === '/') {
                                break 2;
                            } elseif (substr($paramstr, $ptr, strlen($this->rd)) === $this->rd) {
                                break 2;
                            }

                            if ($paramstr[$ptr] === ' ' || $paramstr[$ptr] === ',' || $paramstr[$ptr] === "\r" || $paramstr[$ptr] === "\n" || $paramstr[$ptr] === "\t") {
                                $ptr++;
                            } else {
                                break;
                            }
                        }
                        if ($func === 'if' || $func === 'elseif') {
                            $params = $this->parse($paramstr, $ptr, strlen($paramstr), $params, 'condition', $ptr);
                        } else {
                            $params = $this->parse($paramstr, $ptr, strlen($paramstr), $params, 'function', $ptr);
                        }
                    }
                }
                $paramstr = substr($paramstr, 0, $ptr);
                $state = 0;
                foreach ($params as $k => $p) {
                    if (is_array($p) && is_array($p[1])) {
                        $state |= 2;
                    } else {
                        if (($state & 2) && preg_match('#^(["\'])(.+?)\1$#', $p[0], $m)) {
                            $params[$k] = array($m[2], array('true', 'true'));
                        } else {
                            if ($state & 2) {
                                cerror(__("parameter_error"));
                            }
                            $state |= 1;
                        }
                    }
                }
            }
        }

        if ($pointer !== null) {
            $pointer += (isset($paramstr) ? strlen($paramstr) : 0) + (')' === $paramsep ? 2 : ($paramspos === false ? 0 : 1)) + strlen($func) + (isset($whitespace) ? $whitespace : 0);
        }


        $pluginType = $this->getPluginType($func);

        // blocks
        if ($pluginType & Dwoo::BLOCK_PLUGIN) {
            return $this->addBlock($func, $params, $state);
        }

        // funcs
        if ($pluginType & Dwoo::NATIVE_PLUGIN) {
            $params = $this->mapParams($params, null);
        } elseif ($pluginType & Dwoo::FUNC_PLUGIN) {
            $params = $this->mapParams($params, 'Dwoo_Plugin_' . $func);
        }

        foreach ($params as &$p) {
            $p = $p[0];
        }
        if ($pluginType & Dwoo::NATIVE_PLUGIN) {
            if (isset($params['*'])) {
                $output = $func . '(' . implode(', ', $params['*']) . ')';
            } else {
                $output = $func . '()';
            }
        } elseif ($pluginType & Dwoo::FUNC_PLUGIN) {
            array_unshift($params, '$this');
            $params = self::implode_r($params);
            $output = 'Dwoo_Plugin_' . $func . '(' . $params . ')';
        }


        if (is_array($parsingParams)) {
            $parsingParams[] = array($output, $output);
            return $parsingParams;
        } else {
            return $output;
        }
    }

    protected function parseString($in, $from, $to, $parsingParams = false, $curBlock = '', &$pointer = null)
    {
        $substr = substr($in, $from, $to - $from);
        $first = $substr[0];


        $strend = false;
        $o = $from + 1;
        while ($strend === false) {
            $strend = strpos($in, $first, $o);
            if ($strend === false) {
                cerror(__("string_unfinished", substr($in, $from, $to - $from)));
            }
            if (substr($in, $strend - 1, 1) === '\\') {
                $o = $strend + 1;
                $strend = false;
            }
        }


        $srcOutput = substr($in, $from, $strend + 1 - $from);

        if ($pointer !== null) {
            $pointer += strlen($srcOutput);
        }

        $output = $this->replaceStringVars($srcOutput, $first);

        // handle modifiers
        if ($curBlock !== 'modifier' && preg_match('#^((?:\|(?:@?[a-z0-9_]+(?::.*)*))+)#i', substr($substr, $strend + 1 - $from), $match)) {
            $modstr = $match[1];

            if ($curBlock === 'root' && substr($modstr, -1) === '}') {
                $modstr = substr($modstr, 0, -1);
            }
            $modstr = str_replace('\\' . $first, $first, $modstr);
            $ptr = 0;
            $output = $this->replaceModifiers(array(null, null, $output, $modstr), 'string', $ptr);

            $strend += $ptr;
            if ($pointer !== null) {
                $pointer += $ptr;
            }
            $srcOutput .= substr($substr, $strend + 1 - $from, $ptr);
        }

        if (is_array($parsingParams)) {
            $parsingParams[] = array($output, substr($srcOutput, 1, -1));
            return $parsingParams;
        } elseif ($curBlock === 'namedparam') {
            return array($output, substr($srcOutput, 1, -1));
        } else {
            return $output;
        }
    }

    protected function parseConst($in, $from, $to, $parsingParams = false, $curBlock = '', &$pointer = null)
    {
        $substr = substr($in, $from, $to - $from);

        if (!preg_match('#^%([a-z0-9_:]+)#i', $substr, $m)) {
            cerror(__("invalid_constant"));
        }

        if ($pointer !== null) {
            $pointer += strlen($m[0]);
        }

        $output = $this->parseConstKey($m[1], $curBlock);

        if (is_array($parsingParams)) {
            $parsingParams[] = array($output, $m[1]);
            return $parsingParams;
        } elseif ($curBlock === 'namedparam') {
            return array($output, $m[1]);
        } else {
            return $output;
        }
    }

    protected function parseConstKey($key, $curBlock)
    {
        if ($curBlock !== 'root') {
            $output = '(defined("' . $key . '") ? ' . $key . ' : null)';
        } else {
            $output = $key;
        }

        return $output;
    }

    protected function parseVar($in, $from, $to, $parsingParams = false, $curBlock = '', &$pointer = null)
    {
        $substr = substr($in, $from, $to - $from);

        if (preg_match('#(\$?\.?[a-z0-9_:]*(?:(?:(?:\.|->)(?:[a-z0-9_:]+|(?R))|\[(?:[a-z0-9_:]+|(?R)|(["\'])[^\2]*?\2)\]))*)' . // var key
                        ($curBlock === 'root' || $curBlock === 'function' || $curBlock === 'namedparam' || $curBlock === 'condition' || $curBlock === 'variable' || $curBlock === 'expression' ? '(\(.*)?' : '()') . // method call
                        ($curBlock === 'root' || $curBlock === 'function' || $curBlock === 'namedparam' || $curBlock === 'condition' || $curBlock === 'variable' || $curBlock === 'delimited_string' ? '((?:(?:[+/*%=-])(?:(?<!=)=?-?[$%][a-z0-9.[\]>_:-]+(?:\([^)]*\))?|(?<!=)=?-?[0-9.,]*|[+-]))*)' : '()') . // simple math expressions
                        ($curBlock !== 'modifier' ? '((?:\|(?:@?[a-z0-9_]+(?:(?::("|\').*?\5|:[^`]*))*))+)?' : '(())') . // modifiers
                        '#i', $substr, $match)) {
            $key = substr($match[1], 1);

            $matchedLength = strlen($match[0]);
            $hasModifiers = !empty($match[5]);
            $hasExpression = !empty($match[4]);
            $hasMethodCall = !empty($match[3]);

            if (substr($key, -1) == ".") {
                $key = substr($key, 0, -1);
                $matchedLength--;
            }

            if ($hasMethodCall) {
                $matchedLength -= strlen($match[3]) + strlen(substr($match[1], strrpos($match[1], '->')));
                $key = substr($match[1], 1, strrpos($match[1], '->') - 1);
                $methodCall = substr($match[1], strrpos($match[1], '->')) . $match[3];
            }

            if ($hasModifiers) {
                $matchedLength -= strlen($match[5]);
            }

            if ($pointer !== null) {
                $pointer += $matchedLength;
            }

            // replace useless brackets by dot accessed vars
            $key = preg_replace('#\[([^$%\[.>-]+)\]#', '.$1', $key);

            // prevent $foo->$bar calls because it doesn't seem worth the trouble
            if (strpos($key, '->$') !== false) {
                cerror(__("object_access_error"));
            }

            $key = str_replace('"', '\\"', $key);

            $cnt = substr_count($key, '$');
            if ($cnt > 0) {
                $uid = 0;
                $parsed = array($uid => '');
                $current = & $parsed;
                $curTxt = & $parsed[$uid++];
                $tree = array();
                $chars = str_split($key, 1);
                $inSplittedVar = false;
                $bracketCount = 0;

                while (($char = array_shift($chars)) !== null) {
                    if ($char === '[') {
                        if (count($tree) > 0) {
                            $bracketCount++;
                        } else {
                            $tree[] = & $current;
                            $current[$uid] = array($uid + 1 => '');
                            $current = & $current[$uid++];
                            $curTxt = & $current[$uid++];
                            continue;
                        }
                    } elseif ($char === ']') {
                        if ($bracketCount > 0) {
                            $bracketCount--;
                        } else {
                            $current = & $tree[count($tree) - 1];
                            array_pop($tree);
                            if (current($chars) !== '[' && current($chars) !== false && current($chars) !== ']') {
                                $current[$uid] = '';
                                $curTxt = & $current[$uid++];
                            }
                            continue;
                        }
                    } elseif ($char === '$') {
                        if (count($tree) == 0) {
                            $curTxt = & $current[$uid++];
                            $inSplittedVar = true;
                        }
                    } elseif (($char === '.' || $char === '-') && count($tree) == 0 && $inSplittedVar) {
                        $curTxt = & $current[$uid++];
                        $inSplittedVar = false;
                    }

                    $curTxt .= $char;
                }
                unset($uid, $current, $curTxt, $tree, $chars);
                $key = $this->flattenVarTree($parsed);
                $output = preg_replace('#(^""\.|""\.|\.""$|(\()""\.|\.""(\)))#', '$2$3', '$this->readVar("' . $key . '")');
            } else {
                $output = $this->parseVarKey($key, $hasModifiers ? 'modifier' : $curBlock);
            }

            // methods
            if ($hasMethodCall) {
                $ptr = 0;
                $output = $this->parseMethodCall($output, $methodCall, $curBlock, $ptr);
                if ($pointer !== null) {
                    $pointer += $ptr;
                }
                $matchedLength += $ptr;
            }

            if ($hasExpression) {
                // expressions
                preg_match_all('#(?:([+/*%=-])(=?-?[%$][a-z0-9.[\]>_:-]+(?:\([^)]*\))?|=?-?[0-9.,]+|\1))#i', $match[4], $expMatch);

                foreach ($expMatch[1] as $k => $operator) {
                    if (substr($expMatch[2][$k], 0, 1) === '=') {
                        $assign = true;
                        if ($operator === '=') {
                            cerror(__("expression_error", $substr));
                        }
                        if ($curBlock !== 'root') {
                            cerror(__("expression_error", $substr));
                        }
                        $operator .= '=';
                        $expMatch[2][$k] = substr($expMatch[2][$k], 1);
                    }

                    if (substr($expMatch[2][$k], 0, 1) === '-' && strlen($expMatch[2][$k]) > 1) {
                        $operator .= '-';
                        $expMatch[2][$k] = substr($expMatch[2][$k], 1);
                    }
                    if (($operator === '+' || $operator === '-') && $expMatch[2][$k] === $operator) {
                        $output = '(' . $output . $operator . $operator . ')';
                        break;
                    } elseif (substr($expMatch[2][$k], 0, 1) === '$') {
                        $output = '(' . $output . ' ' . $operator . ' ' . $this->parseVar($expMatch[2][$k], 0, strlen($expMatch[2][$k]), false, 'expression') . ')';
                    } elseif (substr($expMatch[2][$k], 0, 1) === '%') {
                        $output = '(' . $output . ' ' . $operator . ' ' . $this->parseConst($expMatch[2][$k], 0, strlen($expMatch[2][$k]), false, 'expression') . ')';
                    } elseif (!empty($expMatch[2][$k])) {
                        $output = '(' . $output . ' ' . $operator . ' ' . str_replace(',', '.', $expMatch[2][$k]) . ')';
                    } else {
                        cerror(__("expression_error", $substr));
                    }
                }
            }

            // handle modifiers
            if ($curBlock !== 'modifier' && $hasModifiers) {
                $ptr = 0;
                $output = $this->replaceModifiers(array(null, null, $output, $match[5]), 'var', $ptr);
                if ($pointer !== null) {
                    $pointer += $ptr;
                }
                $matchedLength += $ptr;
            }

            if (is_array($parsingParams)) {
                $parsingParams[] = array($output, $key);
                return $parsingParams;
            } elseif ($curBlock === 'namedparam') {
                return array($output, $key);
            } elseif ($curBlock === 'string' || $curBlock === 'delimited_string') {
                return array($matchedLength, $output);
            } elseif ($curBlock === 'expression' || $curBlock === 'variable') {
                return $output;
            } elseif (isset($assign)) {
                return PHP_OPEN . $output . ';' . PHP_CLOSE;
            } else {
                return $output;
            }
        } else {
            if ($curBlock === 'string' || $curBlock === 'delimited_string') {
                return array(0, '');
            } else {
                cerror(__("variable_error", $substr));
            }
        }
    }

    protected function parseMethodCall($output, $methodCall, $curBlock, &$pointer)
    {
        $ptr = 0;
        $len = strlen($methodCall);
        while ($ptr < $len) {
            if (strpos($methodCall, '->', $ptr) === $ptr) {
                $ptr += 2;
            }

            if (in_array($methodCall[$ptr], array(';', '/', ' ', "\t", "\r", "\n", ')', '+', '*', '%', '=', '-', '|')) || substr($methodCall, $ptr, strlen($this->rd)) === $this->rd) {
                // break char found
                break;
            }

            if (!preg_match('/^([a-z0-9_]+)(\(.*?\))?/i', substr($methodCall, $ptr), $methMatch)) {
                cerror(__("method_error", substr($methodCall, $ptr, 20)));
            }

            if (empty($methMatch[2])) {
                // property
                if ($curBlock === 'root') {
                    $output .= '->' . $methMatch[1];
                } else {
                    $output = '(($tmp = ' . $output . ') ? $tmp->' . $methMatch[1] . ' : null)';
                }
                $ptr += strlen($methMatch[1]);
            } else {
                // method
                if (substr($methMatch[2], 0, 2) === '()') {
                    $parsedCall = '->' . $methMatch[1] . '()';
                    $ptr += strlen($methMatch[1]) + 2;
                } else {
                    $parsedCall = '->' . $this->parseFunction($methodCall, $ptr, strlen($methodCall), false, 'method', $ptr);
                }
                if ($curBlock === 'root') {
                    $output .= $parsedCall;
                } else {
                    $output = '(($tmp = ' . $output . ') ? $tmp' . $parsedCall . ' : null)';
                }
            }
        }
        $pointer += $ptr;
        return $output;
    }

    protected function parseVarKey($key, $curBlock)
    {
        if ($key === '') {
            return '$this->scope';
        }
        if (substr($key, 0, 1) === '.') {
            $key = 'dwoo' . $key;
        }
        if (preg_match('#dwoo\.(get|post|server|cookies|session|env|request)((?:\.[a-z0-9_-]+)+)#i', $key, $m)) {
            $global = strtoupper($m[1]);
            if ($global === 'COOKIES') {
                $global = 'COOKIE';
            }
            $key = '$_' . $global;
            foreach (explode('.', ltrim($m[2], '.')) as $part)
                $key .= '[' . var_export($part, true) . ']';
            if ($curBlock === 'root') {
                $output = $key;
            } else {
                $output = '(isset(' . $key . ')?' . $key . ':null)';
            }
        } elseif (preg_match('#dwoo\.const\.([a-z0-9_:]+)#i', $key, $m)) {
            return $this->parseConstKey($m[1], $curBlock);
        } elseif ($this->scope !== null) {
            if (strstr($key, '.') === false && strstr($key, '[') === false && strstr($key, '->') === false) {
                if ($key === 'dwoo') {
                    $output = '$this->globals';
                } elseif ($key === '_root' || $key === '__') {
                    $output = '$this->data';
                } elseif ($key === '_parent' || $key === '_') {
                    $output = '$this->readParentVar(1)';
                } elseif ($key === '_key') {
                    $output = '$tmp_key';
                } else {
                    if ($curBlock === 'root') {
                        $output = '$this->scope["' . $key . '"]';
                    } else {
                        $output = '(isset($this->scope["' . $key . '"]) ? $this->scope["' . $key . '"] : null)';
                    }
                }
            } else {
                preg_match_all('#(\[|->|\.)?((?:[a-z0-9_]|-(?!>))+|(\\\?[\'"])[^\3]*?\3)\]?#i', $key, $m);

                $i = $m[2][0];
                if ($i === '_parent' || $i === '_') {
                    $parentCnt = 0;

                    while (true) {
                        $parentCnt++;
                        array_shift($m[2]);
                        array_shift($m[1]);
                        if (current($m[2]) === '_parent') {
                            continue;
                        }
                        break;
                    }
                    $output = '$this->readParentVar(' . $parentCnt . ')';
                } else {
                    if ($i === 'dwoo') {
                        $output = '$this->globals';
                        array_shift($m[2]);
                        array_shift($m[1]);
                    } elseif ($i === '_root' || $i === '__') {
                        $output = '$this->data';
                        array_shift($m[2]);
                        array_shift($m[1]);
                    } elseif ($i === '_key') {
                        $output = '$tmp_key';
                    } else {
                        $output = '$this->scope';
                    }
                    while (count($m[1]) && $m[1][0] !== '->') {
                        $m[2][0] = preg_replace('/(^\\\([\'"])|\\\([\'"])$)/x', '$2$3', $m[2][0]);
                        if (substr($m[2][0], 0, 1) == '"' || substr($m[2][0], 0, 1) == "'") {
                            $output .= '[' . $m[2][0] . ']';
                        } else {
                            $output .= '["' . $m[2][0] . '"]';
                        }
                        array_shift($m[2]);
                        array_shift($m[1]);
                    }

                    if ($curBlock !== 'root') {
                        $output = '(isset(' . $output . ') ? ' . $output . ':null)';
                    }
                }

                if (count($m[2])) {
                    unset($m[0]);
                    $output = '$this->readVarInto(' . str_replace("\n", '', var_export($m, true)) . ', ' . $output . ', ' . ($curBlock == 'root' ? 'false' : 'true') . ')';
                }
            }
        } else {
            preg_match_all('#(\[|->|\.)?((?:[a-z0-9_]|-(?!>))+)\]?#i', $key, $m);
            unset($m[0]);
            $output = '$this->readVar(' . str_replace("\n", '', var_export($m, true)) . ')';
        }

        return $output;
    }

    protected function flattenVarTree(array $tree, $recursed = false)
    {
        $out = $recursed ? '".$this->readVarInto(' : '';
        foreach ($tree as $bit) {
            if (is_array($bit)) {
                $out .= '.' . $this->flattenVarTree($bit, false);
            } else {
                $key = str_replace('"', '\\"', $bit);

                if (substr($key, 0, 1) === '$') {
                    $out .= '".' . $this->parseVar($key, 0, strlen($key), false, 'variable') . '."';
                } else {
                    $cnt = substr_count($key, '$');


                    if ($cnt > 0) {
                        while (--$cnt >= 0) {
                            if (isset($last)) {
                                $last = strrpos($key, '$', - (strlen($key) - $last + 1));
                            } else {
                                $last = strrpos($key, '$');
                            }
                            preg_match('#\$[a-z0-9_]+((?:(?:\.|->)(?:[a-z0-9_]+|(?R))|\[(?:[a-z0-9_]+|(?R))\]))*' .
                                    '((?:(?:[+/*%-])(?:\$[a-z0-9.[\]>_:-]+(?:\([^)]*\))?|[0-9.,]*))*)#i', substr($key, $last), $submatch);

                            $len = strlen($submatch[0]);
                            $key = substr_replace(
                                    $key, preg_replace_callback(
                                            '#(\$[a-z0-9_]+((?:(?:\.|->)(?:[a-z0-9_]+|(?R))|\[(?:[a-z0-9_]+|(?R))\]))*)' .
                                            '((?:(?:[+/*%-])(?:\$[a-z0-9.[\]>_:-]+(?:\([^)]*\))?|[0-9.,]*))*)#i', array($this, 'replaceVarKeyHelper'), substr($key, $last, $len)
                                    ), $last, $len
                            );
                        }
                        unset($last);

                        $out .= $key;
                    } else {
                        $out .= $key;
                    }
                }
            }
        }
        $out .= $recursed ? ', true)."' : '';
        return $out;
    }

    protected function replaceVarKeyHelper($match)
    {
        return '".' . $this->parseVar($match[0], 0, strlen($match[0]), false, 'variable') . '."';
    }

    protected function parseOthers($in, $from, $to, $parsingParams = false, $curBlock = '', &$pointer = null)
    {
        $first = $in[$from];
        $substr = substr($in, $from, $to - $from);

        $end = strlen($substr);

        if ($curBlock === 'condition') {
            $breakChars = array('(', ')', ' ', '||', '&&', '|', '&', '>=', '<=', '===', '==', '=', '!==', '!=', '<<', '<', '>>', '>', '^', '~', ',', '+', '-', '*', '/', '%', '!', '?', ':', $this->rd, ';');
        } elseif ($curBlock === 'modifier') {
            $breakChars = array(' ', ',', ')', ':', '|', "\r", "\n", "\t", ";", $this->rd);
        } elseif ($curBlock === 'expression') {
            $breakChars = array('/', '%', '+', '-', '*', ' ', ',', ')', "\r", "\n", "\t", ";", $this->rd);
        } else {
            $breakChars = array(' ', ',', ')', "\r", "\n", "\t", ";", $this->rd);
        }

        $breaker = false;
        while (list($k, $char) = each($breakChars)) {
            $test = strpos($substr, $char);
            if ($test !== false && $test < $end) {
                $end = $test;
                $breaker = $k;
            }
        }

        if ($curBlock === 'condition') {
            if ($end === 0 && $breaker !== false) {
                $end = strlen($breakChars[$breaker]);
            }
        }

        if ($end !== false) {
            $substr = substr($substr, 0, $end);
        }

        if ($pointer !== null) {
            $pointer += strlen($substr);
        }

        $src = $substr;
        $substr = trim($substr);

        if (strtolower($substr) === 'false' || strtolower($substr) === 'no' || strtolower($substr) === 'off') {

            $substr = 'false';
        } elseif (strtolower($substr) === 'true' || strtolower($substr) === 'yes' || strtolower($substr) === 'on') {

            $substr = 'true';
        } elseif ($substr === 'null' || $substr === 'NULL') {

            $substr = 'null';
        } elseif (is_numeric($substr)) {
            $substr = (float) $substr;
            if ((int) $substr == $substr) {
                $substr = (int) $substr;
            }
        } elseif (preg_match('{^-?(\d+|\d*(\.\d+))\s*([/*%+-]\s*-?(\d+|\d*(\.\d+)))+$}', $substr)) {

            $substr = '(' . $substr . ')';
        } elseif ($curBlock === 'condition' && array_search($substr, $breakChars, true) !== false) {

            //$substr = '"'.$substr.'"';
        } else {
            $substr = $this->replaceStringVars('\'' . str_replace('\'', '\\\'', $substr) . '\'', '\'', $curBlock);
        }

        if (is_array($parsingParams)) {
            $parsingParams[] = array($substr, $src);
            return $parsingParams;
        } elseif ($curBlock === 'namedparam') {
            return array($substr, $src);
        } elseif ($curBlock === 'expression') {
            return $substr;
        } else {
            cerror(__("unknown_error"));
        }
    }

    protected function replaceStringVars($string, $first, $curBlock = '')
    {
        $pos = 0;

        // replace vars
        while (($pos = strpos($string, '$', $pos)) !== false) {
            $prev = substr($string, $pos - 1, 1);
            if ($prev === '\\') {
                $pos++;
                continue;
            }

            $var = $this->parse($string, $pos, null, false, ($curBlock === 'modifier' ? 'modifier' : ($prev === '`' ? 'delimited_string' : 'string')));
            $len = $var[0];
            $var = $this->parse(str_replace('\\' . $first, $first, $string), $pos, null, false, ($curBlock === 'modifier' ? 'modifier' : ($prev === '`' ? 'delimited_string' : 'string')));

            if ($prev === '`' && substr($string, $pos + $len, 1) === '`') {
                $string = substr_replace($string, $first . '.' . $var[1] . '.' . $first, $pos - 1, $len + 2);
            } else {
                $string = substr_replace($string, $first . '.' . $var[1] . '.' . $first, $pos, $len);
            }
            $pos += strlen($var[1]) + 2;
        }

        // handle modifiers
        // TODO Obsolete?
        $string = preg_replace_callback('#("|\')\.(.+?)\.\1((?:\|(?:@?[a-z0-9_]+(?:(?::("|\').+?\4|:[^`]*))*))+)#i', array($this, 'replaceModifiers'), $string);

        // replace escaped dollar operators by unescaped ones if required
        if ($first === "'") {
            $string = str_replace('\\$', '$', $string);
        }

        return $string;
    }

    protected function replaceModifiers(array $m, $curBlock = null, &$pointer = null)
    {


        if ($pointer !== null) {
            $pointer += strlen($m[3]);
        }
        // remove first pipe
        $cmdstrsrc = substr($m[3], 1);
        // remove last quote if present
        if (substr($cmdstrsrc, -1, 1) === $m[1]) {
            $cmdstrsrc = substr($cmdstrsrc, 0, -1);
            $add = $m[1];
        }

        $output = $m[2];

        $continue = true;
        while (strlen($cmdstrsrc) > 0 && $continue) {
            if ($cmdstrsrc[0] === '|') {
                $cmdstrsrc = substr($cmdstrsrc, 1);
                continue;
            }
            if ($cmdstrsrc[0] === ' ' || $cmdstrsrc[0] === ';' || substr($cmdstrsrc, 0, strlen($this->rd)) === $this->rd) {

                $continue = false;
                if ($pointer !== null) {
                    $pointer -= strlen($cmdstrsrc);
                }
                break;
            }
            $cmdstr = $cmdstrsrc;
            $paramsep = ':';
            if (!preg_match('/^(@{0,2}[a-z][a-z0-9_]*)(:)?/i', $cmdstr, $match)) {
                cerror(__("modifier_error", substr($cmdstr, 0, 10)));
            }
            $paramspos = !empty($match[2]) ? strlen($match[1]) : false;
            $func = $match[1];

            $state = 0;
            if ($paramspos === false) {
                $cmdstrsrc = substr($cmdstrsrc, strlen($func));
                $params = array();
            } else {
                $paramstr = substr($cmdstr, $paramspos + 1);
                if (substr($paramstr, -1, 1) === $paramsep) {
                    $paramstr = substr($paramstr, 0, -1);
                }

                $ptr = 0;
                $params = array();
                while ($ptr < strlen($paramstr)) {

                    $params = $this->parse($paramstr, $ptr, strlen($paramstr), $params, 'modifier', $ptr);


                    if ($ptr >= strlen($paramstr)) {

                        break;
                    }

                    if ($paramstr[$ptr] === ' ' || $paramstr[$ptr] === '|' || $paramstr[$ptr] === ';' || substr($paramstr, $ptr, strlen($this->rd)) === $this->rd) {

                        if ($paramstr[$ptr] !== '|') {
                            $continue = false;
                            if ($pointer !== null) {
                                $pointer -= strlen($paramstr) - $ptr;
                            }
                        }
                        $ptr++;
                        break;
                    }
                    if ($ptr < strlen($paramstr) && $paramstr[$ptr] === ':') {
                        $ptr++;
                    }
                }
                $cmdstrsrc = substr($cmdstrsrc, strlen($func) + 1 + $ptr);
                $paramstr = substr($paramstr, 0, $ptr);
                foreach ($params as $k => $p) {
                    if (is_array($p) && is_array($p[1])) {
                        $state |= 2;
                    } else {
                        if (($state & 2) && preg_match('#^(["\'])(.+?)\1$#', $p[0], $m)) {
                            $params[$k] = array($m[2], array('true', 'true'));
                        } else {
                            if ($state & 2) {
                                cerror(__("parameter_error"));
                            }
                            $state |= 1;
                        }
                    }
                }
            }

            // check if we must use array_map with this plugin or not
            $mapped = false;
            if (substr($func, 0, 1) === '@') {
                $func = substr($func, 1);
                $mapped = true;
            }

            $pluginType = $this->getPluginType($func);

            if ($state & 2) {
                array_unshift($params, array('value', array($output, $output)));
            } else {
                array_unshift($params, array($output, $output));
            }

            if ($pluginType & Dwoo::NATIVE_PLUGIN) {
                $params = $this->mapParams($params, null);

                $params = $params['*'][0];

                $params = self::implode_r($params);

                if ($mapped) {
                    $output = '$this->arrayMap(\'' . $func . '\', array(' . $params . '))';
                } else {
                    $output = $func . '(' . $params . ')';
                }
            } else {

                $pluginName = 'Dwoo_Plugin_' . $func;

                $callback = $pluginName;

                $params = $this->mapParams($params, $callback);

                foreach ($params as &$p)
                    $p = $p[0];

                if ($pluginType & Dwoo::FUNC_PLUGIN) {

                    array_unshift($params, '$this');
                    $params = self::implode_r($params);
                    if ($mapped) {
                        $output = '$this->arrayMap(\'' . $pluginName . '\', array(' . $params . '))';
                    } else {
                        $output = $pluginName . '(' . $params . ')';
                    }
                } else {
                    $params = self::implode_r($params);
                    if ($mapped) {
                        $output = '$this->arrayMap(array($this->getObjectPlugin(\'Dwoo_Plugin_' . $func . '\'), \'process\'), array(' . $params . '))';
                    } else {
                        $output = '$this->classCall(\'' . $func . '\', array(' . $params . '))';
                    }
                }
            }
        }

        if ($curBlock === 'var' || $m[1] === null) {
            return $output;
        } elseif ($curBlock === 'string' || $curBlock === 'root') {
            return $m[1] . '.' . $output . '.' . $m[1] . (isset($add) ? $add : null);
        }
    }

    public static function implode_r(array $params, $recursiveCall = false)
    {
        $out = '';
        foreach ($params as $k => $p) {
            if (is_array($p)) {
                $out2 = 'array(';
                foreach ($p as $k2 => $v)
                    $out2 .= var_export($k2, true) . ' => ' . (is_array($v) ? 'array(' . self::implode_r($v, true) . ')' : $v) . ', ';
                $p = rtrim($out2, ', ') . ')';
            }
            if ($recursiveCall) {
                $out .= var_export($k, true) . ' => ' . $p . ', ';
            } else {
                $out .= $p . ', ';
            }
        }
        return rtrim($out, ', ');
    }

    /**
     *
     * 获取插件类型
     * 
     */
    protected function getPluginType($name)
    {

        $pluginType = -1;

        if ((function_exists($name) || strtolower($name) === 'isset' || strtolower($name) === 'empty')) {
            $phpFunc = true;
        }

        if (class_exists('Dwoo_Plugin_' . $name, false) !== false) {
            $pluginType = Dwoo::BLOCK_PLUGIN;
        } elseif (function_exists('Dwoo_Plugin_' . $name) !== false) {
            $pluginType = Dwoo::FUNC_PLUGIN;
        } else {
            if (isset($phpFunc)) {
                $pluginType = Dwoo::NATIVE_PLUGIN;
            } else {
                //todo 插件类型不存在
            }
        }

        return $pluginType;
    }

    protected function mapParams(array $params, $callback)
    {

        $map = $this->getParamMap($callback);

        $paramlist = array();

        // transforms the parameter array from (x=>array('paramname'=>array(values))) to (paramname=>array(values))
        $ps = array();
        foreach ($params as $p) {
            if (is_array($p[1])) {
                $ps[$p[0]] = $p[1];
            } else {
                $ps[] = $p;
            }
        }

        // loops over the param map and assigns values from the template or default value for unset optional params
        while (list($k, $v) = each($map)) {
            if ($v[0] === '*') {
                // "rest" array parameter, fill every remaining params in it and then break
                if (count($ps) === 0) {
                    if ($v[1] === false) {
                        cerror(__("parameter_missing", is_array($callback) ? $callback[0] : $callback));
                    } else {
                        break;
                    }
                }
                $tmp = array();
                $tmp2 = array();
                foreach ($ps as $i => $p) {
                    $tmp[$i] = $p[0];
                    $tmp2[$i] = $p[1];
                    unset($ps[$i]);
                }
                $paramlist[$v[0]] = array($tmp, $tmp2);
                unset($tmp, $tmp2, $i, $p);
                break;
            } elseif (isset($ps[$v[0]])) {
                // parameter is defined as named param
                $paramlist[$v[0]] = $ps[$v[0]];
                unset($ps[$v[0]]);
            } elseif (isset($ps[$k])) {
                // parameter is defined as ordered param
                $paramlist[$v[0]] = $ps[$k];
                unset($ps[$k]);
            } elseif ($v[2] === null) {
                // enforce lowercased null if default value is null (php outputs NULL with var export)
                $paramlist[$v[0]] = array('null', null);
            } else {
                // outputs default value with var_export
                $paramlist[$v[0]] = array(var_export($v[2], true), $v[2]);
            }
        }

        if (count($ps)) {
            foreach ($ps as $i => $p) {
                array_push($paramlist, $p);
            }
        }

        return $paramlist;
    }

    /**
     *
     * 获取callback参数列表
     * 
     */
    protected function getParamMap($callback)
    {
        if (is_null($callback)) {
            return array(array('*', true));
        }
        if (is_array($callback)) {
            $ref = new ReflectionMethod($callback[0], $callback[1]);
        } else {
            $ref = new ReflectionFunction($callback);
        }

        $out = array();
        foreach ($ref->getParameters() as $param) {
            if (($class = $param->getClass()) !== null && ($class->name === 'Dwoo' || $class->name === 'Dwoo_Compiler')) {
                continue;
            }
            if ($param->getName() === 'rest' && $param->isArray() === true) {
                $out[] = array('*', $param->isOptional(), null);
            }
            $out[] = array($param->getName(), $param->isOptional(), $param->isOptional() ? $param->getDefaultValue() : null);
        }
        return $out;
    }

    /**
     *
     * 获取compiler实例
     * 
     */
    public static function compilerFactory()
    {
        if (self::$instance === null) {
            new self;
        }
        return self::$instance;
    }

}
