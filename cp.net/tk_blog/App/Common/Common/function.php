<?php
/**
 * 格式化打印数据
 * @param $data 需要打印的数据
 */
function p($data){
    header("Content-type:text/html;charset=utf-8");
    // 定义样式
    $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
    // 如果是boolean或者null直接显示文字；否则print
    if (is_bool($data)) {
        $show_data=$data ? 'true' : 'false';
    }elseif (is_null($data)) {
        $show_data='null';
    }else{
        $show_data=print_r($data,true);
    }
    $str.=$show_data;
    $str.='</pre>';
    echo $str;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    if (strlen($str) > $length) {
        return $suffix ? $slice.'...' : $slice;
    } else {
        return $slice;
    }
}


/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = ''){
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}


/**
 * 获取访问用户ip
 */
function getRealIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}


/**
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function rand_string($len=6,$type='',$addChars='') {
    $str ='';
    switch($type) {
        case 0:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 1:
            $chars= str_repeat('0123456789',3);
            break;
        case 2:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
            break;
        case 3:
            $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
            break;
    }
    if($len>10 ) {//位数过长重复字符串一定次数
        $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
    }
    if($type!=4) {
        $chars   =   str_shuffle($chars);
        $str     =   substr($chars,0,$len);
    }else{
        // 中文随机字
        for($i=0;$i<$len;$i++){
            $str.= msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
        }
    }
    return $str;
}

//输出安全的html
function h($text, $tags = null) {
    $text	=	trim($text);
    //完全过滤注释
    $text	=	preg_replace('/<!--?.*-->/','',$text);
    //完全过滤动态代码
    $text	=	preg_replace('/<\?|\?'.'>/','',$text);
    //完全过滤js
    $text	=	preg_replace('/<script?.*\/script>/','',$text);

    $text	=	str_replace('[','&#091;',$text);
    $text	=	str_replace(']','&#093;',$text);
    $text	=	str_replace('|','&#124;',$text);
    //过滤换行符
    //$text	=	preg_replace('/\r?\n/','',$text);
    //br
    $text	=	preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
    $text	=	preg_replace('/<br(\s)?(\/)?'.'>/i','[br/]',$text);
    $text	=	preg_replace('/<p(\s\/)?'.'>/i','[p]',$text);
    $text	=	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
    //过滤危险的属性，如：过滤on事件lang js
    while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
        $text=str_replace($mat[0],$mat[1],$text);
    }
    while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
        $text=str_replace($mat[0],$mat[1].$mat[3],$text);
    }
    if(empty($tags)) {
        $tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a|h1|h2|h3|h4|h5|h6|pre';
    }
    //允许的HTML标签
    $text	=	preg_replace('/<('.$tags.')([^><\[\]]*)>/i','[\1\2]',$text);
    $text = preg_replace('/<\/('.$tags.')>/Ui','[/\1]',$text);
    //过滤多余html
    $text	=	preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
    //过滤合法的html标签
    while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
        $text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
    }
    //转换引号
    while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
        $text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
    }
    //过滤错误的单个引号
    while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
        $text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
    }
    //转换其它所有不合法的 < >
    $text	=	str_replace('<','&lt;',$text);
    $text	=	str_replace('>','&gt;',$text);
    $text	=	str_replace('"','&quot;',$text);
    //反转换
    $text	=	str_replace('[','<',$text);
    $text	=	str_replace(']','>',$text);
    $text	=	str_replace('|','"',$text);
    //过滤多余空格
    //$text	=	str_replace('  ',' ',$text);
    return $text;
}

/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}


/**
 * 密码加密方法
 * @param string $pw 要加密的字符串
 * @return string
 */
function encrypt_password($pw,$authcode='www.mgchen.com'){
    return md5(md5(md5($authcode . $pw)));
}

/**
 * 密码比较方法
 * @param string $password 要比较的密码
 * @param string $password_in_db 数据库保存的已经加密过的密码
 * @return boolean 密码相同，返回true
 */
function compare_password($password,$password_in_db){
    if (encrypt_password($password) == $password_in_db) {
        return true;
    } else {
        return false;
    }
}

/**
 * 发送邮件
 * @param  string $address 需要发送的邮箱地址 发送给多个地址需要写成数组形式
 * @param  string $subject 标题
 * @param  string $content 内容
 * @return boolean       是否成功
 */
