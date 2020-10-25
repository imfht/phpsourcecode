<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------

require_once(APP_PATH . '/Common/Common/developer.php'); //加载开发者二次开发公共函数库

/**
 * 根据配置类型解析配置
 * @param  string $type  配置类型
 * @param  string  $value 配置值
 */
function parse_attr($value, $type){
    switch ($type) {
        default: //解析"1:1\r\n2:3"格式字符串为数组
            $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
            if(strpos($value,':')){
                $value  = array();
                foreach ($array as $val) {
                    list($k, $v) = explode(':', $val);
                    $value[$k]   = $v;
                }
            }else{
                $value = $array;
            }
            break;
    }
    return $value;
}

/**
 * 字符串截取(中文按2个字符数计算)，支持中文和其他编码
 * @static
 * @access public
 * @param str $str 需要转换的字符串
 * @param str $start 开始位置
 * @param str $length 截取长度
 * @param str $charset 编码格式
 * @param str $suffix 截断显示字符
 * @return str
 */
function get_str($str, $start, $length, $charset='utf-8', $suffix=true) {
    $str = trim($str);
    $length = $length * 2;
    if($length){
        //截断字符
        $wordscut = '';
        if(strtolower($charset) == 'utf-8'){
            //utf8编码
            $n = 0;
            $tn = 0;
            $noc = 0;
            while($n < strlen($str)){
                $t = ord($str[$n]);
                if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)){
                    $tn = 1;
                    $n++;
                    $noc++;
                }elseif(194 <= $t && $t <= 223){
                    $tn = 2;
                    $n += 2;
                    $noc += 2;
                }elseif(224 <= $t && $t < 239){
                    $tn = 3;
                    $n += 3;
                    $noc += 2;
                }elseif(240 <= $t && $t <= 247){
                    $tn = 4;
                    $n += 4;
                    $noc += 2;
                }elseif(248 <= $t && $t <= 251){
                    $tn = 5;
                    $n += 5;
                    $noc += 2;
                }elseif($t == 252 || $t == 253){
                    $tn = 6;
                    $n += 6;
                    $noc += 2;
                }else{
                    $n++;
                }
                if ($noc >= $length){
                    break;
                }
            }
            if($noc > $length){
                $n -= $tn;
            }
            $wordscut = substr($str, 0, $n);
        }else{
            for($i = 0; $i < $length - 1; $i++){
                if(ord($str[$i]) > 127) {
                    $wordscut .= $str[$i].$str[$i + 1];
                    $i++;
                } else {
                    $wordscut .= $str[$i];
                }
            }
        }
        if($wordscut == $str){
            return $str;
        }
        return $suffix ? trim($wordscut).'...' : trim($wordscut);
    }else{
        return $str;
    }
}

/**
 * 过滤标签，输出纯文本
 * @param string $str 文本内容
 * @return string 处理后内容
 * @author jry <598821125@qq.com>
 */
function html2text($str){
    $str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU","",$str);
    $alltext = "";
    $start = 1;
    for($i=0;$i<strlen($str);$i++){
        if($start==0 && $str[$i]==">"){
            $start = 1;
        }
        else if($start==1){
            if($str[$i]=="<"){
                $start = 0;
                $alltext .= " ";
            }
            else if(ord($str[$i])>31){
                $alltext .= $str[$i];
            }
        }
    }
    $alltext = str_replace("　"," ",$alltext);
    $alltext = preg_replace("/&([^;&]*)(;|&)/","",$alltext);
    $alltext = preg_replace("/[ ]+/s"," ",$alltext);
    return $alltext;
}

/**
 * 敏感词过滤替换
 * @param  string $text 待检测内容
 * @param  array $sensitive 待过滤替换内容
 * @param  string $suffix 替换后内容
 * @return bool
 * @author jry <598821125@qq.com>
 */
function sensitive_filter($text, $sensitive = null, $suffix = '**'){
    if(!$sensitive){
        $sensitive = C('SENSITIVE_WORDS');
    }
    $sensitive_words = explode(',', $sensitive);
    $sensitive_words_replace = array_combine($sensitive_words,array_fill(0,count($sensitive_words), $suffix));
    return strtr($text, $sensitive_words_replace);
}

