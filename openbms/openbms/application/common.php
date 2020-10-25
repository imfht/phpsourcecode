<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

!\think\Config::get('app_debug') && error_reporting(E_ERROR | E_PARSE);

// 应用公共文件

/**
 * 发送邮件
 * @param $email
 * @param $title
 * @param $content
 * @param null $config
 * @return bool
 */
function send_email($email, $title, $content, $config = null)
{
    $config = empty($config) ? unserialize(config('email_server')) : $config;
    $mail   = new \PHPMailer\PHPMailer\PHPMailer(true); // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = 0;                           // Enable verbose debug output
        $mail->isSMTP();                                // Set mailer to use SMTP
        $mail->Host       = $config['host'];            // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                       // Enable SMTP authentication
        $mail->Username   = $config['username'];        // SMTP username
        $mail->Password   = $config['password'];        // SMTP password
        $mail->SMTPSecure = $config['secure'];          // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = $config['port'];            // TCP port to connect to
        $mail->CharSet    = 'UTF-8';
        //Recipients
        $mail->setFrom($config['username'], $config['fromname']);
        $mail->addAddress($email);                      // Name is optional
        //Content
        $mail->isHTML(true);                            // Set email format to HTML
        $mail->Subject = $title;
        $mail->Body    = $content;
        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

/**
 * 数组转xls格式的excel文件
 * @param $data
 * @param $title
 * 示例数据
 * $data = [
 *     [NULL, 2010, 2011, 2012],
 *     ['Q1', 12, 15, 21],
 *     ['Q2', 56, 73, 86],
 *     ['Q3', 52, 61, 69],
 *     ['Q4', 30, 32, 10],
 * ];
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 * @throws PHPExcel_Writer_Exception
 */
function export_excel($data, $title)
{
    // 最长执行时间,php默认为30秒,这里设置为0秒的意思是保持等待知道程序执行完成
    ini_set('max_execution_time', '0');
    $phpexcel = new PHPExcel();

    // Set properties 设置文件属性
    $properties = $phpexcel->getProperties();
    $properties->setCreator("Boge");//作者是谁 可以不设置
    $properties->setLastModifiedBy("Boge");//最后一次修改的作者
    $properties->setTitle($title);//设置标题
    $properties->setSubject('测试');//设置主题
    $properties->setDescription("备注");//设置备注
    $properties->setKeywords("关键词");//设置关键词
    $properties->setCategory("类别");//设置类别

    $sheet = $phpexcel->getActiveSheet();
    $sheet->fromArray($data);
    $sheet->setTitle('Sheet1'); // 设置sheet名称
    $phpexcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=" . $title . ".xls");
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $objwriter->save('php://output');
    exit;
}

/**
 * http请求
 * @param string $url 请求的地址
 * @param array $data 发送的参数
 */
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * 格式化字节大小
 * @param  number $size 字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 把json字符串转数组
 * @param json $p
 * @return array
 */
function json_to_array($p)
{
    if (mb_detect_encoding($p, array('ASCII', 'UTF-8', 'GB2312', 'GBK')) != 'UTF-8') {
        $p = iconv('GBK', 'UTF-8', $p);
    }
    return json_decode($p, true);
}

// 生成唯一订单号
function build_order_no()
{
    return date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/**
 * 获取随机位数数字
 * @param  integer $len 长度
 * @return string
 */
function rand_number($len = 6)
{
    return substr(str_shuffle(str_repeat('0123456789', 10)), 0, $len);
}

/**
 * 验证手机号是否正确
 * @param number $mobile
 */
function check_mobile($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$|^19[\d]{9}$#', $mobile) ? true : false;
}

/**
 * 验证固定电话格式
 * @param string $tel 固定电话
 * @return boolean
 */
function check_tel($tel)
{
    $chars = "/^([0-9]{3,4}-)?[0-9]{7,8}$/";
    if (preg_match($chars, $tel)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 验证邮箱格式
 * @param string $email 邮箱
 * @return boolean
 */
function check_email($email)
{
    $chars = "/^[0-9a-zA-Z]+(?:[\_\.\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i";
    if (preg_match($chars, $email)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 验证QQ号码是否正确
 * @param number $mobile
 */
function check_qq($qq)
{
    if (!is_numeric($qq)) {
        return false;
    }
    return true;
}

/**
 * 验证密码长度
 * @param string $password 需要验证的密码
 * @param int $min 最小长度
 * @param int $max 最大长度
 */
function check_password($password, $min, $max)
{
    if (strlen($password) < $min || strlen($password) > $max) {
        return false;
    }
    return true;
}

/**
 * 是否在微信中
 */
function in_wechat()
{
    return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
}

/**
 * 配置值解析成数组
 * @param string $value 配置值
 * @return array|string
 */
function parse_attr($value)
{
    if (is_array($value)) {
        return $value;
    }
    $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
    if (strpos($value, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

/**
 * 数组层级缩进转换
 * @param array $array 源数组
 * @param int $pid
 * @param int $level
 * @return array
 */
function list_to_level($array, $pid = 0, $level = 1)
{
    static $list = [];
    foreach ($array as $k => $v) {
        if ($v['pid'] == $pid) {
            $v['level'] = $level;
            $list[]     = $v;
            unset($array[$k]);
            list_to_level($array, $v['id'], $level + 1);
        }
    }
    return $list;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent           = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array $list 过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = 'children', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }

        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }

        return $resultSet;
    }
    return false;
}

// 驼峰命名法转下划线风格
function to_under_score($str)
{
    $array = array();
    for ($i = 0; $i < strlen($str); $i++) {
        if ($str[$i] == strtolower($str[$i])) {
            $array[] = $str[$i];
        } else {
            if ($i > 0) {
                $array[] = '_';
            }
            $array[] = strtolower($str[$i]);
        }
    }
    $result = implode('', $array);
    return $result;
}

/**
 * 自动生成新尺寸的图片
 * @param string $filename 文件名
 * @param int $width 新图片宽度
 * @param int $height 新图片高度(如果没有填写高度，把高度等比例缩小)
 * @param int $type 缩略图裁剪类型
 *                    1 => 等比例缩放类型
 *                    2 => 缩放后填充类型
 *                    3 => 居中裁剪类型
 *                    4 => 左上角裁剪类型
 *                    5 => 右下角裁剪类型
 *                    6 => 固定尺寸缩放类型
 * @return string     生成缩略图的路径
 */
function resize($filename, $width, $height = null, $type = 1)
{
    if (!is_file(ROOT_PATH . 'public/' . $filename)) {
        return;
    }
    // 如果没有填写高度，把高度等比例缩小
    if ($height == null) {
        $info = getimagesize(ROOT_PATH . 'public/' . $filename);
        if ($width > $info[0]) {
            // 如果缩小后宽度尺寸大于原图尺寸，使用原图尺寸
            $width  = $info[0];
            $height = $info[1];
        } elseif ($width < $info[0]) {
            $height = floor($info[1] * ($width / $info[0]));
        } elseif ($width == $info[0]) {
            return $filename;
        }
    }
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $old_image = $filename;
    $new_image = mb_substr($filename, 0, mb_strrpos($filename, '.')) . '_' . $width . 'x' . $height . '.' . $extension;
    $new_image = str_replace('image', 'cache', $new_image); // 缩略图存放于cache文件夹
    if (!is_file(ROOT_PATH . 'public/' . $new_image) || filectime(ROOT_PATH . 'public/' . $old_image) > filectime(ROOT_PATH . 'public/' . $new_image)) {
        $path        = '';
        $directories = explode('/', dirname(str_replace('../', '', $new_image)));
        foreach ($directories as $directory) {
            $path = $path . '/' . $directory;
            if (!is_dir(ROOT_PATH . 'public/' . $path)) {
                @mkdir(ROOT_PATH . 'public/' . $path, 0777);
            }
        }
        list($width_orig, $height_orig) = getimagesize(ROOT_PATH . 'public/' . $old_image);
        if ($width_orig != $width || $height_orig != $height) {
            $image = \think\Image::open(ROOT_PATH . 'public/' . $old_image);
            switch ($type) {
                case 1:
                    $image->thumb($width, $height, \think\Image::THUMB_SCALING);
                    break;

                case 2:
                    $image->thumb($width, $height, \think\Image::THUMB_FILLED);
                    break;

                case 3:
                    $image->thumb($width, $height, \think\Image::THUMB_CENTER);
                    break;

                case 4:
                    $image->thumb($width, $height, \think\Image::THUMB_NORTHWEST);
                    break;

                case 5:
                    $image->thumb($width, $height, \think\Image::THUMB_SOUTHEAST);
                    break;

                case 5:
                    $image->thumb($width, $height, \think\Image::THUMB_FIXED);
                    break;

                default:
                    $image->thumb($width, $height, \think\Image::THUMB_SCALING);
                    break;
            }
            $image->save(ROOT_PATH . 'public/' . $new_image);
        } else {
            copy(ROOT_PATH . 'public/' . $old_image, ROOT_PATH . 'public/' . $new_image);
        }
    }
    return $new_image;
}

/**
 * hashids加密函数
 * @param $id
 * @param string $salt
 * @param int $min_hash_length
 * @return bool|string
 * @throws Exception
 */
function hashids_encode($id, $salt = '', $min_hash_length = 6)
{
    return (new Hashids\Hashids($salt, $min_hash_length))->encode($id);
}

/**
 * hashids解密函数
 * @param $id
 * @param string $salt
 * @param int $min_hash_length
 * @return null
 * @throws Exception
 */
function hashids_decode($id, $salt = '', $min_hash_length = 6)
{
    $id = (new Hashids\Hashids($salt, $min_hash_length))->decode($id);
    if (empty($id)) {
        return null;
    }
    return $id['0'];
}

/**
 * 保存后台用户行为
 * @param string $remark 日志备注
 */
function insert_admin_log($remark)
{
    if (session('?admin_auth')) {
        db('adminLog')->insert([
            'admin_id'    => session('admin_auth.admin_id'),
            'username'    => session('admin_auth.username'),
            'useragent'   => request()->server('HTTP_USER_AGENT'),
            'ip'          => request()->ip(),
            'url'         => request()->url(true),
            'method'      => request()->method(),
            'type'        => request()->type(),
            'param'       => json_encode(request()->param()),
            'remark'      => $remark,
            'create_time' => time(),
        ]);
    }
}

/**
 * 保存前台用户行为
 * @param string $remark 日志备注
 */
function insert_user_log($remark)
{
    if (session('?user_auth')) {
        db('userLog')->insert([
            'user_id'     => session('user_auth.user_id'),
            'username'    => session('user_auth.username'),
            'useragent'   => request()->server('HTTP_USER_AGENT'),
            'ip'          => request()->ip(),
            'url'         => request()->url(true),
            'method'      => request()->method(),
            'type'        => request()->type(),
            'param'       => json_encode(request()->param()),
            'remark'      => $remark,
            'create_time' => time(),
        ]);
    }
}

/**
 * 检测管理员是否登录
 * @return integer 0/管理员ID
 */
function is_admin_login()
{
    $admin = session('admin_auth');
    if (empty($admin)) {
        return 0;
    } else {
        return session('admin_auth_sign') == data_auth_sign($admin) ? $admin['admin_id'] : 0;
    }
}

/**
 * 检测会员是否登录
 * @return integer 0/管理员ID
 */
function is_user_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['user_id'] : 0;
    }
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    // 数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); // 排序
    $code = http_build_query($data); // url编码并生成query字符串
    $sign = sha1($code); // 生成签名
    return $sign;
}

/**
 * 清除系统缓存
 * @param null $directory
 * @return bool
 */
function clear_cache($directory = null)
{
    $directory = empty($directory) ? ROOT_PATH . 'runtime/cache/' : $directory;
    if (is_dir($directory) == false) {
        return false;
    }
    $handle = opendir($directory);
    while (($file = readdir($handle)) !== false) {
        if ($file != "." && $file != "..") {
            is_dir($directory . '/' . $file) ?
                clear_cache($directory . '/' . $file) :
                unlink($directory . '/' . $file);
        }
    }
    if (readdir($handle) == false) {
        closedir($handle);
        rmdir($directory);
    }
}