function send_email($address,$subject,$content){
    $email_smtp=C('CP_EMAIL_SMTP');
    $email_username=C('CP_EMAIL_USERNAME');
    $email_password=C('CP_EMAIL_PASSWORD');
    $email_from_name=C('CP_EMAIL_FROM_NAME');
    if(empty($email_smtp) || empty($email_username) || empty($email_password) || empty($email_from_name)){
        return array("error"=>1,"message"=>'邮箱配置不完整');
    }
    require './ThinkPHP/Library/Org/Cp/class.phpmailer.php';
    require './ThinkPHP/Library/Org/Cp/class.smtp.php';
    $phpmailer=new \Phpmailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $phpmailer->IsSMTP();
    // 设置为html格式
    $phpmailer->IsHTML(true);
    // 设置邮件的字符编码'
    $phpmailer->CharSet='UTF-8';
    // 设置SMTP服务器。
    $phpmailer->Host=$email_smtp;
    // 设置为"需要验证"
    $phpmailer->SMTPAuth=true;
    // 设置用户名
    $phpmailer->Username=$email_username;
    // 设置密码
    $phpmailer->Password=$email_password;
    // 设置邮件头的From字段。
    $phpmailer->From=$email_username;
    // 设置发件人名字
    $phpmailer->FromName=$email_from_name;
    // 添加收件人地址，可以多次使用来添加多个收件人
    if(is_array($address)){
        foreach($address as $addressv){
            $phpmailer->AddAddress($addressv);
        }
    }else{
        $phpmailer->AddAddress($address);
    }
    // 设置邮件标题
    $phpmailer->Subject=$subject;
    // 设置邮件正文
    $phpmailer->Body=$content;
    // 发送邮件。
    if(!$phpmailer->Send()) {
        $phpmailererror=$phpmailer->ErrorInfo;
        return array("error"=>1,"message"=>$phpmailererror);
    }else{
        return array("error"=>0);
    }
}


/**
 * 判断前台用户是否登录
 * @return boolean
 */
function is_user_login(){
    return  !empty($_SESSION['user']);
}


/**
 * 获取自行注册的用户名
 * @param $username 需要处理的字符串
 *
 */
function getUserName($username){
    if (empty($username) || !is_string($username))  return false;
    return 'Cc_' . substr($username,0,strrpos($username,'@'));
}


/**
 * 替换表情
 * @param  [String] $content [需要处理的微博字符串]
 * @return [String]          [处理完成后的字符串]
 */
function replace_phiz($content,$showImg=false){
    if (empty($content))  return;
    if ($showImg) {
        $root = "http://www.mgchen.com";
    } else {
        $root = "";
    }
    //提取微博内容中所有表情文件
    $preg = '/\[(\S+?)\]/is';
    preg_match_all($preg, $content, $arr);
    //载入表情库
    $phiz = include APP_PATH . 'Common/Data/data.php';
    if (!empty($arr[1])) {
        foreach ($arr[1] as $k => $v) {
            //搜索值对应的键名
            $name = array_search($v,$phiz);
            if  ($name) {
                $content = str_replace($arr[0][$k],'<img src=" ' . $root .'/Public/home/biaoqing/' .$name. '.gif"  title="'. $v .'" />',$content);
            }
        }
    }
    return $content;

}


if (!function_exists('node_merge')) {
    /**
     * 递归重组节点 往多层节点中压入child数组
     * @param  type  $node          要处理的节点数组
     * @param  integer $pid         父级ID（顶级）
     * @param  string $fieldPri     分类ID字段名称
     * @param  string $fieldPid     分类的父级ID字段名称
     * @param  string $child        压入子集的名称
     * @return array                返回多维数组
     */
    function node_merge($node,$pid=0,$fieldPri='id',$fieldPid='pid',$child='child'){
        $temp = array();
        foreach ($node as $v) {
            if ($v[$fieldPid] == $pid) {
                $v[$child] = node_merge($node,$v[$fieldPri],$fieldPri,$fieldPid,$child='child');
                $temp[] = $v;
            }
        }
        return $temp;
    }
}


/**
 * 取得所有子分类
 * @param  $data 所有分类的数据
 * @param  $cid 分类id 当前获取的cid
 * @param  $fieldPri  分类id
 * @param $fieldPid  分类的父级id
 * @return 返回所有的子分类
 */
