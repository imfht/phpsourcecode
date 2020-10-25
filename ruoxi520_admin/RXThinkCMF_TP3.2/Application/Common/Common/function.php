<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 返回消息对象
 * 
 * @author 牧羊人
 * @date 2018-07-06
 */
if (defined('IS_API')) {
    function message($msg = "系统繁忙，请稍候再试" , $success = false , $data = array(), $code=0){
        $msg =  array("success" => $success , "msg" => $msg , "data" => $data);
        if($msg['success']) {
            $msg['code'] = 10000;
        }else {
            $msg['code'] = $code ? $code : 90000;
        }
        return $msg;
    }
}else{
    function message($msg = "操作成功" , $success = true , $data = array()){
        $msg =  array("success" => $success , "msg" => $msg , "data" => $data);
        return $msg;
    }
}

/**
 * 获取数组中某个字段的所有值
 * 
 * @author 牧羊人
 * @date 2018-10-23
 * @param unknown $value
 * @param string $name
 * @return multitype:
 */
function array_key_value($arr, $name=""){
    $return = array();
    if($arr){
        foreach($arr as $key=>$val){
            if($name){
                $return[] = $val[$name];
            }else{
                $return[] = $key;
            }
        }
    }
    $return = array_unique($return);
    return $return;
}

/**
 * 文件上传
 *
 * @author 牧羊人
 * @date 2018-07-16
 */
function uploadOne($files) {
    import ( 'Org.Net.UploadFile' );
    $upload = new \Org\Net\UploadFile (); // 实例化上传类
    $upload->maxSize = 1024*1024*20; // 设置附件上传大小
    // 设置附件上传类型
    $upload->allowExts = array("jpg","JPG","jpeg","JPEG","gif","GIF","png","PNG");

    $upload->savePath = UPLOAD_TEMP_PATH . "/"; // 设置附件上传目录
    $upload->thumb= false;//缩略图
    $result = $upload->uploadOne($files, $upload->savePath);
    if (!$result) {
        // 上传错误提示错误信息
        return $upload->getErrorMsg();
    }
    $imgArr = [];
    if(is_array($result)) {
        foreach ($result as $val) {
            $filePath = $val['savepath']. "/" .$val['savename'];
            if(strpos($filePath , IMG_PATH)!==FALSE) {
                $filePath = IMG_URL . str_replace(IMG_PATH, '', $filePath);
            }
            $imgArr[] = $filePath;
        }
    }
    return $imgArr;
}

/**
 * APP图片上传
 * 
 * @author 牧羊人
 * @date 2018-09-30
 */
function app_upload_image($path,$maxSize=52428800){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path = trim($path,'.');
    $path = trim($path,'/');
    $config = array(
        'rootPath'  =>'./',         //文件上传保存的根路径
        'savePath'  =>'./'.$path.'/',
        'exts'      => array("jpg","JPG","jpeg","JPEG","gif","GIF","png","PNG"),
        'maxSize'   => $maxSize,
        'autoSub'   => true,
    );
    $upload = new \Think\Upload($config);// 实例化上传类
    $info = $upload->upload();
    $imgArr = [];
    if($info) {
        foreach ($info as $v) {
            $imgArr[] = trim($v['savepath'],'.').$v['savename'];
        }
    }
    return $imgArr;
}

/**
 * 计算执行耗费时间
 * 
 * @author 牧羊人
 * @date 2018-07-18
 * @return number 返回耗时(单位：秒)
 */
function get_runtime(){
    $ntime = microtime(true);
    $total = $ntime-$GLOBALS['_beginTime'];
    return round($total,4);
}

/**
 * 检查是否登录
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @return boolean
 */
function check_login(){
    if (!empty($_SESSION['userId'])){
        return true;
    }else{
        return false;
    }
}

/**
 * 获取用户ID
 * 
 * @author 牧羊人
 * @date 2018-09-30
 */
function get_uid(){
    return $_SESSION['userId'];
}

