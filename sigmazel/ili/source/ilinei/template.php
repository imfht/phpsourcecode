<?php
//版权所有(C) 2014 www.ilinei.com

namespace ilinei;

use admin\model\_ilinei;

/**
 * 模板引擎
 * @author sigmazel
 * @since v1.0.2
 */
class template{
	private $file = '';
    private $extension = '.htm';
    private $json = '';
    private $state = 0; //状态值：0解析，1在线编辑
    private $cache_path = '/_cache';
    private $cache_file = '';

	public function __construct($file = '', $state = false){
	    $this->state = $state;
	    $this->load($file);
	}

	public function load($file){
	    if(!$file) return '';

        $this->file = ROOTPATH.$file.$this->extension;
        $this->cache_file = ROOTPATH.$this->cache_path.$file.$this->extension.'.php';

        $_cache_path = dirname($this->cache_file);

        if(!is_dir($_cache_path)) {
            mkdir($_cache_path, 0777, true);
            chown($_cache_path, 'apache');
        }
    }

    public function json($json){
	    $this->json = $json;
    }

    public function state($state){
	    $this->state = $state;
    }

	private function read_file(){
		if(!@$fp = fopen($this->file, 'r')) exit("template_not found:$this->file");
		
		$template = @fread($fp, filesize($this->file));
		fclose($fp);
		
		return $template;
	}
	
	private function write_file($template){
		if(!@$fp = fopen($this->cache_file, 'w')) exit('directory_notfound'.dirname($this->cache_file));
		
		flock($fp, 2);
		fwrite($fp, $template);
		fclose($fp);
	}
	
	public static function lang_tags($var){
		return isset($GLOBALS['lang']) ? $GLOBALS['lang'][$var] : '';
	}
	
	public static function strip_tags($expr, $statement = ''){
		$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
		$statement = str_replace("\\\"", "\"", $statement);
		return $expr.$statement;
	}
	
	public static function desc_name_tags($var){
		return '';
	}

    public static function desc_page_tags($var){
        return '';
    }

    public static function desc_block_tags($var){
        return '';
    }

	public static function date_tags($var){
		return "<?php echo date($var); ?>";
	}
		
	public static function code_tags($var){
		$php = str_replace('\"', '"', $var);
		return "<?php
$php
?>\r\n";
	}
	
	public static function timeout_tags($var){
		return "<?php echo round($var - \$_var['starttime'], 3); ?>";
	}

	public static function block_tags($var, $state = 0){
	    global $setting;

        $var = substr($var, -1) == '/' ? substr($var, 0, -1) : $var;
        $vstring = str_replace(array('"', '\''), '', $var);
        $vstring = explode(' ', $vstring);

        $params = array();
	    foreach($vstring as $key => $v){
	        $v = trim($v);
	        if(!$v) continue;

	        $arr = explode('=', $v);
	        $params[$arr[0]] = $arr[1];
        }

        $code = "";

        if($state){
            $GLOBALS['_ILINEI_ID'] = $GLOBALS['_ILINEI_ID'] + 1;

            if($params['file'] != 'meta'){
                $code .= "
<div id=\"_ilinei_id_{$GLOBALS[_ILINEI_ID]}\" class=\"-ilinei-block\" level=\"0\" rel=\"_ilinei_id_{$GLOBALS[_ILINEI_ID]}\" tag=\"block\" {$var}>
<textarea name=\"_ilinei_id_{$GLOBALS[_ILINEI_ID]}\" style=\"display:none;\"></textarea>
<p class=\"-ilinei-icon\"><span class=\"-ilinei-icon-img\"></span><span class=\"-ilinei-icon-text\"></span></p>
<div class=\"-ilinei-block-content\">";
            }
        }

        //引用块
        if(isset($params['file'])){
            //如果为空，返回
            if(empty($params['file'])){
                if($state) $code .= "</div></div>";
                return template::block_tags_wrap($params, $code);
            }

            $file = "/{$setting[SiteTheme]}/page/{$params[file]}";
            $code .= "
<?php
\$_template->load('{$file}');";

            if($state){
                $code .= "
\$_template->state({$state});
";
            }

            $code .= "
include \$_template->parsed(\$setting['SiteTemplateCache'] + 0);
?>
";
            if($state && $params['file'] != 'meta') $code .= "</div></div>";


            return template::block_tags_wrap($params, $code);
        }

        //如果未设置key值，直接返回
        if(empty($params['key'])) return template::block_tags_wrap($params, $code);

        $_ilinei = new _ilinei();
        $block = $_ilinei->block($params);

        //如果未找到对应的TAG信息，直接返回
        if(empty($block['class']) || empty($block['model']) || empty($block['method'])) return template::block_tags_wrap($params, $code);

        $class = $block['class'];
        $json = json_encode($params);
        $json = str_replace('\\', '\\\\', $json);

        $code .= "
<?php
!\${$block[model]} && \${$block[model]} = new $class();
";
        if($block['var']) $code .= "\${$block['var']} = \${$block[model]}->block_{$block[method]}('{$json}');";
        else $code .= "\${$block[model]}->block_{$block[method]}('{$json}');";

        if($params['pager']) $code .= "\r\n\$pager = \$GLOBALS['pager'];";

        if($params['theme']){
            $code .= "
\$_template->load('{$block[theme]}');
\$_template->json('{$json}');";

            if($state){
                $code .= "
\$_template->state({$state});
";
            }

            $code .= "
include \$_template->parsed(\$setting['SiteTemplateCache'] + 0);";
        }

        $code .="
?>
";
        if($state) $code .= "</div></div>";

        return template::block_tags_wrap($params, $code);
    }

