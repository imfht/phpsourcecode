<?php

/*------------------------- 数字转文字 -------------------------*/
/*
*	数字替换文字
*	tpl:int_str(1,'1:开启,0:关闭')
*   tpl:int_str(1,'1:true,0:false')
*	tpl:int_str(0,'TYPE_NAME');
*/
function int_str($int=0,$str=''){
	if(empty($str)) return false;
	$array=array();$icon=false;
    //int_str(1,'1:开启,0:关闭')
    if(strpos($str,',') !== false){
        //判断英文返回符号
        if(preg_match('/:([a-z]+),*/', $str)) $icon=true;
        $arr=explode(',',$str);
        foreach ($arr as $k => $v) {
            list($kk,$vv)=explode(':', $v);
            $array[$kk]=$vv;
        }
    }
    //int_str(0,'TYPE_NAME',1);
    elseif(preg_match('/^[a-zA-z]+$/', $str)){
        $array=C($str);
    }else{
        return false;
    }

    if(isset($array[$int])){
        if(!$icon) return $array[$int];
        else return "<i class='icon-".$array[$int]."'></i>";
    }else{
        return false;
    }
}
function str_arr($string=''){
    if(empty($string)) return false;
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  = array();
        foreach ($array as $val) {
            //解决domain:http://static.mitangtrip.com问题
            $_i=strpos($val,':');
            $_k=substr($val, 0,$_i);
            $_v=substr($val, $_i+1);
            $value[$_k] = $_v;
        }
        return $value;
    }else{
        return $array;
    }
}
function arr_str($array=''){
    if(empty($array)) return false;
    $string='';
    //判断是否为索引数组
    if(is_numeric(key($array))){
        $string=explode(',', $array);
    }else{
        foreach ($array as $k => $v) {
            $string.=$k.':'.$v.',';
        }
        $string=trim($string,',');
    }
    return $string;
}


/*------------------------- 字符串 -------------------------*/
/**
 * 字符串截取，支持中文和其他编码
 */
function msubstr($str, $start=0, $length, $charset="utf-8") {
	if(mb_strlen($str,$charset) <= $length) $suffix=false;
	else $suffix=true;
	
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
    return $suffix ? $slice.'...' : $slice;
}
//获取说及字符串
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


/*------------------------- 安全过滤 -------------------------*/
//输出安全的html
function safe_html($text, $tags = null) {
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
	$text	=	preg_replace('/\r?\n/','',$text);
	//br
	$text	=	preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
	$text	=	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
	//过滤危险的属性，如：过滤on事件lang js
	while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1],$text);
	}
	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].$mat[3],$text);
	}
	if(empty($tags)) {
		$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
	}
	//允许的HTML标签
	$text	=	preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);
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
	$text	=	str_replace('  ',' ',$text);
	return $text;
}


/*------------------------- 核心封装 -------------------------*/
/**
 * 用于加载第三方Common/Lib中的类
 */
function lib($class,$ext='.php'){
    $baseUrl = COMMON_PATH.'Lib/';
    return import($class, $baseUrl, $ext);
}
/**
 * dump简写封装用于调试输出
 */
function p($param){
	//dump(数组参数,是否显示1/0,显示标签('<pre>'),模式[0为print_r])
	dump($param,1,'',0);
}
/**
 * 处理插件钩子
 */
function hook($hook,$params=array()){
    \Think\Hook::listen($hook,$params);
}
/**
 * 搭配UrlMapModel使用的根据路由生成URL
 */