/**
 * 实例化阿里云OSS
 * 
 * @author 牧羊人
 * @date 2018-09-30
 */
function get_oss() {
    vendor('Alioss.autoload');
    $config = C('ALIOSS_CONFIG');
    $oss = new \OSS\OssClient($config['KEY_ID'],$config['KEY_SECRET'],$config['END_POINT']);
    return $oss;
}

/**
 * 上传文件到OSS并删除本地文件
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $path 文件路径
 */
function oss_upload($path,$del=false) {
    // 获取bucket名称
    $bucket = C('ALIOSS_CONFIG.BUCKET');
    // 先统一去除左侧的.或者/ 再添加./
    $oss_path=ltrim($path,'./');
    $path='./'.$oss_path;
    if (file_exists($path)) {
        // 实例化oss类
        $oss = get_oss();
        // 上传到oss
        $oss->uploadFile($bucket,$oss_path,$path);
        //删除本地文件
        if($del) {
            unlink($path);
        }
        return true;
    }
    return false;
}

/**
 * 删除OSS上指定文件
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $object 文件路径(例如： /Public/README.md文件  传Public/README.md 即可)
 */
function oss_delet_object($object) {
    // 实例化oss类
    $oss = get_oss();
    // 获取bucket名称
    $bucket = C('ALIOSS_CONFIG.BUCKET');
    $result = $oss->deleteObject($bucket,$object);
}

/**
 * APP视频上传
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $path 上传路径
 * @param number $maxSize 最大上传大小
 */
function app_upload_video($path,$maxSize=52428800) {
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path = trim($path,'.');
    $path = trim($path,'/');
    $config = array(
        'rootPath'  =>'./',         //文件上传保存的根路径
        'savePath'  =>'./'.$path.'/',
        'exts'      => array('mp4','avi','3gp','rmvb','gif','wmv','mkv','mpg','vob','mov','flv','swf','mp3','ape','wma','aac','mmf','amr','m4a','m4r','ogg','wav','wavpack'),
        'maxSize'   => $maxSize,
        'autoSub'   => true,
    );
    $upload = new \Think\Upload($config);// 实例化上传类
    $info = $upload->upload();
    $list = [];
    if($info) {
        foreach ($info as $v) {
            $list[] = trim($v['savepath'],'.').$v['savename'];
        }
    }
    return $list;
}

/**
 * 上传文件类型控制,此方法仅限ajax上传使用
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * 
 * @param string $path 字符串 保存文件路径示例：/Uploads/img/
 * @param string $format 文件格式限制
 * @param string $maxSize 允许的上传文件最大值 52428800
 * @return booler 返回ajax的json格式数据
 */
function ajax_upload($path='file',$format='empty',$maxSize='52428800') {
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path = trim($path,'/');
    // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='uploads' ? ucfirst($path) : 'Uploads/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
        'photo' => array('jpg', 'jpeg', 'png'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
    );
    if(!empty($_FILES)){
        // 上传文件配置
        $config=array(
            'maxSize'   =>  $maxSize,       //   上传文件最大为50M
            'rootPath'  =>  './',           //文件上传保存的根路径
            'savePath'  =>  './'.$path.'/',         //文件上传的保存路径（相对于根路径）
            'saveName'  =>  array('uniqid',''),     //上传文件的保存规则，支持数组和字符串方式定义
            'autoSub'   =>  true,                   //  自动使用子目录保存上传文件 默认为true
            'exts'    =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
        );
        // 实例化上传
        $upload = new \Think\Upload($config);
        // 调用上传方法
        $info = $upload->upload();
        $data=array();
        if(!$info){
            // 返回错误信息
            $error = $upload->getError();
            $data['error_info'] = $error;
            echo json_encode($data);
        }else{
            // 返回成功信息
            foreach($info as $file){
                $data['name'] = trim($file['savepath'].$file['savename'],'.');
                echo json_encode($data);
            }
        }
    }
}

/**
 * 获取文件格式
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $filename 文件名称
 */
