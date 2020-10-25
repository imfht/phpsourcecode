<?php

/**
 * FerOS PHP template engine
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0.2
 */

namespace feros;

/**
 * 模板解析
 * @author sanliang
 */
class compile {

    public $view;
    private $_block = array(), $_template_preg = array(), $_template_replace = array();

    public function __construct(\feros\view $view, &$content) {
        $this->view = $view;

        $this->compile_layout($content);
        $this->compile_var($content);
        $this->compile_xml();
        $this->compile_php();
        $this->compile_html($content);
        $this->compile_code();

        $content = preg_replace($this->_template_preg, $this->_template_replace, $content);
        $content = preg_replace_callback("/##XML(.*?)XML##/s", array($this, 'xml_substitution'), $content);
        if ($this->view->strip_space) {
            $content = preg_replace(array('~>\s+<~', '~>(\s+\n|\r)~'), array('><', '>'), $content);
            $content = str_replace('?><?php', '', $content);
        }

        return $content;
    }

    private function xml_substitution($capture) {
        return "<?php echo '<?xml " . stripslashes($capture[1]) . " ?>'; ?>";
    }

    /**
     * 解析标签
     * @param array $content
     * @return string
     */
    private function parse_tag($content) {
        $content = stripslashes($content[0]);
        $content = preg_replace_callback('/\$\w+((\.\w+)*)?/', array($this, 'parse_var'), $content);
        return $content;
    }

    /**
     * 解析变量
     * @param array $var
     * @return string
     */
    private function parse_var($var) {
        if (empty($var[0]))
            return;

        $vars = explode('.', $var[0]);
        $var = array_shift($vars);
        $name = $var;
        foreach ($vars as $val)
            $name .= '["' . trim($val) . '"]';
        return $name;
    }

    private function parse_load($content) {
        $file = $content[1];
        $parse = '';
        
        $array = explode(',', $file);
        foreach ($array as $val) {
            $type = $reset = strtolower(substr(strrchr($val, '.'), 1));
            switch ($type) {
                case 'js':
                    $parse .= '<script type="text/javascript" src="' . $val . '"></script>';
                    break;
                case 'css':
                    $parse .= '<link rel="stylesheet" type="text/css" href="' . $val . '" />';
                    break;
                case 'icon':
                    $parse .= '<link rel="shortcut icon" href="' . $val . '" />';
                    break;
                case 'php':
                    $parse .= '<?php include_once("' . $val . '"); ?>';
                    break;
            }
        }
        return $parse;
    }

    /**
     * 解析布局
     * @param array $content
     * @return string
     */
    private function compile_layout(&$content) {
        $find = preg_match('/' . $this->view->__ldel . 'layout\sname=[\'"](.+?)[\'"]\s*?' . $this->view->__rdel . '/is', $content, $matches);
        if ($find) {
            $content = str_replace($matches[0], '', $content);
            preg_replace_callback('/' . $this->view->__ldel . 'block\sname=[\'"](.+?)[\'"]\s*?' . $this->view->__rdel . '(.*?)' . $this->view->__ldel . '\/block' . $this->view->__rdel . '/is', array($this, 'parse_block'), $content);
            $content = $this->replace_block(file_get_contents($this->view->get_template_file($matches[1])));
        } else {
            $content = preg_replace_callback('/' . $this->view->__ldel . 'block\sname=[\'"](.+?)[\'"]\s*?' . $this->view->__rdel . '(.*?)' . $this->view->__ldel . '\/block' . $this->view->__rdel . '/is', function($match) {
                return stripslashes($match[2]);
            }, $content);
        }
        return $content;
    }

    /**
     * 记录当前页面中的block标签
     * @access private
     * @param string $name block名称
     * @param string $content  模板内容
     * @return string
     */
    private function parse_block($name, $content = '') {
        if (is_array($name)) {
            $content = $name[2];
            $name = $name[1];
        }
        $this->_block[$name] = $content;
        return '';
    }

    private function replace_block($content) {
        static $parse = 0;
        $reg = '/(' . $this->view->__ldel . 'block\sname=[\'"](.+?)[\'"]\s*?' . $this->view->__rdel . ')(.*?)' . $this->view->__ldel . '\/block' . $this->view->__rdel . '/is';
        if (is_string($content)) {
            do {
                $content = preg_replace_callback($reg, array($this, 'replace_block'), $content);
            } while ($parse && $parse--);
            return $content;
        } elseif (is_array($content)) {
            if (preg_match('/' . $this->view->__ldel . 'block\sname=[\'"](.+?)[\'"]\s*?' . $this->view->__rdel . '/is', $content[3])) {
                $parse = 1;
                $content[3] = preg_replace_callback($reg, array($this, 'replace_block'), "{$content[3]}{$this->view->__ldel}/block{$this->view->__rdel}");
                return $content[1] . $content[3];
            } else {
                $name = $content[2];
                $content = $content[3];
                $content = isset($this->_block[$name]) ? $this->_block[$name] : $content;
                return $content;
            }
        }
    }