    //包装TAG
    public static function block_tags_wrap($params, $block){
        if($params['cols'] + 0 == 2){//如果两栏
            if($params['col'] == 1){
                $html = "
<div class=\"-ilinei-columns\" columns=\"2\">
    <span class=\"-ilinei-column-icon\"></span>
    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
        <tr>
            <td class=\"-ilinei-column\" width=\"{$params[width]}\">
            {$block}
            </td>";

                return $html;
            }

            if($params['col'] == 2){
                $html = "
            <td class=\"-ilinei-column\" width=\"{$params[width]}\">
            {$block}
            </td>
        </tr>
    </table>
</div>";

                return $html;
            }
        }

        if($params['cols'] + 0 == 3){ //如果两栏
            if($params['col'] == 1){
                $html = "
<div class=\"-ilinei-columns\" columns=\"3\">
    <span class=\"-ilinei-column-icon\"></span>
    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
        <tr>
            <td class=\"-ilinei-column\" width=\"{$params[width]}\">
            {$block}
            </td>";

                return $html;
            }

            if($params['col'] == 2){
                $html = "
            <td class=\"-ilinei-column\" width=\"{$params[width]}\">
            {$block}
            </td>";

                return $html;
            }

            if($params['col'] == 3){
                $html = "
            <td class=\"-ilinei-column\" width=\"{$params[width]}\">
            {$block}
            </td>
        </tr>
    </table>
</div>";

                return $html;
            }
        }

        return $block;
    }