function get_file_format($filename) {
    // 取文件后缀名
    $str=strtolower(pathinfo($str, PATHINFO_EXTENSION));
    // 图片格式
    $image=array('webp','jpg','png','ico','bmp','gif','tif','pcx','tga','bmp','pxc','tiff','jpeg','exif','fpx','svg','psd','cdr','pcd','dxf','ufo','eps','ai','hdri');
    // 视频格式
    $video=array('mp4','avi','3gp','rmvb','gif','wmv','mkv','mpg','vob','mov','flv','swf','mp3','ape','wma','aac','mmf','amr','m4a','m4r','ogg','wav','wavpack');
    // 压缩格式
    $zip=array('rar','zip','tar','cab','uue','jar','iso','z','7-zip','ace','lzh','arj','gzip','bz2','tz');
    // 文档格式
    $text=array('exe','doc','ppt','xls','wps','txt','lrc','wfs','torrent','html','htm','java','js','css','less','php','pdf','pps','host','box','docx','word','perfect','dot','dsf','efe','ini','json','lnk','log','msi','ost','pcs','tmp','xlsb');
    // 匹配不同的结果
    switch ($str) {
        case in_array($str, $image):
            return 'image';
            break;
        case in_array($str, $video):
            return 'video';
            break;
        case in_array($str, $zip):
            return 'zip';
            break;
        case in_array($str, $text):
            return 'text';
            break;
        default:
            return 'image';
            break;
    }
}

/**
 * 获取完整的网络连接
 * 
 * @author 牧羊人
 * @date 2018-09-30
 */
function get_url($path) {
    // 如果是空；返回空
    if (empty($path)) {
        return '';
    }
    // 如果已经有http直接返回
    if (strpos($path, 'http://')!==false) {
        return $path;
    }
    // 判断是否使用了oss
    $alioss = C('ALIOSS_CONFIG');
    if (empty($alioss['KEY_ID'])) {
        return 'http://'.$_SERVER['HTTP_HOST'].$path;
    }else{
        return 'http://'.$alioss['BUCKET'].'.'.$alioss['END_POINT'].$path;
    }
}

/**
 * 删除指定的标签和内容
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $tags 需要删除的标签数组
 * @param unknown $str 数据源
 * @param number $content 是否删除标签内的内容 0保留内容 1不保留内容
 */
function strip_html_tags($tags,$str,$content=0) {
    if($content){
        $html = array();
        foreach ($tags as $tag) {
            $html[] = '/(<'.$tag.'.*?>[\s|\S]*?<\/'.$tag.'>)/';
        }
        $data = preg_replace($html,'',$str);
    }else{
        $html = array();
        foreach ($tags as $tag) {
            $html[] = "/(<(?:\/".$tag."|".$tag.")[^>]*>)/i";
        }
        $data = preg_replace($html, '', $str);
    }
    return $data;
}

/**
 * 字符串截取
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * 
 * @param unknown $str 需要转换的字符串
 * @param number $start 开始位置
 * @param unknown $length 截取长度
 * @param string $suffix 截断显示字符
 * @param string $charset 编码格式
 */
function sub_str($str, $start=0, $length, $suffix=true, $charset="utf-8") {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    $omit=mb_strlen($str) >=$length ? '...' : '';
    return $suffix ? $slice.$omit : $slice;
}

/**
 * 按符号截取字符串的指定部分
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * 
 * @param unknown $str 需要截取的字符串
 * @param unknown $sign 需要截取的符号
 * @param unknown $number 如是正数以0为起点从左向右截  负数则从右向左截
 * @return string 返回截取的内容
 */
/* 示例
 $str='123/456/789';
 cut_str($str,'/',0);  返回 123
 cut_str($str,'/',-1);  返回 789
 cut_str($str,'/',-2);  返回 456
 */
