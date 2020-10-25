<?php
namespace app\common\model;

use think\Model;
use app\common\fun\Cfgfield;
//use think\Db;


class User extends Model
{
    //protected static $passport_table = 'members';   //整合论坛的话，就要写上论坛的数据表前缀
	
    // 设置当前模型对应的完整数据表名称memberdata
    protected $table = '__MEMBERDATA__';
	
	//主键不是ID,要单独指定
	public $pk = 'uid';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;

    /**
     * 根据帐号获取用户信息
     * @param string $name 帐号
     * @return unknown
     */
    public static function getByName($name = '')
    {
        return static::format(self::get(['username' => $name]));
    }
	
    /**
     * 根据UID获取用户信息
     * @param string $id 用户UID
     * @return unknown
     */
	public static function getById($id = '')
    {
        $rarray = static::format(self::get(['uid' => $id]));
        if (empty($rarray)) {
	        return ;
	    }
	    return $rarray;
    }
    
    
    /**
     *  获取某个用户的所有信息
     * @param unknown $value 可以是数组
     * @param string $type 可以取任何字段
     * @param string $format 是否对字段进行转义处理
     * @return array|\app\common\model\NULL[]|\app\common\model\unknown|array
     */
    public static function get_info($value,$type='uid',$format=true){
        if(is_array($value)){
            $map = $value;
        }elseif($type=='name'){
            $map['username'] = $value;
        }elseif(preg_match('/^[\w]+$/', $type)){
            $map[$type] = $value;
        }
        $array = self::get($map);
        if ($format) {
            return static::format($array);
        }else{
            $array = getArray(self::get($map));
            $array['sendmsg'] = json_decode($array['sendmsg'],true)?:[];
            $array['ext_field'] = json_decode($array['ext_field'],true)?:[];
            if ($array['ext_field']) {
                $array = array_merge($array['ext_field'],$array);
            }
            return $array;
        }
    }
    
    /**
     * 对字段做进一步的处理,后面继承的,可以重写
     * @param array $array
     * @return array|NULL[]|unknown
     */
    protected static function format($array=[]){
        $array = getArray($array);
        if (empty($array)) {
            return [];
        }
        if ($array['group_endtime'] && $array['groupid'] != 8 && $array['group_endtime']<time()) { //用户组过期了
            $array['groupid'] = ($array['old_groupid']&&getGroupByid($array['old_groupid'])) ? $array['old_groupid'] : 8;     //恢复之前的用户组
            static::edit_user([
                'uid'=>$array['uid'],
                'groupid'=>$array['groupid'],
            ]);
        }        
        $array['sendmsg'] = json_decode($array['sendmsg'],true)?:[];
        $array['ext_field'] = json_decode($array['ext_field'],true)?:[];
        if ($array['ext_field']) {
            $array = array_merge(Cfgfield::format($array['ext_field'],$array['groupid']),$array);   //ext_field自定义字段的优先级要低于系统字段
        }
        $array['icon'] && $array['icon'] = tempdir($array['icon']);
        $array['qun_group'] = fun('qun@get_my_group',$array['uid']);
        return $array;
    }
    
	
	/**
	 * 检查密码是否正确,密码正确,返回用户所有信息, 用户不存在,返回0, 密码不正确返回-1
	 * @param string $username 默认是用户帐号,也可以是UID或手机号,要重新定义$type值
	 * @param string $password 密码,也可以是加密后的密码,但用的很少,一般是原始密码
	 * @param string $type 对应第一项的字段,默认是username
	 * @param string $checkmd5
	 * @return number|unknown
	 */
	public static function check_password($username='',$password='',$type='username',$checkmd5=false){
	    $rs = self::get_info($username,$type);
	    if(empty($rs) || !is_array($rs)){
			return 0;
		}
		if($checkmd5===true && strlen($password)==32 && $password==$rs['password'] ){
		    return $rs;
		}elseif(static::md5pwd($password,$rs['password_rand'])==$rs['password']){
		    return $rs;
		}
		return -1;
	}
	
	/**
	 * 检查帐号即用户名是否合法,合法返回true,不合法返回false
	 * @param unknown $username
	 * @return boolean
	 */
	public static function check_username($username) {
		$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
		$len = strlen($username);
		if($len > 50 || $len < 2 || preg_match("/\s+|^c:\\con\\con|[%,\*\'\"\s\<\>\&]|$guestexp/is", $username)) {
			return '用户名不合法';
		} else {
			return true;
		}
	}
	
	/**
	 * 检查用户名是否存在,存在返回
	 * @param unknown $value
	 * @return unknown
	 */
	public static function check_userexists($value) {
	    $info = self::get(['username'=>$value]);
	    return $info?$info:false;
	}