	public static function adlog_tags($var, $state = 0){
        $code = "
<?php
!\$_ad_log && \$_ad_log = new \ad\_ad_log();
echo \$_ad_log->display(\"{$var}\");
?>
";
		return $code;
	}
	
	public static function template($file){
        $code = "
<?php
\$_template->load('{$file}'); 
include \$_template->parsed(\$setting['SiteTemplateCache'] + 0);
?>
";

        return $code;
	}

    public static function include_file($file){
        return "
<?php
include ROOTPATH.'{$file}.htm';
?>
";
    }
	
	public function parsing($template){
		$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
		$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
		$template = preg_replace_callback("/[\n\r\t]*\{@name\s+(.+?)\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::desc_name_tags($matches[1]);'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{@page\s+(.+?)\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::desc_page_tags($matches[1]);'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{@block\s+(.+?)\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::desc_block_tags($matches[1]);'), $template);
		$template = preg_replace_callback("/[\n\r\t]*\{lang\s+(.+?)\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::lang_tags($matches[1]);'), $template);
		$template = preg_replace_callback("/[\n\r\t]*\{date\s+(.+?)\}[\n\r\t]*/i", create_function('$matches', 'return \ilinei\template::date_tags($matches[1]);'), $template);
		$template = preg_replace_callback("/[\n\r\t]*\{code\s+(.+?)\s*\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::code_tags($matches[1]);'), $template);
		$template = preg_replace_callback("/[\n\r\t]*\<\?php\s+(.+?)\s*\\?>[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::code_tags($matches[1]);'), $template);
		$template = preg_replace_callback("/\{timeout\s+(.+?)\}/i", create_function('$matches', 'return \ilinei\template::timeout_tags($matches[1]);'), $template);
		$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?php echo \\1; ?>", $template);
		$template = preg_replace_callback("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::strip_tags(\'<?php echo \'.$matches[1].\'; ?>\');'), $template);

        $template = preg_replace_callback("/[\n\r\t]*\{include\s+([a-z0-9_:\/]+)\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::include_file($matches[1]);'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{include\s+(.+?)\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::include_file($matches[1]);'), $template);

        $template = preg_replace_callback("/[\n\r\t]*\{template\s+([a-z0-9_:\/]+)\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::template($matches[1]);'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::template($matches[1]);'), $template);

		//如果解析中！
		if($this->state){
            $template = preg_replace_callback("/[\n\r\t]*\{block\s+(.+?)\s*\}\s*[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::block_tags($matches[1], '.$this->state.');'), $template);
        }else{
            $template = preg_replace_callback("/[\n\r\t]*\{block\s+(.+?)\s*\}\s*[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::block_tags($matches[1]);'), $template);
        }

        $template = preg_replace_callback("/[\n\r\t]*\{adlog\s+(.+?)\}[\n\r\t]*/i", create_function('$matches', 'return \ilinei\template::adlog_tags($matches[1]);'), $template);
        $template = preg_replace_callback("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/is", create_function('$matches', 'return \ilinei\template::strip_tags($matches[1].\'<?php if (\'.$matches[2].\') { ?>\'.$matches[3]);'), $template);
        $template = preg_replace_callback("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/is", create_function('$matches', 'return \ilinei\template::strip_tags($matches[1].\'<?php } elseif (\'.$matches[2].\') { ?>\'.$matches[3]);'), $template);
        $template = preg_replace("/\{else\}/i", "<?php }else{ ?>", $template);
        $template = preg_replace("/\{\/if\}/i", "<?php } ?>", $template);
        $template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::strip_tags(\'<?php if (is_array(\'.$matches[1].\')) foreach (\'.$matches[1].\' as \'.$matches[2].\') { ?>\');'), $template);
        $template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", create_function('$matches', 'return \ilinei\template::strip_tags(\'<?php if (is_array(\'.$matches[1].\')) foreach (\'.$matches[1].\' as \'.$matches[2].\' => \'.$matches[3].\') { ?>\');'), $template);
        $template = preg_replace("/\{\/loop\}/i", "<?php } ?>", $template);

		$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);

		if($this->json){
            $params = json_decode($this->json, 1);
            foreach($params as $key => $param){
                $template = str_replace('{'.strtoupper($key).'}', $param, $template);
            }
        }

		return $template;
	}
	
	public function parsed($cached = false){
		if($cached && is_file($this->cache_file)) return $this->cache_file;
		
		$template = $this->read_file();
		$template = $this->parsing($template);
		$template = "<?php
//版权所有(C) 2014 www.ilinei.com
if(!defined('INIT')) exit('Access Denied');

//全局变量
\$_var = \$GLOBALS['_var'];
\$config = \$GLOBALS['config'];
\$db = \$GLOBALS['db'];
\$setting = \$GLOBALS['setting'];
\$ADMIN_SCRIPT = \$GLOBALS['ADMIN_SCRIPT'];
\$THEME = \$GLOBALS['THEME'];
\$dispatches = \$GLOBALS['dispatches'];
\$page_title = \$GLOBALS['page_title'];

!\$_ilinei && \$_ilinei = new admin\\model\\_ilinei();
!\$_template && \$_template = new \ilinei\\template(); 
?>\r\n{$template}";
		
		$this->write_file($template);
		
		return $this->cache_file;
	}
}

?>