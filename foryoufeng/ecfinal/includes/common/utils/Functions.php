<?php
/**
 * 通用的函数工具类
 * Created by PhpStorm.
 * User: root
 * Date: 7/8/16
 * Time: 5:31 PM
 */
if ( ! function_exists('session'))
{
    /**
     * session管理函数
     * @param string $name 定义session的名称 不传返回所有session 传null就清空session
     * @param string $value 不传表示获取值 传就设置值
     * @return array|null session数据
     */
    function session($name='',$value='')
    {
        if(''===$value){
            if(''===$name){
                return $_SESSION;
            }elseif(is_null($name)){// 清空sessio
                $_SESSION = array();
            }else{
                return isset($_SESSION[$name])?$_SESSION[$name]:null;
            }
        }else{
            $_SESSION[$name]  =  $value;
        }
        return null;
    }
}
if (! function_exists('I')) {
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
        if (strpos($name, '.')) { // 指定参数来源
            list($method, $name) = explode('.', $name, 2);
        } else { // 默认为自动判断
            $method = 'param';
        }
        switch (strtolower($method)) {
            case 'get':
                $input = & $_GET;
                break;
            case 'post':
                $input = & $_POST;
                break;
            case 'put':
                parse_str(file_get_contents('php://input'), $input);
                break;
            case 'param':
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'POST':
                        $input = $_POST;
                        break;
                    case 'PUT':
                        parse_str(file_get_contents('php://input'), $input);
                        break;
                    default:
                        $input = $_GET;
                }
                break;
            case 'path':
                $input = array();
                if (!empty($_SERVER['PATH_INFO'])) {
                    $depr = '/';
                    $input = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
                }
                break;
            case 'request':
                $input = & $_REQUEST;
                break;
            case 'session':
                $input = & $_SESSION;
                break;
            case 'cookie':
                $input = & $_COOKIE;
                break;
            case 'server':
                $input = & $_SERVER;
                break;
            case 'globals':
                $input = & $GLOBALS;
                break;
            case 'data':
                $input = & $datas;
                break;
            default:
                return NULL;
        }
        if ('' == $name) { // 获取全部变量
            $data = $input;
            array_walk_recursive($data, 'filter_exp');
            $filters = isset($filter) ? $filter : 'htmlspecialchars';
            if ($filters) {
                if (is_string($filters)) {
                    $filters = explode(',', $filters);
                }
                foreach ($filters as $filter) {
                    $data = array_map_recursive($filter, $data); // 参数过滤
                }
            }
        } elseif (isset($input[$name])) { // 取值操作
            $data = $input[$name];
            is_array($data) && array_walk_recursive($data, 'filter_exp');
            $filters = isset($filter) ? $filter : 'htmlspecialchars';
            if ($filters) {
                if (is_string($filters)) {
                    $filters = explode(',', $filters);
                } elseif (is_int($filters)) {
                    $filters = array(
                        $filters
                    );
                }

                foreach ($filters as $filter) {
                    if (function_exists($filter)) {
                        $data = is_array($data) ? array_map_recursive($filter, $data) : $filter($data); // 参数过滤
                    } else {
                        $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                        if (false === $data) {
                            return isset($default) ? $default : NULL;
                        }
                    }
                }
            }
        } else { // 变量默认值
            $data = isset($default) ? $default : NULL;
        }
        return $data;
    }
}
if ( ! function_exists('M'))
{
    /**
     * 实例化模型类 格式 [资源://][模块/]模型
     * @param string $name 资源地址
     * @param string $layer 模型层名称
     * @return
     */
    function M($name='') {
        if(empty($name)) return false;
        static $_model  =   array();
        $layer          =   '';
        if(isset($_model[$name])){
            return $_model[$name];
        }

        $file=Models.ucfirst(strtolower($name)).'Model.php';
        if(is_file($file)) {
            include_once $file;
            $class          =   ucfirst(strtolower($name)).'Model';
            $model      =   new $class(basename($name));
        }else {
            $model      =  null;
        }
        $_model[$name.$layer]  =  $model;
        return $model;
    }
}
if(! function_exists('S')){
    /**
     * 缓存管理
     * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
     * @param mixed $value 缓存值
     * @param mixed $options 缓存参数
     * @return mixed
     */
    function S($name,$value='',$options=null) {
        static $cache   =   '';
        if(is_array($options)){
            // 缓存操作的同时初始化
            $type       =   isset($options['type'])?$options['type']:'';
            $cache      =   Cache::getInstance($type,$options);
        }elseif(is_array($name)) { // 缓存初始化
            $type       =   isset($name['type'])?$name['type']:'';
            $cache      =   Cache::getInstance($type,$options);
            return $cache;
        }elseif(empty($cache)) { // 自动初始化
            $cache      =   Cache::getInstance();
        }
        if(''=== $value){ // 获取缓存
            return $cache->get($name);
        }elseif(is_null($value)) { // 删除缓存
            return $cache->rm($name);
        }else { // 缓存数据
            if(is_array($options)) {
                $expire     =   isset($options['expire'])?$options['expire']:NULL;
            }else{
                $expire     =   is_numeric($options)?$options:NULL;
            }
            return $cache->set($name, $value, $expire);
        }
    }
}
if(! function_exists('parse_name')){
    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @param string $name 字符串
     * @param integer $type 转换类型
     * @return string
     */
    function parse_name($name, $type=0) {
        if ($type) {
            return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
        } else {
            return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
    }
}
if(!function_exists('N')){
    /**
     * 设置和获取统计数据
     * 使用方法:
     * <code>
     * N('db',1); // 记录数据库操作次数
     * N('read',1); // 记录读取次数
     * echo N('db'); // 获取当前页面数据库的所有操作次数
     * echo N('read'); // 获取当前页面读取次数
     * </code>
     * @param string $key 标识位置
     * @param integer $step 步进值
     * @param boolean $save 是否保存结果
     * @return mixed
     */
    function N($key, $step=0,$save=false) {
        static $_num    = array();
        if (!isset($_num[$key])) {
            $_num[$key] = (false !== $save)? S('N_'.$key) :  0;
        }
        if (empty($step)){
            return $_num[$key];
        }else{
            $_num[$key] = $_num[$key] + (int)$step;
        }
        if(false !== $save){ // 保存结果
            S('N_'.$key,$_num[$key],$save);
        }
        return null;
    }
}
if(!function_exists('G')){
    /**
     * 记录和统计时间（微秒）和内存使用情况
     * 使用方法:
     * <code>
     * G('begin'); // 记录开始标记位
     * // ... 区间运行代码
     * G('end'); // 记录结束标签位
     * echo G('begin','end',6); // 统计区间运行时间 精确到小数后6位
     * echo G('begin','end','m'); // 统计区间内存使用情况
     * 如果end标记位没有定义，则会自动以当前作为标记位
     * 其中统计内存使用需要 MEMORY_LIMIT_ON 常量为true才有效
     * </code>
     * @param string $start 开始标签
     * @param string $end 结束标签
     * @param integer|string $dec 小数位或者m
     * @return mixed
     */
    function G($start, $end = '', $dec = 4) {
        static $_info = array();
        static $_mem = array();
        if (is_float($end)) { // 记录时间
            $_info[$start] = $end;
        } elseif (!empty($end)) { // 统计时间和内存使用
            if (!isset($_info[$end]))
                $_info[$end] = microtime(TRUE);
            if (MEMORY_LIMIT_ON && $dec == 'm') {
                if (!isset($_mem[$end]))
                    $_mem[$end] = memory_get_usage();
                return number_format(($_mem[$end] - $_mem[$start]) / 1024);
            } else {
                return number_format(($_info[$end] - $_info[$start]), $dec);
            }
        } else { // 记录时间和内存使用
            $_info[$start] = microtime(TRUE);
            if (MEMORY_LIMIT_ON)
                $_mem[$start] = memory_get_usage();
        }
    }
}
if(!function_exists('E')){
    /**
     * 抛出异常处理
     * @param string $msg 异常消息
     * @param integer $code 异常代码 默认为0
     * @throws Exception
     * @return void
     */
    function E($msg, $code=0) {
        throw new Exception($msg, $code);
    }
}
if(!function_exists('db_dns')){
    /**
     * 抛出异常处理
     * @param string $msg 异常消息
     * @param integer $code 异常代码 默认为0
     * @throws Exception
     * @return void
     */
    function db_dns($db_host, $db_name,$db_user,$db_pass,$db_port='3306') {
        static $_db_dns;
        if(!isset($_db_dns)){
            $_db_dns="mysql://{$db_user}:{$db_pass}@{$db_host}:$db_port/{$db_name}";
        }
        return $_db_dns;
    }
}
if(!function_exists('C')){
    /**
     * 获取和设置配置参数 支持批量定义
     * @param string|array $name 配置变量
     * @param mixed $value 配置值
     * @param mixed $default 默认值
     * @return mixed
     */
    function C($name=null, $value=null,$default=null) {
        static $_config = array();
        // 无参数时获取所有
        if (empty($name)) {
            return $_config;
        }
        // 优先执行设置获取或赋值
        if (is_string($name)) {
            if (!strpos($name, '.')) {
                $name = strtoupper($name);
                if (is_null($value))
                    return isset($_config[$name]) ? $_config[$name] : $default;
                $_config[$name] = $value;
                return null;
            }
            // 二维数组设置和获取支持
            $name = explode('.', $name);
            $name[0]   =  strtoupper($name[0]);
            if (is_null($value))
                return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
            $_config[$name[0]][$name[1]] = $value;
            return null;
        }
        // 批量设置
        if (is_array($name)){
            $_config = array_merge($_config, array_change_key_case($name,CASE_UPPER));
            return null;
        }
        return null; // 避免非法参数
    }
}

if(!function_exists('get_cate')){
    /**
     *无限分类列表
     *@param $types array 分类结果集
     *@param $html string 子级分类填充字符串
     *@param $pid int 父类id
     *@param $num int 填充字符串个数
     *@return array 返回排序后结果集
     */
    function get_cate($types, $html = '&nbsp;', $pid = 0, $num = 0){
        $arr = array();
        foreach($types['lists'] as $v){
            if($v['parent_id'] == $pid){
                $v['level'] = $num + 1;//可做自定义级别使用
                //$v['html'] = str_repeat($html, $num);//填充字符串个数
                $v['html'] = str_repeat('&nbsp;', $v['level']*4);//填充字符串个数
                $arr[] = $v;
                $arr = array_merge($arr, get_cate($types, $html, $v['cat_id'], $num + 1));//递归把子类压入父类数组后
            }
        }
        return $arr;
    }
}
if(!function_exists('manage_log')){
    /**
     * 记录管理员的操作
     * @param $msg
     * @return mixed
     */
    function manage_log($msg){
        $info['user_id']=session('admin_id');
        $info['log_time']=time();
        $info['ip_address']=real_ip();
        $info['log_info']=$msg;
        return M('adminlog')->add($info);
    }
}
if(!function_exists('get_month_time')){
    /**
     * 记录管理员的操作
     * @param $info
     * @return mixed
     */
    function get_month_time($start_time,$end_time){
        $time1 = $start_time; // 自动为00:00:00 时分秒
        $time2 = $end_time;

        $monarr = array();
        $monarr[] = $time1;

        while( ($time1 = strtotime('+1 month', $time1)) <= $time2){

            $monarr[] = $time1; // 取得递增月;
        }
        $monarr[] = $time2;
        sort($monarr);
        return $monarr;
    }
}
if(!function_exists('nowtime')){
    /**
     * @return string
     */
    function nowtime(){
        $h=date('G');
        $time='';
        if ($h<11) $time='早上好';
        else if ($h<13) $time= '中午好';
        else if ($h<17) $time= '下午好';
        else $time= '晚上好';
        return $time;
    }

}
if(!function_exists('redirect')){
    /**
     * 跳转
     * @param $url
     */
    function redirect($url){
        header("Location:".$url);
        exit(0);
    }

}
if(!function_exists('check_login')){
    /**
     * 判断登陆
     */
    function check_login(){
        if(!$_SESSION['user_id']){
            redirect("/user.html?redirect=".$_SERVER['REQUEST_URI']);
        }
    }

}
if(!function_exists('order_status')){
    /**
     * 获取订单状态
     * @param $order
     * @return string
     */
    function order_status($order){
        switch ($order['order_status']) {
            case Constant::OS_UNCONFIRMED://未确认
                $order_status = '未确认';
                break;
            case Constant::OS_CONFIRMED://已确认
                $order_status = '已确认';
                break;
            case Constant::OS_CANCELED://已取消
                $order_status = '<font color="red">取消</font>';
                break;
            case Constant::OS_RETURNED://退货
                $order_status = '退换货';
                break;
            default:
                $order_status = '';
        }
        switch ($order['pay_status']) {
            case Constant::PS_UNPAYED://未付款
                $pay_status = '未付款';
                break;
            case Constant::PS_PAYED://已付款
                $pay_status = '已付款';
                break;
            case Constant::PS_PARTPAY://部分付款
                $pay_status = '部分付款';
                break;
            default:
                $pay_status = '';
        }
        switch ($order['shipping_status']) {
            case Constant::SS_UNSHIPPED://未发货
                $shipping_status = '未发货';
                break;
            case Constant::SS_PREPARING://配货中
                $shipping_status = '配货中';
                break;
            case Constant::SS_SHIPPED://已发货
                $shipping_status = '已发货';
                break;
            case Constant::SS_RECEIVED://已收货
                $shipping_status = '已收货';
                break;
            case Constant::SS_PART://已发货(部分商品)
                $shipping_status = '已发货(部分商品)';
                break;
            default:
                $shipping_status = '';
        }
        return $order_status.','.$pay_status.','.$shipping_status;
    }
}
/*支付状态*/
if(!function_exists('pay_status')){
    /**
     * 获取订单状态
     * @param $order
     * @return string
     */
    function pay_status($pay_status){
        switch ($pay_status) {
            case Constant::PS_UNPAYED://未付款
                $pay_status = '未付款';
                break;
            case Constant::PS_PAYED://已付款
                $pay_status = '已付款';
                break;
            case Constant::PS_PARTPAY://部分付款
                $pay_status = '部分付款';
                break;
            default:
                $pay_status = '';
        }
        return $pay_status;
    }
}
/*物流状态*/
if(!function_exists('shipping_status')){
    /**
     * 获取订单状态
     * @param $order
     * @return string
     */
    function shipping_status($shipping_status){
        switch ($shipping_status) {
            case Constant::SS_UNSHIPPED://未发货
                $shipping_status = '未发货';
                break;
            case Constant::SS_PREPARING://配货中
                $shipping_status = '配货中';
                break;
            case Constant::SS_SHIPPED://已发货
                $shipping_status = '已发货';
                break;
            case Constant::SS_RECEIVED://已收货
                $shipping_status = '已收货';
                break;
            case Constant::SS_PART://已发货(部分商品)
                $shipping_status = '已发货(部分商品)';
                break;
            default:
                $shipping_status = '';
        }
        return $shipping_status;
    }
}

/*订单状态*/
if(!function_exists('order_status_alone')){
    /**
     * 获取订单状态
     * @param $order
     * @return string
     */
    function order_status_alone($order_status){
        switch ($order_status) {
            case Constant::OS_UNCONFIRMED://未确认
                $order_status = '未确认';
                break;
            case Constant::OS_CONFIRMED://已确认
                $order_status = '已确认';
                break;
            case Constant::OS_CANCELED://已取消
                $order_status = '已取消';
                break;
            case Constant::OS_RETURNED://退货
                $order_status = '退换货';
                break;
            default:
                $order_status = '';
        }
        return $order_status;
    }
}


/*截取UTF-8编码下字符串的函数*/
if(!function_exists('sub_str')){
    /**
     * 截取UTF-8编码下字符串的函数
     *
     * @param   string      $str        被截取的字符串
     * @param   int         $length     截取的长度
     * @param   bool        $append     是否附加省略号
     *
     * @return  string
     */
    function sub_str($str, $length = 0, $append = true)
    {
        $str = trim($str);
        $strlength = strlen($str);

        if ($length == 0 || $length >= $strlength)
        {
            return $str;
        }
        elseif ($length < 0)
        {
            $length = $strlength + $length;
            if ($length < 0)
            {
                $length = $strlength;
            }
        }

        if (function_exists('mb_substr'))
        {
            $newstr = mb_substr($str, 0, $length, EC_CHARSET);
        }
        elseif (function_exists('iconv_substr'))
        {
            $newstr = iconv_substr($str, 0, $length, EC_CHARSET);
        }
        else
        {
            //$newstr = trim_right(substr($str, 0, $length));
            $newstr = substr($str, 0, $length);
        }

        if ($append && $str != $newstr)
        {
            $newstr .= '...';
        }

        return $newstr;
    }
}

if(!function_exists('get_order_sn')) {
    /**
     * 得到新订单号
     * @return   string
     */
    function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand((double)microtime() * 1000000);

        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}
if(!function_exists('real_ip')){

    /**
     * 获得用户的真实IP地址
     *
     * @access  public
     * @return  string
     */
    function real_ip() {
        static $realip = NULL;

        if ($realip !== NULL) {
            return $realip;
        }

        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip) {
                    $ip = trim($ip);

                    if ($ip != 'unknown') {
                        $realip = $ip;

                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

        return $realip;
    }
}
if(!function_exists('check_email')) {
    /**
     * 得到新订单号
     * @return   string
     */
    function check_email($email)
    {
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        return preg_match($pattern,$email);
    }
}
if(!function_exists('utf_to_gbk')) {
    /**
     * 得到新订单号
     * @return   string
     */
    function utf_to_gbk($str)
    {
        return iconv('utf-8','gb2312',$str);
    }
}

if(!function_exists('price_format')) {
    /**
     * 格式化金额
     * @param $price
     * @return string
     */
    function price_format($price){
        if($price==='')
        {
            $price=0;
        }
        $price = number_format($price, 2);
        return sprintf('￥%s', $price);
    }
}

if(!function_exists('dump')) {
    /**
     * 格式化输出
     * @param $price
     * @return string
     */
     function dump($var)
        {
            foreach (func_get_args() as $var) {
                var_dump($var);
            }
            exit(0);
     }

}
if(!function_exists('doPost')) {
    /**
     * 执行post获取数据
     * @param $url
     * @param $para 请求的数据
     * @return mixed
     */
    function doPost($url,$post_data)
    {
        if (empty($url) || empty($post_data)) {
            return false;
        }

        $o = "";
        foreach ( $post_data as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }

}
if(!function_exists('doGet')){
    /**
     * 远程获取数据，GET模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     * @param $url 指定URL完整路径地址
     * @param $cacert_url 指定当前工作目录绝对路径
     * return 远程输出的数据
     */
    function doGet($url,$cacert_url){
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);

        return $responseText;
    }

}