	/**
	 * 检查邮箱是否存在
	 * @param unknown $value
	 * @return boolean|unknown
	 */
	public static function check_emailexists($value) {
	    $rs = self::get(['email'=>$value]);
	    return $rs?$rs:false;
	}
	
	/**
	 * 过滤掉emoji表情
	 * @param unknown $str
	 * @return mixed
	 */
	protected static function filterEmoji($str)
	{
	    $str = preg_replace_callback( '/./u',
	            function (array $match) {
	                return strlen($match[0]) >= 4 ? '' : $match[0];
	            },
	            $str);
	    return $str;
	}
	
	/**
	 * 用户注册 注册成功,只返回UID数值,不成功,返回对应的提示字符串
	 * @param unknown $array
	 * @return string|mixed
	 */
	public static function register_user($array){
	    
	    if(self::get_info($array['username'],'username')){
	        return '当前用户已经存在了'.$array['username'];
	    }
	    if(config('webdb.forbidRegName')!=''){
	        $detail = str_array(config('webdb.forbidRegName'));
	        if(in_array($array['username'], $detail)){
	            return '请换一个用户名,当前用户名不允许使用'.$array['username'];
	        }
	    }
	    if(!$array['username']){
	        return '帐号不能为空';
	    }elseif(!$array['password']){
	        return '密码不能为空';
	    }elseif(strlen($array['username'])>50||strlen($array['username'])<2){
	        return '用户名不能小于2个字节或大于50个字节';
	    }elseif (strlen($array['password'])>30 || strlen($array['password'])<5){
	        return '密码不能小于5个字符或大于30个字符';
	    }elseif($array['email']&&!preg_match("/^[-a-zA-Z0-9_\.]+\@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$/",$array['email'])){
	        return '邮箱不符合规则';
	    }elseif( config('webdb.emailOnly') && $array['email'] && self::check_emailexists($array['email'])){
	        return "当前邮箱“{$array['email']}”已被注册了,请更换一个邮箱!";
	    }
	    
	    $S_key=array('|',' ','',"'",'"','/','*',',','~',';','<','>','$',"\\","\r","\t","\n","`","!","?","%","^");
	    
	    //后来增加
	    $array['username'] = str_replace(array('|',' ','',"'",'"','/','*',',','~',';','<','>','$',"\\","\r","\t","\n","`","!","?","%","^"),'',$array['username']);
	    
	    foreach($S_key as $value){
	        if (strpos($array['username'],$value)!==false){
	            return "用户名中包含有禁止的符号“{$value}”";
	        }
	        if (strpos($array['password'],$value)!==false){
	            //return "密码中包含有禁止的符号“{$value}”";
	        }
	    }
	    if($array['username']==''){
	        return '用户名为空了!';
	    }
	    
	    foreach($array AS $key=>$value){
	        $array[$key] = filtrate($value);
	    }

		$result = get_hook('user_add_begin',$array,[],[],true);
	    if ($result!==null) {
	        return $result;
	    }
	    hook_listen('user_add_begin',$array);	    

	    if(($array['uid'] = static::insert_data($array))==false){
	        return "创建用户失败";
	    }
		get_hook('user_add_end',$array,[],[],true);
	    hook_listen('user_add_end',$array);	    
	    
	    return $array['uid'];
	}
	
	/**
	 * 注册用户信息入库,成功返回uid,失败返回false
	 * @param unknown $array
	 * @return boolean|unknown
	 */
	protected static function insert_data($array){
		$array['groupid'] || $array['groupid']=8;
		isset($array['yz']) || $array['yz']=1;
		$array['regdate'] = time();
		$array['lastvist'] = time();
		$array['regip'] = get_ip();
		$array['lastip'] = get_ip();
        
		//用户昵称
		$array['nickname'] = $array['username'];
		$array['password_rand'] = rands(rand(5,10));
		$array['password'] = static::md5pwd ($array['password'],$array['password_rand']);

		if( ($result = self::create($array))!=false){
		    return $result->uid;
		}
		return false;
	}
	