function cut_str($str,$sign,$number) {
    $array = explode($sign, $str);
    $length = count($array);
    if($number<0){
        $new_array = array_reverse($array);
        $abs_number = abs($number);
        if($abs_number>$length){
            return 'error';
        }else{
            return $new_array[$abs_number-1];
        }
    }else{
        if($number>=$length){
            return 'error';
        }else{
            return $array[$number];
        }
    }
}

/**
 * 显示验证码
 * 
 * @author 牧羊人
 * @date 2018-09-30
 */
function show_verify($config=array()) {
    if(!$config){
        $config = array(
            'codeSet'=>'1234567890',
            'fontSize'=>30,
            'useCurve'=>false,
            'imageH'=>60,
            'imageW'=>240,
            'length'=>4,
            'fontttf'=>'4.ttf',
        );
    }
    $verify = new \Think\Verify($config);
    return $verify->entry();
}

/**
 * 检查验证码
 * 
 * @author 牧羊人
 * @date 2018-09-30
 */
function check_verify($code) {
    $verify = new \Think\Verify();
    return $verify->check($code);
}

/**
 * 获取URL根域名
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $domain 域名
 */
function get_url_to_domain($domain) {
    $re_domain = '';
    $domain_postfix_cn_array = array("com", "net", "org", "gov", "edu", "com.cn", "cn");
    $array_domain = explode(".", $domain);
    $array_num = count($array_domain) - 1;
    if ($array_domain[$array_num] == 'cn') {
        if (in_array($array_domain[$array_num - 1], $domain_postfix_cn_array)) {
            $re_domain = $array_domain[$array_num - 2] . "." . $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        } else {
            $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        }
    } else {
        $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
    }
    return $re_domain;
}

/**
 * 发送邮件
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * 
 * @param unknown $address 需要发送的邮箱地址 发送给多个地址需要写成数组形式
 * @param unknown $subject 标题
 * @param unknown $content 内容
* @return boolean       是否成功
 */
function send_email($address,$subject,$content) {
    $email_smtp = C('EMAIL_SMTP');
    $email_username = C('EMAIL_USERNAME');
    $email_password = C('EMAIL_PASSWORD');
    $email_from_name = C('EMAIL_FROM_NAME');
    $email_smtp_secure = C('EMAIL_SMTP_SECURE');
    $email_port = C('EMAIL_PORT');
    if(empty($email_smtp) || empty($email_username) || empty($email_password) || empty($email_from_name)){
        return array("error"=>1,"message"=>'邮箱配置不完整');
    }
    require_once './ThinkPHP/Library/Org/Nx/class.phpmailer.php';
    require_once './ThinkPHP/Library/Org/Nx/class.smtp.php';
    $phpmailer = new PHPMailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $phpmailer->IsSMTP();
    // 设置设置smtp_secure
    $phpmailer->SMTPSecure = $email_smtp_secure;
    // 设置port
    $phpmailer->Port = $email_port;
    // 设置为html格式
    $phpmailer->IsHTML(true);
    // 设置邮件的字符编码'
    $phpmailer->CharSet = 'UTF-8';
    // 设置SMTP服务器。
    $phpmailer->Host = $email_smtp;
    // 设置为"需要验证"
    $phpmailer->SMTPAuth = true;
    // 设置用户名
    $phpmailer->Username = $email_username;
    // 设置密码
    $phpmailer->Password = $email_password;
    // 设置邮件头的From字段。
    $phpmailer->From = $email_username;
    // 设置发件人名字
    $phpmailer->FromName = $email_from_name;
    // 添加收件人地址，可以多次使用来添加多个收件人
    if(is_array($address)){
        foreach($address as $addressv){
            $phpmailer->AddAddress($addressv);
        }
    }else{
        $phpmailer->AddAddress($address);
    }
    // 设置邮件标题
    $phpmailer->Subject = $subject;
    // 设置邮件正文
    $phpmailer->Body = $content;
    // 发送邮件。
    if(!$phpmailer->Send()) {
        $phpmailererror = $phpmailer->ErrorInfo;
        return array("error"=>1,"message"=>$phpmailererror);
    }else{
        return array("error"=>0);
    }
}