function childLevel($data,$cid=0,$fieldPri='cid',$fieldPid='pid'){
    $children=array();
    foreach ($data as $value) {
        if($value[$fieldPid]==$cid){
            $children[]=$value[$fieldPri];
            $children=array_merge($children,childLevel($data,$value[$fieldPri],$fieldPri,$fieldPid));
        }
    }
    return $children;
}


/**
 * 递归重组节点信息为多维数组 评论专用
 * @param  [type]  $node [要处理的节点数组]
 * @param  integer $pid  [父级ID]
 * @return [array]        [返回多维数组]
 */
function node_merge_comment($node,$pid=0){
    $temp = array();
    foreach ($node as $v) {
        if ($v['parentid'] == $pid) {
            $v['child'] = node_merge_comment($node,$v['id']);
            $temp[] = $v;
        }
    }
    return $temp;
}

/**
 * 替换关键字并且写入样式
 * @param $keywords 查询的关键字
 * @param $content  查询的内容
 * @return mixed
 */
function keyWrods_replace($keywords,$content){
    $str = "<span style='color: #D2322D;font-weight: 700;'>{$keywords}</span>";
    return str_replace($keywords,$str,$content);
}

/**
 * 格式化时间
 * @param $time
 * @return bool|string
 */
function time_format($time){
    //获取当前时间
    $now = time();
    //今天零时零分零秒
    $today = strtotime(date('y-m-d',$now));
    //当前时间与传递时间相差的秒数
    $diff = $now - $time;
    $str = '';
    switch ($time) {
        case $diff < 60 :
            $str = $diff . '秒前';
            break;
        case $diff < 3600 :
            $str = floor($diff / 60) . '分钟前';
            break;
        case $diff < (3600 * 8) :
            $str = floor($diff / 3600) . '小时前';
            break;
        case $time > $today :
            $str = '今天&nbsp;&nbsp;' . date('H:i',$time);
            break;
        default:
            $str = date('Y-m-d H:i:s',$time);
            break;
    }
    return $str;

}

/**
 * 获取文章的上下篇
 * @param $aid 文章ID
 * @param $pos 文章的方向 prev 上一篇 next 下一篇
 */
function getArticlePos($aid,$pos){
    if (!$aid) return false;
    if ($pos == 'prev') {
        $newaid = M('Article')->order('aid ASC')->limit(1)->getField('aid');
        if ($aid <= $newaid) {
            return false;
        }
        $where['aid'] = array('lt',$aid);
        $data = M('Article')->where($where)->limit(1)->order('aid DESC')->field('aid,title')->find();
        $data['url'] = "/a/{$data['aid']}";
        return $data;
    } else if ($pos == 'next') {
        $newaid = M('Article')->order('aid DESC')->limit(1)->getField('aid');
        if ($newaid <= $aid) {
            return false;
        }
        $where['aid'] = array('gt',$aid);
        $data = M('Article')->where($where)->limit(1)->order('aid ASC')->field('aid,title')->find();
        $data['url'] = "/a/{$data['aid']}";
        return $data;
    }
}


/**
 * 检测验证码
 * @param $code
 * @param string $id
 * @return bool
 */
function check_verify_code($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}



/**
 * [往内存写入推送消息]
 * @param [int] $uid  [用户ID]
 * @param [int] $type [1:评论; 2:私信; 3: @用户]
 * @param [boolean] $flush  [是否清0]
 */
function set_msg ($uid,$type,$flush=false) {
    $name = '';
    switch ($type) {
        case 1 :
            $name = 'comment';
            break;

        case 2 :
            $name = 'letter';
            break;

        case 3 :
            $name = 'atme';
            break;
    }
    if ($flush) {
        $data = S('usermsg' . $uid);
        $data[$name]['total'] = 0;
        $data[$name]['status'] = 0;
        S('usermsg' . $uid, $data, 0);
        return;
    }

    if (S('usermsg' . $uid)) {
        //内存数据存在时，让相应数据+1
        $data = S('usermsg' . $uid);
        $data[$name]['total']++;
        $data[$name]['status'] = 1;
        S('usermsg' . $uid, $data, 0);
    } else {
        //内存数据不存在时，初始化数据并且写入内存
        $data = array(
            'comment' => array('total' => 0 , 'status' => 0),
            'letter' => array('total' => 0 , 'status' => 0),
            'atme' => array('total' => 0 , 'status' => 0)
        );
        $data[$name]['total']++;
        $data[$name]['status'] = 1;
        S('usermsg' . $uid, $data, 0);
    }
}