	/**
	 * 修改用户任意信息,修改成功 返回true
	 * @param array $array 数值当中必须要存在uid
	 * @return string|boolean
	 */
	public static function edit_user($array=[]) {
        
	    //cache('user_'.$array['uid'],null);
	    
		$result = get_hook('user_edit_begin',$array,[],[],true);
	    if ($result!==null) {
	        return $result;
	    }
	    hook_listen('user_edit_begin',$array);	    
		
	    if( config('webdb.emailOnly') && $array['email'] ){
	        $r = self::check_emailexists($array['email']);
	        if($r && $r['uid']!=$array['uid']){
	            return "当前邮箱存在了,请更换一个!";
	        }
	    }
	    
	    if($array['password'] && strlen($array['password'])<32){
	        $array['password_rand'] = rands(rand(5,10));
	        $array['password'] = static::md5pwd($array['password'],$array['password_rand']);
	    }else{
	        unset($array['password'],$array['password_rand']);
	    }
	    
	    $field_array = table_field('memberdata');  //主表标准字段
	    $ext_field = [];   //用户自定义字段
	    foreach($array AS $key=>$value){
	        if (!in_array($key, $field_array)) {   //非主表的标准字段,就当作自字义字段统一处理
	            $ext_field[$key] = $value;
	        }
	    }
	    $info = getArray(self::get($array['uid']));
	    if ($info['ext_field'] && json_decode($info['ext_field'],true)) {
	        $array['ext_field'] = array_merge(json_decode($info['ext_field'],true),$ext_field); //新的覆盖之前的
	    }else{
	        $array['ext_field'] = $ext_field;
	    }
	    
	    $array['ext_field'] = json_encode($array['ext_field']);
		
		if(self::update($array)){
		    cache('user_'.$array['uid'],null);
			get_hook('user_edit_end',$array,[],[],true);
		    hook_listen('user_edit_end',$array);		   
		    return true;
		}else{
		    return '数据库修改失败';
		}
	}

	
	/**
	 * 删除会员
	 * @param unknown $uid
	 * @return boolean
	 */
	public static function delete_user($uid=0) {
		get_hook('user_delete_begin',$array=[],[],['uid'=>$uid],true);
	    hook_listen('user_delete_begin',$uid);	    

		if(self::destroy($uid)){
		    cache('user_'.$uid,null);
			get_hook('user_delete_end',$array=[],[],['uid'=>$uid],true);
		    hook_listen('user_delete_end',$uid);		    
		    return true;
		}
	}
	
	/**
	 * 获取会员总数
	 * @param array $map 查询条件
	 * @return mixed
	 */
	public static function total_num($map = []) {
	    return self::where($map)->count('uid');
	}
	
	/**
	 * 获取一批会员资料信息
	 * @param array $map 查询条件
	 * @param number $rows 每页几条
	 * @param string $order 排序方式
	 * @param array $pages 分页格式
	 * @return unknown
	 */
	public static function get_list($map=[], $rows=10, $order='uid desc',$pages=[]) {
	    $data_list = self::where($map)->order($order)->paginate(
	            empty($rows)?null:$rows,    //每页显示几条记录
	            empty($pages[0])?false:$pages[0],
	            empty($pages[1])?[]:$pages[1]
	           );
	    $data_list->each(function($rs,$key){
	        $rs['icon'] && $rs['icon'] = tempdir($rs['icon']);
	    });
	    return $data_list;
	}
	
	
	