/**
 * 生成不重复的随机数
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * 
 * @param number $start 需要生成的数字开始范围
 * @param number $end 结束范围
 * @param number $length 需要生成的随机数个数
 * @return array       生成的随机数
 */
function get_rand_number($start=1,$end=10,$length=4) {
    $connt=0;
    $temp=array();
    while($connt<$length){
        $temp[]=rand($start,$end);
        $data=array_unique($temp);
        $connt=count($data);
    }
    sort($data);
    return $data;
}

/**
 * 实例化Page
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * 
 * @param unknown $count 总数
 * @param number $limit 每页数量
 */
function init_page($count,$limit=10) {
    return new \Org\Nx\Page($count,$limit);
}

/**
 * 获取分页数据
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * 
 * @param unknown $model model对象
 * @param unknown $map where条件
 * @param string $order 排序规则
 * @param number $limit 每页数量
 * @return array 分页数据
 */
function get_page_data($model,$map,$order='',$limit=10) {
    $count = $model
        ->where($map)
        ->count();
    $page = init_page($count,$limit);
    // 获取分页数据
    $list=$model
        ->where($map)
        ->order($order)
        ->limit($page->firstRow.','.$page->listRows)
        ->select();
    $data = array(
        'data'=>$list,
        'page'=>$page->show()
    );
    return $data;
}

/**
 * 使用curl获取远程数据
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * 
 * @param unknown $url URL连接
 * @return string 获取到的数据
 */
function curl_get_contents($url) {
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);                //设置访问的url地址
    // curl_setopt($ch,CURLOPT_HEADER,1);               //是否显示头部信息
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);               //设置超时
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);   //用户访问代理 User-Agent
    curl_setopt($ch, CURLOPT_REFERER,$_SERVER['HTTP_HOST']);        //设置 referer
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);          //跟踪301
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
    $r=curl_exec($ch);
    curl_close($ch);
    return $r;
}

/**
 * 计算星座
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $month 月
 * @param unknown $day 日期
 * @return boolean|multitype: 星座名称或者错误信息
 */
function get_zodiac_sign($month, $day) {
    // 检查参数有效性
    if ($month < 1 || $month > 12 || $day < 1 || $day > 31)
        return (false);
    // 星座名称以及开始日期
    $signs = array(
        array( "20" => "水瓶座"),
        array( "19" => "双鱼座"),
        array( "21" => "白羊座"),
        array( "20" => "金牛座"),
        array( "21" => "双子座"),
        array( "22" => "巨蟹座"),
        array( "23" => "狮子座"),
        array( "23" => "处女座"),
        array( "23" => "天秤座"),
        array( "24" => "天蝎座"),
        array( "22" => "射手座"),
        array( "22" => "摩羯座")
    );
    list($sign_start, $sign_name) = each($signs[(int)$month-1]);
    if ($day < $sign_start) {
        list($sign_start, $sign_name) = each($signs[($month -2 < 0) ? $month = 11: $month -= 2]);
    } 
    return $sign_name;
}

/**
 * 将路径转换加密
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $file_path 路径
 */

/**
 *  * 将路径转换加密
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $file_path 路径
 * @return string 转换后的路径
 */
function path_encode($file_path) {
    return rawurlencode(base64_encode($file_path));
}

/**
 * 将路径解密
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $file_path 加密后的字符串
 * @return string 解密后的路径
 */
function path_decode($file_path) {
    return base64_decode(rawurldecode($file_path));
}

/**
 * 传入时间戳,计算距离现在的时间
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $time 时间戳
 * @return string 返回多少以前
 */
