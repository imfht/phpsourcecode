<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

use app\admin\model\Options as OptionsModel;
use PHPMailer\PHPMailer\PHPMailer;
use think\Db;
use think\facade\Config;
use think\facade\Env;
use think\captcha\Captcha;
use think\facade\Request;
use think\Loader;
use app\common\model\Module as ModuleModel;

if (!function_exists('get_imgurl')) {
    /**
     * 获取图片完整路径
     *
     * @param string $url 待获取图片url
     * @param int    $cat 待获取图片类别 0为文章 1前台头像 2后台头像
     *
     * @return string 完整图片imgurl
     */
    function get_imgurl($url, $cat = 0)
    {
        if (stripos($url, 'http') !== false) {
            //网络图片
            return $url;
        } elseif ($url && stripos($url, '/') === false && stripos($url, '\\') === false) {
            //头像
            return '/data/upload/avatar/' . $url;
        } elseif (empty($url)) {
            //$url为空
            if ($cat == 2) {
                $imgurl = 'girl.jpg';
            } elseif ($cat == 1) {
                $imgurl = 'headicon.png';
            } else {
                $imgurl = 'no_img.jpg';
            }
            return '/public/images/' . $imgurl;
        } else {
            //本地上传图片
            return '/' . $url;
        }
    }
}
if (!function_exists('is_assoc')) {
    /**
     * 判断是否关联数组
     *
     * @param array $arr
     *
     * @return boolean
     */
    function is_assoc($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
if (!function_exists('sys_config_setbykey')) {
    /**
     * 设置全局配置到文件
     *
     * @param $key
     * @param $value
     *
     * @return boolean
     */
    function sys_config_setbykey($key, $value)
    {
        $file = Env::get('root_path') . 'config/yfcmf.php';
        $cfg  = [];
        if (file_exists($file)) {
            $cfg = include $file;
        }
        $item = explode('.', $key);
        switch (count($item)) {
            case 1:
                $cfg[$item[0]] = $value;
                break;
            case 2:
                $cfg[$item[0]][$item[1]] = $value;
                break;
        }
        return file_put_contents($file, "<?php\nreturn " . var_export($cfg, true) . ";");
    }
}
if (!function_exists('sys_config_setbyarr')) {
    /**
     * 设置全局配置到文件
     *
     * @param array
     *
     * @return boolean
     */
    function sys_config_setbyarr($data)
    {
        $file = Env::get('root_path') . 'config/yfcmf.php';
        if (file_exists($file)) {
            $configs = include $file;
        } else {
            $configs = [];
        }
        $configs = array_merge($configs, $data);
        return file_put_contents($file, "<?php\treturn " . var_export($configs, true) . ";");
    }
}
if (!function_exists('sys_config_get')) {
    /**
     * 获取全局配置
     *
     * @param $key
     *
     * @return array|null
     */
    function sys_config_get($key)
    {
        $file = Env::get('root_path') . 'config/yfcmf.php';
        $cfg  = [];
        if (file_exists($file)) {
            $cfg = (include $file);
        }
        return isset($cfg[$key]) ? $cfg[$key] : null;
    }
}
if (!function_exists('remove_dir')) {
    /**
     * 删除文件夹
     * @author rainfer <81818832@qq.com>
     *
     * @param string
     * @param int
     */
    function remove_dir($dir, $time_thres = -1)
    {
        foreach (list_file($dir) as $f) {
            if ($f ['isDir']) {
                remove_dir($f ['pathname'] . '/');
            } elseif ($f ['isFile'] && $f ['filename']) {
                if ($time_thres == -1 || $f ['mtime'] < $time_thres) {
                    @unlink($f ['pathname']);
                }
            }
        }
    }
}
if (!function_exists('rmdirs')) {

    /**
     * 删除文件夹
     * @param string $dirname 目录
     * @param bool $withself 是否删除自身
     * @return boolean
     */
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname)) {
            return false;
        }
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if ($withself) {
            @rmdir($dirname);
        }
        return true;
    }

}
if (!function_exists('copydirs')) {

    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest 目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($source)) {
            return;
        }
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                $sontDir = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                if (!is_dir($sontDir)) {
                    mkdir($sontDir, 0755, true);
                }
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

}
if (!function_exists('force_download_content')) {
    /**
     * 强制下载
     * @author rainfer <81818832@qq.com>
     *
     * @param string $filename
     * @param string $content
     */
    function force_download_content($filename, $content)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Transfer-Encoding: binary");
        header("Content-Disposition: attachment; filename=$filename");
        echo $content;
        exit;
    }
}
if (!function_exists('format_bytes')) {
    /**
     * 格式化字节大小
     *
     * @param  number $size      字节数
     * @param  string $delimiter 数字和单位分隔符
     *
     * @return string            格式化后的带单位的大小
     * @author rainfer <81818832@qq.com>
     */
    function format_bytes($size, $delimiter = '')
    {
        $units = [' B', ' KB', ' MB', ' GB', ' TB', ' PB'];
        for ($i = 0; $size >= 1024 && $i < 5; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $delimiter . $units[$i];
    }
}
if (!function_exists('format_tobytes')) {
    /**
     * 转换成字节数
     *
     * @param  string $size      大小如10M 100G 1.2T
     * @param  int    $dec   小数位数
     * @return int           格式化后字节数
     * @author rainfer <81818832@qq.com>
     */
    function format_tobytes($size, $dec = 0)
    {
        $size = trim($size);
        preg_match('/(^[0-9\.]+)(\w+)/',$size, $info);
        $isMatched = preg_match('/(^[0-9\.]+)(\w+)/s', $size, $info);
        if ($isMatched) {
            $size = $info[1];
            $suffix = strtoupper($info[2]);
            $a = array_flip(array("B", "KB", "MB", "GB", "TB", "PB"));
            $b = array_flip(array("B", "K", "M", "G", "T", "P"));
            $pos = (isset($a[$suffix]) && isset($b[$suffix])) !==false ? $a[$suffix] : $b[$suffix];
            return round($size*pow(1024, $pos), $dec);
        } else {
            return 0;
        }
    }
}
if (!function_exists('list_file')) {
    /**
     * 列出本地目录的文件
     * @author rainfer <81818832@qq.com>
     *
     * @param string $path
     * @param string $pattern
     *
     * @return array
     */
    function list_file($path, $pattern = '*')
    {
        if (strpos($pattern, '|') !== false) {
            $patterns = explode('|', $pattern);
        } else {
            $patterns [0] = $pattern;
        }
        $i   = 0;
        $dir = [];
        if (is_dir($path)) {
            $path = rtrim($path, '/') . '/';
        }
        foreach ($patterns as $pattern) {
            $list = glob($path . $pattern);
            if ($list !== false) {
                foreach ($list as $file) {
                    $dir [$i] ['filename']   = basename($file);
                    $dir [$i] ['path']       = dirname($file);
                    $dir [$i] ['pathname']   = realpath($file);
                    $dir [$i] ['owner']      = fileowner($file);
                    $dir [$i] ['perms']      = substr(base_convert(fileperms($file), 10, 8), -4);
                    $dir [$i] ['atime']      = fileatime($file);
                    $dir [$i] ['ctime']      = filectime($file);
                    $dir [$i] ['mtime']      = filemtime($file);
                    $dir [$i] ['size']       = filesize($file);
                    $dir [$i] ['type']       = filetype($file);
                    $dir [$i] ['ext']        = is_file($file) ? strtolower(substr(strrchr(basename($file), '.'), 1)) : '';
                    $dir [$i] ['isDir']      = is_dir($file);
                    $dir [$i] ['isFile']     = is_file($file);
                    $dir [$i] ['isLink']     = is_link($file);
                    $dir [$i] ['isReadable'] = is_readable($file);
                    $dir [$i] ['isWritable'] = is_writable($file);
                    $i++;
                }
            }
        }
        usort($dir, 'sort_dir');
        return $dir;
    }
}
if (!function_exists('sort_dir')) {
    function sort_dir($a, $b)
    {
        if (($a["isDir"] && $b["isDir"]) || (!$a["isDir"] && !$b["isDir"])) {
            return $a["filename"] > $b["filename"] ? 1 : -1;
        } else {
            if ($a["isDir"]) {
                return -1;
            } elseif ($b["isDir"]) {
                return 1;
            }
            if ($a["filename"] == $b["filename"]) {
                return 0;
            }
            return $a["filename"] > $b["filename"] ? -1 : 1;
        }
    }
}
if (!function_exists('hook')) {
    /**
     * 监听钩子
     *
     * @param string $name   钩子名称
     * @param mixed  $params 传入参数
     * @param bool   $once   只获取一个有效返回值
     */
    function hook($name = '', $params = null, $once = false)
    {
        \think\facade\Hook::listen($name, $params, $once);
    }
}
if (!function_exists('data_signature')) {
    /**
     * 数据签名
     *
     * @param array $data 被认证的数据
     *
     * @return string 签名
     */
    function data_signature($data = [])
    {
        if (!is_array($data)) {
            $data = (array)$data;
        }
        ksort($data);
        $code = http_build_query($data);
        $sign = sha1($code);
        return $sign;
    }
}
if (!function_exists('encrypt_password')) {
    /**
     * 所有用到密码的不可逆加密方式
     * @author rainfer <81818832@qq.com>
     *
     * @param string $password
     * @param string $password_salt
     *
     * @return string
     */
    function encrypt_password($password, $password_salt)
    {
        return md5(md5($password) . md5($password_salt));
    }
}
if (!function_exists('jiami')) {
    /**
     * 加密函数
     *
     * @param string $txt 需加密的字符串
     * @param string $key 加密密钥，默认读取data_auth_key配置
     *
     * @return string 加密后的字符串
     */
    function jiami($txt, $key = null)
    {
        empty($key) && $key = config('data_auth_key');
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
        $nh    = rand(0, 64);
        $ch    = $chars[$nh];
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt   = base64_encode($txt);
        $tmp   = '';
        $k     = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh + strpos($chars, $txt [$i]) + ord($mdKey[$k++])) % 64;
            $tmp .= $chars[$j];
        }
        return $ch . $tmp;
    }
}
if (!function_exists('jiemi')) {
    /**
     * 解密函数
     *
     * @param string $txt 待解密的字符串
     * @param string $key 解密密钥，默认读取data_auth_key配置
     *
     * @return string 解密后的字符串
     */
    function jiemi($txt, $key = null)
    {
        empty($key) && $key = config('data_auth_key');
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
        $ch    = $txt[0];
        $nh    = strpos($chars, $ch);
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt   = substr($txt, 1);
        $tmp   = '';
        $k     = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
            while ($j < 0) {
                $j += 64;
            }
            $tmp .= $chars[$j];
        }
        return base64_decode($tmp);
    }
}
if (!function_exists('random')) {
    /**
     * 随机字符
     *
     * @param int    $length  长度
     * @param string $type    类型
     * @param int    $convert 转换大小写 1大写 0小写
     *
     * @return string
     */
    function random($length = 10, $type = 'letter', $convert = 0)
    {
        $config = [
            'number' => '1234567890',
            'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
            'all'    => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ];
        if (!isset($config[$type])) {
            $type = 'letter';
        }
        $string = $config[$type];
        $code   = '';
        $strlen = strlen($string) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $string{mt_rand(0, $strlen)};
        }
        if (!empty($convert)) {
            $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
        }
        return $code;
    }
}
if (!function_exists('get_addon_class')) {
    /**
     * 获取插件类的类名
     *
     * @param string $name  插件名
     * @param string $type  返回命名空间类型
     * @param string $class 当前类名
     *
     * @return string
     */
    function get_addon_class($name, $type = 'hook', $class = null)
    {
        $name = Loader::parseName($name);

        // 处理多级控制器情况
        if (!is_null($class) && strpos($class, '.')) {
            $class = explode('.', $class);
            foreach ($class as $key => $cls) {
                $class[$key] = Loader::parseName($cls, 1);
            }
            $class = implode('\\', $class);
        } else {
            $class = Loader::parseName(is_null($class) ? $name : $class, 1);
        }
        switch ($type) {
            case 'controller':
                $namespace = "\\addons\\" . $name . "\\controller\\" . $class;
                break;
            default:
                $namespace = "\\addons\\" . $name . "\\" . $class;
        }

        return class_exists($namespace) ? $namespace : '';
    }
}
if (!function_exists('addon_url')) {
    /**
     * 插件显示内容里生成访问插件的url
     *
     * @param             $url
     * @param array       $param
     *
     * @return bool|string
     *
     * @param bool|string $suffix 生成的URL后缀
     * @param bool|string $domain 域名
     */
    function addon_url($url, $param = [], $suffix = true, $domain = false)
    {
        $url        = parse_url($url);
        $case       = Config::get('url_convert');
        $addons     = $case ? Loader::parseName($url['scheme']) : $url['scheme'];
        $controller = $case ? Loader::parseName($url['host']) : $url['host'];
        $action     = trim($case ? strtolower($url['path']) : $url['path'], '/');

        /* 解析URL带的参数 */
        if (isset($url['query'])) {
            parse_str($url['query'], $query);
            $param = array_merge($query, $param);
        }

        // 生成插件链接新规则
        $actions = "{$addons}-{$controller}-{$action}";

        return url("addons/execute/{$actions}", $param, $suffix, $domain);
    }
}
if (!function_exists('get_query')) {
    /**
     * 获取当前request参数数组,去除值为空
     * @return array
     */
    function get_query()
    {
        $param = request()->except(['s']);
        $rst   = [];
        foreach ($param as $k => $v) {
            if (!empty($v)) {
                $rst[$k] = $v;
            }
        }
        return $rst;
    }
}
if (!function_exists('has_controller')) {
    /**
     * 是否存在控制器
     *
     * @param string $module     模块
     * @param string $controller 待判定控制器名
     *
     * @return boolean
     */
    function has_controller($module, $controller)
    {
        $arr = \ReadClass::readDir(APP_PATH . $module . DIRECTORY_SEPARATOR . 'controller');
        if ((!empty($arr[$controller])) && $arr[$controller]['class_name'] == $controller) {
            return true;
        } else {
            return false;
        }
    }
}
if (!function_exists('has_action')) {
    /**
     * 是否存在方法
     *
     * @param string $module     模块
     * @param string $controller 待判定控制器名
     * @param string $action     待判定控制器名
     *
     * @return number 方法结果，0不存在控制器 1存在控制器但是不存在方法 2存在控制和方法
     */
    function has_action($module, $controller, $action)
    {
        $arr = \ReadClass::readDir(APP_PATH . $module . DIRECTORY_SEPARATOR . 'controller');
        if ((!empty($arr[$controller])) && $arr[$controller]['class_name'] == $controller) {
            $method_name = array_map('array_shift', $arr[$controller]['method']);
            if (in_array($action, $method_name)) {
                return 2;
            } else {
                return 1;
            }
        } else {
            return 0;
        }
    }
}
if (!function_exists('getBroswer')) {
    /**
     * 获取客户端浏览器信息 添加win10 edge浏览器判断
     * @author  Jea杨
     * @return string
     */
    function getBroswer()
    {
        $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
        if (stripos($sys, "Firefox/") > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
            $exp[0] = "Firefox";
            $exp[1] = $b[1];  //获取火狐浏览器的版本号
        } elseif (stripos($sys, "Maxthon") > 0) {
            preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
            $exp[0] = "傲游";
            $exp[1] = $aoyou[1];
        } elseif (stripos($sys, "MSIE") > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
            $exp[0] = "IE";
            $exp[1] = $ie[1];  //获取IE的版本号
        } elseif (stripos($sys, "OPR") > 0) {
            preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
            $exp[0] = "Opera";
            $exp[1] = $opera[1];
        } elseif (stripos($sys, "Edge") > 0) {
            //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
            $exp[0] = "Edge";
            $exp[1] = $Edge[1];
        } elseif (stripos($sys, "Chrome") > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
            $exp[0] = "Chrome";
            $exp[1] = $google[1];  //获取google chrome的版本号
        } elseif (stripos($sys, 'rv:') > 0 && stripos($sys, 'Gecko') > 0) {
            preg_match("/rv:([\d\.]+)/", $sys, $IE);
            $exp[0] = "IE";
            $exp[1] = $IE[1];
        } elseif (stripos($sys, 'Safari') > 0) {
            preg_match("/safari\/([^\s]+)/i", $sys, $safari);
            $exp[0] = "Safari";
            $exp[1] = $safari[1];
        } else {
            $exp[0] = "未知浏览器";
            $exp[1] = "";
        }
        return $exp[0] . '(' . $exp[1] . ')';
    }
}
if (!function_exists('getOs')) {
    /**
     * 获取客户端操作系统信息包括win10
     * @author  Jea杨
     * @return string
     */
    function getOs()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
            $os = 'Windows 95';
        } elseif (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
            $os = 'Windows ME';
        } elseif (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
            $os = 'Windows 98';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
            $os = 'Windows Vista';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
            $os = 'Windows 8';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
            $os = 'Windows 10';#添加win10判断
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
            $os = 'Windows XP';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
            $os = 'Windows 2000';
        } elseif (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
            $os = 'Windows NT';
        } elseif (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
            $os = 'Windows 32';
        } elseif (preg_match('/linux/i', $agent)) {
            $os = 'Linux';
        } elseif (preg_match('/unix/i', $agent)) {
            $os = 'Unix';
        } elseif (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'SunOS';
        } elseif (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'IBM OS/2';
        } elseif (preg_match('/Mac/i', $agent)) {
            $os = 'Mac';
        } elseif (preg_match('/PowerPC/i', $agent)) {
            $os = 'PowerPC';
        } elseif (preg_match('/AIX/i', $agent)) {
            $os = 'AIX';
        } elseif (preg_match('/HPUX/i', $agent)) {
            $os = 'HPUX';
        } elseif (preg_match('/NetBSD/i', $agent)) {
            $os = 'NetBSD';
        } elseif (preg_match('/BSD/i', $agent)) {
            $os = 'BSD';
        } elseif (preg_match('/OSF1/i', $agent)) {
            $os = 'OSF1';
        } elseif (preg_match('/IRIX/i', $agent)) {
            $os = 'IRIX';
        } elseif (preg_match('/FreeBSD/i', $agent)) {
            $os = 'FreeBSD';
        } elseif (preg_match('/teleport/i', $agent)) {
            $os = 'teleport';
        } elseif (preg_match('/flashget/i', $agent)) {
            $os = 'flashget';
        } elseif (preg_match('/webzip/i', $agent)) {
            $os = 'webzip';
        } elseif (preg_match('/offline/i', $agent)) {
            $os = 'offline';
        } elseif (preg_match('/ucweb|MQQBrowser|J2ME|IUC|3GW100|LG-MMS|i60|Motorola|MAUI|m9|ME860|maui|C8500|gt|k-touch|X8|htc|GT-S5660|UNTRUSTED|SCH|tianyu|lenovo|SAMSUNG/i', $agent)) {
            $os = 'mobile';
        } else {
            $os = '未知操作系统';
        }
        return $os;
    }
}
if (!function_exists('db_get_tables')) {
    /**
     * 返回不含前缀的数据库表数组
     *
     * @author rainfer <81818832@qq.com>
     *
     * @param bool
     *
     * @return array
     */
    function db_get_tables($prefix = false)
    {
        $db_prefix = config('database.prefix');
        $list      = Db::query('SHOW TABLE STATUS FROM ' . config('database.database'));
        $list      = array_map('array_change_key_case', $list);
        $tables    = [];
        foreach ($list as $k => $v) {
            if (empty($prefix)) {
                if (stripos($v['name'], strtolower(config('database.prefix'))) === 0) {
                    $tables [] = strtolower(substr($v['name'], strlen($db_prefix)));
                }
            } else {
                $tables [] = strtolower($v['name']);
            }
        }
        return $tables;
    }
}
if (!function_exists('db_get_insert_sqls')) {
    /**
     * 返回数据表的sql
     *
     * @author rainfer <81818832@qq.com>
     *
     * @param $table : 不含前缀的表名
     *
     * @return string
     */
    function db_get_insert_sqls($table)
    {
        $db_prefix        = config('database.prefix');
        $db_prefix_re     = preg_quote($db_prefix);
        $db_prefix_holder = db_get_db_prefix_holder();
        $export_sqls      = [];
        $export_sqls []   = "DROP TABLE IF EXISTS $db_prefix_holder$table";
        switch (config('database.type')) {
            case 'mysql':
                if (!($d = Db::query("SHOW CREATE TABLE $db_prefix$table"))) {
                    $this->error("'SHOW CREATE TABLE $table' Error!");
                }
                $table_create_sql = $d [0] ['Create Table'];
                $table_create_sql = preg_replace('/' . $db_prefix_re . '/', $db_prefix_holder, $table_create_sql);
                $export_sqls []   = $table_create_sql;
                $data_rows        = Db::query("SELECT * FROM $db_prefix$table");
                $data_values      = [];
                foreach ($data_rows as &$v) {
                    foreach ($v as &$vv) {
                        $vv = "'" . addslashes(str_replace(["\r", "\n"], ['\r', '\n'], $vv)) . "'";
                    }
                    $data_values [] = '(' . join(',', $v) . ')';
                }
                if (count($data_values) > 0) {
                    $export_sqls [] = "INSERT INTO `$db_prefix_holder$table` VALUES \n" . join(",\n", $data_values);
                }
                break;
        }
        return join(";\n", $export_sqls) . ";";
    }
}
if (!function_exists('db_is_valid_table_name')) {
    /**
     * 检测当前数据库中是否含指定表
     *
     * @author rainfer <81818832@qq.com>
     *
     * @param $table : 不含前缀的数据表名
     *
     * @return bool
     */
    function db_is_valid_table_name($table)
    {
        return in_array($table, db_get_tables());
    }
}
if (!function_exists('db_restore_file')) {
    /**
     * 不检测表前缀,恢复数据库
     *
     * @author rainfer <81818832@qq.com>
     *
     * @param $file
     * @param $prefix
     */
    function db_restore_file($file, $prefix = '')
    {
        $prefix    = $prefix ?: db_get_db_prefix_holder();
        $db_prefix = config('database.prefix');
        $sqls      = file_get_contents($file);
        $sqls      = str_replace($prefix, $db_prefix, $sqls);
        $sqlarr    = explode(";\n", $sqls);
        foreach ($sqlarr as &$sql) {
            Db::execute($sql);
        }
    }
}
if (!function_exists('db_get_db_prefix_holder')) {
    /**
     * 返回表前缀替代符
     * @author rainfer <81818832@qq.com>
     *
     * @return string
     */
    function db_get_db_prefix_holder()
    {
        return '<--db-prefix-->';
    }
}
if (!function_exists('str_right')) {
    /*
     * 获取字符串右侧字符串
     */
    function str_right($str, $you)
    {
        $wz = strrpos($str, $you);
        if ($wz === false) {
            return null;
        } else {
            return substr($str, $wz + strlen($you));
        }
    }
}
if (!function_exists('str_left')) {
    /*
     * 获取字符串左侧字符串
     */
    function str_left($str, $zuo)
    {
        $wz = strpos($str, $zuo);
        if (empty($wz)) {
            return null;
        }
        if (!$text = substr($str, 0, $wz)) {
            return null;
        } else {
            return $text;
        }
    }
}
if (!function_exists('strzhong')) {
    /*
     * 获取字符串中间,通过左右字符串
     */
    function strzhong($str, $leftStr, $rightStr)
    {
        if (!empty($str)) {
            $left = strpos($str, $leftStr);
            if ($left === false) {
                return '';
            }
            $right = strpos($str, $rightStr, $left + strlen($leftStr));
            if ($left === false or $right === false) {
                return '';
            }
            return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
        } else {
            return '';
        }
    }
}
if (!function_exists('sendMail')) {
    /**
     * 发送邮件
     * @author rainfer <81818832@qq.com>
     *
     * @param string $to      收件人邮箱
     * @param string $title   标题
     * @param string $content 内容
     *
     * @return array
     * @throws
     */
    function sendMail($to, $title, $content)
    {
        $model         = new OptionsModel();
        $email_options = $model->getOptions('email_options', 'zh-cn');
        if ($email_options && $email_options['email_open']) {
            $mail = new PHPMailer(); //实例化
            // 设置PHPMailer使用SMTP服务器发送Email
            $mail->IsSMTP();
            $mail->Mailer = 'smtp';
            $mail->IsHTML(true);
            // 设置邮件的字符编码，若不指定，则为'UTF-8'
            $mail->CharSet = 'UTF-8';
            // 添加收件人地址，可以多次使用来添加多个收件人
            $mail->AddAddress($to);
            // 设置邮件正文
            $mail->Body = $content;
            // 设置邮件头的From字段。
            $mail->From = $email_options['email_name'];
            // 设置发件人名字
            $mail->FromName = $email_options['email_rename'];
            // 设置邮件标题
            $mail->Subject = $title;
            // 设置SMTP服务器。
            $mail->Host = $email_options['email_smtpname'];
            //by Rainfer
            // 设置SMTPSecure。
            $mail->SMTPSecure = $email_options['smtpsecure'];
            // 设置SMTP服务器端口。
            $port       = $email_options['smtp_port'];
            $mail->Port = empty($port) ? "25" : $port;
            // 设置为"需要验证"
            $mail->SMTPAuth = true;
            // 设置用户名和密码。
            $mail->Username = $email_options['email_emname'];
            $mail->Password = $email_options['email_pwd'];
            // 发送邮件。
            if (!$mail->Send()) {
                $mailerror = $mail->ErrorInfo;
                return ["error" => 1, "message" => $mailerror];
            } else {
                return ["error" => 0, "message" => "success"];
            }
        } else {
            return ["error" => 1, "message" => '未开启邮件发送或未配置'];
        }
    }
}
if (!function_exists('get_host')) {
    /**
     * 返回带协议的域名
     * @author rainfer <81818832@qq.com>
     */
    function get_host()
    {
        $host     = $_SERVER["HTTP_HOST"];
        $protocol = Request::isSsl() ? "https://" : "http://";
        return $protocol . $host;
    }
}
if (!function_exists('tree_left')) {
    /**
     * 返回按层级加前缀的树形数组
     * @author  rainfer
     *
     * @param array|mixed $tree      待处理菜单数组
     * @param string      $id_field  主键id字段名
     * @param string      $pid_field 父级字段名
     * @param string      $lefthtml  前缀
     * @param int         $pid       父级id
     * @param int         $lvl       当前lv
     * @param int         $leftpin   左侧距离
     *
     * @return array
     */
    function tree_left($tree, $id_field = 'id', $pid_field = 'pid', $lefthtml = '─', $pid = 0, $lvl = 0, $leftpin = 0)
    {
        $arr = [];
        foreach ($tree as $v) {
            if ($v[$pid_field] == $pid) {
                $v['lvl']      = $lvl + 1;
                $v['leftpin']  = $leftpin;
                $v['lefthtml'] = '├' . str_repeat($lefthtml, $lvl);
                $arr[]         = $v;
                $arr           = array_merge($arr, tree_left($tree, $id_field, $pid_field, $lefthtml, $v[$id_field], $lvl + 1, $leftpin + 20));
            }
        }
        return $arr;
    }
}
if (!function_exists('get_themes')) {
    /**
     * 获取模块主题
     * @author rainfer <81818832@qq.com>
     * @param string $module
     * @return array|mixed
     */
    function get_themes($module = 'cms')
    {
        $themes = cache('themes_' . $module);
        if (empty($themes)) {
            $arr = list_file(Env::get('app_path') . $module . '/view/');
            foreach ($arr as $v) {
                if ($v['isDir'] && !in_array(strtolower($v['filename']), ['public', 'admin'])) {
                    $themes[] = strtolower($v['filename']);
                }
            }
            cache('themes_' .$module, $themes);
        }
        return $themes;
    }
}
if (!function_exists('get_tpls')) {
    /**
     * 取模块模板列表
     * @param string $module
     *
     * @return array|mixed
     */
    function get_tpls($module = 'cms')
    {
        $tpls = cache('tpls_' . $module);
        if (empty($tpls)) {
            $model = new ModuleModel();
            $site_tpl = $model->where('name', $module)->value('tpl');
            $arr      = list_file(Env::get('app_path') . $module . '/view/' . $site_tpl, '*.html');
            $tpls     = [];
            foreach ($arr as $v) {
                $tpls[] = basename($v['filename'], '.html');
            }
            cache('tpls_' . $module, $tpls);
        }
        return $tpls;
    }
}
if (!function_exists('save_storage_content')) {
    /**
     * 将内容存到Storage中，返回转存后的文件路径
     * @author rainfer <81818832@qq.com>
     *
     * @param string $ext
     * @param string $content
     *
     * @return array
     */
    function save_storage_content($ext = null, $content = null)
    {
        $rst  = [
            'path' => '',
            'name' => ''
        ];
        $path = 'data/upload/';
        if ($ext && $content) {
            do {
                $name    = uniqid() . '.' . $ext;
                $newfile = $path . date('Y-m-d/') . $name;
            } while (file_exists($newfile));
            $dir = dirname($newfile);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            if (file_put_contents($newfile, $content)) {
                $rst['path'] = $newfile;
                $rst['name'] = $name;
            };
        }
        return $rst;
    }
}
if (!function_exists('parse_config')) {
    /**
     * 解析配置参数
     * @author rainfer <81818832@qq.com>
     *
     * @param array $temp_arr
     *
     * @return array
     */
    function parse_config($temp_arr)
    {
        $config = [];
        $i        = 0;
        foreach ($temp_arr as $value) {
            $type = isset($value[0]) ? $value[0] : '';
            $i++;
            switch ($type) {
                case 'checkbox':
                case 'radio':
                case 'select':
                case 'selects':
                    $k          = isset($value[1]) ? $value[1] : ($type . $i);
                    $v          = isset($value[4]) ? $value[4] : '';
                    $config[$k] = $v;
                    break;
                case 'color':
                case 'date':
                case 'daterange':
                case 'datetime':
                case 'file':
                case 'files':
                case 'icon':
                case 'image':
                case 'images':
                case 'mask':
                case 'range':
                case 'switch':
                case 'tag':
                case 'text':
                case 'textarea':
                case 'time':
                case 'ueditor':
                    $k          = isset($value[1]) ? $value[1] : ($type . $i);
                    $v          = isset($value[3]) ? $value[3] : '';
                    $config[$k] = $v;
                    break;
                case 'linkage':
                    $datas = isset($value[2]) ? $value[2] : [];
                    if (is_array($datas) && $datas) {
                        foreach ($datas as $data) {
                            $k          = $data['name'];
                            $v          = $data['value'];
                            $config[$k] = $v;
                        }
                    }
                    break;
                case 'group':
                    $groups = isset($value[1]) ? $value[1] : [];
                    if (is_array($groups) && $groups) {
                        foreach ($groups as $group) {
                            if (isset($group['dropdown']) && $group['dropdown']) {
                                foreach ($group['dropdown'] as $dropdown) {
                                    $items = isset($dropdown['items']) ? $dropdown['items'] : [];
                                    if ($items) {
                                        foreach ($items as $item) {
                                            $_type = isset($item[0]) ? $item[0] : '';
                                            $i++;
                                            switch ($_type) {
                                                case 'checkbox':
                                                case 'radio':
                                                case 'select':
                                                case 'selects':
                                                    $k          = isset($item[1]) ? $item[1] : ($_type . $i);
                                                    $v          = isset($item[4]) ? $item[4] : '';
                                                    $config[$k] = $v;
                                                    break;
                                                case 'color':
                                                case 'date':
                                                case 'daterange':
                                                case 'datetime':
                                                case 'file':
                                                case 'files':
                                                case 'icon':
                                                case 'image':
                                                case 'images':
                                                case 'mask':
                                                case 'range':
                                                case 'switch':
                                                case 'tag':
                                                case 'text':
                                                case 'textarea':
                                                case 'time':
                                                case 'ueditor':
                                                    $k          = isset($item[1]) ? $item[1] : ($_type . $i);
                                                    $v          = isset($item[3]) ? $item[3] : '';
                                                    $config[$k] = $v;
                                                    break;
                                                case 'linkage':
                                                    $datas = isset($item[2]) ? $item[2] : [];
                                                    if (is_array($datas) && $datas) {
                                                        foreach ($datas as $data) {
                                                            $k          = $data['name'];
                                                            $v          = $data['value'];
                                                            $config[$k] = $v;
                                                        }
                                                    }
                                                    break;
                                            }
                                        }
                                    }
                                }
                            } else {
                                $items = isset($group['items']) ? $group['items'] : [];
                                if ($items) {
                                    foreach ($items as $item) {
                                        $_type = isset($item[0]) ? $item[0] : '';
                                        $i++;
                                        switch ($_type) {
                                            case 'checkbox':
                                            case 'radio':
                                            case 'select':
                                            case 'selects':
                                                $k          = isset($item[1]) ? $item[1] : ($_type . $i);
                                                $v          = isset($item[4]) ? $item[4] : '';
                                                $config[$k] = $v;
                                                break;
                                            case 'color':
                                            case 'date':
                                            case 'daterange':
                                            case 'datetime':
                                            case 'file':
                                            case 'files':
                                            case 'icon':
                                            case 'image':
                                            case 'images':
                                            case 'mask':
                                            case 'range':
                                            case 'switch':
                                            case 'tag':
                                            case 'text':
                                            case 'textarea':
                                            case 'time':
                                            case 'ueditor':
                                                $k          = isset($item[1]) ? $item[1] : ($_type . $i);
                                                $v          = isset($item[3]) ? $item[3] : '';
                                                $config[$k] = $v;
                                                break;
                                            case 'linkage':
                                                $datas = isset($item[2]) ? $item[2] : [];
                                                if (is_array($datas) && $datas) {
                                                    foreach ($datas as $data) {
                                                        $k          = $data['name'];
                                                        $v          = $data['value'];
                                                        $config[$k] = $v;
                                                    }
                                                }
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
            }
        }
        return $config;
    }
}
if (!function_exists('regular_domain')) {
    /**
     * @param $domain
     *
     * @return string
     */
    function regular_domain($domain)
    {
        if (substr($domain, 0, 7) == 'http://') {
            $domain = substr($domain, 7);
        } elseif (substr($domain, 0, 8) == 'https://') {
            $domain = substr($domain, 8);
        }
        if (strpos($domain, '/') !== false) {
            $domain = substr($domain, 0, strpos($domain, '/'));
        }
        return strtolower($domain);
    }
}
if (!function_exists('top_domain')) {
    /**
     * @param $domain
     *
     * @return string
     */
    function top_domain($domain)
    {
        $domain = regular_domain($domain);
        $iana_root = array (
            'ac',
            'ad',
            'ae',
            'aero',
            'af',
            'ag',
            'ai',
            'al',
            'am',
            'an',
            'ao',
            'aq',
            'ar',
            'arpa',
            'as',
            'asia',
            'at',
            'au',
            'aw',
            'ax',
            'az',
            'ba',
            'bb',
            'bd',
            'be',
            'bf',
            'bg',
            'bh',
            'bi',
            'biz',
            'bj',
            'bl',
            'bm',
            'bn',
            'bo',
            'bq',
            'br',
            'bs',
            'bt',
            'bv',
            'bw',
            'by',
            'bz',
            'ca',
            'cat',
            'cc',
            'cd',
            'cf',
            'cg',
            'ch',
            'ci',
            'ck',
            'cl',
            'cm',
            'cn',
            'co',
            'com',
            'coop',
            'cr',
            'cu',
            'cv',
            'cw',
            'cx',
            'cy',
            'cz',
            'de',
            'dj',
            'dk',
            'dm',
            'do',
            'dz',
            'ec',
            'edu',
            'ee',
            'eg',
            'eh',
            'er',
            'es',
            'et',
            'eu',
            'fi',
            'fj',
            'fk',
            'fm',
            'fo',
            'fr',
            'ga',
            'gb',
            'gd',
            'ge',
            'gf',
            'gg',
            'gh',
            'gi',
            'gl',
            'gm',
            'gn',
            'gov',
            'gp',
            'gq',
            'gr',
            'gs',
            'gt',
            'gu',
            'gw',
            'gy',
            'hk',
            'hm',
            'hn',
            'hr',
            'ht',
            'hu',
            'id',
            'ie',
            'il',
            'im',
            'in',
            'info',
            'int',
            'io',
            'iq',
            'ir',
            'is',
            'it',
            'je',
            'jm',
            'jo',
            'jobs',
            'jp',
            'ke',
            'kg',
            'kh',
            'ki',
            'km',
            'kn',
            'kp',
            'kr',
            'kw',
            'ky',
            'kz',
            'la',
            'lb',
            'lc',
            'li',
            'lk',
            'lr',
            'ls',
            'lt',
            'lu',
            'lv',
            'ly',
            'ma',
            'mc',
            'md',
            'me',
            'mf',
            'mg',
            'mh',
            'mil',
            'mk',
            'ml',
            'mm',
            'mn',
            'mo',
            'mobi',
            'mp',
            'mq',
            'mr',
            'ms',
            'mt',
            'mu',
            'museum',
            'mv',
            'mw',
            'mx',
            'my',
            'mz',
            'na',
            'name',
            'nc',
            'ne',
            'net',
            'nf',
            'ng',
            'ni',
            'nl',
            'no',
            'np',
            'nr',
            'nu',
            'nz',
            'om',
            'org',
            'pa',
            'pe',
            'pf',
            'pg',
            'ph',
            'pk',
            'pl',
            'pm',
            'pn',
            'pr',
            'pro',
            'ps',
            'pt',
            'pw',
            'py',
            'qa',
            're',
            'ro',
            'rs',
            'ru',
            'rw',
            'sa',
            'sb',
            'sc',
            'sd',
            'se',
            'sg',
            'sh',
            'si',
            'sj',
            'sk',
            'sl',
            'sm',
            'sn',
            'so',
            'sr',
            'ss',
            'st',
            'su',
            'sv',
            'sx',
            'sy',
            'sz',
            'tc',
            'td',
            'tel',
            'tf',
            'tg',
            'th',
            'tj',
            'tk',
            'tl',
            'tm',
            'tn',
            'to',
            'tp',
            'tr',
            'travel',
            'tt',
            'tv',
            'tw',
            'tz',
            'ua',
            'ug',
            'uk',
            'um',
            'us',
            'uy',
            'uz',
            'va',
            'vc',
            've',
            'vg',
            'vi',
            'vn',
            'vu',
            'wf',
            'ws',
            'xxx',
            'ye',
            'yt',
            'za',
            'zm',
            'zw'
        );
        $sub_domain = explode('.', $domain);
        $top_domain = '';
        $top_domain_count = 0;
        for ($i = count($sub_domain) - 1; $i >= 0; $i --) {
            if ($i == 0) {
                break;
            }
            if (in_array($sub_domain [$i], $iana_root)) {
                $top_domain_count ++;
                $top_domain = '.' . $sub_domain [$i] . $top_domain;
                if ($top_domain_count >= 2) {
                    break;
                }
            }
        }
        $top_domain = $sub_domain[count($sub_domain) - $top_domain_count - 1] . $top_domain;
        return $top_domain;
    }
}
if (!function_exists('nav_url')) {
    /**
     * @param $url
     * @return string
     */
    function nav_url($url)
    {
        if (substr($url, 0, 7) ==='http://' || substr($url, 0, 8) ==='https://' || substr($url, 0, 2) ==='//') {
            return $url;
        } else {
            return url($url);
        }
    }
}
if (!function_exists('html_trim')) {
    /**
     * 截取待html的文本
     * @author rainfer <81818832@qq.com>
     * @param string $html
     * @param int $max
     * @param string $suffix
     * @return string;
     */
    function html_trim($html, $max, $suffix = '...')
    {
        $html = trim($html);
        if (strlen($html) <= $max) {
            return $html;
        }
        $non_paired_tags = ['br', 'hr', 'img', 'input', 'param'];
        $html            = preg_replace('/<img([^>]+)>/i', '', $html);
        $count           = 0;
        $tag_status      = 0;
        $nodes           = [];
        $segment         = '';
        $tag_name        = '';
        for ($i = 0; $i < strlen($html); $i++) {
            $char = $html[$i];
            $segment .= $char;
            if ($tag_status == 4) {
                $tag_status = 0;
            }
            if ($tag_status == 0 && $char == '<') {
                $tag_status = 1;
            }
            if ($tag_status == 1 && $char != '<') {
                $tag_status = 2;
                $tag_name   = '';
                $nodes[]    = [0, substr($segment, 0, strlen($segment) - 2), 'text', 0];
                $segment    = '<' . $char;
            }
            if ($tag_status == 2) {
                if ($char == ' ' || $char == '>' || $char == "\t") {
                    $tag_status = 3;
                } else {
                    $tag_name .= $char;
                }
            }
            if ($tag_status == 3 && $char == '>') {
                $tag_status = 4;
                $tag_name   = strtolower($tag_name);
                $tag_type   = 1;
                if (in_array($tag_name, $non_paired_tags)) {
                    $tag_type = 0;
                } elseif ($tag_name[0] == '/') {
                    $tag_type = 2;
                }
                $nodes[] = [1, $segment, $tag_name, $tag_type];
                $segment = '';
            }
            if ($tag_status == 0) {
                if ($char == '&') {
                    for ($e = 1; $e <= 10; $e++) {
                        if ($html[$i + $e] == ';') {
                            $segment .= substr($html, $i + 1, $e);
                            $i += $e;
                            break;
                        }
                    }
                } else {
                    $char_code = ord($char);
                    if ($char_code >= 224) {
                        $segment .= $html[$i + 1] . $html[$i + 2];
                        $i += 2;
                    } elseif ($char_code >= 129) {
                        $segment .= $html[$i + 1];
                        $i += 1;
                    }
                }
                $count++;
                if ($count == $max) {
                    $nodes[] = [0, $segment . $suffix, 'text', 0];
                    break;
                }
            }
        }
        $html           = '';
        $tag_open_stack = [];
        for ($i = 0; $i < count($nodes); $i++) {
            $node = $nodes[$i];
            if ($node[3] == 1) {
                array_push($tag_open_stack, $node[2]);
            } elseif ($node[3] == 2) {
                array_pop($tag_open_stack);
            }
            $html .= $node[1];
        }
        while ($tag_name = array_pop($tag_open_stack)) {
            $html .= '</' . $tag_name . '>';
        }
        return $html;
    }
}