	/**
	 * 用户登录,登录成功返回用户的所有信息, 0代表用户不存在,-1代表密码错误
	 * @param string $username 用户名或者是手机号
	 * @param string $password 原始密码
	 * @param unknown $cookietime 登录有效时长
	 * @param string $not_pwd 是否不需要密码,比如QQ或微信登录
	 * @param string $type 用户的方式,帐号还是手机号还是邮箱
	 * @return number|unknown 登录成功返回用户的所有信息, 0代表用户不存在,-1代表密码错误
	 */
	public static function login($username='',$password='',$cookietime=null,$not_pwd=false,$type='username'){
// 	    if(!table_field('memberdata','password_rand')){    //升级数据库
// 	        into_sql(APP_PATH.'common/upgrade/5.sql');
// 	    }
	    $array = [
	            'username'=>$username,
	            'password'=>$password,
	            'time'=>$cookietime,
	            'not_pwd'=>$not_pwd,
	            'type'=>$type,
	    ];
		get_hook('user_login_begin',$array,[],[],true);
	    hook_listen('user_login_begin', $array);	    
	    
	    if($username==''){
            return 0;
        }
		if($not_pwd===true){	//不需要知道原始密码就能登录
		    $rs = static::get_info($username,$type);
		}else{
		    $rs = static::check_password($username,$password,$type);
			if(!is_array($rs)){
				return $rs;		//0为用户不存在,-1为密码不正确
			}
		}
		
		if ($not_pwd==false) {
		    
		    if(!config('webdb.allow_allcity_login') && ( strlen($rs['weixin_api'])>20 || strlen($rs['qq_api'])>20 || ($rs['mob_yz']&&preg_match("/^1([\d]{10})+/", $rs['mobphone'])) ) ){
		        $str = file_get_contents("http://api.map.baidu.com/location/ip?ak=MGdbmO6pP5Eg1hiPhpYB0IVd&ip=".$rs['lastip']."&coor=bd09ll");
		        $array = json_decode($str ,true);
		        $lastcity = $array['content'] ? $array['content']['address_detail']['city'] : '';
		        if($lastcity){
		            $str = file_get_contents("http://api.map.baidu.com/location/ip?ak=MGdbmO6pP5Eg1hiPhpYB0IVd&ip=".get_ip()."&coor=bd09ll");
		            $array = json_decode($str ,true);
		            if($lastcity!=$array['content']['address_detail']['city']){
		                $show = '';
		                $rs['weixin_api'] && $show.='微信、';
		                $rs['qq_api'] && $show.='QQ、';
		                $rs['mob_yz'] && $show.='手机、';
		                showerr("你当前登录城市与上一次登录城市不一致，请选择其它方式登录！比如 ".$show);
		            }
		        }
		    }
		    
		    $content = '友情提醒：你的帐号 '.$username.' 刚刚登录过 '.config('webdb.webname').'，如果不是你本人操作，估计密码已被盗，请尽快修改密码！'.' <a href="'.murl('member/user/edit').'" target="_blank">立即登录</a>';
		    $rs['weixin_api'] && send_wx_msg($rs['weixin_api'], $content);
		}

		$data = [
			        'uid'=>$rs['uid'],
			        'lastvist'=>time(),
			        'lastip'=>get_ip(),
		];
		self::edit_user($data);

		set_cookie("passport","{$rs['uid']}\t$username\t".mymd5($rs['password'],'EN'),$cookietime);

		$array = [
		        'uid'=>$rs['uid'],
		        'username'=>$username,
		        'password'=>$password,
		        'time'=>$cookietime,
		        'not_pwd'=>$not_pwd,
		        'type'=>$type,
		];
		get_hook('user_login_end',$array,$rs,[],true);
		hook_listen('user_login_end', $array,$rs);
		
		return $rs;
	}
	
	/**
	 * 用户退出
	 * @param number $uid
	 */
	public static function quit($uid=0){
		set_cookie('passport',null);
		set_cookie('_passport',null);
		cache('user_'.$uid,null);
		set_cookie('token_secret',null);
		setcookie('adminID',null);	//同步后台退出
		get_hook('user_quit_end',$array=[],[],['uid'=>$uid],true);
		hook_listen('user_quit_end',$uid);		
	}
	
	/**
	 * 获取用户的登录token
	 * @return unknown[]|array[]
	 */
	public static  function get_token(){
	    $token = input('token');
	    if( empty(input('havelogin')) && strlen($token)>=32 && $token_string = cache($token) ){   //APP或小程序 havelogin=1的时候,就不要覆盖登录了,比如充值的时候。
	        list($uid,$username,$password) = explode("\t",$token_string);
	        if(input('once')==1){
	            cache($token,null);    //出于安全考虑,1次有效
	        }
	        if($uid&&$username&&$password){
	            set_cookie('passport',$token_string,3600*72);       //同步登录框架小程序
	            return ['uid'=>$uid,'username'=>$username,'password'=>$password];
	        }
	    }
	    $toke = get_cookie('passport')?:get_cookie('_passport');
	    list($uid,$username,$password) = explode("\t",$toke);
	    if($uid&&$username&&$password){
	        if (empty(get_cookie('_passport'))) {
	            set_cookie('_passport',$toke); //避免用户在操作过程,因登录过期,而自动退出
	        }
	        return ['uid'=>$uid,'username'=>$username,'password'=>$password];
	    }
	}
	
	/**
	 * 用户登录状态的信息
	 * @return void|mixed|\think\cache\Driver|boolean
	 */
	public static function login_info(){        
	    if(!$token=self::get_token()){
	        return false;
	    }	    
	    $usr_info = cache('user_'.$token['uid']);
	    if(empty($usr_info['password'])){
	        $usr_info = static::get_info(intval($token['uid']));
	        cache('user_'.$usr_info['uid'],$usr_info,3600);
	    }
	    if( mymd5($usr_info['password'],'EN') != $token['password'] ){
	        static::quit($usr_info['uid']);
	        return false;
	    }
		return $usr_info;
	}

	/**
	 * 检查微信openid是否存在
	 * @param unknown $openid
	 * @return unknown
	 */
	public static function check_wxIdExists($openid) {
		return self::get(['weixin_api'=>$openid]);
	}
	