function word_time($time) {
    $time = (int) substr($time, 0, 10);
    $int = time() - $time;
    $str = '';
    if ($int <= 2){
        $str = sprintf('刚刚', $int);
    }elseif ($int < 60){
        $str = sprintf('%d秒前', $int);
    }elseif ($int < 3600){
        $str = sprintf('%d分钟前', floor($int / 60));
    }elseif ($int < 86400){
        $str = sprintf('%d小时前', floor($int / 3600));
    }elseif ($int < 1728000){
        $str = sprintf('%d天前', floor($int / 86400));
    }else{
        $str = date('Y-m-d H:i:s', $time);
    }
    return $str;
}

/**
 * 生成缩略图
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $image_path 原图path
 * @param number $width 缩略图的宽
 * @param number $height 缩略图的高
 * @return string 缩略图路径
 */
function crop_image($image_path,$width=170,$height=170) {
    $image_path=trim($image_path,'.');
    $min_path='.'.str_replace('.', '_'.$width.'_'.$height.'.', $image_path);
    $image = new \Think\Image();
    $image->open($image_path);
    // 生成一个居中裁剪为$width*$height的缩略图并保存
    $image->thumb($width, $height,\Think\Image::IMAGE_THUMB_CENTER)->save($min_path);
    oss_upload($min_path);
    return $min_path;
}

/**
 * 把用户输入的文本转义（主要针对特殊符号和emoji表情）
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $str 需要转移的字符
 * @return unknown|string|mixed 返回的转移后的值
 */
function emoji_encode($str) {
    if(!is_string($str))return $str;
    if(!$str || $str=='undefined')return '';
    
    $text = json_encode($str); //暴露出unicode
    $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i",function($str){
        return addslashes($str[0]);
    },$text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
    return json_decode($text);
}

/**
 * 将utf-16的emoji表情转为utf8文字形
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $str 需要转的字符串
 * @return mixed|string 转完成后的字符串
 */
function escape_sequence_decode($str) {
    $regex = '/\\\u([dD][89abAB][\da-fA-F]{2})\\\u([dD][c-fC-F][\da-fA-F]{2})|\\\u([\da-fA-F]{4})/sx';
    return preg_replace_callback($regex, function($matches) {
        if (isset($matches[3])) {
            $cp = hexdec($matches[3]);
        } else {
            $lead = hexdec($matches[1]);
            $trail = hexdec($matches[2]);
            $cp = ($lead << 10) + $trail + 0x10000 - (0xD800 << 10) - 0xDC00;
        }
    
        if ($cp > 0xD7FF && 0xE000 > $cp) {
            $cp = 0xFFFD;
        }
        if ($cp < 0x80) {
            return chr($cp);
        } else if ($cp < 0xA0) {
            return chr(0xC0 | $cp >> 6).chr(0x80 | $cp & 0x3F);
        }
        $result =  html_entity_decode('&#'.$cp.';');
        return $result;
    }, $str);
}

/**
 * 检测是否是手机访问
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @return boolean
 */
function is_mobile_visit() {
    $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';
    function _is_mobile($substrs,$text){
        foreach($substrs as $substr)
            if(false!==strpos($text,$substr)){
            return true;
        }
        return false;
    }
    $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
    $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');
    
    $found_mobile=_is_mobile($mobile_os_list,$useragent_commentsblock) ||
    _is_mobile($mobile_token_list,$useragent);
    if ($found_mobile){
        return true;
    }else{
        return false;
    }
}

/**
 * 获取当前访问的设备类型
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @return number 0：其他  1：iOS  2：Android
 */
function get_device_type() {
    //全部变成小写字母
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $type = 0;
    //分别进行判断
    if(strpos($agent, 'iphone')!==false || strpos($agent, 'ipad')!==false){
        $type = 1;
    }
    if(strpos($agent, 'android')!==false){
        $type = 2;
    }
    return $type;
}

/**
 * 生成PDF
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param string $html 需要生成的内容
 */