/***
 * 自定义分页
 * @param $CommentData 数据集
 * @param int $limit 一页取的条数
 * @param array $setConfig 设置分页显示
 */
function pageShow($CommentData,$limit=5,$setConfig=array()){
    if (!is_array($CommentData) || !$CommentData) return false;
    $count = count($CommentData);//数组总数
    $Page = new \Think\Page($count,$limit);
    //设置分页显示
    if ($setConfig) {
        $Page->setConfig('prev',$setConfig['prev']);
        $Page->setConfig('next',$setConfig['next']);
    } else {
        $Page->setConfig('prev','← Previous');
        $Page->setConfig('next','Next →');
    }
    $show = $Page->show(); // 分页显示输出
    //重新组合索引
    $listData = array_values($CommentData);
    $tempArray = array();
    $page = I('get.p',1,'intval');
    //配合page参数值,重组新数组,生成新分页
    //截取数组个数,一页显示多少条数据
    for ($i=($page-1)*$limit;$i < $page*$limit;$i++){
        $tempArray[] = $listData[$i];
    }
    //去除空数据,重新组合数组
    $newData = array();
    foreach($tempArray as $k=>$v){
        if ($v) {
            $newData[] = $v;
        }
    }
    $result = array(
        'page' => $show,
        'data' => $newData,
        'total' => $count,
        'limit' => $limit
    );
    return $result;
}

/**
 * 检测恶意输入
 * @param $idType id类型
 * @param $id     id值
 * @return bool
 */
function checkId($idType,$id){
    switch (true) {
        case $idType == 'aid':
            $temp = M('Article')->where(array('aid'=>$id))->find();
            break;
        case $idType == 'cid':
            $temp = M('Article')->where(array('cid'=>$id))->find();
            break;
        case $idType == 'tid':
            $temp = M('article_tags')->where(array('cp_tags_tid'=>$id))->field('cp_article_aid')->find();
            break;
        default:
            $temp = false;
            break;
    }

    if ($temp) {
        return true;
    } else {
        return false;
    }
}


/**
 * 从服务器获取图片写入到本地的指定目录
 * @param $url 远程图片路径
 * @param $url 需要上传的指定目录 列子: “./Uploads/Face/2016-09-01”
 * @return bool|string 返回false 写入失败
 */
function GrabImage($url,$file = '') {
    if ($url == "") return false;
    if (empty($file)) {
        $file = './Uploads/Face/' . date('Y-m-d');
        if (!file_exists($file)) {
            mkdir($file,0777,true);
        }
    }

    $ext = strrchr($url, ".");

    if ($ext != ".gif" || $ext != ".jpg" || $ext != ".png" || $ext != ".jpeg") {
        $new_fileName = $file . "/" . 'face_' . date('YmdHis') . ".png";
    } else {
        $new_fileName = $file . "/" . 'face_' . date('YmdHis') . $ext;
    }

    ob_start();//打开输出
    readfile($url);//输出图片文件
    $img = ob_get_contents();//得到浏览器输出
    ob_end_clean();//清除输出并关闭
    $fp2 = @fopen($new_fileName, "a");
    fwrite($fp2, $img);//向当前目录写入图片文件，并重新命名
    fclose($fp2);
    return substr($new_fileName,1);//返回新的文件名
}

/**
 * 验证手机
 * @param string $subject
 * @return boolean
 */
function isMobile($subject = '') {
    $pattern = "/^(0|86|17951)?(13[0-9]|15[012356789]|1[78][0-9]|14[57])[0-9]{8}$/";
    if (preg_match($pattern, $subject)) {
        return true;
    }
    return false;
}

/**
 * 验证邮箱
 * @param string $subject
 * @return boolean
 */
function isEmail($subject = ''){
    $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
    if (preg_match($pattern, $subject)) {
        return true;
    }
    return false;
}


/**
 * 验证是否是邮箱
 * @param  string  $email 邮箱
 * @return boolean        是否是邮箱
 */
function is_email($email){
    if(filter_var($email,FILTER_VALIDATE_EMAIL)){
        return true;
    }else{
        return false;
    }
}

/**
 * 验证是否是URL地址
 * @param  string  $email 邮箱
 * @return boolean  是否是邮箱
 */
function is_url($url){
    if(filter_var($url,FILTER_VALIDATE_URL)){
        return true;
    }else{
        return false;
    }
}

