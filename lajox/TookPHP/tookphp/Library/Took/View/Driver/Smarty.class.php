<?php
/**
 * TookPHP Smarty模板引擎
 *
 * @package     TookPHP模板
 * @subpackage  Driver
 * @author      lajox <lajox@19www.com>
 */
namespace Took\View\Driver;
class Smarty extends \Took\View
{

    /**
     * 运行编译
     */
    public function run(&$view = null, &$content = null, &$compileFile = null)
    {
        //模板内容
        $content = is_null($content) ? $view->content : $content;
        //编译内容
        vendor('Smarty.Smarty#class');
        C('SHOW_NOTICE', FALSE);
        $compileFile = $compileFile ? $compileFile : $view->compileFile;
        $class = class_exists('SmartyBC') ? '\\SmartyBC' : '\\Smarty';
        $tpl                = new $class;
        //$tpl->debugging     = DEBUG;
        $tpl->debugging     = true;
        $tpl->caching       = C('TMPL_CACHE_ON') ? true : false;
        $tpl->allow_php_templates = true;
        $tpl->template_dir  = "";
        $tpl->compile_id    = md5($compileFile);
        $tpl->compile_dir   = dirname($compileFile);
        $tpl->cache_dir     = TEMP_CACHE_PATH;
        $tpl->assign($view->vars);
        //注册到全局变量
        $content = str_replace(array('{php}','{/php}'), array('<?php ','?>'), $content);
        $gvars = '';
        foreach($view->vars as $key=>$var) {
            $gvars .= "global \$$key;\$$key = ".var_export($var, true)."; ";
        }
        $gvars = empty($gvars) ? "" : "<?php ".$gvars." ?>\n";
        $content = $gvars.$content;
        //清空包含{__NOLAYOUT__}字符串
        $content = str_replace('<!--{__NOLAYOUT__}-->','',$content);
        $content = str_replace('{__NOLAYOUT__}','',$content);
        $view->savefile($content);
        $combine = $tpl->fetch($compileFile);
        $view->savefile($combine);
    }

}