function pdf($html='<h1 style="color:red">hello word</h1>') {
    vendor('Tcpdf.tcpdf');
    $pdf = new \Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // 设置打印模式
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Nicola Asuni');
    $pdf->SetTitle('TCPDF Example 001');
    $pdf->SetSubject('TCPDF Tutorial');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    // 是否显示页眉
    $pdf->setPrintHeader(false);
    // 设置页眉显示的内容
    $pdf->SetHeaderData('logo.png', 60, 'baijunyao.com', '平台', array(0,64,255), array(0,64,128));
    // 设置页眉字体
    $pdf->setHeaderFont(Array('dejavusans', '', '12'));
    // 页眉距离顶部的距离
    $pdf->SetHeaderMargin('5');
    // 是否显示页脚
    $pdf->setPrintFooter(true);
    // 设置页脚显示的内容
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    // 设置页脚的字体
    $pdf->setFooterFont(Array('dejavusans', '', '10'));
    // 设置页脚距离底部的距离
    $pdf->SetFooterMargin('10');
    // 设置默认等宽字体
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // 设置行高
    $pdf->setCellHeightRatio(1);
    // 设置左、上、右的间距
    $pdf->SetMargins('10', '10', '10');
    // 设置是否自动分页  距离底部多少距离时分页
    $pdf->SetAutoPageBreak(TRUE, '15');
    // 设置图像比例因子
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->setFontSubsetting(true);
    $pdf->AddPage();
    // 设置字体
    $pdf->SetFont('stsongstdlight', '', 14, '', true);
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $pdf->Output('example_001.pdf', 'I');
}

/**
 * 数组转xls格式的excel文件
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $data 需要生成excel文件的数组
 * @param string $filename 生成的excel文件名
 */
/*示例数据：
$data = array(
    array(NULL, 2010, 2011, 2012),
    array('Q1',   12,   15,   21),
    array('Q2',   56,   73,   86),
    array('Q3',   52,   61,   69),
    array('Q4',   30,   32,    0),
   );
*/
function create_xls($data,$filename='simple.xls') {
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $filename=str_replace('.xls', '', $filename).'.xls';
    $phpexcel = new PHPExcel();
    $phpexcel->getProperties()
    ->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Maarten Balliauw")
    ->setTitle("Office 2007 XLSX Test Document")
    ->setSubject("Office 2007 XLSX Test Document")
    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Test result file");
    $phpexcel->getActiveSheet()->fromArray($data);
    $phpexcel->getActiveSheet()->setTitle('Sheet1');
    $phpexcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=$filename");
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $objwriter->save('php://output');
    exit;
}

/**
 * 数据转csv格式的excle
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $data 需要转的数组
 * @param string $header 要生成的excel表头
 * @param string $filename 生成的excel文件名
 */
/*示例数组：
$data = array(
    '1,2,3,4,5',
    '6,7,8,9,0',
    '1,3,5,6,7'
    );
    $header='用户名,密码,头像,性别,手机号';
*/
function create_csv($data,$header=null,$filename='simple.csv') {
    // 如果手动设置表头；则放在第一行
    if (!is_null($header)) {
        array_unshift($data, $header);
    }
    // 防止没有添加文件后缀
    $filename=str_replace('.csv', '', $filename).'.csv';
    ob_clean();
    Header( "Content-type:  application/octet-stream ");
    Header( "Accept-Ranges:  bytes ");
    Header( "Content-Disposition:  attachment;  filename=".$filename);
    foreach( $data as $k => $v){
        // 如果是二维数组；转成一维
        if (is_array($v)) {
            $v=implode(',', $v);
        }
        // 替换掉换行
        $v=preg_replace('/\s*/', '', $v);
        // 解决导出的数字会显示成科学计数法的问题
        $v=str_replace(',', "\t,", $v);
        // 转成gbk以兼容office乱码的问题
        echo iconv('UTF-8','GBK',$v)."\t\r\n";
    }
}

/**
 * 导入excel文件
 * 
 * @author 牧羊人
 * @date 2018-09-30
 * @param unknown $file excel文件路径
 * @return Ambigous <multitype:, mixed> excel文件内容数组
 */
