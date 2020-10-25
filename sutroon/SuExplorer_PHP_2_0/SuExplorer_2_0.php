<?php

session_start();
/*
 * suFileEditor.php
 * 在线php/ini/conf/sh等脚本文件编辑器, 不依赖ftp和服务器帐号(单页绿色文件,方便部署)
 * @since 1.0 <2015-5-11> SoChishun Added.
 * @since 2.0 <2015-7-24> SoChishun 
 *      1.重命名为SuExplorer.php
 *      2.改进若干外观样式
 *      3.新增登录验证模块
 *      4.新增删除功能
 * @since 2.1 <2015-9-14> SoChishun
 *      1. 新增全局配置功能
 *      2. 新增文件名后缀限制功能
 *      3. 新增图片查看功能
 */
// 全局配置信息
$config = array(
    'USER' => array('uid' => 'admin', 'pwd' => 'admin123456_'),
    'EXTS' => array(// 文件类型权限设定, 每种后缀一行, w=写入,r=查看
        'php' => array('w' => true, 'r' => true),
        'exe' => array('w' => false, 'r' => false),
    ),
);


$opage = new SuFileEditor($config);
$opage->index();

/**
 * 页面主类
 * @since 1.0 <2015-5-11> SoChishun Added.
 */
class SuFileEditor {

    protected $fns;
    protected $config;

    public function __construct($config) {
        $this->config = $config;
        $this->fns = new CommonFn();
    }

    public function __destruct() {
        if ($this->fns) {
            unset($this->fns);
        }
    }

    /**
     * 显示网站目录的项目内容
     * @since 1.0 <2015-5-11> SoChishun Added.
     */
    public function index() {
        // 登录验证
        if (!$this->check_login()) {
            exit;
        }

        $path = (!isset($_GET['path']) || !$_GET['path']) ? $_SERVER['DOCUMENT_ROOT'] : urldecode($_GET['path']);
        // 删除文件
        if ('del' == I('get.action')) {
            if ('yes' != I('get.do')) {
                // 为防止黑客破坏,删除操作需要手动增加参数do=yes
                echo '非法操作!<a href="', $_SERVER["SCRIPT_NAME"], '">[返回]</a>';
                exit;
            }
            if (is_dir($path)) {
                if (!rmdir($path)) {
                    echo '目录删除失败!';
                    exit;
                }
            }
            if (is_file($path)) {
                if (!unlink($path)) {
                    echo '文件删除失败';
                    exit;
                }
            }
        }
        // 保存文件
        if ('save' == I('post.action') && is_file($path)) {
            $result = file_put_contents($path, I('post.content'));
            if (false === $result) {
                die('写入文件失败');
            }
        }
        $this->fns->location_to_breadcrumb($path);
        $this->view_path_form($path);
        // 列出文件
        if (is_dir($path)) {
            $this->view_content_list($path);
        } else if (is_file($path)) {
            $this->view_content_edit($path);
        } else {
            $this->view_style('body{font-size:12px}strong{color:#F00}');
            echo '<strong>文件或目录已删除或不存在!</strong>';
        }
    }

    /**
     * 检查用户登录
     * @since 1.0 <2015-7-24> SoChishun Added.
     */
    function check_login() {
        $session_id = 'suexplorer';
        $user = $this->config['USER'];
        $do_login = 'login' == I('post.action');
        if ('logoff' == I('post.action')) {
            $_SESSION[$session_id] = null;
        }
        if (!$do_login && (!isset($_SESSION[$session_id]) || $user['uid'] != $_SESSION[$session_id])) {
            $this->view_login_form();
            return false;
        }
        if ($do_login) {
            $uid = I('post.uid');
            $pwd = I('post.pwd');
            if (!$uid || !$pwd) {
                echo '表单项必填！<a href="', $_SERVER["SCRIPT_NAME"], '">[返回]</a>';
                return false;
            }
            if ($user['uid'] != $uid) {
                echo '用户名不存在!<a href="', $_SERVER["SCRIPT_NAME"], '">[返回]</a>';
                return false;
            }
            if ($user['pwd'] != $pwd) {
                echo '密码错误!<a href="', $_SERVER["SCRIPT_NAME"], '">[返回]</a>';
                return false;
            }
            $_SESSION['suexplorer'] = $uid;
        }
        echo '<div style="margin:5px 0px;padding-bottom:3px;border-bottom:solid 1px #AAA;display:table;">欢迎您：', $_SESSION[$session_id], ' <a href="?action=logoff" style="text-decoration:none;">[注销]</a></div>';
        return true;
    }