    private function compile_php() {
        if (!$this->view->php_off) {
            $this->_template_preg[] = '/<\?(=|php|)(.+?)\?>/is';
            $this->_template_replace[] = '&lt;?\\1\\2?&gt;';
        } else {
            $this->_template_preg[] = '/(<\?(?!php|=|$))/i';
            $this->_template_replace[] = '<?php echo \'\\1\'; ?>';
        }
    }

    private function compile_xml() {
        $this->_template_preg[] = "/<\?xml(.*?)\?>/s";
        $this->_template_replace[] = "##XML\\1XML##";
    }

    private function compile_var(&$content) {
        $content = preg_replace_callback('/(' . $this->view->__ldel . ')([^\d\s].+?)(' . $this->view->__rdel . ')/is', array($this, 'parse_tag'), $content);
    }

    private function compile_code() {
        $this->_template_preg[] = '/' . $this->view->__ldel . '(else if|elseif) (.*?)' . $this->view->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->view->__ldel . 'for (.*?)' . $this->view->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->view->__ldel . 'while (.*?)' . $this->view->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->view->__ldel . '(loop|foreach) (.*?)' . $this->view->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->view->__ldel . 'if (.*?)' . $this->view->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->view->__ldel . 'else' . $this->view->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->view->__ldel . "(eval|_)( |[\r\n])(.*?)" . $this->view->__rdel . '/is';
        $this->_template_preg[] = '/' . $this->view->__ldel . '_e (.*?)' . $this->view->__rdel . '/is';
        $this->_template_preg[] = '/' . $this->view->__ldel . '_p (.*?)' . $this->view->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->view->__ldel . '\/(if|for|loop|foreach|eval|while)' . $this->view->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->view->__ldel . '((( *(\+\+|--) *)*?\!?(([_a-zA-Z][\w]*\(.*?\))|\$((\w+)((\[|\()(\'|")?\$*\w*(\'|")?(\)|\]))*((->)?\$?(\w*)(\((\'|")?(.*?)(\'|")?\)|))){0,})( *\.?[^ \.]*? *)*?){1,})' . $this->view->__rdel . '/i';
        $this->_template_preg[] = "/(	| ){0,}(\r\n){1,}\";/";
        $this->_template_preg[] = '/' . $this->view->__ldel . '(\#|\*)(.*?)(\#|\*)' . $this->view->__rdel . '/';
        $this->_template_preg[] = '/' . $this->view->__ldel . 'view\sname=[\'"](.+?)[\'"]\s*?' . $this->view->__rdel . '/';
        $this->_template_preg[] = '/' . $this->view->__ldel . 'lang\sname=[\'"](.+?)[\'"]\s*?' . $this->view->__rdel . '/';
        $this->_template_replace[] = '<?php }else if (\\2){ ?>';
        $this->_template_replace[] = '<?php for (\\1) { ?>';
        $this->_template_replace[] = '<?php while (\\1) { ?>';
        $this->_template_replace[] = '<?php foreach (\\2) {?>';
        $this->_template_replace[] = '<?php if (\\1){ ?>';
        $this->_template_replace[] = '<?php }else{ ?>';
        $this->_template_replace[] = '<?php \\3; ?>';
        $this->_template_replace[] = '<?php echo \\1; ?>';
        $this->_template_replace[] = '<?php print_r(\\1); ?>';
        $this->_template_replace[] = '<?php } ?>';
        $this->_template_replace[] = '<?php echo \\1;?>';
        $this->_template_replace[] = '';
        $this->_template_replace[] = '';
        $this->_template_replace[] = '<?php echo $this->fetch("\\1");?>';
        $this->_template_replace[] = '<?php echo $this->lang("\\1");?>';
    }

    private function compile_html(&$content) {
        $content = preg_replace_callback('/' . $this->view->__ldel . 'load\shref=[\'"](.+?)[\'"]\s*?' . $this->view->__rdel . '/is', array($this, 'parse_load'), $content);
 
        /**
         * $this->_template_preg[] = '/' . $this->view->__ldel . 'load\shref=[\'"](.+?)[\'"]\s*?' . $this->view->__rdel . '/';
         * $this->_template_replace[] = '<?php echo $this->parse_load("\\1");?>';
         */
    }

}