/**
 * 验证是否是URL地址
 * @param  string  $email 邮箱
 * @return boolean  是否是邮箱
 */
function is_ip($ip){
    if(filter_var($ip,FILTER_VALIDATE_IP)){
        return true;
    }else{
        return false;
    }
}

/**
 * 发送评论回复消息给作者
 * @param $article_id       文章ID
 * @param $toName           评论者名称
 * @param $comment          评论回复内容
 * @param $comment_id       评论者ID
 * @return array
 */
function cp_sendEmaill_to_User($article_id,$toName,$comment,$comment_id){
    $option = M('Options')->where(array('option_name'=>'邮箱评论'))->find();

    if (!$article_id) {
        return false;
    }
    if ($comment_id == $_SESSION['user']['uid']) {
        return false;
    }
    $aData = M('Article')->where(array('aid'=>$article_id))->field('title,users_uid')->find();
    $uData = M('Users')->where(array('uid'=>$aData['users_uid']))->field('uname,u_email')->find();

    if (!$uData['u_email']) {
        return false;
    }
    $options = json_decode($option['option_value'], true);
    //邮件标题
    $title = $options['title'];
    //邮件内容
    $template = $options['content'];
    $webUrl = "http://www.mgchen.com/a/{$article_id}.html";
    $content = str_replace(array('#username#','#toname#','#titleName#','#content#','#link#'), array($uData['uname'],$toName,$aData['title'],$comment,$webUrl),$template);

    $send_result=send_email($uData['u_email'], $title, $content);

    if($send_result['error']){
        return array(
            'msg' => '邮件发送失败,请重试...',
            'error' => 1
        );
    }
}

/**
 * 替换手机号码
 * @param $str
 * @return string
 */
function replace_phone($str){
    $start = substr($str,0,3);
    $end = substr($str,-4);
    return $start . "****" . $end;
}

/**
 * 截取邮箱@后面的内容 替换对应的登录地址
 * @param $email
 * @return bool
 */
function cutEmailUrl($email){
    if (!is_string($email)) return false;
    $oldStr = substr($email,strrpos($email,"@"));
    $str = substr($oldStr,1);
    $temp = explode(".",$str);
    if ($temp[0] == 'qq' || $temp[0] == 'QQ') {
        $url = "https://mail.qq.com/cgi-bin/loginpage";
    } else if ($temp[0] == '163'){
        $url = "http://mail.163.com/";
    } else if ($temp[0] == '126') {
        $url = "http://mail.126.com/";
    } else if ($temp[0] == 'sina') {
        $url = "http://mail.sina.com.cn/?from=mail";
    } else {
        $url = "http://mail" . $temp[0] . "com";
    }
    return $url;
}

/**
 * 计算中英文字符长度
 * @param $str
 * @return int
 */
function mbs_strlen($str){
    preg_match_all("/./us", $str, $matches);
    return count(current($matches));
}

if (!function_exists('getImage')) {
    /**
     * 远程路径保存文件到指定目录
     * @param $url 远程路径
     * @param string $save_dir  保存的目录
     * @param string $filename  保存的文件名
     * @param int $type         保存的方式 0 服务缓存区保存方式 1：curl获取保存
     * @return array {'file_name' => 文件名称,'save_path' => 保存的全路径 ,'error' => 错误码 }
     */
    function getImage($url,$type=0,$save_dir='',$filename=''){
        $start_time = time();
        if(trim($url)==''){
            return array('file_name'=>'','save_path'=>'','error'=>1);
        }

        if(trim($save_dir)==''){
            //创建文件保存目录
            $save_dir = './Uploads/Face/' . date('Y-m-d');
            if (!file_exists($save_dir)) {
                mkdir($save_dir,0777,true);
            }
        }

        if(trim($filename)==''){//保存文件名
            $ext=strrchr($url,'.');
            if ($ext != ".gif" || $ext != ".jpg" || $ext != ".png" || $ext != ".jpeg") {
                $filename = "/" . 'face_' . date('YmdHis') . ".png";
            } else {
                $filename = "/" . 'face_' . date('YmdHis') . $ext;
            }
        }
        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $img=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2=@fopen($save_dir . $filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        $time = time() - $start_time . "s";
        return array('file_name'=>$filename,'save_path'=>substr($save_dir . $filename,1),'error'=>0,'time'=>$time);
    }
}