    // 用户登录表单视图 2015-7-24 SoChishun Added.
    function view_login_form() {
        $this->view_style('body,td,th{font-size:12px}th{text-align:right;}');
        echo '<form method="post" action="#"><table><tr><th>用户名：</th><td><input type="text" name="uid" /></td></tr><tr><th>密码：</th><td><input type="password" name="pwd" /></td></tr></table><input type="hidden" name="action" value="login" /><button type="submit">登录</button><button type="reset">重置</button></form>';
    }

    // 路径表单视图 2015-5-11 SoChishun Added.
    function view_path_form($path) {
        echo '<form method="get" action="#" id="frm-path">', '<input type="text" name="path" value="' . $path . '" style="width:50%" /><input type="hidden" id="action" name="action" value="" /><input type="hidden" id="do" name="do" value="" /><button type="submit">转到</button><button type="button" onclick="return del();">删除</button>', '</form><script type="text/javascript">var i=0;function del(){if(!confirm("您确定要删除吗?")){return false;};if(i<3){i++;alert("重要的事情要重复做三遍,请再次点击删除按钮!");return false;};document.getElementById("action").value="del";document.getElementById("do").value="yes";document.getElementById("frm-path").submit();}</script>';
    }

    // 目录内容视图 2015-5-11 SoChishun Added.
    function view_content_list($path) {
        $fns = new CommonFn();
        $files = $this->fns->get_dir_contents($path);
        $this->view_style('body{font-size:12px}.dir-contents{width:1050px; display:table;}.dir-contents a{ margin-right:20px;line-height:21px;text-decoration:none;float:left;}.blue{color:#0000DB}.green{color:#009900}');
        echo '<div class="dir-contents">';
        foreach ($files as $file) {
            if ($file['is_dir']) {
                echo '<a href="?path=' . urlencode($file['real_path']) . '" class="blue"><strong>' . $file['name'] . '</strong></a>';
            } else {
                echo '<a href="?path=' . urlencode($file['real_path']) . '" class="green">' . $file['name'] . '</a>';
            }
        }
        echo '<div style="clear:both"></div></div>';
    }

    // 文件内容编辑视图 2015-5-11 SoChishun Added.
    function view_content_edit($path) {
        $this->view_style('body{font-size:12px}textarea{font-size:12px;line-height:18px}');
        if (is_file($path)) {
            $btns = '<button type="submit">保存</button><button type="reset">重置</button>';
            if (!is_writable($path)) {
                echo '<div style="color:#F00">文件不可写!</div>';
                $btns = '';
            }
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $exts = empty($this->config['EXTS']) ? false : $this->config['EXTS'];
            if ($ext && $exts && array_key_exists($ext, $exts)) {
                $rule = $exts[$ext];
                if (isset($rule['r']) && false === $rule['r']) {
                    echo '<div style="color:#F00">文件类型不在允许查看的后缀设定范围内!</div>';
                    return;
                }
                if (isset($rule['w']) && false === $rule['w']) {
                    echo '<div style="color:#F00">文件类型不在允许编辑的后缀设定范围内!</div>';
                    $btns = '';
                }
            }
            $imgs = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
            if ($ext && in_array($ext, $imgs)) {
                $doc_root = $_SERVER['DOCUMENT_ROOT'];
                $path = substr($path, strlen($doc_root));
                $content = '<img src="' . $path . '" alt="' . $path . '" style="max-width:90%" />';
            } else {
                $content = '<textarea name="content" cols="60" rows="36" style="width:90%">' . file_get_contents($path) . '</textarea>';
            }
            echo '<form method="post" action="#">', $btns, '<div>' . $content . '</div>', $btns, '<input type="hidden" name="action" value="save" /></form>';
        }
    }

    // 样式视图 2015-5-11 SoChishun Added.
    function view_style($style) {
        echo '<style type="text/css">', $style, '</style>';
    }

}

/**
 * 通用方法类
 * @since 1.0 <2015-5-11> SoChishun Added.
 */
class CommonFn {

