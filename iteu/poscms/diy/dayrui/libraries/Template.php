<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Template {

    public $ci; // ci控制器对象
    public $cron; //执行计划任务代码
    public $mobile; // 是否是手机访问

    public $_dir; // 模板目录
    public $_tname; // 判断是否是手机端目录

    public $_cache; // 模板缓存目录

    public $_root; // 默认前端项目模板目录
    public $_mroot; // 默认会员项目模板目录

    public $_root_array; // 默认前端项目模板目录,pc+移动
    public $_mroot_array; // 默认会员项目模板目录,pc+移动
    public $_module_root_array; // 默认模块前端模板目录,pc+移动

    public $_aroot; // 默认后台项目模板目录

    public $_options; // 模板变量
    public $_filename; // 主模板名称
    public $_include_file; // 引用计数
    public $pagination; // 自定义分页查询
    public $pos_order; // 是否包含有地图定位的排序

    /**
     * 构造函数
     */

    public function __construct() {

        // 关闭自动运行任务js
        $this->cron = FALSE;
        // 默认主项目模板目录
        $this->_root_array = array(
            'pc' => is_dir(TPLPATH.'pc/web/'.SITE_TEMPLATE.'/common/') ? TPLPATH.'pc/web/'.SITE_TEMPLATE.'/common/' : FCPATH.'dayrui/templates/'.SITE_TEMPLATE.'/',
            'mobile' => is_dir(TPLPATH.'mobile/web/'.SITE_TEMPLATE.'/common/') ? TPLPATH.'mobile/web/'.SITE_TEMPLATE.'/common/' : FCPATH.'dayrui/mobiles/'.SITE_TEMPLATE.'/',
        );
        // 默认会员项目模板目录
        $this->_mroot_array = array(
            'pc' => is_dir(TPLPATH.'pc/member/'.MEMBER_TEMPLATE.'/common/') ? TPLPATH.'pc/member/'.MEMBER_TEMPLATE.'/common/' : FCPATH.'module/member/templates/member/'.MEMBER_TEMPLATE.'/',
            'mobile' => is_dir(TPLPATH.'mobile/member/'.MEMBER_TEMPLATE.'/common/') ? TPLPATH.'mobile/member/'.MEMBER_TEMPLATE.'/common/' : FCPATH.'module/member/mobiles/member/'.MEMBER_TEMPLATE.'/',
        );
        // 默认后台模板目录
        $this->_aroot = FCPATH.'dayrui/templates/admin/';
        // 模板缓存目录
        $this->_cache = WEBPATH.'cache/templates/';

        // 模板选择
        $this->mobile = IS_MOBILE;
        $this->_tname = $this->mobile ? 'mobile' : 'pc';

        // 当前项目模板目录
        if (IS_ADMIN) { // 后台
            $this->_dir = APPPATH.'templates/admin/';
        } elseif (IS_MEMBER) { // 会员
            $this->_root = $this->_root_array[$this->_tname];
            $this->_mdir = $this->_dir = $this->_mroot = $this->_mroot_array[$this->_tname];
            if (APP_DIR != 'member') { // 模块和应用的会员目录
                $name = is_dir(FCPATH.'app/'.APP_DIR.'/') ? 'app' : 'module';
                $this->_module_root_array = array(
                    'pc' => is_dir(TPLPATH.'pc/member/'.SITE_TEMPLATE.'/'.APP_DIR.'/') ? TPLPATH.'pc/member/'.SITE_TEMPLATE.'/'.APP_DIR.'/' : FCPATH.$name.'/'.APP_DIR.'/templates/member/'.SITE_TEMPLATE.'/',
                    'mobile' => is_dir(TPLPATH.'mobile/member/'.SITE_TEMPLATE.'/'.APP_DIR.'/') ? TPLPATH.'mobile/member/'.SITE_TEMPLATE.'/'.APP_DIR.'/' : FCPATH.$name.'/'.APP_DIR.'/mobiles/member/'.SITE_TEMPLATE.'/',
                );
                $this->_dir = $this->_module_root_array[$this->_tname];
            }
        } elseif (APP_DIR && is_dir(FCPATH.'app/'.APP_DIR.'/')) { // 应用
            $this->_root = $this->_root_array[$this->_tname];
            $this->_module_root_array = array(
                'pc' => is_dir(TPLPATH.'pc/web/'.SITE_TEMPLATE.'/'.APP_DIR.'/') ? TPLPATH.'pc/web/'.SITE_TEMPLATE.'/'.APP_DIR.'/' : FCPATH.'app/'.APP_DIR.'/templates/'.SITE_TEMPLATE.'/',
                'mobile' => is_dir(TPLPATH.'mobile/web/'.SITE_TEMPLATE.'/'.APP_DIR.'/') ? TPLPATH.'mobile/web/'.SITE_TEMPLATE.'/'.APP_DIR.'/' : FCPATH.'app/'.APP_DIR.'/mobiles/'.SITE_TEMPLATE.'/',
            );
            $this->_dir = $this->_module_root_array[$this->_tname];
        } else { // 首页前端页面
            $this->_dir = $this->_root = $this->_root_array[$this->_tname];
            $this->cron = SYS_CRON_QUEUE  ? TRUE : FALSE; // 开启任务js

        }

    }

    /**
     * 输出模板
     *
     * @param	string	$_name		模板文件名称（含扩展名）
     * @param	string	$_dir		模块名称
     * @return  void
     */
    public function display($_name, $_dir = NULL) {

        // 处理变量
        $this->_options['ci'] = $this->ci;
        extract($this->_options, EXTR_PREFIX_SAME, 'data');
        $this->_options = NULL;
        $this->_filename = $_name;

        // 加载编译后的缓存文件
        include $this->load_view_file($this->get_file_name($_name, $_dir));

        // 消毁变量
        $this->_include_file = NULL;
    }

    /**
     * 设置模块/应用的模板目录
     *
     * @param	string	$file		文件名
     * @param	string	$dir		模块/应用名称
     * @param	string	$include	是否使用的是include标签
     */
    public function get_file_name($file, $dir = NULL, $include = FALSE) {

        if (IS_ADMIN || $dir == 'admin') {
            // 后台操作时，不需要加载风格目录，如果文件不存在可以尝试调用主项目模板
            if (@is_file($this->_dir.$file)) {
                return $this->_dir.$file; // 调用当前后台的模板
            } elseif (@is_file($this->_aroot.$file)) {
                return $this->_aroot.$file; // 当前项目目录模板不存在时调用主项目的
            }
            $error = $this->_dir.$file;
        } elseif (IS_MEMBER || $dir == 'member') {
            // 会员操作时，需要加载风格目录，如果文件不存在可以尝试调用主项目模板
            if ($dir === '/' && is_file($this->_root.$file)) {
                return $this->_root.$file;
            } elseif (is_file($this->_dir.$file)) {
                // 调用当前的会员模块目录
                return $this->_dir.$file;
            } elseif ($this->_mdir && is_file($this->_mdir.$file)) {
                // 调用模块或者应用的会员目录
                return $this->_mdir.$file;
            } elseif (is_file($this->_mroot.$file)) {
                // 调用默认的会员模块目录
                return $this->_mroot.$file;
            }
            $error = $dir === '/' ? $this->_root.$file : $this->_dir.$file;
        } elseif ($file == 'go') {
            // 转向字段模板
            return $this->_aroot.'go.html';
        } else {
            if ($dir === '/' && is_file($this->_root.$file)) {
                // 强制主目录
                return $this->_root.$file;
            } else if (@is_file($this->_dir.$file)) {
                // 调用本目录
                return $this->_dir.$file;
            } else if (@is_file($this->_root.$file)) {
                // 再次调用主程序下的文件
                return $this->_root.$file;
            }
            $error = $dir === '/' ? $this->_root.$file : $this->_dir.$file;
        }

        $this->mobile && get_cookie('mobile') && set_cookie('mobile', 0);

        // 如果移动端模板不存在就调用主网站风格
        if ($this->mobile && is_file(str_replace('/mobile/', '/pc/', $error))) {
            return str_replace('/mobile/', '/pc/', $error);
        } elseif ($file == 'msg.html' && is_file(TPLPATH.'pc/web/default/common/msg.html')) {
            return TPLPATH.'pc/web/default/common/msg.html';
        }elseif ($file == 'msg.html' && is_file(FCPATH.'dayrui/templates/default/msg.html')) {
            return FCPATH.'dayrui/templates/default/msg.html';
        }

        show_error('模板文件 ('.(SYS_DEBUG ? $error : basename($error)).') 不存在', 500, '模板解析错误');
    }

    /**
     * 强制设置为后台模板目录
     */
    public function admin() {
        $this->_dir = $this->_mdir = APPPATH.'templates/admin/';
        $this->_mroot = $this->_root = FCPATH.'dayrui/templates/admin/'; // 默认后台模板目录
    }

    /**
     * 强制设置模会员中心模板目录
     *
     * @param	string	$dir	模板名称
     */
    public function space($dir) {
        $this->_dir = $this->_root = $this->_mroot = WEBPATH.'statics/space/'.$dir.'/';
    }

    /**
     * 强制设置模块模板目录
     *
     * @param	string	$dir	模板名称
     */
    public function module($dir) {

        if (IS_ADMIN || IS_MEMBER) {
            return NULL;
        }

        $module = $this->ci->dir ? $this->ci->dir : MOD_DIR;

        // 默认模板目录
        $this->_module_root_array = array(
            'pc' => is_dir(TPLPATH.'pc/web/'.$dir.'/'.$module.'/') ? TPLPATH.'pc/web/'.$dir.'/'.$module.'/' : FCPATH.'module/'.$module.'/templates/'.$dir.'/',
            'mobile' => is_dir(TPLPATH.'mobile/web/'.$dir.'/'.$module.'/') ? TPLPATH.'mobile/web/'.$dir.'/'.$module.'/' : FCPATH.'module/'.$module.'/mobiles/'.$dir.'/',
        );

        $this->_root = $this->_root_array[$this->_tname];
        $this->_dir = $this->_module_root_array[$this->_tname];
    }

    /**
     * 强制设置指定目录的模板目录
     *
     * @param	string	$path	模板目录
     * @param	string	$dir	模块目录
     */
    public function set_template($path, $dir = MOD_DIR) {

    }

    /**
     * 设置模板变量
     */
    public function assign($key, $value = NULL) {

        if (!$key) {
            return FALSE;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->_options[$k] = $v;
            }
        } else {
            $this->_options[$key] = $value;
        }
    }

    /**
     * 获取模板变量
     */
    public function get_value($key) {

        if (!$key) {
            return NULL;
        }

        return $this->_options[$key];
    }

    /**
     * 模板标签include/template
     *
     * @param	string	$name	模板文件
     * @param	string	$dir	应用、模块目录
     * @return  bool
     */
    public function _include($name, $dir = NULL) {

        $dir = $dir == 'MOD_DIR' ? MOD_DIR : $dir;
        $file = $this->get_file_name($name, $dir, TRUE);
        $fname = md5($file);
        $this->_include_file[$fname] ++;

        $this->_include_file[$fname] > 50 && show_error('模板文件 ('.str_replace(WEBPATH, '/', $file).') 标签template引用文件目录结构错误', 500, '模板结构错误');

        return $this->load_view_file($file);
    }

    /**
     * 模板标签load
     *
     * @param	string	$file	模板文件
     * @return  bool
     */
    public function _load($file) {

        $fname = md5($file);
        $this->_include_file[$fname] ++;

        $this->_include_file[$fname] > 50 && show_error('模板文件 ('.str_replace(WEBPATH, '/', $file).') 标签load引用文件目录结构错误', 500, '模板结构错误');

        return $this->load_view_file($file);
    }

    /**
     * 加载
     *
     * @param	string
     * @return  string
     */
    public function load_view_file($name) {

        $cache_file = $this->_cache.str_replace(array(WEBPATH, '/', '\\', DIRECTORY_SEPARATOR), array('', '.', '.', '.'), $name).($this->mobile ? '.mobile.' : '').'.cache.php';

        // 当缓存文件不存在时或者缓存文件创建时间少于了模板文件时,再重新生成缓存文件
        if (!is_file($cache_file) || (is_file($cache_file) && is_file($name) && filemtime($cache_file) < filemtime($name))) {
            $content = $this->handle_view_file(file_get_contents($name));
            // 执行任务队列代码
            !$this->mobile && $this->cron && basename($name) == basename($this->_filename) && $content.= '
<script type="text/javascript">
$.ajax({  
	type: "GET",  
	async: false,  
	url:"' . SITE_URL . 'index.php?c=cron",  
	dataType: "jsonp",
	success: function(json){ },  
	error: function(){ }  
});  
</script>';
            @file_put_contents($cache_file, $content, LOCK_EX) === FALSE && show_error('请将模板缓存目录（/cache/templates/）权限设为777', 404, '无写入权限');
        }

        return $cache_file;
    }

    // 将模板代码转化为php
    public function code2php($code) {

        $file = md5($code).'.code.php';
        !is_file($this->_cache.$file) && @file_put_contents($this->_cache.$file, str_replace('$this->', '$this->ci->template->', $this->handle_view_file($code)));

        return $this->_cache.$file;
    }

    /**
     * 解析模板文件
     *
     * @param	string
     * @param	string
     * @return  string
     */
    public function handle_view_file($view_content) {

        if (!$view_content) {
            return '';
        }

        // 兼容性替换
        $view_content = str_replace(
            array(
                '$member.group.name',
                '$member.level.name',
                '$member.level.stars',
                '$space.group.name',
                '$space.level.name',
                '$space.level.stars',
            ),
            array(
                '$member.groupname',
                '$member.levelname',
                '$member.levelstars',
                '$space.groupname',
                '$space.levelname',
                '$space.levelstars',
            ),
            $view_content
        );

        // 正则表达式匹配的模板标签
        $regex_array = array(
            // 站点缓存数据变量
            '#{([A-Z\-]+)\.(.+)}#U',
            // 3维数组变量
            '#{\$(\w+?)\.(\w+?)\.(\w+?)\.(\w+?)}#i',
            // 2维数组变量
            '#{\$(\w+?)\.(\w+?)\.(\w+?)}#i',
            // 1维数组变量
            '#{\$(\w+?)\.(\w+?)}#i',
            // 3维数组变量
            '#\$(\w+?)\.(\w+?)\.(\w+?)\.(\w+?)#Ui',
            // 2维数组变量
            '#\$(\w+?)\.(\w+?)\.(\w+?)#Ui',
            // 1维数组变量
            '#\$(\w+?)\.(\w+?)#Ui',
            // PHP函数
            '#{([a-z_0-9]+)\((.*)\)}#Ui',
            // PHP常量
            '#{([A-Z_]+)}#',
            // PHP变量
            '#{\$(.+?)}#i',
            // 引入模板
            '#{\s*template\s+"([\$\-_\/\w\.]+)",\s*"(.+)"\s*}#Uis',
            '#{\s*template\s+"([\$\-_\/\w\.]+)",\s*MOD_DIR\s*}#Uis',
            '#{\s*template\s+"([\$\-_\/\w\.]+)"\s*}#Uis',
            '#{\s*template\s+([\$\-_\/\w\.]+)\s*}#Uis',
            // 加载指定文件到模板
            '#{\s*load\s+"([\$\-_\/\w\.]+)"\s*}#Uis',
            '#{\s*load\s+([\$\-_\/\w\.]+)\s*}#Uis',
            // php标签
            '#{php\s+(.+?)}#is',
            // list标签
            '#{list\s+(.+?)return=(.+?)\s?}#i',
            '#{list\s+(.+?)\s?}#i',
            '#{\s?\/list\s?}#i',
            // if判断语句
            '#{\s?if\s+(.+?)\s?}#i',
            '#{\s?else\sif\s+(.+?)\s?}#i',
            '#{\s?else\s?}#i',
            '#{\s?\/if\s?}#i',
            // 循环语句
            '#{\s?loop\s+\$(.+?)\s+\$(\w+?)\s?\$(\w+?)\s?}#i',
            '#{\s?loop\s+\$(.+?)\s+\$(\w+?)\s?}#i',
            '#{\s?loop\s+\$(.+?)\s+\$(\w+?)\s?=>\s?\$(\w+?)\s?}#i',
            '#{\s?\/loop\s?}#i',
            // 结束标记
            '#{\s?php\s?}#i',
            '#{\s?\/php\s?}#i',
            '#\?\>\s*\<\?php\s#s',
        );

        // 替换直接变量输出
        $replace_array = array(
            "<?php \$cache = \$this->_cache_var('\\1'); eval('echo \$cache'.\$this->_get_var('\\2').';');unset(\$cache); ?>",
            "<?php echo \$\\1['\\2']['\\3']['\\4']; ?>",
            "<?php echo \$\\1['\\2']['\\3']; ?>",
            "<?php echo \$\\1['\\2']; ?>",
            "\$\\1['\\2']['\\3']['\\4']",
            "\$\\1['\\2']['\\3']",
            "\$\\1['\\2']",
            "<?php echo \\1(\\2); ?>",
            "<?php echo \\1; ?>",
            "<?php echo \$\\1; ?>",
            "<?php if (\$fn_include = \$this->_include(\"\\1\", \"\\2\")) include(\$fn_include); ?>",
            "<?php if (\$fn_include = \$this->_include(\"\\1\", \"MOD_DIR\")) include(\$fn_include); ?>",
            "<?php if (\$fn_include = \$this->_include(\"\\1\")) include(\$fn_include); ?>",
            "<?php if (\$fn_include = \$this->_include(\"\\1\")) include(\$fn_include); ?>",
            "<?php if (\$fn_include = \$this->_load(\"\\1\")) include(\$fn_include); ?>",
            "<?php if (\$fn_include = \$this->_load(\"\\1\")) include(\$fn_include); ?>",
            "<?php \\1 ?>",
            "<?php \$return_\\2 = \$this->list_tag(\"\\1 return=\\2\"); if (\$return_\\2) extract(\$return_\\2); \$count_\\2=count(\$return_\\2); if (is_array(\$return_\\2)) { foreach (\$return_\\2 as \$key_\\2=>\$\\2) { ?>",
            "<?php \$return = \$this->list_tag(\"\\1\"); if (\$return) extract(\$return); \$count=count(\$return); if (is_array(\$return)) { foreach (\$return as \$key=>\$t) { ?>",
            "<?php } } ?>",
            "<?php if (\\1) { ?>",
            "<?php } else if (\\1) { ?>",
            "<?php } else { ?>",
            "<?php } ?>",
            "<?php if (is_array(\$\\1)) { \$count=count(\$\\1);foreach (\$\\1 as \$\\2=>\$\\3) { ?>",
            "<?php if (is_array(\$\\1)) { \$count=count(\$\\1);foreach (\$\\1 as \$\\2) { ?>",
            "<?php if (is_array(\$\\1)) { \$count=count(\$\\1);foreach (\$\\1 as \$\\2=>\$\\3) { ?>",
            "<?php } } ?>",
            "<?php ",
            " ?>",
            " ",
        );

        $view_content = preg_replace($regex_array, $replace_array, $view_content);

        // 兼容php5.5
        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            $view_content = preg_replace_callback("/_get_var\('(.*)'\)/Ui", 'php55_replace_cache_array', $view_content);
            $view_content = preg_replace_callback("/list_tag\(\"(.*)\"\)/Ui", 'php55_replace_array', $view_content);
        } else {
            $view_content = preg_replace("/_get_var\('(.*)'\)/Uie", "\$this->_replace_cache_array('\\1')", $view_content);
            $view_content = preg_replace("/list_tag\(\"(.*)\"\)/Uie", "\$this->_replace_array('\\1')", $view_content);
        }

        return $view_content;
    }

    // 替换cache标签中的单引号数组
    public function _replace_cache_array($string) {
        return "_get_var('" . preg_replace('#\[\'(\w+)\'\]#Ui', '.\\1', $string) . "')";
    }

    // 替换list标签中的单引号数组
    public function _replace_array($string) {
        return "list_tag(\"" . preg_replace('#\[\'(\w+)\'\]#Ui', '[\\1]', $string) . "\")";
    }

    // list 标签解析
    public function list_tag($_params) {

        if (!$this->ci) {
            return NULL;
        }

        $system = array(
            'oot' => '', // 过期商品
            'num' => '', // 显示数量
            'form' => '', // 表单
            'page' => '', // 是否分页
            'site' => '', // 站点id
            'flag' => '', // 推荐位id
            'more' => '', // 是否显示栏目附加表
            'catid' => '', // 栏目id，支持多id
            'field' => '', // 显示字段
            'order' => '', // 排序
            'space' => '', // 空间uid
            'table' => '', // 表名变量
            'join' => '', // 关联表名
            'on' => '', // 关联表条件
            'cache' => (int)SYS_CACHE_LIST, // 默认缓存时间
            'action' => '', // 动作标识
            'return' => '', // 返回变量
            'sbpage' => '', // 不按默认分页
            'module' => MOD_DIR, // 模块名称
            'modelid' => '', // 模型id
            'keyword' => '', // 关键字
            'urlrule' => '', // 自定义分页规则
            'pagesize' => '', // 自定义分页数量
        );
        $param = $where = array();
        $params = explode(' ', $_params);
        $sysadj = array('IN', 'BEWTEEN', 'BETWEEN', 'LIKE', 'NOTIN', 'NOT', 'BW');
        foreach ($params as $t) {
            $var = substr($t, 0, strpos($t, '='));
            $val = substr($t, strpos($t, '=') + 1);
            if (!$var) {
                continue;
            }
            $val = defined($val) ? constant($val) : $val;
            if ($var == 'fid' && !$val) {
                continue;
            }
            if (isset($system[$var])) { // 系统参数，只能出现一次，不能添加修饰符
                $system[$var] = $val;
            } else {
                if (preg_match('/^([A-Z_]+)(.+)/', $var, $match)) { // 筛选修饰符参数
                    $_pre = explode('_', $match[1]);
                    $_adj = '';
                    foreach ($_pre as $p) {
                        in_array($p, $sysadj) && $_adj = $p;
                    }
                    $where[] = array(
                        'adj' => $_adj,
                        'name' => $match[2],
                        'value' => $val
                    );
                } else {
                    $where[] = array(
                        'adj' => '',
                        'name' => $var,
                        'value' => $val
                    );
                }
                $param[$var] = $val; // 用于特殊action
            }
        }

        // 替换order中的非法字符
        isset($system['order']) && $system['order'] && $system['order'] = str_ireplace(
            array('"', "'", ')', '(', ';', 'select', 'insert'),
            '',
            $system['order']
        );

        $action = $system['action'];
        // 当hits动作时，定位到moule动作
        $system['action'] == 'hits' && $system['action'] = 'module';

        // action
        switch ($system['action']) {

            case 'cache': // 系统缓存数据

                if (!isset($param['name'])) {
                    return $this->_return($system['return'], 'name参数不存在');
                }

                $pos = strpos($param['name'], '.');
                if ($pos !== FALSE) {
                    $_name = substr($param['name'], 0, $pos);
                    $_param = substr($param['name'], $pos + 1);
                } else {
                    $_name = $param['name'];
                    $_param = NULL;
                }

                $cache = $this->_cache_var($_name, !$system['site'] ? SITE_ID : $system['site']);
                if (!$cache) {
                    return $this->_return($system['return'], "缓存({$_name})不存在，请在后台更新缓存");
                }

                if ($_param) {
                    $data = array();
                    @eval('$data=$cache'.$this->_get_var($_param).';');
                    if (!$data) {
                        return $this->_return($system['return'], "缓存({$_name})参数不存在!!");
                    }
                } else {
                    $data = $cache;
                }

                return $this->_return($system['return'], $data, '');
                break;

            case 'content': // 模块文档内容

                if (!isset($param['id'])) {
                    return $this->_return($system['return'], 'id参数不存在');
                }
                $param['id'] = intval($param['id']);
                $dirname = $system['module'] ? $system['module'] : MOD_DIR;
                if (!$dirname) {
                    return $this->_return($system['return'], 'module参数不能为空');
                }

                $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $module = get_module($dirname, $system['site']);
                if (!$module) {
                    return $this->_return($system['return'], "模块({$system['module']})未安装");
                }

                // 定义的模块内容模型类
                if (!class_exists('Content_model')) {
                    $file = FCPATH.'module/'.$module['dirname'].'/models/Content_model.php';
                    if (!is_file($file)) {
                        return $this->_return($system['return'], "模块({$system['module']})文件models/Content_model.php不存在");
                    }
                    require_once $file;
                }

                $db = new Content_model();
                $db->link = $this->ci->site[$system['site']];
                $db->prefix = $this->ci->db->dbprefix($system['site'].'_'.$module['dirname']);

                // 缓存查询结果
                $data = $param['extend'] ? $db->get_extend($param['id']) : $db->get($param['id']);
                $page = max(1, (int)$_GET['page']);
                $name = 'list-action-content-'.$dirname.'-'.md5(dr_array2string($param).dr_array2string($system)).'-'.$page;
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    if ($param['extend']) {
                        $fields = $module['extend'];
                    } else {
                        $fields = $module['field'];
                        $fields = $module['category'][$data['catid']]['field'] ? array_merge($fields, $module['category'][$data['catid']]['field']) : $fields;
                    }
                    // 模块表的系统字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    $fields['updatetime'] = array('fieldtype' => 'Date');
                    // 格式化数据
                    $data = $this->ci->field_format_value($fields, $data, $page, $module['dirname']);
                    if ($system['field'] && $data) {
                        $_field = explode(',', $system['field']);
                        foreach ($data as $i => $t) {
                            if (strpos($i, '_') !== 0 && !in_array($i, $_field)) {
                                unset($data[$i]);
                            }
                        }
                    }
                    // 格式化显示自定义字段内容
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], array($cache), '');
                break;

            case 'category': // 栏目

                $dirname = $system['module'] ? $system['module'] : MOD_DIR;
                if (!$dirname) {
                    return $this->_return($system['return'], 'module参数不能为空');
                }

                $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $module = get_module($dirname, $system['site']);
                if (!$module || count($module['category']) == 0) {
                    return $this->_return($system['return'], "模块({$system['module']})尚未安装");
                }

                $i = 0;
                $show = isset($param['show']) ? 1 : 0; // 有show参数表示显示隐藏栏目
                $return = array();
                foreach ($module['category'] as $t) {
                    if ($system['num'] && $i >= $system['num']) {
                        break;
                    } elseif (!$t['show'] && !$show) {
                        continue;
                    } elseif (isset($param['pid']) && $t['pid'] != (int)$param['pid']) {
                        continue;
                    } elseif (isset($param['mid']) && $t['mid'] != $param['mid']) {
                        continue;
                    } elseif (isset($param['child']) && $t['child'] != (int)$param['child']) {
                        continue;
                    } elseif (isset($param['letter']) && $t['letter'] != $param['letter']) {
                        continue;
                    } elseif (isset($param['id']) && !in_array($t['id'], explode(',', $param['id']))) {
                        continue;
                    } elseif (isset($system['more']) && !$system['more']) {
                        unset($t['field'], $t['setting']);
                    }
                    $return[] = $t;
                    $i ++;
                }

                if (!$return) {
                    return $this->_return($system['return'], '没有匹配到内容');
                }

                return $this->_return($system['return'], $return, '');
                break;

            case 'linkage': // 联动菜单

                $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $linkage = $this->ci->get_cache('linkage-'.$system['site'].'-'.$param['code']);
                if (!$linkage) {
                    return $this->_return($system['return'], "联动菜单{$param['code']}不存在，请在后台更新缓存");
                }

                // 通过别名找id
                $ids = @array_flip($this->ci->get_cache('linkage-'.$system['site'].'-'.$param['code'].'-id'));
                if (isset($param['pid'])) {
                    if (is_numeric($param['pid'])) {
                        $pid = intval($param['pid']);
                    } else {
                        $pid = isset($ids[$param['pid']]) ? $ids[$param['pid']] : 0;
                        !$pid && is_numeric($param['pid']) && $this->ci->get_cache('linkage-'.$system['site'].'-'.$param['code'].'-id', $param['pid']) && $pid = intval($param['pid']);
                    }
                }

                $i = 0;
                $return = array();
                foreach ($linkage as $t) {
                    if ($system['num'] && $i >= $system['num']) {
                        break;
                    } elseif (isset($param['pid']) && $t['pid'] != $pid) {
                        continue;
                    } elseif (isset($param['id']) && !in_array($t['id'], explode(',', $param['id']))) {
                        continue;
                    }
                    $return[] = $t;
                    $i ++;
                }

                if (!$return && isset($param['pid'])) {
                    $rpid = isset($param['fid']) ? (int)$ids[$param['fid']] : (int)$linkage[$param['pid']]['pid'];
                    foreach ($linkage as $t) {
                        if ($t['pid'] == $rpid) {
                            if ($system['num'] && $i >= $system['num']) {
                                break;
                            }
                            if (isset($param['id']) && !in_array($t['id'], explode(',', $param['id']))) {
                                continue;
                            }
                            $return[] = $t;
                            $i ++;
                        }
                    }
                    if (!$return) {
                        return $this->_return($system['return'], '没有匹配到内容');
                    }
                }

                return $this->_return($system['return'], isset($param['call']) && $param['call'] ? @array_reverse($return) : $return, '');
                break;

            case 'search_field': // 搜索字段筛选

                $catid = $system['catid'];
                $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $module = get_module($system['module'] ? $system['module'] : MOD_DIR, $system['site']);
                if (!$module || count($module['category'][$catid]['field']) == 0) {
                    return $this->_return($system['return'], '模块未安装或者此栏目无附加字段');
                }

                $return = array();
                foreach ($module['category'][$catid]['field'] as $t) {
                    if ($t['issearch'] && $t['ismain'] && ($t['fieldtype'] == 'Select'
                            || $t['fieldtype'] == 'Radio')) {
                        $data = @explode(PHP_EOL, $t['setting']['option']['options']);
                        if ($data) {
                            $list = array();
                            foreach ($data as $c) {
                                list($name, $value) = @explode('|', $c);
                                $name && !is_null($value) && $list[] = array(
                                    'name' => trim($name),
                                    'value' => trim($value)
                                );
                            }
                            $list && $return[] = array(
                                'name' => $t['name'],
                                'field' => $t['fieldname'],
                                'data' => $list,
                            );
                        }
                    }
                }

                return $this->_return($system['return'], $return, '');
                break;

            case 'navigator': // 网站导航

                $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $navigator = $this->ci->get_cache('navigator-'.$system['site']); // 导航缓存
                if (!$navigator) {
                    return $this->_return($system['return'], '导航数据为空');
                }

                $i = 0;
                $show = isset($param['show']) ? 1 : 0; // 有show参数表示显示隐藏栏目
                $data = $navigator[(int) $param['type']];
                if (!$data) {
                    // 没有查询到内容
                    return $this->_return($system['return'], '没有查询到内容');
                }

                $return = array();
                foreach ($data as $t) {
                    if ($system['num'] && $i >= $system['num']) {
                        break;
                    } elseif (isset($param['pid']) && $t['pid'] != (int)$param['pid']) {
                        continue;
                    } elseif (isset($param['id']) && $t['id'] != (int)$param['id']) {
                        continue;
                    } elseif (!$t['show'] && !$show) {
                        continue;
                    }
                    $return[] = $t;
                    $i ++;
                }

                if (!$return) {
                    return $this->_return($system['return'], '没有匹配到内容');
                }

                return $this->_return($system['return'], $return, '');
                break;

            case 'page': // 单页调用

                $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $name = $system['module'] ? $system['module'] : 'index';
                $data = $this->ci->get_cache('page-'.$system['site'], 'data', $name); // 单页缓存
                if (!$data) {
                    return $this->_return($system['return'], '没有查询到内容');
                }

                $i = 0;
                $show = isset($param['show']) ? 1 : 0; // 有show参数表示显示隐藏栏目
                $field = $this->ci->dcache->get('page-field-'.$system['site']);
                $return = array();
                foreach ($data as $id => $t) {
                    if (!is_numeric($id)) {
                        continue;
                    } elseif ($system['num'] && $i >= $system['num']) {
                        break;
                    } elseif (!$t['show'] && !$show) {
                        continue;
                    } elseif (isset($param['pid']) && $t['pid'] != (int) $param['pid']) {
                        continue;
                    } elseif (isset($param['id']) && !in_array($t['id'], explode(',', $param['id']))) {
                        continue;
                    }
                    $t['setting'] = dr_string2array($t['setting']);
                    $return[] = $this->ci->field_format_value($field, $t, 1);
                    $i ++;
                }

                if (!$return) {
                    return $this->_return($system['return'], '没有匹配到内容');
                }

                return $this->_return($system['return'], $return, '');
                break;

            case 'related': // 相关文章

                $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $module = get_module($system['module'] ? $system['module'] : MOD_DIR, $system['site']);
                if (!$module) {
                    return $this->_return($system['return'], "模块({$system['module']})未安装"); // 没有模块数据时返回空
                }
                if (!$param['tag']) {
                    return $this->_return($system['return'], '没有查询到内容'); // 没有查询到内容
                }

                $where = array();

                $array = explode(',', $param['tag']);
                foreach ($array as $name) {
                    $name && $where[] = '(`title` LIKE "%'.$this->ci->db->escape_str($name).'%" OR `keywords` LIKE "%'.$this->ci->db->escape_str($name).'%")';
                }

                // 栏目筛选
                if ($system['catid']) {
                    $cat_where = '';
                    if (strpos($system['catid'], ',') !== FALSE) {
                        $temp = @explode(',', $system['catid']);
                        if ($temp) {
                            $catids = array();
                            foreach ($temp as $i) {
                                $catids = $module['category'][$i]['child'] ? array_merge($catids, $module['category'][$i]['catids']) : array_merge($catids, array($i));
                            }
                            $catids && $cat_where = '`catid` IN ('.implode(',', $catids).')';
                            unset($catids);
                        }
                        unset($temp);
                    } elseif ($module['category'][$system['catid']]['child']) {
                        $cat_where = '`catid` IN ('.$module['category'][$system['catid']]['childids'].')';
                    } else {
                        $cat_where = '`catid` = '.(int)$system['catid'];
                    }
                    $cat_where && $where = $cat_where.' AND ('.implode(' OR ', $where).')';
                } else {
                    $where = implode(' OR ', $where);
                }

                $where = '('.$where.') AND `status`=9';
                $table = $this->ci->db->dbprefix($system['site'].'_'.$module['dirname']); // 模块主表
                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM {$table} WHERE {$where} ORDER BY updatetime DESC LIMIT ".($system['num'] ? $system['num'] : 10);
                $data = $this->_query($sql, $system['site'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-sql-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    $fields = $module['field'];
                    // 模块表的系统字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    $fields['updatetime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    foreach ($data as $i => $t) {
                        $data[$i] = $this->ci->field_format_value($fields, $t, 1, $module['dirname']);
                    }
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql);
                break;

            case 'tag': // 调用tag

                $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $module = get_module($system['module'] ? $system['module'] : MOD_DIR, $system['site']);
                if (!$module) {
                    // 没有模块数据时返回空
                    return $this->_return($system['return'], "模块({$system['module']})未安装");
                }

                $system['order'] = $system['order'] ? ($system['order'] == 'rand' ? 'RAND()' : $system['order']) : 'hits desc';

                $table = $this->ci->db->dbprefix($system['site'].'_'.$module['dirname'].'_tag'); // tag表
                $sql = "SELECT id,name,code,hits FROM {$table} ORDER BY ".$system['order']." LIMIT ".($system['num'] ? $system['num'] : 10);
                $data = $this->_query($sql, $system['site'], $system['cache']);

                // 没有查询到内容
                if (!$data) {
                    return $this->_return($system['return'], '没有查询到内容');
                }

                // 缓存查询结果
                $name = 'list-action-tag-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache) {
                    foreach ($data as $i => $t) {
                        $data[$i]['url'] = dr_tag_url($module, $t['name']);
                    }
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql);
                break;

            case 'sql': // 直接sql查询

                if (preg_match('/sql=\'(.+)\'/sU', $_params, $sql)) {

                    // 默认站点参数
                    $system['site'] = !$system['site'] ? SITE_ID : $system['site'];

                    // 数据源的选择
                    if (isset($param['db']) && $param['db']) {
                        $db = $this->ci->load->database((string)$param['db'], TRUE);
                    } elseif ($system['site']) {
                        $db = $this->ci->site[$system['site']];
                    } else {
                        $db = $system['module'] ? $this->ci->site[$system['site']] : $this->ci->db;
                    }

                    // 替换前缀
                    $sql = str_replace(
                        array('@#S', '@#'),
                        array($db->dbprefix.$system['site'], $db->dbprefix),
                        trim($sql[1])
                    );
                    if (stripos($sql, 'SELECT') !== 0) {
                        return $this->_return($system['return'], 'SQL语句只能是SELECT查询语句');
                    }

                    $total = 0;
                    $pages = '';

                    // 如存在分页条件才进行分页查询
                    if ($system['page'] && $system['urlrule']) {
                        $page = max(1, (int)$_GET['page']);
                        $row = $this->_query(preg_replace('/select \* from/iUs', 'SELECT count(*) as c FROM', $sql), $system['site'], $system['cache'], FALSE);
                        $total = (int)$row['c'];
                        $pagesize = $system['pagesize'] ? $system['pagesize'] : 10;
                        // 没有数据时返回空
                        if (!$total) {
                            return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                        }
                        $sql.= ' LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                        $pages = $this->_get_pagination(str_replace('[page]', '{page}', urldecode($system['urlrule'])), $pagesize, $total);
                    }

                    $data = $this->_query($sql, $system['site'], $system['cache']);
                    $fields = NULL;

                    if ($system['module']
                        && $module = get_module($system['module'], $system['site'])) {
                        $fields = $module['field']; // 模块主表的字段
                    } elseif ($system['modelid']
                        && $model = $this->ci->get_cache('space-model', $system['modelid'])) {
                        $fields = $model['field']; // 空间模型的字段
                    }

                    if ($fields) {
                        // 缓存查询结果
                        $name = 'list-action-sql-'.md5($sql);
                        $cache = $this->ci->get_cache_data($name);
                        if (!$cache && is_array($data)) {
                            // 模块表的系统字段
                            $fields['inputtime'] = array('fieldtype' => 'Date');
                            $fields['updatetime'] = array('fieldtype' => 'Date');
                            // 格式化显示自定义字段内容
                            foreach ($data as $i => $t) {
                                $data[$i] = $this->ci->field_format_value($fields, $t, 1, isset($module['dirname']) && $module['dirname'] ? $module['dirname'] : '');
                            }
                            //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                            $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                        }
                        $data = $cache;
                    }
                    return $this->_return($system['return'], $data, $sql, $total, $pages, $pagesize);
                } else {
                    return $this->_return($system['return'], '参数不正确，SQL语句必须用单引号包起来'); // 没有查询到内容
                }
                break;

            case 'table': // 表名查询

                if (!$system['table']) {
                    return $this->_return($system['return'], 'table参数不存在');
                }

                // 默认站点参数
                $system['site'] = !$system['site'] ? SITE_ID : $system['site'];

                $tableinfo = $this->ci->get_cache('table');
                if (!$tableinfo) {
                    $this->ci->load->model('system_model');
                    $tableinfo = $this->ci->system_model->cache(); // 表结构缓存
                }
                if (!$tableinfo) {
                    return $this->_return($system['return'], '表结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）'); // 没有表结构缓存时返回空
                }

                $table = $this->ci->db->dbprefix($system['table']); // 主表
                if (!isset($tableinfo[$table]['field'])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                $where = $this->_set_where_field_prefix($where, $tableinfo[$table]['field'], $table); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table]['field'], $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table]['field'], $table); // 给排序字段加上表前缀

                $total = 0;
                $sql_from = $table; // sql的from子句

                // 关联表
                if ($system['join'] && $system['on']) {
                    $table2 = $this->ci->db->dbprefix($system['join']); // 关联表
                    if (!$tableinfo[$table2]) {
                        return $this->_return($system['return'], '关联数据表（'.$table2.'）不存在');
                    }
                    list($a, $b) = explode(',', $system['on']);
                    $b = $b ? $b : $a;
                    $sql_from.= ' LEFT JOIN '.$table2.' ON `'.$table.'`.`'.$a.'`=`'.$table2.'`.`'.$b.'`';
                }

                $sql_limit = $pages = '';
                $sql_where = $this->_get_where($where); // sql的where子句

                if ($system['page'] && $system['urlrule']) {
                    $page = max(1, (int)$_GET['page']);
                    $urlrule = str_replace('[page]', '{page}', urldecode($system['urlrule']));
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, $system['site'], $system['cache'], FALSE);
                    $total = (int)$row['c'];
                    // 没有数据时返回空
                    if (!$total) {
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination($urlrule, $pagesize, $total);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] ? "ORDER BY {$system['order']}" : "")." $sql_limit";
                $data = $this->_query($sql, $system['site'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-table-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;

            case 'comment': // 评论查询

                // 默认站点参数
                $system['site'] = !$system['site'] ? SITE_ID : $system['site'];

                // 判断评论类型的主表名称
                if (isset($system['module']) && $system['module']) {
                    $table = $this->ci->db->dbprefix($system['site'].'_'.$system['module'].'_comment_data_0');
                } elseif (isset($param['extend']) && $param['extend']) {
                    $table = $this->ci->db->dbprefix($system['site'].'_'.$param['extend'].'_extend_comment_data_0');
                    unset($param['extend']);
                } elseif (isset($system['space']) && $system['space']) {
                    $table = $this->ci->db->dbprefix('space_comment_data_0');
                    $system['site'] = 0;
                } elseif (isset($param['model']) && $param['model']) {
                    $model = $this->ci->get_cache('space-model', $param['model']);
                    if (!$model) {
                        return $this->_return($system['return'], "空间模型({$param['model']})未安装"); // 没有模型数据时返回空
                    }
                    $table = $this->ci->db->dbprefix('space_'.$model['table']); // 模块主表
                    $system['site'] = 0;
                    unset($param['model']);
                } else {
                    return $this->_return($system['return'], "缺少关键参数");
                }

                $tableinfo = $this->ci->get_cache('table');
                if (!$tableinfo) {
                    $this->ci->load->model('system_model');
                    $tableinfo = $this->ci->system_model->cache(); // 表结构缓存
                }

                if (!isset($tableinfo[$table]['field'])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                $where = array();
                $where[] = array(
                    'adj' => '',
                    'name' => 'status',
                    'value' => 1
                );

                // 按内容id查询
                if (isset($param['cid']) && $param['cid']) {
                    $where[] = array(
                        'adj' => '',
                        'name' => 'cid',
                        'value' => (int)$param['cid']
                    );
                    unset($param['cid']);
                }

                // 是否查询回复
                if (isset($param['all']) && $param['all']) {
                    unset($param['all']);
                } else {
                    $where[] = array(
                        'adj' => '',
                        'name' => 'reply',
                        'value' => 0
                    );
                }

                if (!$system['order']) {
                    $system['order'] = 'inputtime_desc';
                }

                $where = $this->_set_where_field_prefix($where, $tableinfo[$table]['field'], $table); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table]['field'], $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table]['field'], $table); // 给排序字段加上表前缀

                $total = 0;
                $sql_from = $table; // sql的from子句
                $sql_limit = $pages = '';
                $sql_where = $this->_get_where($where); // sql的where子句

                if ($system['page'] && $system['urlrule']) {
                    $page = max(1, (int)$_GET['page']);
                    $urlrule = str_replace('[page]', '{page}', urldecode($system['urlrule']));
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, $system['site'], $system['cache'], FALSE);
                    $total = (int)$row['c'];
                    // 没有数据时返回空
                    if (!$total) {
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination($urlrule, $pagesize, $total);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] ? "ORDER BY {$system['order']}" : "")." $sql_limit";
                $data = $this->_query($sql, $system['site'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-comment-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    if (1 == $system['more']) {
                        foreach ($data as $i => $t) {
                            $data[$i]['replys'] = $t['in_reply'] ? $this->_query("SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from WHERE `reply`=".$t['id']." ORDER BY `inputtime` DESC", $system['site'], $system['cache']) : array();
                        }
                    }
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;

            case 'model': // 空间模型

                $uid = (int)$system['space'];
                $mid = (int)$system['modelid'];
                if (!$mid) {
                    return $this->_return($system['return'], 'modelid参数必须存在，请在后台更新缓存'); // 参数判断
                }
                $model = $this->ci->get_cache('space-model', $mid);
                if (!$model) {
                    return $this->_return($system['return'], "空间模型({$system['modelid']})未安装"); // 没有模型数据时返回空
                }
                $tableinfo = $this->ci->get_cache('table');
                if (!$tableinfo) {
                    $this->ci->load->model('system_model');
                    $tableinfo = $this->ci->system_model->cache(); // 表结构缓存
                }
                if (!$tableinfo) {
                    return $this->_return($system['return'], '表结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）'); // 没有表结构缓存时返回空
                }
                $system['order'] = !$system['order'] ? 'updatetime' : $system['order']; // 默认排序参数
                if ($uid) {
                    $where[] = array(
                        'adj' => '',
                        'name' => 'uid',
                        'value' => $uid
                    );
                    if (isset($system['catid']) && $system['catid']) {
                        $this->ci->load->add_package_path(FCPATH.'module/space/');
                        $this->ci->load->model('space_category_model');
                        $category = $this->ci->space_category_model->get_data(0, $uid, 1);
                        // 栏目id集合不存在时则重新修复栏目数据
                        !$category[$system['catid']]['childids'] && $this->ci->space_category_model->repair($uid);
                        $where[] = array(
                            'adj' => $category[$system['catid']]['child'] ? 'IN' : '',
                            'name' => 'catid',
                            'value' => $category[$system['catid']]['child'] ? $category[$system['catid']]['childids'] : $system['catid']
                        );
                    }
                }

                $fields = $model['field']; // 主表的字段
                $table = $this->ci->db->dbprefix('space_'.$model['table']); // 模块主表
                if (!isset($tableinfo[$table]['field'])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }
                $where = $this->_set_where_field_prefix($where, $tableinfo[$table]['field'], $table, $fields); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table]['field'], $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table]['field'], $table); // 给排序字段加上表前缀

                $total = 0;
                $sql_from = $table; // sql的from子句
                $sql_limit = $pages = '';
                $sql_where = $this->_get_where($where); // sql的where子句

                // 当前作者不缓存
                $this->ci->uid == $uid && $system['cache'] = 0;

                if ($system['page'] && $system['urlrule']) {
                    $page = max(1, (int)$_GET['page']);
                    $urlrule = str_replace('[page]', '{page}', urldecode($system['urlrule']));
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, 0, $system['cache'], FALSE);
                    $total = (int) $row['c'];
                    // 没有数据时返回空
                    if (!$total) {
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination($urlrule, $pagesize, $total);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] ? "ORDER BY {$system['order']}" : "")." $sql_limit";
                $data = $this->_query($sql, 0, $system['cache']);

                // 缓存查询结果
                $name = 'list-action-space-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    // 模块表的系统字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    $fields['updatetime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    foreach ($data as $i => $t) {
                        $data[$i] = $this->ci->field_format_value($fields, $t);
                    }
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;

            case 'space_category': // 空间栏目信息

                $i = 0;
                $uid = (int)$system['space'];
                $show = isset($param['show']) ? 1 : 0; // 有show参数表示显示隐藏栏目
                $return = array();
                $this->ci->load->add_package_path(FCPATH.'module/space/');
                $this->ci->load->model('space_category_model');
                $category = $this->ci->space_category_model->get_data(0, $uid, 1);
                foreach ($category as $t) {
                    if ($system['num'] && $i >= $system['num']) {
                        break;
                    } elseif (!$t['show'] && !$show) {
                        continue;
                    } elseif (isset($param['pid']) && $t['pid'] != (int)$param['pid']) {
                        continue;
                    } elseif (isset($param['child']) && $t['child'] != (int)$param['child']) {
                        continue;
                    } elseif (isset($param['letter']) && $t['letter'] != $param['letter']) {
                        continue;
                    } elseif (isset($param['id']) && !in_array($t['id'], explode(',', $param['id']))) {
                        continue;
                    } elseif (isset($system['more']) && !$system['more']) {
                        unset($t['field'], $t['setting']);
                    }
                    $return[] = $t;
                    $i ++;
                }

                if (!$return) {
                    return $this->_return($system['return'], '没有匹配到内容');
                }

                return $this->_return($system['return'], $return, '');
                break;

            case 'space_content': // 空间模型文档内容

                $id = (int)$param['id'];
                $mid = (int)$system['modelid'];
                if (!$id) {
                    return $this->_return($system['return'], 'id参数不存在');
                }
                if (!$mid) {
                    return $this->_return($system['return'], 'modelid参数必须存在，请在后台更新缓存'); // 参数判断
                }

                // 模型缓存
                $model = $this->ci->get_cache('space-model', $mid);
                if (!$model) {
                    return $this->_return($system['return'], "空间模型({$system['modelid']})未安装"); // 没有模型数据时返回空
                }

                // 模型表名称和缓存
                $name = $this->ci->db->dbprefix('space_'.$model['table']).'-space-show-'.$id;
                $data = $this->ci->get_cache_data($name);
                $time = $system['cache'] ? $system['cache'] : 36000;
                if ($time && !$data) {
                    $data = $this->ci->db->where('id', $id)->get('space_'.$model['table'])->row_array();
                    $this->ci->set_cache_data($name, $data, $time);
                }

                // 格式化输出自定义字段
                $fields = $model['field'];
                $fields['inputtime'] = array('fieldtype' => 'Date');
                $fields['updatetime'] = array('fieldtype' => 'Date');
                $data = $this->ci->field_format_value($fields, $data, max(1, (int)$_GET['page']));

                // 输出返回字段
                $field = $system['field'] ? explode(',', $system['field']) : NULL;
                if ($field) {
                    $temp = $data;
                    $data = array();
                    foreach ($field as $i) {
                        $data[$i] = isset($temp[$i]) ? $temp[$i] : '';
                    }
                }

                return $this->_return($system['return'], array($data), '');
                break;

            case 'extend': // 子内容调用

                $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $dirname = $system['module'] ? $system['module'] : MOD_DIR;
                if (!$dirname) {
                    return $this->_return($system['return'], 'module参数不能为空');
                }
                $module = get_module($dirname, $system['site']);
                if (!$module) {
                    return $this->_return($system['return'], "模块({$dirname})未安装"); // 没有模块数据时返回空
                }
                $tableinfo = $this->ci->get_cache('table');
                if (!$tableinfo) {
                    $this->ci->load->model('system_model');
                    $tableinfo = $this->ci->system_model->cache(); // 表结构缓存
                }
                if (!$tableinfo) {
                    return $this->_return($system['return'], '表结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）'); // 没有表结构缓存时返回空
                }

                $where[] = array( 'adj' => '', 'name' => 'status', 'value' => 9);
                $fields = $module['extend']; // 主表的字段
                $system['order'] = !$system['order'] ? 'inputtime asc' : $system['order']; // 默认排序参数
                $table = $this->ci->db->dbprefix($system['site'].'_'.$module['dirname'].'_extend'); // 表名称
                if (!isset($tableinfo[$table]['field'])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                $where = $this->_set_where_field_prefix($where, $tableinfo[$table]['field'], $table, $fields); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table]['field'], $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table]['field'], $table); // 给排序字段加上表前缀

                // 栏目筛选
                if ($system['catid']) {
                    if (strpos($system['catid'], ',') !== FALSE) {
                        $temp = @explode(',', $system['catid']);
                        if ($temp) {
                            $catids = array();
                            foreach ($temp as $i) {
                                $catids = $module['category'][$i]['child'] ? array_merge($catids, $module['category'][$i]['catids']) : array_merge($catids, array($i));
                            }
                            $catids && $where[] = array(
                                'adj' => 'IN',
                                'name' => 'catid',
                                'value' => implode(',', $catids),
                            );
                            unset($catids);
                        }
                        unset($temp);
                    } elseif ($module['category'][$system['catid']]['child']) {
                        $where[] = array(
                            'adj' => 'IN',
                            'name' => 'catid',
                            'value' => $module['category'][$system['catid']]['childids']
                        );
                    } else {
                        $where[] = array(
                            'adj' => '',
                            'name' => 'catid',
                            'value' => (int)$system['catid']
                        );
                    }
                }

                $total = 0;
                $sql_from = $table; // sql的from子句
                $sql_limit = $pages = '';
                $sql_where = $this->_get_where($where); // sql的where子句

                if ($system['page'] && $system['urlrule']) {
                    $page = max(1, (int)$_GET['page']);
                    $urlrule = str_replace('[page]', '{page}', urldecode($system['urlrule']));
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, 0, $system['cache'], FALSE);
                    $total = (int) $row['c'];
                    // 没有数据时返回空
                    if (!$total) {
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination($urlrule, $pagesize, $total);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] ? "ORDER BY {$system['order']}" : "")." $sql_limit";
                $data = $this->_query($sql, $system['site'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-extend-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    // 表的系统字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    foreach ($data as $i => $t) {
                        $data[$i] = $this->ci->field_format_value($fields, $t, 1, $module['dirname']);
                    }
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;

            case 'form': // 表单调用

                $mid = $system['form'];
                $site = $system['site'] ? $system['site'] : SITE_ID;

                // 表单参数为数字时按id读取
                if (is_numeric($mid)) {
                    $form = $this->ci->get_cache('form-'.$site, $mid);
                } else {
                    $form = $this->ci->get_cache('form-name-'.$site, $mid);
                }

                // 判断是否存在
                if (!$form) {
                    return $this->_return($system['return'], "表单($mid)不存在"); // 参数判断
                }

                // 表结构缓存
                $tableinfo = $this->ci->get_cache('table');
                if (!$tableinfo) {
                    $this->ci->load->model('system_model');
                    $tableinfo = $this->ci->system_model->cache();
                }
                if (!$tableinfo) {
                    return $this->_return($system['return'], '表结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）'); // 没有表结构缓存时返回空
                }

                $table = $this->ci->db->dbprefix($site.'_form_'.$form['table']); // 主表
                if (!isset($tableinfo[$table]['field'])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                // 将catid作为普通字段
                if (isset($system['catid']) && $system['catid']) {
                    $where[] = array(
                        'adj' => '',
                        'name' => 'catid',
                        'value' => $system['catid']
                    );
                }

                $fields = $form['field'];
                $system['order'] = !$system['order'] ? 'inputtime desc' : $system['order']; // 默认排序参数
                $table = $this->ci->db->dbprefix($site.'_form_'.$form['table']); // 表单表名称
                $where = $this->_set_where_field_prefix($where, $tableinfo[$table]['field'], $table, $fields); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table]['field'], $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table]['field'], $table); // 给排序字段加上表前缀

                $total = 0;
                $fields = $form['field']; // 主表的字段
                $sql_from = $table; // sql的from子句
                $sql_limit = $pages = '';
                $sql_where = $this->_get_where($where); // sql的where子句

                if ($system['page'] && $system['urlrule']) {
                    $page = max(1, (int)$_GET['page']);
                    $urlrule = str_replace('[page]', '{page}', urldecode($system['urlrule']));
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, $site, $system['cache'], FALSE);
                    $total = (int)$row['c'];
                    // 没有数据时返回空
                    if (!$total) {
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination($urlrule, $pagesize, $total);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] ? "ORDER BY {$system['order']}" : "")." $sql_limit";
                $data = $this->_query($sql, 0, $system['cache']);

                // 缓存查询结果
                $name = 'list-action-form-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    // 表的系统字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    foreach ($data as $i => $t) {
                        $data[$i] = $this->ci->field_format_value($fields, $t);
                    }
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;

            case 'mform': // 模块表单调用

                $site = $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $dirname = $system['module'] ? $system['module'] : MOD_DIR;
                if (!$dirname) {
                    return $this->_return($system['return'], 'module参数不能为空');
                }
                $module = get_module($dirname, $system['site']);
                if (!$module) {
                    return $this->_return($system['return'], "模块({$dirname})未安装"); // 没有模块数据时返回空
                }

                $fid = $system['form'];
                $form = $module['form'][$fid];
                if (!$form) {
                    return $this->_return($system['return'], "模块表单($fid)不存在"); // 参数判断
                }

                $tableinfo = $this->ci->get_cache('table');
                if (!$tableinfo) {
                    $this->ci->load->model('system_model');
                    $tableinfo = $this->ci->system_model->cache(); // 表结构缓存
                }
                if (!$tableinfo) {
                    return $this->_return($system['return'], '表结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）'); // 没有表结构缓存时返回空
                }

                $fields = $form['fields'];
                $system['order'] = !$system['order'] ? 'inputtime' : $system['order']; // 默认排序参数

                $table = $this->ci->db->dbprefix($site.'_'.$module['dirname'].'_form_'.$form['table']); // 表单表名称
                if (!isset($tableinfo[$table]['field'])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                $where = $this->_set_where_field_prefix($where, $tableinfo[$table]['field'], $table, $fields); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table]['field'], $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table]['field'], $table); // 给排序字段加上表前缀

                $total = NULL;
                $fields = $form['field']; // 主表的字段
                $sql_from = $table; // sql的from子句
                $sql_where = $this->_get_where($where); // sql的where子句
                $sql_limit = $pages = '';

                if ($system['page'] && $system['urlrule']) {
                    $page = max(1, (int)$_GET['page']);
                    $urlrule = str_replace('[page]', '{page}', urldecode($system['urlrule']));
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, $site, $system['cache'], FALSE);
                    $total = (int)$row['c'];
                    // 没有数据时返回空
                    if (!$total) {
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination($urlrule, $pagesize, $total);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] ? "ORDER BY {$system['order']}" : "")." $sql_limit";
                $data = $this->_query($sql, $site, $system['cache']);

                // 缓存查询结果
                $name = 'list-action-mform-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    // 表的系统字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    foreach ($data as $i => $t) {
                        $data[$i] = $this->ci->field_format_value($fields, $t, 1, $module['dirname']);
                    }
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;

            case 'member': // 会员信息

                $tableinfo = $this->ci->get_cache('table');
                if (!$tableinfo) {
                    $this->ci->load->model('system_model');
                    $tableinfo = $this->ci->system_model->cache(); // 表结构缓存
                }
                if (!$tableinfo) {
                    // 没有表结构缓存时返回空
                    return $this->_return($system['return'], '表结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                $table = $this->ci->db->dbprefix('member'); // 主表
                if (!isset($tableinfo[$table]['field'])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                $system['order'] = !$system['order'] ? 'uid' : $system['order']; // 默认排序参数

                $fields = $this->ci->get_cache('member', 'field');
                $where = $this->_set_where_field_prefix($where, $tableinfo[$table]['field'], $table, $fields); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table]['field'], $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table]['field'], $table); // 给排序字段加上表前缀

                $sql_from = $table; // sql的from子句

                if ($system['more']) { // 会员附表
                    $more = $this->ci->db->dbprefix('member_data'); // 附表
                    $where = $this->_set_where_field_prefix($where, $tableinfo[$more]['field'], $more, $fields); // 给条件字段加上表前缀
                    $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$more]['field'], $more); // 给显示字段加上表前缀
                    $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$more]['field'], $more); // 给排序字段加上表前缀
                    $sql_from.= " LEFT JOIN $more ON `$table`.`uid`=`$more`.`uid`"; // sql的from子句
                }

                $total = 0;
                $sql_limit = '';
                $sql_where = $this->_get_where($where); // sql的where子句


                if ($system['page'] && $system['urlrule']) { // 如存在分页条件才进行分页查询
                    $page = max(1, (int)$_GET['page']);
                    $urlrule = str_replace('[page]', '{page}', urldecode($system['urlrule']));
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $row = $this->_query("SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL", $system['site'], $system['cache'], FALSE);
                    $total = (int)$row['c'];
                    if (!$total) {
                        // 没有数据时返回空
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = ' LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination(str_replace('[page]', '{page}', $urlrule), $pagesize, $total);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] == "null" ? "" : " ORDER BY {$system['order']}")." $sql_limit";
                $data = $this->_query($sql, $system['site'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-member-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    // 系统字段
                    $fields['regtime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    foreach ($data as $i => $t) {
                        $data[$i] = $this->ci->field_format_value($fields, $t);
                    }
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;

            case 'space': // 空间数据

                $tableinfo = $this->ci->get_cache('table');
                if (!$tableinfo) {
                    $this->ci->load->model('system_model');
                    $tableinfo = $this->ci->system_model->cache(); // 表结构缓存
                }
                if (!$tableinfo) {
                    return $this->_return($system['return'], '表结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）'); // 没有表结构缓存时返回空
                }

                $table = $this->ci->db->dbprefix('space'); // 空间主表
                if (!isset($tableinfo[$table]['field'])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                $system['order'] = !$system['order'] ? 'regtime' : $system['order']; // 默认排序参数

                if ($system['keyword']) {
                    $where[] = array(
                        'adj' => 'LIKE',
                        'name' => 'name',
                        'value' => '%'.$system['keyword'].'%'
                    );
                }

                $where[] = array(
                    'adj' => '',
                    'name' => 'status',
                    'value' => 1
                );

                $fields = $this->ci->get_cache('member', 'spacefield');
                $where = $this->_set_where_field_prefix($where, $tableinfo[$table]['field'], $table, $fields); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table]['field'], $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table]['field'], $table); // 给排序字段加上表前缀

                $sql_from = $table; // sql的from子句

                if ($system['more']) { // 会员附表
                    $more = $this->ci->db->dbprefix('member_data'); // 附表
                    $where = $this->_set_where_field_prefix($where, $tableinfo[$more]['field'], $more, $fields); // 给条件字段加上表前缀
                    $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$more]['field'], $more); // 给显示字段加上表前缀
                    $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$more]['field'], $more); // 给排序字段加上表前缀
                    $sql_from.= " LEFT JOIN $more ON `$table`.`uid`=`$more`.`uid`"; // sql的from子句
                }

                if ($system['more'] == 2) { // 会员主表
                    $more2 = $this->ci->db->dbprefix('member'); // 附表
                    $where = $this->_set_where_field_prefix($where, $tableinfo[$more2]['field'], $more2, $fields); // 给条件字段加上表前缀
                    $system['field'] = $system['field'] ? $this->_set_select_field_prefix($system['field'], $tableinfo[$more2]['field'], $more2) : "`{$table}`.*".($more ? ",`{$more}`.*" : "").",`{$more2}`.`username`,`{$more2}`.`groupid`,`{$more2}`.`uid`";
                    $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$more2]['field'], $more2); // 给排序字段加上表前缀
                    $sql_from.= " LEFT JOIN $more2 ON `$table`.`uid`=`$more2`.`uid`"; // sql的from子句
                }

                $total = 0;
                $sql_limit = '';
                $sql_where = $this->_get_where($where); // sql的where子句

                if ($system['flag']) { // 推荐位调用
                    $_ids = $this->_query("select uid from {$table}_flag where `flag`=".(int) $system['flag'], $system['site'], $system['cache']);
                    $in = array();
                    foreach ($_ids as $t) {
                        $in[] = $t['uid'];
                    }
                    if (!$in) {
                        // 没有查询到内容
                        return $this->_return($system['return'], '没有查询到推荐位内容');
                    }
                    $sql_where = ($sql_where ? $sql_where.' AND' : '')." `$table`.`uid` IN (".implode(',', $in).")";
                }

                if ($system['page'] && $system['urlrule']) { // 如存在分页条件才进行分页查询
                    $page = max(1, (int)$_GET['page']);
                    $urlrule = str_replace('[page]', '{page}', urldecode($system['urlrule']));
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, $system['site'], $system['cache'], FALSE);
                    $total = (int)$row['c'];
                    if (!$total) {
                        // 没有数据时返回空
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = ' LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination(str_replace('[page]', '{page}', $urlrule), $pagesize, $total);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] == "null" ? "" : " ORDER BY {$system['order']}")." $sql_limit";
                $data = $this->_query($sql, $system['site'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-space-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    // 系统字段
                    $fields['regtime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    foreach ($data as $i => $t) {
                        $data[$i] = $this->ci->field_format_value($fields, $t);
                    }
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;

            case 'module': // 模块数据

                $system['module'] = $dirname = $system['module'] ? $system['module'] : MOD_DIR;
                if (!$dirname) {
                    return $this->_return($system['return'], 'module参数不能为空');
                }

                $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $module = get_module($dirname, $system['site']);
                if (!$module) {
                    // 没有模块数据时返回空
                    return $this->_return($system['return'], "模块({$dirname})未安装");
                }

                $tableinfo = $this->ci->get_cache('table');
                if (!$tableinfo) {
                    $this->ci->load->model('system_model');
                    $tableinfo = $this->ci->system_model->cache(); // 表结构缓存
                }

                if (!$tableinfo) {
                    // 没有表结构缓存时返回空
                    return $this->_return($system['return'], '表结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                $table = $this->ci->db->dbprefix($system['site'].'_'.$module['dirname']); // 模块主表
                if (!isset($tableinfo[$table]['field'])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                // 排序操作
                if (!$system['order']
                    && $where[0]['adj'] == 'IN'
                    && $where[0]['name'] == 'id') {
                    // 按id序列来排序
                    $system['order'] = strlen($where[0]['value']) < 10000 && $where[0]['value'] ? 'instr("'.$where[0]['value'].'", `'.$table.'`.`id`)' : 'NULL';
                } else {
                    !$system['order'] && $system['order'] = $system['flag'] ? 'updatetime_desc' : $action == 'hits' ? 'hits' : 'updatetime'; // 默认排序参数
                }

                // 栏目筛选
                if ($system['catid']) {
                    if (strpos($system['catid'], ',') !== FALSE) {
                        $temp = @explode(',', $system['catid']);
                        if ($temp) {
                            $catids = array();
                            foreach ($temp as $i) {
                                $catids = $module['category'][$i]['child'] ? array_merge($catids, $module['category'][$i]['catids']) : array_merge($catids, array($i));
                            }
                            $catids && $where[] = array(
                                'adj' => 'IN',
                                'name' => 'catid',
                                'value' => implode(',', $catids),
                            );
                            unset($catids);
                        }
                        unset($temp);
                    } elseif ($module['category'][$system['catid']]['child']) {
                        $where[] = array(
                            'adj' => 'IN',
                            'name' => 'catid',
                            'value' => $module['category'][$system['catid']]['childids']
                        );
                    } else {
                        $where[] = array(
                            'adj' => '',
                            'name' => 'catid',
                            'value' => (int)$system['catid']
                        );
                    }
                }

                $fields = $module['field']; // 主表的字段
                $where[] = array( 'adj' => '', 'name' => 'status', 'value' => 9);
                $where = $this->_set_where_field_prefix($where, $tableinfo[$table]['field'], $table, $fields); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table]['field'], $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table]['field'], $table); // 给排序字段加上表前缀

                // sql的from子句
                if ($action == 'hits') {
                    $sql_from = '`'.$table.'` LEFT JOIN `'.$table.'_hits` ON `'.$table.'`.`id`=`'.$table.'_hits`.`id`';
                    $table_more = $table.'_hits'; // hits表
                    $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table_more]['field'], $table_more); // 给显示字段加上表前缀
                    $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table_more]['field'], $table_more); // 给排序字段加上表前缀
                } else {
                    $sql_from = '`'.$table.'`';
                }

                // 关联栏目附加表
                if ($system['more']) {
                    $_catid = (int)$system['catid'];
                    if (isset($module['category'][$_catid]['field'])
                        && $module['category'][$_catid]['field']) {
                        $fields = array_merge($fields, $module['category'][$_catid]['field']);
                        $table_more = $table.'_category_data'; // 栏目附加表
                        $where = $this->_set_where_field_prefix($where, $tableinfo[$table_more]['field'], $table_more, $fields); // 给条件字段加上表前缀
                        $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table_more]['field'], $table_more); // 给显示字段加上表前缀
                        $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table_more]['field'], $table_more); // 给排序字段加上表前缀
                        $sql_from.= " LEFT JOIN $table_more ON `$table_more`.`id`=`$table`.`id`"; // sql的from子句
                    }
                }

                // 关联表
                if ($system['join'] && $system['on']) {
                    $table_more = $this->ci->db->dbprefix($system['join']); // 关联表
                    if (!$tableinfo[$table_more]) {
                        return $this->_return($system['return'], '关联数据表（'.$table_more.'）不存在');
                    }
                    list($a, $b) = explode(',', $system['on']);
                    $b = $b ? $b : $a;
                    $where = $this->_set_where_field_prefix($where, $tableinfo[$table_more]['field'], $table_more); // 给条件字段加上表前缀
                    $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table_more]['field'], $table_more); // 给显示字段加上表前缀
                    $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table_more]['field'], $table_more); // 给排序字段加上表前缀
                    $sql_from.= ' LEFT JOIN `'.$table_more.'` ON `'.$table.'`.`'.$a.'`=`'.$table_more.'`.`'.$b.'`';
                }

                $total = 0;
                $sql_limit = $pages = '';
                $sql_where = $this->_get_where($where, $fields); // sql的where子句

                // 商品有效期
                isset($fields['order_etime']) && ($system['oot'] ? $sql_where.= ' AND `order_etime` BETWEEN 1 AND '.SYS_TIME : $sql_where.= ' AND NOT (`order_etime` BETWEEN 1 AND '.SYS_TIME.')');

                // 推荐位调用
                if ($dirname != 'share' && $system['flag']) {
                    $_w = strpos($system['flag'], ',') ? '`flag` IN ('.$system['flag'].')' : '`flag`='.(int)$system['flag'];
                    $_i = $this->_query("select id from {$table}_flag where ".$_w, $system['site'], $system['cache']);
                    $in = array();
                    foreach ($_i as $t) {
                        $in[] = $t['id'];
                    }
                    // 没有查询到内容
                    if (!$in) {
                        return $this->_return($system['return'], '没有查询到推荐位内容');
                    }
                    $sql_where = ($sql_where ? $sql_where.' AND' : '')."`$table`.`id` IN (".implode(',', $in).")";
                    unset($_w, $_i, $in);
                }

                if ($system['page']) {
                    $page = max(1, (int)$_GET['page']);
                    if (is_numeric($system['catid'])) {
                        $urlrule = $this->mobile ? dr_mobile_category_url($module['dirname'],  $system['catid'], '{page}') : dr_category_url($module, $module['category'][$system['catid']], '{page}');
                        $pagesize = $system['pagesize'] ? (int)$system['pagesize'] : (int)$module['category'][$system['catid']]['setting']['template']['pagesize'];
                    }
                    if ($system['sbpage'] || !$urlrule){
                        $urlrule = str_replace('[page]', '{page}', urldecode($system['urlrule']));
                        $pagesize = (int)$system['pagesize'];
                    }
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, $system['site'], $system['cache'], FALSE);
                    $total = (int)$row['c'];
                    // 没有数据时返回空
                    if (!$total) {
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination($urlrule, $pagesize, $total);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".$this->_get_select_field($system['field'] ? $system['field'] : '*')." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "").($system['order'] == "null" ? "" : " ORDER BY {$system['order']}")." $sql_limit";
                $data = $this->_query($sql, $system['site'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-module-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    // 模块表的系统字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    $fields['updatetime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    foreach ($data as $i => $t) {
                        $data[$i] = $this->ci->field_format_value($fields, $t, 1, $module['dirname']);
                    }
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache ? $cache : $data, $sql, $total, $pages, $pagesize);
                break;

            case 'search': // 模块的搜索

                $total = (int)$param['total'];

                // 没有数据时返回空
                if (!$total) {
                    return $this->_return($system['return'], 'total参数为空', '', 0);
                }

                $dirname = $system['module'] ? $system['module'] : MOD_DIR;
                if (!$dirname) {
                    return $this->_return($system['return'], 'module参数不能为空');
                }

                // 没有id时返回空
                if (!$param['id']) {
                    return $this->_return($system['return'], 'id参数为空', '', 0);
                }

                $system['site'] = !$system['site'] ? SITE_ID : $system['site']; // 默认站点参数
                $module = get_module($dirname, $system['site']);
                if (!$module) {
                    // 没有模块数据时返回空
                    return $this->_return($system['return'], "模块({$dirname})未安装");
                }

                $tableinfo = $this->ci->get_cache('table');
                if (!$tableinfo) {
                    $this->ci->load->model('system_model');
                    $tableinfo = $this->ci->system_model->cache(); // 表结构缓存
                }

                if (!$tableinfo) {
                    // 没有表结构缓存时返回空
                    return $this->_return($system['return'], '表结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                $table = $this->ci->db->dbprefix($system['site'].'_'.$module['dirname']); // 模块主表
                if (!isset($tableinfo[$table]['field'])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在（排查方式：后台-系统-数据备份，查看是否显示正常的表结构）');
                }

                $fields = $module['field']; // 主表的字段
                $sql_from = $table; // sql的from子句
                $system['catid'] = intval($system['catid']);

                // 排序操作
                $system['order'] = $this->_set_order_field_prefix(
                    $system['order'] ? $system['order'] : 'updatetime',
                    $tableinfo[$table]['field'],
                    $table
                ); // 给排序字段加上表前缀

                // 关联栏目附加表
                if ($system['more']
                    && isset($module['category'][$system['catid']]['field'])
                    && $module['category'][$system['catid']]['field']) {
                    $fields = array_merge($fields, $module['category'][$system['catid']]['field']);
                    $table_more = $table.'_category_data'; // 栏目附加表
                    $sql_from.= " LEFT JOIN $table_more ON `$table_more`.`id`=`$table`.`id`"; // sql的from子句
                    $system['order'] = $this->_set_order_field_prefix(
                        $system['order'],
                        $tableinfo[$table_more]['field'],
                        $table_more
                    ); // 给排序字段加上表前缀
                }

                $sql_limit = $pages = '';
                $sql_where = '`'.$table.'`.`id` IN(SELECT `cid` FROM `'.$table.'_search_index` WHERE `id`="'.$param['id'].'")'; // sql的where子句

                // 搜索分页
                $page = max(1, (int)$_GET['page']);
                $urlrule = str_replace('[page]', '{page}', urldecode($system['urlrule']));
                $pagesize = (int)$system['pagesize'];
                $pagesize = $pagesize ? $pagesize : 10;
                $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                $pages = $this->_get_pagination($urlrule, $pagesize, $total);

                $sql = "SELECT ".$this->_get_select_field($system['field'] ? $system['field'] : '*')." FROM $sql_from WHERE $sql_where ORDER BY {$system['order']} $sql_limit";
                $data = $this->_query($sql, $system['site'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-search-'.md5($sql);
                $cache = $this->ci->get_cache_data($name);
                if (!$cache && is_array($data)) {
                    // 模块表的系统字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    $fields['updatetime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    foreach ($data as $i => $t) {
                        $data[$i] = $this->ci->field_format_value($fields, $t, 1, $module['dirname']);
                    }
                    //$cache = $this->ci->set_cache_data($name, $data, $system['cache']);
                    $cache = $system['cache'] ? $this->ci->set_cache_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;


            default :
                return $this->_return($system['return'], 'list标签必须含有参数action或者action参数错误');
                break;
        }
    }

    /**
     * 查询缓存
     */
    public function _query($sql, $site, $cache, $all = TRUE) {

        // 数据库对象
        $db = $site ? $this->ci->site[$site] : $this->ci->db;
        $cname = md5($sql.dr_now_url());
        // 缓存存在时读取缓存文件
        if ($cache && $data = $this->ci->get_cache_data($cname)) {
            return $data;
        }

        // 执行SQL
        $db->db_debug = FALSE;
        $query = $db->query($sql);

        if (!$query) {
            return 'SQL查询解析不正确：'.$sql;
        }

        // 查询结果
        $data = $all ? $query->result_array() : $query->row_array();

        // 开启缓存时，重新存储缓存数据
        $cache && $this->ci->set_cache_data($cname, $data, $cache);

        $db->db_debug = TRUE;
        
        return $data;
    }

    /**
     * 分页
     */
    public function _get_pagination($url, $pagesize, $total) {

        $this->ci->load->library('pagination');

        if (!$this->pagination) {
            $config = array();
            is_file(APPPATH.'config/pagination.php') ? include APPPATH.'config/pagination.php' : include WEBPATH.'config/pagination.php';
            $page = $config['pagination'];
        } else {
            $page = $this->pagination;
        }

        $page['base_url'] = $url;
        $page['per_page'] = $pagesize;
        $page['total_rows'] = $total;
        $page['use_page_numbers'] = TRUE;
        $page['query_string_segment'] = 'page';

        $this->ci->pagination->initialize($page);

        return $this->ci->pagination->dr_links();
    }

    // 条件子句格式化
    public function _get_where($where) {

        if ($where) {
            $string = '';
            foreach ($where as $i => $t) {
                if (isset($t['use']) && $t['use'] == 0 || !strlen($t['value'])) {
                    continue;
                }
                $join = $string ? ' AND' : '';
                switch ($t['adj']) {
                    case 'LIKE':
                        $string.= $join." {$t['name']} LIKE \"".$this->ci->db->escape_str($t['value'])."\"";
                        break;

                    case 'IN':
                        $string.= $join." {$t['name']} IN (".$this->ci->db->escape_str($t['value']).")";
                        break;

                    case 'NOTIN':
                        $string.= $join." {$t['name']} NOT IN (".$this->ci->db->escape_str($t['value']).")";
                        break;

                    case 'NOT':
                        $string.= $join.(is_numeric($t['value']) ? " {$t['name']} <> ".$t['value'] : " {$t['name']} <> \"".($t['value'] == "''" ? '' : $this->ci->db->escape_str($t['value']))."\"");
                        break;

                    case 'BETWEEN':
                        $string.= $join." {$t['name']} BETWEEN ".str_replace(',', ' AND ', $t['value'])."";
                        break;

                    case 'BEWTEEN':
                        $string.= $join." {$t['name']} BETWEEN ".str_replace(',', ' AND ', $t['value'])."";
                        break;

                    case 'BW':
                        $string.= $join." {$t['name']} BETWEEN ".str_replace(',', ' AND ', $t['value'])."";
                        break;

                    default:
                        if (strpos($t['name'], '`thumb`')) {
                            $t['value'] == 1 ? $string.= $join." {$t['name']}<>''" : $string.= $join." {$t['name']}=''";
                        } else {
                            $string.= $join.(is_numeric($t['value']) ? " {$t['name']} = ".$t['value'] : " {$t['name']} = \"".($t['value'] == "''" ? '' : $this->ci->db->escape_str($t['value']))."\"");
                        }
                        break;
                }
            }
            return trim($string);
        }

        return 1;
    }

    // 给条件字段加上表前缀
    public function _set_where_field_prefix($where, $field, $prefix, $myfield = array()) {
        if ($where) {
            foreach ($where as $i => $t) {
                if (isset($field[$t['name']])) {
                    $where[$i]['use'] = 1;
                    $where[$i]['name'] = "`$prefix`.`{$t['name']}`";
                    if ($myfield && $myfield[$t['name']]['fieldtype'] == 'Linkage') {
                        // 联动菜单
                        $data = dr_linkage($myfield[$t['name']]['setting']['option']['linkage'], $t['value']);
                        if ($data) {
                            if ($data['child']) {
                                $where[$i]['adj'] = 'IN';
                                $where[$i]['value'] = $data['childids'];
                            } else {
                                $where[$i]['value'] = intval($data['ii']);
                            }
                        }
                    }
                } else {
                    $where[$i]['use'] = $t['use'] ? 1 : 0;
                }
            }
        }
        return $where;
    }

    // 给显示字段加上表前缀
    public function _set_select_field_prefix($select, $field, $prefix) {

        $select = str_replace('DISTINCT_', 'DISTINCT ', $select);

        if ($select) {
            $array = explode(',', $select);
            foreach ($array as $i => $t) {
                isset($field[$t]) && $array[$i] = "`$prefix`.`$t`";
            }
            return implode(',', $array);
        }

        return $select;
    }

    // 给排序字段加上表前缀
    public function _set_order_field_prefix($order, $field, $prefix) {

        if ($order) {
            if (in_array(strtoupper($order), array('RAND()', 'RAND'))) {
                // 随机排序
                return 'RAND()';
            } else {
                // 字段排序
                $array = explode(',', $order);
                foreach ($array as $i => $t) {
                    if (strpos($t, '`') !== false) {
                        $array[$i] = $t;
                        continue;
                    }
                    $a = explode('_', $t);
                    $b = end($a);
                    if (in_array(strtolower($b), array('desc', 'asc'))) {
                        $a = str_replace('_'.$b, '', $t);
                    } else {
                        $a = $t;
                        $b = '';
                    }
                    $b = strtoupper($b);
                    if (isset($field[$a])) {
                        $array[$i] = "`$prefix`.`$a` ".($b ? $b : "DESC");
                    } elseif (isset($field[$a.'_lat']) && isset($field[$a.'_lng'])) {
                        if ($this->ci->my_position) {
                            $this->pos_order = $a;
                            $array[$i] = $a.' ASC';
                        } else {
                            $this->ci->msg('没有定位到您的坐标');
                        }
                    } else {
                        $array[$i] = $t;
                    }
                }
                return implode(',', $array);
            }
        }

        return NULL;
    }

    // 格式化查询参数
    private function _get_select_field($field) {

        $this->pos_order && ($this->ci->my_position ? $field.= ',ROUND(6378.138*2*ASIN(SQRT(POW(SIN(('.$this->ci->my_position['lat'].'*PI()/180-'.$this->pos_order.'_lat*PI()/180)/2),2)+COS('.$this->ci->my_position['lat'].'*PI()/180)*COS('.$this->pos_order.'_lat*PI()/180)*POW(SIN(('.$this->ci->my_position['lng'].'*PI()/180-'.$this->pos_order.'_lng*PI()/180)/2),2)))*1000) AS '.$this->pos_order : $this->ci->msg('没有定位到您的坐标'));

        return $field;
    }

    // list 返回
    public function _return($return, $data = NULL, $sql = NULL, $total = NULL, $pages = NULL, $pagesize = NULL) {

        $error = '';
        if ($data && !is_array($data)) {
            $error = $data;
            $data = NULL;
        }

        // 错误信息格式化
        $error && $error = '<div style="padding:10px;margin:10px;margin-top:20px;border:1px solid #ffbe7a;background:#fffced;color:red;font-weight:bold;">'.$error."</div>";

        $total = isset($total) ? $total : count($data);
        $page = max(1, (int)$_GET['page']);
        $nums = $pagesize ? ceil($total/$pagesize) : 0;

        // 返回数据格式
        if ($return) {
            return array(
                'sql_'.$return => $sql,
                'nums_'.$return => $nums,
                'page_'.$return => $page,
                'pages_'.$return => $pages,
                'error_'.$return => $error,
                'total_'.$return => $total,
                'return_'.$return => $data,
                'pagesize_'.$return => $pagesize,
            );
        } else {
            return array(
                'sql' => $sql,
                'nums' => $nums,
                'page' => $page,
                'pages' => $pages,
                'error' => $error,
                'total' => $total,
                'return' => $data,
                'pagesize' => $pagesize,
            );
        }
    }

    public function _get_var($param) {

        $array = explode('.', $param);
        if (!$array) {
            return '';
        }

        $string = '';
        foreach ($array as $var) {
            $string.= '[';
            if (strpos($var, '$') === 0) {
                $string.= preg_replace('/\[(.+)\]/U', '[\'\\1\']', $var);
            } elseif (preg_match('/[A-Z_]+/', $var)) {
                $string.= ''.$var.'';
            } else {
                $string.= '\''.$var.'\'';
            }
            $string.= ']';
        }

        return $string;
    }

    // 公共变量参数
    public function _cache_var($name, $site = SITE_ID) {

        $data = NULL;
        $name = strtoupper($name);

        switch ($name) {
            case 'SPACE-MODEL':
                $data = $this->ci->get_cache('SPACE-MODEL');
                break;
            case 'MEMBER':
                $data = $this->ci->get_cache('member');
                break;
            case 'URLRULE':
                $data = $this->ci->get_cache('urlrule');
                break;
            case 'MODULE':
                $site = $site ? $site : SITE_ID;
                $data = $this->ci->get_module($site);
                if (!$data) {
                    // 修复获取不到模块缓存问题
                    $MOD = $this->ci->dcache->get('module');
                    if ($MOD[$site]) {
                        foreach ($MOD[$site] as $dir) {
                            $data[$dir] = $this->ci->dcache->get('module-'.$site.'-'.$dir);
                        }
                    }
                }
                break;
            case 'CATEGORY':
                $site = $site ? $site : SITE_ID;
                $data = $this->ci->get_cache('module-'.$site.'-'.MOD_DIR, 'category');
                break;
            case 'OAUTH':
                $oauth = require WEBPATH.'config/oauth.php';
                if ($oauth) {
                    foreach ($oauth as $id => $value) {
                        if ($value['use'] == 1) {
                            $value['id'] = $id;
                            $data[] = $value;
                        }
                    }
                }
                break;
            case 'PAGE':
                $site = $site ? $site : SITE_ID;
                $data = $this->ci->get_cache('PAGE-'.$site, 'data');
                break;
            default:
                $data = $this->ci->get_cache($name.'-'.$site);
                break;
        }

        return $data;
    }

}

// 替换cache标签中的单引号数组 for php5.5
function php55_replace_cache_array($string) {
    return "_get_var('".preg_replace('#\[\'(\w+)\'\]#Ui', '.\\1', $string[1])."')";
}

// 替换list标签中的单引号数组
function php55_replace_array($string) {
    return "list_tag(\"".preg_replace('#\[\'(\w+)\'\]#Ui', '[\\1]', $string[1])."\")";
}
