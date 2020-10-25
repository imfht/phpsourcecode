<?php
use Core\Config,Core\Route,Core\Model;
// 浏览器友好的变量输出
function dump($var, $exit=false){
	$output = print_r($var, true);
	$output = "<pre>" . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
	echo $output;
	if($exit) exit();
}

//获取微秒时间，常用于计算程序的运行时间
function utime(){
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

//生成唯一的值
function cp_uniqid(){
	return md5(uniqid(rand(), true));
}

//用户识别标记，防止用户盗用cookie
function user_agent(){
	//return md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);	
	return md5($_SERVER['HTTP_USER_AGENT']);	
}

//产生随机字符
function random($len=6,$type='',$addChars='') {
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
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=false) {
	if(function_exists("mb_substr"))
	$slice = mb_substr($str, $start, $length, $charset);
	elseif(function_exists('iconv_substr')) {
		$slice = iconv_substr($str,$start,$length,$charset);
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
 * 对象转换为数组
 *
 * @param unknown_type $obj
 * @return unknown
 */
function objectToArray($obj){
	$obj=(array)$obj;
	foreach($obj as $k=>$v){
		if( gettype($v)=='resource' ) return;
		if( gettype($v)=='object' || gettype($v)=='array' )
		$obj[$k]=(array)objectToArray($v);
	}
	return $obj;
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
function list_sort_by($list,$field, $sortby='asc') {
	if(is_array($list)){
		$refer = $resultSet = array();
		foreach ($list as $i => $data)
		$refer[$i] = &$data[$field];
		switch ($sortby) {
			case 'asc': // 正向排序
			asort($refer);
			break;
			case 'desc':// 逆向排序
			arsort($refer);
			break;
			case 'nat': // 自然排序
			natcasesort($refer);
			break;
		}
		foreach ( $refer as $key=> $val)
		$resultSet[] = &$list[$key];
		return $resultSet;
	}
	return false;
}

/**
 * 在数据列表中搜索
 * @access public
 * @param array $list 数据列表
 * @param mixed $condition 查询条件
 * 支持 array('name'=>$value) 或者 name=$value
 * @return array
 */
function list_search($list,$condition) {
	if(is_string($condition))
	parse_str($condition,$condition);
	// 返回的结果集合
	$resultSet = array();
	foreach ($list as $key=>$data){
		$find   =   false;
		foreach ($condition as $field=>$value){
			if(isset($data[$field])) {
				if(0 === strpos($value,'/')) {
					$find   =   preg_match($value,$data[$field]);
				}elseif($data[$field]==$value){
					$find = true;
				}
			}
		}
		if($find)
		$resultSet[]     =   &$list[$key];
	}
	return $resultSet;
}
/**
 * 生成url
 * @access public
 * @param array $url 地址参数url( '[模块/操作]','额外参数1=值1&额外参数2=值2...')
 * 支持 array('name'=>$value) 或者 name=$value
 * @return array
 */
function url($module,$request='',$author=''){	
	return Route::url($module,$request,$author);
}

//模块之间相互调用
function  module($group_module){
	static $module_obj=array();
	$config=array();
	if(isset($module_obj[$group_module])){
		return $module_obj[$group_module];
	}
	
	$config['CP_CONFIG_PATH'] = CP_CONFIG_PATH;
	$config['MODULE_PATH']=Config::get('MODULE_PATH');
	$config['MODULE_SUFFIX']=Config::get('MODULE_SUFFIX');	
	
	//分组处理
	$gm = explode('/',$group_module);
	$group = $gm[0];
	$module = $gm[1];
	
	if(empty($module)){//只有一个参数，没有$module
		$module = $group;
		$group = CP_GROUP;//默认当前分组
	}else{//$group,$module都有值 ，说明指定分组时
		$group_config_file = $config['CP_CONFIG_PATH'] .'/'. $group . '.php';		
		$group_config = array();//初始化变量防止E_NOTICE报错
		$group_config = include($group_config_file);
		!empty($group_config) && $config = array_merge($config, $group_config);//参数配置
	}
	$suffix_arr=explode('.',$config['MODULE_SUFFIX'],2);
	$config['MODULE_CLASS_SUFFIX']=$suffix_arr[0];//类后缀
	$module_file = $config['MODULE_PATH'].$module.$config['MODULE_SUFFIX'];//模块文件路径
	if(file_exists($module_file)){	    
		require_once($module_file);//加载模块文件
		$classname=$module.$config['MODULE_CLASS_SUFFIX'];
		//处理字符成为命名空间格式
    	$namespace = str_replace(array('../','./','/'), '\\', $config['MODULE_PATH']);
    	//模块名+模块后缀组成完整类名	
    	$classname=$module . $config['MODULE_CLASS_SUFFIX'];
    	//命名空间与类名合并
    	$spaceClass = $namespace.$classname;    
    	$module_obj[$group_module] = new $spaceClass();
    	return $module_obj[$group_module];
	}else{
		return false;
	}
}

//模型调用函数
function  model($model=''){
	//加载相应模型
	static $model_obj=array();
	static $config=array();
	empty($model) && $model=0;
	if(isset($model_obj[$model])){
		return $model_obj[$model];
	}
	//如果没有指定模型则 初始化model模型
	if (empty($model)) {		
		$model_obj[0] = new Model();//创建模型对象并返回		
		return $model_obj[0];
	}
	//判断配置信息
	if(!isset($config['MODEL_PATH'])){
		$config['MODEL_PATH']=Config::get('MODEL_PATH');
		$config['MODEL_SUFFIX']=Config::get('MODEL_SUFFIX');
		$suffix_arr=explode('.',$config['MODEL_SUFFIX'],2);
		$config['MODEL_CLASS_SUFFIX']=$suffix_arr[0];
	}
	//加载模型并初始化
	if(file_exists($config['MODEL_PATH'].$model.$config['MODEL_SUFFIX'])){
		require_once($config['MODEL_PATH'].$model.$config['MODEL_SUFFIX']);//加载模型文件
		$classname=$model.$config['MODEL_CLASS_SUFFIX'];
		if(class_exists($classname)){
		    //实例化对象			
			$model_obj[$model]=new $classname();
			//设置基本操作表
			if(empty($model_obj[$model]->tableName)) $model_obj[$model]->table($model);
			//返回对象实例
			return $model_obj[$model];
		}
	}else {//找不到模型文件时初始化Model 并设置表名
		$model_obj[$model] = new Model();//创建模型对象并返回
		$model_obj[$model]->table($model);
		return $model_obj[$model];
	}
	return false;
}


//加密函数，可用cp_decode()函数解密，$data：待加密的字符串或数组；$key：密钥；$expire 过期时间
function cp_encode($data,$key='',$expire = 0)
{
	$string=serialize($data);
	$ckey_length = 4;
	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = substr(md5(microtime()), -$ckey_length);

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string =  sprintf('%010d', $expire ? $expire + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++)
	{
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++)
	{
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++)
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	return $keyc.str_replace('=', '', base64_encode($result));
}
//cp_encode之后的解密函数，$string待解密的字符串，$key，密钥
function cp_decode($string,$key='')
{
	$ckey_length = 4;
	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = substr($string, 0, $ckey_length);

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string =  base64_decode(substr($string, $ckey_length));
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++)
	{
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++)
	{
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++)
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
		return unserialize(substr($result, 26));
	}
	else
	{
		return '';
	}
}
/**
 * Cookie 设置、获取、删除
 * @param string $name cookie名称
 * @param mixed $value cookie值
 * @param mixed $options cookie参数
 * @return mixed
 * 
 * cookie('name');//取得cookie值
 * cookie('name','val');//设置cookie值
 * cookie('name',null);//删除指定cookie
 * cookie(null,'pre');//删除指定前缀的cookie
 */
function cookie($name, $value='', $option=null) {
	// 默认设置
	$config = array(
	'prefix'    =>  Config::get('COOKIE_PREFIX'), // cookie 名称前缀
	'expire'    =>  Config::get('COOKIE_EXPIRE'), // cookie 保存时间
	'path'      =>  Config::get('COOKIE_PATH'), // cookie 保存路径
	'domain'    =>  Config::get('COOKIE_DOMAIN'), // cookie 有效域名
	);
	// 参数设置(会覆盖黙认设置)
	if (!is_null($option)) {
		$config = array_merge($config, array_change_key_case($option));
	}
	// 清除指定前缀的所有cookie
	if (is_null($name)) {
		if (empty($_COOKIE))
		return;
		// 要删除的cookie前缀，不指定则删除config设置的指定前缀
		$prefix = empty($value) ? $config['prefix'] : $value;
		if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
			foreach ($_COOKIE as $key => $val) {
				if (0 === stripos($key, $prefix)) {
					setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
					unset($_COOKIE[$key]);
				}
			}
		}
		return;
	}
	$name = $config['prefix'] . $name;
	if ('' === $value) {//获取cookie
		if(isset($_COOKIE[$name])){
			return $_COOKIE[$name];
		}else{
			return null;
		}
	} else {//删除cookie
		if (is_null($value)) {
			setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
			unset($_COOKIE[$name]); // 删除指定cookie
		} else {
			// 设置cookie
			$expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
			setcookie($name, $value, $expire, $config['path'], $config['domain']);
			$_COOKIE[$name] = $value;
		}
	}
}

/**
 * session管理函数
 * @param string|array $name session名称 如果为数组则表示进行session设置
 * @param mixed $value session值
 * @return mixed
 * 
 * session(array('name'=>'session_id','expire'=>3600));//初始化
 * session('name','value');  //设置 相当于 $_SESSION['name'] = 'value';
 * $value = session('name'); //取值 相当于 $value = $_SESSION['name'];
 * session('name',null); // 删除name 相当于unset($_SESSION['name']);
 * session(null); // 清空当前的session 相当于$_SESSION = array();
 * session('?name'); //相当于isset($_SESSION['name']);
 */
function session($name,$value='') {
    $prefix   =  Config::get('SESSION_PREFIX');
	if(is_array($name)) { // session初始化 在session_start 之前调用
		if(isset($name['prefix'])) Config::set('SESSION_PREFIX',$name['prefix']);
		if(Config::get('VAR_SESSION_ID') && isset($_REQUEST[Config::get('VAR_SESSION_ID')])){
			session_id($_REQUEST[Config::get('VAR_SESSION_ID')]);
		}elseif(isset($name['id'])) {
			session_id($name['id']);
		}
		ini_set('session.auto_start', 0);
		if(isset($name['name']))            session_name($name['name']);
		if(isset($name['path']))            session_save_path($name['path']);
		if(isset($name['domain']))          ini_set('session.cookie_domain', $name['domain']);
		if(isset($name['expire']))          ini_set('session.gc_maxlifetime', $name['expire']);
		if(isset($name['use_trans_sid']))   ini_set('session.use_trans_sid', $name['use_trans_sid']?1:0);
		if(isset($name['use_cookies']))     ini_set('session.use_cookies', $name['use_cookies']?1:0);
		if(isset($name['cache_limiter']))   session_cache_limiter($name['cache_limiter']);
		if(isset($name['cache_expire']))    session_cache_expire($name['cache_expire']);		
		// 启动session
		if(Config::get('SESSION_AUTO_START'))  session_start();
	}elseif('' === $value){
		if(0===strpos($name,'[')) { // session 操作
			if('[pause]'==$name){ // 暂停session
				session_write_close();
			}elseif('[start]'==$name){ // 启动session
				session_start();
			}elseif('[destroy]'==$name){ // 销毁session
				$_SESSION =  array();
				session_unset();
				session_destroy();
			}elseif('[regenerate]'==$name){ // 重新生成id
				session_regenerate_id();
			}
		}elseif(0===strpos($name,'?')){ // 检查session
			$name   =  substr($name,1);
			if($prefix) {
				return isset($_SESSION[$prefix][$name]);
			}else{
				return isset($_SESSION[$name]);
			}
		}elseif(is_null($name)){ // 清空session
			if($prefix) {
				unset($_SESSION[$prefix]);
			}else{
				$_SESSION = array();
				/***删除sessin id.由于session默认是基于cookie的，所以使用setcookie删除包含session id的cookie.***/
		        if (isset($_COOKIE[session_name()])) {
		        	setcookie(session_name(), '', time()-42000, '/');
		        }
		        // 最后彻底销毁session.
		        session_destroy();
			} 
		}elseif($prefix){ // 获取session
			return isset($_SESSION[$prefix][$name])?$_SESSION[$prefix][$name]:null;
		}else{
			return isset($_SESSION[$name])?$_SESSION[$name]:null;
		}
	}elseif(is_null($value)){ // 删除session
		if($prefix){
			unset($_SESSION[$prefix][$name]);
		}else{
			unset($_SESSION[$name]);
		}
	}else{ // 设置session
		if($prefix){
			if (!is_array($_SESSION[$prefix])) {
				$_SESSION[$prefix] = array();
			}
			$_SESSION[$prefix][$name]   =  $value;
		}else{
			$_SESSION[$name]  =  $value;
		}
	}
}

//如果json_encode没有定义，则定义json_encode函数，常用于返回ajax数据
if (!function_exists('json_encode')) {
	function format_json_value(&$value){
		if(is_bool($value)) {
			$value = $value?'true':'false';
		}else if(is_int($value)){
			$value = intval($value);
		}else if(is_float($value)){
			$value = floatval($value);
		}else if(defined($value) && $value === null){
			$value = strval(constant($value));
		}else if(is_string($value)){
			$value = '"'.addslashes($value).'"';
		}
		return $value;
	}

	function json_encode($data){
		if(is_object($data)){
			//对象转换成数组
			$data = get_object_vars($data);
		}else if(!is_array($data)) {
			// 普通格式直接输出
			return format_json_value($data);
		}
		// 判断是否关联数组
		if(empty($data) || is_numeric(implode('',array_keys($data)))) {
			$assoc  =  false;
		}else {
			$assoc  =  true;
		}
		// 组装 Json字符串
		$json = $assoc ? '{' : '[' ;
		foreach($data as $key=>$val) {
			if(!is_null($val)) {
				if($assoc){
					$json .= "\"$key\":".json_encode($val).",";
				}else{
					$json .= json_encode($val).",";
				}
			}
		}
		if(strlen($json)>1) {// 加上判断 防止空数组
			$json  = substr($json,0,-1);
		}
		$json .= $assoc ? '}' : ']' ;
		return $json;
	}
}
/**
 * XML编码
 * @param mixed $data 数据
 * @param string $encoding 数据编码
 * @param string $root 根节点名
 * @return string
 */
function xml_encode($data, $encoding='utf-8', $root='think') {
	$xml    = '<?xml version="1.0" encoding="' . $encoding . '"?>';
	$xml   .= '<' . $root . '>';
	$xml   .= data_to_xml($data);
	$xml   .= '</' . $root . '>';
	return $xml;
}

/**
 * 数据XML编码
 * @param mixed $data 数据
 * @return string
 */
function data_to_xml($data) {
	$xml = '';
	foreach ($data as $key => $val) {
		is_numeric($key) && $key = "item id=\"$key\"";
		$xml    .=  "<$key>";
		$xml    .=  ( is_array($val) || is_object($val)) ? data_to_xml($val) : $val;
		list($key, ) = explode(' ', $key);
		$xml    .=  "</$key>";
	}
	return $xml;
}