function import_excel($file) {
    // 判断文件是什么格式
    $type = pathinfo($file);
    $type = strtolower($type["extension"]);
    if ($type=='xlsx') {
        $type='Excel2007';
    }elseif($type=='xls') {
        $type = 'Excel5';
    }
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    // 判断使用哪种格式
    $objReader = PHPExcel_IOFactory::createReader($type);
    $objPHPExcel = $objReader->load($file);
    $sheet = $objPHPExcel->getSheet(0);
    // 取得总行数
    $highestRow = $sheet->getHighestRow();
    // 取得总列数
    $highestColumn = $sheet->getHighestColumn();
    //总列数转换成数字
    $numHighestColum = PHPExcel_Cell::columnIndexFromString("$highestColumn");
    //循环读取excel文件,读取一条,插入一条
    $data=array();
    //从第一行开始读取数据
    for($j=1;$j<=$highestRow;$j++){
        //从A列读取数据
        for($k=0;$k<=$numHighestColum;$k++){
            //数字列转换成字母
            $columnIndex = PHPExcel_Cell::stringFromColumnIndex($k);
            // 读取单元格
            $data[$j][]=$objPHPExcel->getActiveSheet()->getCell("$columnIndex$j")->getValue();
        }
    }
    return $data;
}

/**
 * 取汉字的第一个字的首字母
 * 
 * @author 牧羊人
 * @date 2018-11-22
 * @param unknown $str
 * @return string|NULL
 */
function getFirstCharter($str) {
    if (empty($str)) {
        return '';
    }
    $fchar = ord($str{0});
    if ($fchar >= ord('A') && $fchar <= ord('z'))
        return strtoupper($str{0});
    $s1 = iconv('UTF-8', 'gb2312', $str);
    $s2 = iconv('gb2312', 'UTF-8', $s1);
    $s = $s2 == $str ? $s1 : $str;
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 && $asc <= -20284)
        return 'A';
    if ($asc >= -20283 && $asc <= -19776)
        return 'B';
    if ($asc >= -19775 && $asc <= -19219)
        return 'C';
    if ($asc >= -19218 && $asc <= -18711)
        return 'D';
    if ($asc >= -18710 && $asc <= -18527)
        return 'E';
    if ($asc >= -18526 && $asc <= -18240)
        return 'F';
    if ($asc >= -18239 && $asc <= -17923)
        return 'G';
    if ($asc >= -17922 && $asc <= -17418)
        return 'H';
    if ($asc >= -17417 && $asc <= -16475)
        return 'J';
    if ($asc >= -16474 && $asc <= -16213)
        return 'K';
    if ($asc >= -16212 && $asc <= -15641)
        return 'L';
    if ($asc >= -15640 && $asc <= -15166)
        return 'M';
    if ($asc >= -15165 && $asc <= -14923)
        return 'N';
    if ($asc >= -14922 && $asc <= -14915)
        return 'O';
    if ($asc >= -14914 && $asc <= -14631)
        return 'P';
    if ($asc >= -14630 && $asc <= -14150)
        return 'Q';
    if ($asc >= -14149 && $asc <= -14091)
        return 'R';
    if ($asc >= -14090 && $asc <= -13319)
        return 'S';
    if ($asc >= -13318 && $asc <= -12839)
        return 'T';
    if ($asc >= -12838 && $asc <= -12557)
        return 'W';
    if ($asc >= -12556 && $asc <= -11848)
        return 'X';
    if ($asc >= -11847 && $asc <= -11056)
        return 'Y';
    if ($asc >= -11055 && $asc <= -10247)
        return 'Z';
    return null;
}

/**
 * 获取城市信息
 *
 * @author 牧羊人
 * @date 2018-04-16
 */
function ip2city($ip){
    $url = "http://ip.taobao.com/service/getIpInfo.php?ip={$ip}";
    $ip = json_decode(file_get_contents($url));
    if((string)$ip->code == '1'){
        return '';
    }
    $data = (array)$ip->data;
    return $data['region'] . " " . $data['city'] . " " . $data['isp'];
}

?>