/**
 * 解析文档内容
 * @param string $str 待解析内容
 * @return string
 * @author jry <598821125@qq.com>
 */
function parse_content($str){
    return preg_replace('/(<img.*?)src=/i', "$1 class='lazy img-responsive' data-original=", $str);//将img标签的src改为data-original用户前台图片lazyload加载
}

/**
 * 友好的时间显示
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu | full | ymd | other
 * @param string $alt   已失效
 * @return string
 * @author jry <598821125@qq.com>
 */
function friendly_date($sTime, $type = 'normal', $alt = 'false'){
    if (!$sTime)
        return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime      =   time();
    $dTime      =   $cTime - $sTime;
    $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if($type=='normal'){
        if( $dTime < 60 ){
            if($dTime < 10){
                return '刚刚';
            }else{
                return intval(floor($dTime / 10) * 10)."秒前";
            }
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
            //今天的数据.年份相同.日期相同.
        }elseif( $dYear==0 && $dDay == 0  ){
            //return intval($dTime/3600)."小时前";
            return '今天'.date('H:i',$sTime);
        }elseif($dYear==0){
            return date("m月d日 H:i",$sTime);
        }else{
            return date("Y-m-d H:i",$sTime);
        }
    }elseif($type=='mohu'){
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif( $dDay > 0 && $dDay<=7 ){
            return intval($dDay)."天前";
        }elseif( $dDay > 7 &&  $dDay <= 30 ){
            return intval($dDay/7) . '周前';
        }elseif( $dDay > 30 ){
            return intval($dDay/30) . '个月前';
        }
        //full: Y-m-d , H:i:s
    }elseif($type=='full'){
        return date("Y-m-d , H:i:s",$sTime);
    }elseif($type=='ymd'){
        return date("Y-m-d",$sTime);
    }else{
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif($dYear==0){
            return date("Y-m-d H:i:s",$sTime);
        }else{
            return date("Y-m-d H:i:s",$sTime);
        }
    }
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author jry <598821125@qq.com>
 */
function time_format($time = NULL, $format='Y-m-d H:i'){
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * 解析数据库语句函数
 * @param string $sql  sql语句   带默认前缀的
 * @param string $tablepre  自己的前缀
 * @return multitype:string 返回最终需要的sql语句
 */
function sql_split($sql, $tablepre){
    if($tablepre != "ct_"){
        $sql = str_replace("ct_", $tablepre, $sql);
    }
    $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
    if($r_tablepre != $s_tablepre){
        $sql = str_replace($s_tablepre, $r_tablepre, $sql);
    }
    $sql = str_replace("\r", "\n", $sql);
    $ret = array();
    $num = 0;
    $queriesarray = explode(";\n", trim($sql));
    unset($sql);
    foreach($queriesarray as $query){
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        $queries = array_filter($queries);
        foreach($queries as $query){
            $str1 = substr($query, 0, 1);
            if($str1 != '#' && $str1 != '-'){
                $ret[$num] .= $query;
            }
        }
        $num++;
    }
    return $ret;
}

/**
 * 执行文件中SQL语句函数
 * @param string $file sql语句文件路径
 * @param string $tablepre  自己的前缀
 * @return multitype:string 返回最终需要的sql语句
 */
function execute_sql_from_file($file){
    $sql_data = file_get_contents($file);
    $sql_format = sql_split($sql_data, C('DB_PREFIX'));
    $counts = count($sql_format);
    for($i = 0; $i < $counts; $i++){
        $sql = trim($sql_format[$i]);
        D()->execute($sql);
    }
    return true;
}


/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 * @author jry <598821125@qq.com>
 */
function user_md5($str, $auth_key){
    if(!$auth_key){
        $auth_key = C('AUTH_KEY');
    }
    return '' === $str ? '' : md5(sha1($str) . $auth_key);
}

/**
 * 生成用户的唯一标识，用于某些判定
 * @param $data
 * @return string
 */
function validcode($data){
    if($data){
        $md5=$data.mt_rand(1000, 9999);
        $result=substr(md5($md5),0,8);
        return $result;
    }
}
/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author jry <598821125@qq.com>
 */
function is_login(){
    //return D('User')->isLogin();
}
/**
 * 检测管理员用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author jry <598821125@qq.com>
 */
function admin_login(){
    return D('Manager')->isLogin();
}
/**
 * 根据用户ID获取用户信息
 * @param  integer $id 用户ID
 * @param  string $field
 * @return array  用户信息
 * @author jry <598821125@qq.com>
 */
function get_user_info($id, $field){
    $userinfo = D('User')->find($id);
    if($field){
        $userinfo[$field];
    }
    return $userinfo;
}

/**
 * 获取上传文件路径
 * @param  int $id 文件ID
 * @return string
 * @author jry <598821125@qq.com>
 */
function get_cover($id, $type){
    $upload_info = D('PublicUpload')->find($id);
    $url = $upload_info['real_path'];
    if(!$url){
        switch($type){
            case 'default' : //默认图片
                $url = C('TMPL_PARSE_STRING.__HOME_IMG__').'/logo/default.gif';
                break;
            case 'avatar' : //用户头像
                $url = C('TMPL_PARSE_STRING.__HOME_IMG__').'/avatar/avatar'.rand(1,7).'.png';
                break;
            default: //文档列表默认图片
                break;
        }
    }
    return $url;
}

/**
 * 获取上传文件信息
 * @param  int $id 文件ID
 * @return string
 * @author jry <598821125@qq.com>
 */
function get_upload_info($id, $field){
    $upload_info = D('PublicUpload')->where('status = 1')->find($id);
    if($field){
        if(!$upload_info[$field]){
            return $upload_info['id'];
        }else{
            return $upload_info[$field];
        }
    }
    return $upload_info;
}


/**
 * 获取所有数据并转换成一维数组
 * @author jry <598821125@qq.com>
 */
function select_list_as_tree($model, $map = null, $extra = null){
    //获取列表
    $con['status'] = array('eq', 1);
    if($map){
        $con = array_merge($con, $map);
    }
    $list = D($model)->where($con)->select();

    //转换成树状列表(非严格模式)
    $tree = new \Common\Util\Tree();
    $list = $tree->toFormatTree($list, 'title', 'id', 'pid', 0, false);

    if($extra){
        $result[0] = $extra;
    }

    //转换成一维数组
    foreach($list as $val){
        $result[$val['id']] = $val['title_show'];
    }
    return $result;
}
/**
 * 获取所有数据并转换成一维数组
 * @author jry <598821125@qq.com>
 */
function select_to_tree($model, $map = null, $extra = null){
    $list = D($model)->where($map)->select();

    $tree = new \Common\Util\Tree();
    $list = $tree->toFormatTree($list, 'title', 'id', 'pid', 0, false);
    //
    if($extra){
        $result[0] = $extra;
    }

    //转换成一维数组
    foreach($list as $val){
        $result[$val['id']] = $val['name'];
    }
    return $result;
}
/**
 * 系统邮件发送函数
 * @param string $receiver 收件人
 * @param string $subject 邮件主题
 * @param string $body 邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 * @author jry <598821125@qq.com>
 */
function send_mail($receiver, $subject, $body, $attachment){
    return R('Addons://Email/Email/sendMail', array($receiver, $subject, $body, $attachment));
}

/**
 * 短信发送函数
 * @param string $receiver 接收短信手机号码
 * @param string $body 短信内容
 * @return boolean
 * @author jry <598821125@qq.com>
 */
function send_mobile_message($receiver, $body){
    return false; //短信功能待开发
}


/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 * @author jry <598821125@qq.com>
 */
function hook($hook, $params = array()){
    \Think\Hook::listen($hook,$params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 * @author jry <598821125@qq.com>
 */
function get_addon_class($name){
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author jry <598821125@qq.com>
 */
function addons_url($url, $param = array()){
    return D('Addon')->getAddonUrl($url, $param);
}