function UU($url='',$vars='',$suffix=true,$domain=false){
	//获取数据库URL并缓存
	$routes=D('Common/Urlmap')->getRouteUrl();

	if(!C('URL_ROUTER_ON') && empty($routes)){
		//按变量顺序绑定
		if(C('URL_PARAMS_BIND_TYPE') && !empty($url)){
			if(!empty($vars)){
				$url.='/'.implode('/', $vars);
				$vars='';
			}else{
				$_parse=parse_url($url);
				parse_str($_parse['query'],$_query);
				$url=$_parse['path'].'/'.implode('/', $_query);
			}
		}
		return U($url,$vars,$suffix,$domain);
	} 

    // 解析URL
    $info   =  parse_url($url);
    $url    =  !empty($info['path'])?$info['path']:ACTION_NAME;
    if(isset($info['fragment'])) { // 解析锚点
        $anchor =   $info['fragment'];
        if(false !== strpos($anchor,'?')) { // 解析参数
            list($anchor,$info['query']) = explode('?',$anchor,2);
        }        
        if(false !== strpos($anchor,'@')) { // 解析域名
            list($anchor,$host)    =   explode('@',$anchor, 2);
        }
    }elseif(false !== strpos($url,'@')) { // 解析域名
        list($url,$host)    =   explode('@',$info['path'], 2);
    }
    // 解析子域名
    if(isset($host)) {
        $domain = $host.(strpos($host,'.')?'':strstr($_SERVER['HTTP_HOST'],'.'));
    }elseif($domain===true){
        $domain = $_SERVER['HTTP_HOST'];
        if(C('APP_SUB_DOMAIN_DEPLOY') ) { // 开启子域名部署
            $domain = $domain=='localhost'?'localhost':'www'.strstr($_SERVER['HTTP_HOST'],'.');
            // '子域名'=>array('模块[/控制器]');
            foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
                $rule   =   is_array($rule)?$rule[0]:$rule;
                if(false === strpos($key,'*') && 0=== strpos($url,$rule)) {
                    $domain = $key.strstr($domain,'.'); // 生成对应子域名
                    $url    =  substr_replace($url,'',0,strlen($rule));
                    break;
                }
            }
        }
    }
    // 解析参数
    if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
        parse_str($vars,$vars);
    }elseif(!is_array($vars)){
        $vars = array();
    }
    if(isset($info['query'])) { // 解析地址里面参数 合并到vars
        parse_str($info['query'],$params);
        $vars = array_merge($params,$vars);
    }
	
	ksort($vars);//路由链接_微风添加

	// URL组装
	$depr       =   C('URL_PATHINFO_DEPR');
	$urlCase    =   C('URL_CASE_INSENSITIVE');
    // if($url) {
    //     if(0=== strpos($url,'/')) {// 定义路由
    //         $route      =   true;
    //         $url        =   substr($url,1);
    //         if('/' != $depr) {
    //             $url    =   str_replace('/',$depr,$url);
    //         }
    //     }else{
				if('/' != $depr) { // 安全替换
					$url    =   str_replace('/',$depr,$url);
				}
				// 解析模块、控制器和操作
				$url        =   trim($url,$depr);
				$path       =   explode($depr,$url);
				$var        =   array();
				$varModule      =   C('VAR_MODULE');
				$varController  =   C('VAR_CONTROLLER');
				$varAction      =   C('VAR_ACTION');
				$var[$varAction]       =   !empty($path)?array_pop($path):ACTION_NAME;
				$var[$varController]   =   !empty($path)?array_pop($path):CONTROLLER_NAME;
				if($maps = C('URL_ACTION_MAP')) {
					if(isset($maps[strtolower($var[$varController])])) {
						$maps    =   $maps[strtolower($var[$varController])];
						if($action = array_search(strtolower($var[$varAction]),$maps)){
							$var[$varAction] = $action;
						}
					}
				}
				if($maps = C('URL_CONTROLLER_MAP')) {
					if($controller = array_search(strtolower($var[$varController]),$maps)){
						$var[$varController] = $controller;
					}
				}
				if($urlCase) {
					$var[$varController]   =   parse_name($var[$varController]);
				}
				$module =   '';

				if(!empty($path)) {
					$var[$varModule]    =   implode($depr,$path);
				}else{
					if(C('MULTI_MODULE')) {
						if(MODULE_NAME != C('DEFAULT_MODULE') || !C('MODULE_ALLOW_LIST')){
							$var[$varModule]=   MODULE_NAME;
						}
					}
				}
				if($maps = C('URL_MODULE_MAP')) {
					if($_module = array_search(strtolower($var[$varModule]),$maps)){
						$var[$varModule] = $_module;
					}
				}
				if(isset($var[$varModule])){
					$module =   $var[$varModule];
					//unset($var[$varModule]);
				}
    // }

	if(C('URL_MODEL') == 0) { // 普通模式URL转换
		//$url        =   __APP__.'?'.C('VAR_MODULE')."={$module}&".http_build_query(array_reverse($var));
		$url        =   __APP__.'?'.http_build_query(array_reverse($var));
		if($urlCase){
			$url    =   strtolower($url);
		}
		if(!empty($vars)) {
			$vars   =   http_build_query($vars);
			$url   .=   '&'.$vars;
		}
	}else{ // PATHINFO模式或者兼容URL模式
		
		/*路由链接_替换_开始*/

		// if(empty($var[C('VAR_MODULE')])){
		// 	$var[C('VAR_MODULE')]=MODULE_NAME;
		// }
		$module_controller_action=strtolower(implode($depr,array_reverse($var)));

		$has_route=false;
		if(isset($routes[$module_controller_action])){
			$urlrules=$routes[$module_controller_action];
			$empty_query_urlrule=array();
			foreach ($urlrules as $ur){
				if(!empty($ur['query'])){
					$intersect=array_intersect($ur['query'], $vars);
				}else{
					$intersect=true;
				}
				
				if($intersect){
					$vars=array_diff_key($vars,$ur['query']);
					$url= $ur['url'];
					$has_route=true;
					break;
				}
				if(empty($empty_query_urlrule) && empty($ur['query'])){
					$empty_query_urlrule=$ur;
				}
			}

			// if(!empty($empty_query_urlrule)){
			// 	$url=$empty_query_urlrule['url'];
			// 	foreach ($vars as $key =>$value){
			// 		if(strpos($url, ":$key")!==false){
			// 			$url=str_replace(":$key", $value, $url);
			// 			unset($vars[$key]);
			// 		}
			// 	}
			// 	$url=str_replace(array("\d","$"), "", $url);
			// 	$has_route=true;
			// }
			if(!empty($empty_query_urlrule) || !empty($vars)){
				$url=isset($empty_query_urlrule['url']) ? $empty_query_urlrule['url'] : $url;
				foreach ($vars as $key =>$value){
					if(strpos($url, ":$key")!==false){
						$url=str_replace(":$key", $value, $url);
						unset($vars[$key]);
					}
				}
			}
			$url=str_replace(array("\d","$","[:p]"), "", $url);
			
			if($has_route){
				if(!empty($vars)) { // 添加参数
					foreach ($vars as $var => $val){
						if('' !== trim($val))   $url .= $depr . $var . $depr . urlencode($val);
					}
				}
				$url =__APP__."/".$url ;
			}
		}
		
		if(!$has_route){
			$module =   defined('BIND_MODULE') ? '' : $module;
			$url    =   __APP__.'/'.implode($depr,array_reverse($var));
		}
		/*路由链接_替换_结束*/

		if($urlCase){
			$url    =   strtolower($url);
		}
			
		if(!empty($vars)) { // 添加参数
			foreach ($vars as $var => $val){
				if('' !== trim($val))   $url .= $depr . $var . $depr . urlencode($val);
			}
		}
		
		if($suffix) {
			$suffix   =  $suffix===true?C('URL_HTML_SUFFIX'):$suffix;
			if($pos = strpos($suffix, '|')){
				$suffix = substr($suffix, 0, $pos);
			}
			if($suffix && '/' != substr($url,-1)){
				$url  .=  '.'.ltrim($suffix,'.');
			}
		}
	}
	
	if(isset($anchor)){
		$url  .= '#'.$anchor;
	}
	if($domain) {
		$url   =  (is_ssl()?'https://':'http://').$domain.$url;
	}
	
	return $url;
}

