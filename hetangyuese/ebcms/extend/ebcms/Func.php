<?php
namespace ebcms;
class Func
{

    // 检测验证码
    public static function check_captcha($str,$key=null){
        $config = \ebcms\Config::get('home.captcha');
        $captcha = new \think\captcha\Captcha($config);
        if ($captcha->check($str, $key)) {
            return true;
        }
        return false;
    }

    // 删除目录
    public static function deldir($dir)
    {
        if (is_dir($dir)) {
            $dh = opendir($dir);
            while ($file = readdir($dh)) {
                if ($file != "." && $file != "..") {
                    $fullpath = $dir . "/" . $file;
                    if (!is_dir($fullpath)) {
                        unlink($fullpath);
                    } else {
                        self::deldir($fullpath);
                    }
                }
            }
            return true;
        }
        return false;
    }

    // 将多行特定字符串解析成数组
    // 类型1：abc:标题|链接
    // 类型2：高度|30cm
    public static function render_param($str)
    {
        if (!$str) {
            return [];
        }
        $arr = explode(PHP_EOL, \ebcms\Func::streol($str));
        $array = array();
        foreach ($arr as $key => $value) {
            if ($value) {
                if (strpos($value, ':')) {
                    $tmp = explode(':', $value);
                    if (strpos($tmp[1], '|')) {
                        $temp = explode('|', $tmp[1]);
                        foreach ($temp as $k => $v) {
                            $temp[$k] = $v;
                        }
                        $tmp[1] = $temp;
                    } else {
                        $tmp[1] = $tmp[1];
                    }
                    $array[$tmp[0]] = $tmp[1];
                } else {
                    if (strpos($value, '|')) {
                        $temp = explode('|', $value);
                        foreach ($temp as $k => $v) {
                            $temp[$k] = $v;
                        }
                        $array[] = $temp;
                    } else {
                        $array[] = $value;
                    }
                }
            }
        }
        return $array;
    }

    // 获取多维数组中点语法的值
    public static function get_point_value($data = [], $str)
    {
        $pos = strpos($str, '.');
        if (false === $pos) {
            return isset($data[$str]) ? $data[$str] : null;
        } else {
            $key = mb_substr($str, 0, $pos);
            if (isset($data[$key])) {
                return self::get_point_value($data[$key], mb_substr($str, $pos + 1));
            } else {
                return null;
            }
        }
    }

    // 密码加密
    public static function crypt_pwd($password, $email = '')
    {
        return md5($password . $email . ' love ebcms forever!');
    }

    // 
    public static function streol($str){
        $str = str_replace(["\r\n","\r"], "\n", $str);
        return str_replace("\n", PHP_EOL, $str);
    }

    // 发送邮件
    public static function sendmail($address, $name = '收件人', $subject = '测试邮件！', $body = '测试内容！')
    {
        $config = \ebcms\Config::get('system.email');
        vendor('PHPMailer.PHPMailerAutoload');
        $mail = new \PHPMailer();
        $mail->IsSMTP();
        $mail->CharSet = 'utf-8';
        $mail->Host = $config['host'];
        $mail->Port = $config['port'];
        $mail->SMTPAuth = $config['smtpauth'];
        $mail->Username = $config['name'];
        $mail->Password = $config['password'];
        $mail->From = $config['from'];
        $mail->FromName = $config['fromname'];
        $mail->IsHTML($config['html']);

        $mail->AddAddress($address, $name);
        $mail->Subject = $subject;
        $mail->Body = $body;
        return $mail->Send();
    }

    // 渲染模板
    public static function render_tpl($html,$data=[]){
        $view = new \think\View();
        if ($data) {
            $view -> assign($data);
        }
        return $view -> display($html);
    }

    // 生成select选项
    public static function select($data,$config,$value=null){
        $config['rootitem'] = isset($config['rootitem'])?$config['rootitem']:false;
        $config['valuefield'] = (isset($config['valuefield']) && $config['valuefield'])?$config['valuefield']:'id';
        $config['titlefield'] = (isset($config['titlefield']) && $config['titlefield'])?$config['titlefield']:'title';
        $config['levelstr'] = (isset($config['levelstr']) && $config['levelstr'])?$config['levelstr']:'&nbsp;&nbsp;&nbsp;&nbsp;';
        $config['rootstr'] = (isset($config['rootstr']) && $config['rootstr'])?$config['rootstr']:'┣';
        $config['group'] = isset($config['group'])?$config['group']:false;
        $config['tree'] = isset($config['tree'])?$config['tree']:false;
        $str = '';
        if ($config['rootitem']) {
            $str .= '<option value="0">请选择</option>';
        }
        if($config['group']){
            $data = \ebcms\Tree::group($data);
            if($config['tree']){
                foreach($data as $k => $v){
                    $str .= '<optgroup label="' . $k . '">';
                    $res = [];
                    \ebcms\Tree::leveltree(\ebcms\Tree::tree($data[$k]),$res);
                    $str .= self::_select($res,$config,$value);
                    $str .= '</optgroup>';
                }
            }else{
                foreach($data as $k => $v){
                    $str .= '<optgroup label="' . $k . '">';
                    $str .= self::_select($v,$config,$value);
                    $str .= '</optgroup>';
                }
            }
        }elseif($config['tree']){
            $res = [];
            \ebcms\Tree::leveltree(\ebcms\Tree::tree($data),$res);
            $str .= self::_select($res,$config,$value);
        }else{
            $str .= self::_select($data,$config,$value);
        }
        return $str;
    }

    private static function _select($data,$config,$value=null){
        $str = '';
        foreach ($data as $k => $v) {
            $str .= '<option value="'.$v[$config['valuefield']].'"';
            if ($v[$config['valuefield']] == $value) {
                $str .= ' selected="selected"';
            }
            $str .= '>';
            if (isset($v['_level'])) {
                $str .= str_repeat($config['levelstr'], $v['_level']).$config['rootstr'];
            }
            $str .= $v[$config['titlefield']];
            $str .= '</option>';
        }
        return $str;
    }
    
    // 自定义分页
    public static function paginate($content, $pagestr='[page]')
    {
        $items = explode($pagestr, $content);

        $config   = \think\Config::get('paginate');

        $class = false !== strpos($config['type'], '\\') ? $config['type'] : '\\think\\paginator\\driver\\' . ucwords($config['type']);
        $page  = isset($config['page']) ? (int) $config['page'] : call_user_func([
            $class,
            'getCurrentPage',
        ], $config['var_page']);

        $page = $page < 1 ? 1 : $page;

        $config['path'] = isset($config['path']) ? $config['path'] : call_user_func([$class, 'getCurrentPath']);

        $total   = count($items);

        $x = $class::make($items, 1, $page, $total, false, $config);
        return [
            'page'  =>  $x -> render(),
            'content'  =>  $items[$page-1],
        ];
    }

    // 加密
    public static function eb_encrypt($str, $salt=null, $expire=99999)
    {
        return \ebcms\Crypt::encode($str, $salt?:\think\Config::get('safe_code'), $expire);
    }

    // 解密
    public static function eb_decrypt($str, $salt=null)
    {
        return \ebcms\Crypt::decode($str, $salt?:\think\Config::get('safe_code'));
    }

}