	/**
	 * 检查QQ的openid是否存在
	 * @param unknown $openid
	 * @return unknown
	 */
	public static function check_qqIdExists($openid) {
	    return self::get(['qq_api'=>$openid]);
	}
	
	/**
	 * 检查小程序openid是否存在
	 * @param unknown $openid
	 * @return unknown
	 */
	public static function check_wxappIdExists($openid) {
	    return self::get(['wxapp_api'=>$openid]);
	}
	
	/**
	 * 密码加密方式
	 * @param string $password 原始密码
	 * @param string $pwdRand 随机串
	 * @return string
	 */
	protected static function md5pwd($password='',$pwdRand=''){
	    switch (config('md5_pwd_type')){
	        case 1:
	            return md5(md5($password).$pwdRand);
	            break;
	        case 2:
	            return md5($password.md5($pwdRand));
	            break;
	        case 3:
	            return md5(md5($password.$pwdRand));
	            break;
	        default:
	            return md5($password.$pwdRand);
	    }
	}
	
	/**
	 * 会员标签调用数据
	 * @param unknown $tagArray
	 * @param number $page
	 * @return string
	 */
	public static function labelGet($tagArray , $page=0)
	{
	    $map = [];
	    $cfg = unserialize($tagArray['cfg']);
	    $cfg['rows'] || $cfg['rows'] = 10;
	    $cfg['order'] || $cfg['order'] = 'uid';
	    $cfg['by'] || $cfg['by'] = 'desc';
	    
	    $page = intval($page);
	    if ($page<1) {
	        $page=1;
	    }
	    $min = ($page-1)*$cfg['rows'];
	    
	    if($cfg['where']){  //用户自定义的查询语句
	        $_array = fun('label@where',$cfg['where'],$cfg);
	        if($_array){
	            $map = array_merge($map,$_array);
	        }
	    }
	    $whereor = [];
	    if($cfg['whereor']){  //用户自定义的查询语句
	        $_array = fun('label@where',$cfg['whereor'],$cfg);
	        if($_array){
	            $whereor = $_array;
	        }
	    }
	    $obj = self::where($map)->whereOr($whereor);
	    if(strstr($cfg['order'],'rand()')){
	        $obj -> orderRaw('rand()');
	    }else{
	        $obj -> order($cfg['order'],$cfg['by']);
	    }	    
	    $array = $obj -> paginate($cfg['rows'],false,['page'=>$page]);
	    $array->each(function(&$rs,$key){
	        $rs['title'] = $rs['username'];
	        $rs['full_lastvist'] = $rs['lastvist'];
	        $rs['lastvist'] = date('Y-m-d H:i',$rs['lastvist']);
	        $rs['full_regdate'] = $rs['regdate'];
	        $rs['regdate'] = date('Y-m-d H:i',$rs['regdate']);
	        $rs['icon'] = $rs['picurl'] = tempdir($rs['icon']);
	        $rs['url'] = get_url('user',['uid'=>$rs['uid']]);
	        $rs['group_name'] = getGroupByid($rs['groupid']);
	        unset($rs['password'],$rs['password_rand'],$rs['lastip'],$rs['regip'],$rs['qq_api'],$rs['weixin_api'],$rs['wxapp_api'],$rs['email'],$rs['address'],$rs['mobphone'],$rs['idcard'],$rs['idcardpic'],$rs['truename'],$rs['config'],$rs['rmb_pwd']);
	        //$rs['password'] = $rs['password_rand'] = $rs['lastip'] = $rs['regip'] = $rs['qq_api'] = $rs['weixin_api'] = $rs['wxapp_api'] = $rs['email'] = $rs['address'] = $rs['mobphone'] = $rs['idcard'] = $rs['idcardpic'] = $rs['truename'] = $rs['config'] = $rs['rmb_pwd'] = '';
	        return $rs;
	    });
	    return $array;
	}
	
	
	/**
	 * 按地图位置远近获取数据
	 * @param array $map    查询条件
	 * @param string $point    地图点坐标
	 * @param number $rows
	 * @param array $pages
	 * @return \think\Paginator
	 */
	public static function getListByMap($map=[],$point='113.224932,23.184547',$rows=0,$pages=[]){
	    list($x,$y) = explode(',',$point);
	    $x = (float)$x;
	    $y = (float)$y;
	    $data_list = self::where($map)->field("*,(POW( `map_x`-$x,2 )+POW(`map_y`-$y,2)) AS map_point")->order('map_point asc')->paginate(
	            empty($rows)?null:$rows,    //每页显示几条记录
	            empty($pages[0])?false:$pages[0],
	            empty($pages[1])?['query'=>input('get.')]:$pages[1]
	            );
	    return $data_list;
	}
	
}