    /**
     * 返回指定路径下的内容
     * @param string $directory 路径
     * @param array $config 选项
     * @return array
     * @throws Exception
     * @since 1.0 <2015-5-11> SoChishun Added.
     */
    public function get_dir_contents($directory, $config = array()) {
        $config = array_merge(
                array('name' => true, 'path' => true, 'real_path' => true, 'exten' => false, 'ctime' => false, 'mtime' => false, 'size' => false, 'is_dir' => true, 'is_file' => false, 'is_link' => false, 'is_executable' => false, 'is_readable' => false, 'is_writable' => false,), $config);
        $files = array();
        try {
            $dir = new DirectoryIterator($directory);
        } catch (Exception $e) {
            throw new Exception($directory . ' is not readable');
        }
        foreach ($dir as $file) {
            if ($file->isDot()) {
                continue;
            }
            if ($config['name']) {
                $item['name'] = $file->getFileName();
            }
            if ($config['path']) {
                $item['path'] = $file->getPath();
            }
            if ($config['real_path']) {
                $item['real_path'] = $file->getRealPath();
            }
            if ($config['exten']) {
                $item['exten'] = $file->getExtension();
            }
            if ($config['mtime']) {
                $item['mtime'] = $file->getMTime();
            }
            if ($config['ctime']) {
                $item['ctime'] = $file->getCTime();
            }
            if ($config['size']) {
                $item['size'] = $file->getSize();
            }
            if ($config['is_dir']) {
                $item['is_dir'] = $file->isDir();
            }
            if ($config['is_file']) {
                $item['is_file'] = $file->isFile();
            }
            if ($config['is_link']) {
                $item['is_link'] = $file->isLink();
            }
            if ($config['is_executable']) {
                $item['is_executable'] = $file->isExecutable();
            }
            if ($config['is_readable']) {
                $item['is_readable'] = $file->isReadable();
            }
            if ($config['is_writable']) {
                $item['is_writable'] = $file->isWritable();
            }
            $files[] = $item;
        }
        return $files;
    }

    /**
     * 路径转为导航
     * @param string $path
     * @since 1.0 <2015-5-11> SoChishun Added.
     */
    public function location_to_breadcrumb($path) {
        $doc_root = $_SERVER['DOCUMENT_ROOT'];
        $sub_root = strlen($path) > strlen($doc_root) ? trim(substr($path, strlen($doc_root)), DIRECTORY_SEPARATOR) : '';
        echo '<div><a href="?path=', urlencode($doc_root), '">/</a>';
        $arr_sub = $sub_root ? explode(DIRECTORY_SEPARATOR, $sub_root) : false;
        if ($arr_sub) {
            $str = $doc_root;
            foreach ($arr_sub as $sub) {
                $str.=DIRECTORY_SEPARATOR . $sub;
                echo ' &gt; <a href="?path=', urlencode($str), '">', $sub, '</a>';
            }
        }
        echo '</div>';
    }

}

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
function I($name, $default = '', $filter = null, $datas = null) {
    static $_PUT = null;
    if (strpos($name, '/')) { // 指定修饰符
        list($name, $type) = explode('/', $name, 2);
    } elseif (false) { // 默认强制转换为字符串
        $type = 's';
    }
    if (strpos($name, '.')) { // 指定参数来源
        list($method, $name) = explode('.', $name, 2);
    } else { // 默认为自动判断
        $method = 'param';
    }
    switch (strtolower($method)) {
        case 'get' :
            $input = & $_GET;
            break;
        case 'post' :
            $input = & $_POST;
            break;
        case 'put' :
            if (is_null($_PUT)) {
                parse_str(file_get_contents('php://input'), $_PUT);
            }
            $input = $_PUT;
            break;
        case 'param' :
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input = $_POST;
                    break;
                case 'PUT':
                    if (is_null($_PUT)) {
                        parse_str(file_get_contents('php://input'), $_PUT);
                    }
                    $input = $_PUT;
                    break;
                default:
                    $input = $_GET;
            }
            break;
        case 'path' :
            $input = array();
            if (!empty($_SERVER['PATH_INFO'])) {
                $depr = C('URL_PATHINFO_DEPR');
                $input = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
            }
            break;
        case 'request' :
            $input = & $_REQUEST;
            break;
        case 'session' :
            $input = & $_SESSION;
            break;
        case 'cookie' :
            $input = & $_COOKIE;
            break;
        case 'server' :
            $input = & $_SERVER;
            break;
        case 'globals' :
            $input = & $GLOBALS;
            break;
        case 'data' :
            $input = & $datas;
            break;
        default:
            return null;
    }
    if ('' == $name) { // 获取全部变量
        $data = $input;
    } elseif (isset($input[$name])) { // 取值操作
        $data = $input[$name];
    } else { // 变量默认值
        $data = isset($default) ? $default : null;
    }
    return $data;
}
