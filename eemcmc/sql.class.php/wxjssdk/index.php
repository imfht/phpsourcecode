<?php
require_once dirname(__DIR__) .'/sql.class.php';
require_once __DIR__ .'/unirest.class.php';

/* 仓鼠 */
define('G_TIMESTAMP', time());

class G
{

	/**
	 * @var SqlDataSource
	 */
	static $ds = null;

	const TABLE = 'wxsdk_uses';

	static function normalize($input, $delimiter = ',')
	{
		if (!is_array($input))
		{
			$input = explode($delimiter, $input);
		}
		$input = array_map('trim', $input);
		return array_filter($input, 'strlen');
	}
	
	static function fast_uuid($suffix_len=3){
		//! 计算种子数的开始时间
        static $being_timestamp = 1336981180;
        
        $time = explode(' ', microtime());
        $id = ($time[1] - $being_timestamp) . sprintf('%06u', substr($time[0], 2, 6));
        if ($suffix_len > 0)
        {
            $id .= substr(sprintf('%010u', mt_rand()), 0, $suffix_len);
        }
        return $id;
	}

    static function is_domain($value){return preg_match('/[a-z0-9\.]+/i', $value);}
    
	static function is_url($value)
	{
		// SCHEME
		$urlregex = "^(https?|ftp)\:\/\/";
		
		// USER AND PASS (optional)
		$urlregex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?";
		
		// HOSTNAME OR IP
		$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*";

		// PORT (optional)
		$urlregex .= "(\:[0-9]{2,5})?";
		// PATH (optional)
		$urlregex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";
		// GET Query (optional)
		$urlregex .= "(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?";
		// ANCHOR (optional)
		$urlregex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
		
		$urlregex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
		return preg_match("~{$urlregex}~i", $value);
	}

	static function printr($vars, $label = '', $return = false)
	{
		$content = "<pre>\n";
	    if ($label != '') {
	        $content .= "<strong>{$label} :</strong>\n";
	    }
	    $content .= htmlspecialchars(print_r($vars, true),ENT_COMPAT | ENT_IGNORE);
	    $content .= "\n</pre>\n";

	    if ($return) { return $content; }
	    echo $content;
	}

	static function access_token($appid, $secret)
	{
		if ( empty($appid) || empty($secret) ) return null;
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
	    Unirest::verifyPeer(false);
	    $response = Unirest::get($url, array("Accept" => "application/json"));

	    $response_body = $response->raw_body;           
	    if (200 == $response->code && !empty($response_body))
	    {
	        $json = json_decode($response_body, true);
	        if ( !empty($json) && is_array($json) && !empty($json['access_token']) )
	        {
	            return $json;
	        }
	    }
	    return null;
	}

	static function jsapi_ticket($access_token)
	{
		if ( empty($access_token) ) return null;

	    $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
	    Unirest::verifyPeer(false);
	    $response = Unirest::get($url, array("Accept" => "application/json"));
	    
	    $response_body = $response->raw_body;           
	    if (200 == $response->code && !empty($response_body))
	    {
	        $json = json_decode($response_body, true);
	        if ( !empty($json) && is_array($json) && !empty($json['ticket']) )
	        {
	            return $json;
	        }
	    }

	    return null;
	}

	static function jsconfig($appid, $jsapi_ticket, $url)
	{
		if ( empty($jsapi_ticket) || empty($url) ) return null;
		
		$config = array(
            'appid' => $appid,
            'timestamp' => G_TIMESTAMP, // 生成签名的时间戳
            'noncestr' => G::fast_uuid(2) + G_TIMESTAMP,// 生成签名的随机串
            'signature' => '',// 签名
        );

		$config['signature'] = "jsapi_ticket={$ticket}&noncestr={$config['noncestr']}&timestamp={$config['timestamp']}&url={$url}";
        $config['signature'] = sha1($config['signature']);

        return $config;
	}

	static function error($errcode, $errmsg='')
	{
		$data = array(
				'errcode' => $errcode,
				'errmsg'   => $errmsg
			);
		echo $_GET['callback'].'(' . json_encode($data) . ')';
		exit;
	}

	static function success($data)
	{
		echo $_GET['callback'].'(' . json_encode($data) . ')';
		exit;
	}

	static function incr_count($fld, $id)
	{
		if ( empty($fld) || empty($id)) return;
		$sql = sprintf("update %s set `{$fld}`=`{$fld}`+1 where id=%d", G::TABLE, $id);
		Sql::write(G::$ds, Sql::MODE_WRITE_UPDATE, array($sql));
	}

	static function by_code()
	{
		if ( !empty($_GET['code']) )
		{
			$_GET['code'] = trim($_GET['code'], "+ \t\r\n\0\x0B");
			if ( !empty($_GET['code']) )
			{
				return Sql::assistant(G::$ds)->select_row(G::TABLE,array('code'=>$_GET['code']));
			}
		}
		return null;
	}

	static function read_access_token(array $result, $withcode=false, $return=false)
	{
		$data = array(
				'access_token' 	=> $result['access_token'],
				'expires_in'	=> $result['access_token_fetchtime'] + $result['access_token_expirein'] - 100,
			);
		if ( $withcode ) $data['code'] = $result['code'];

		# 验证access_token是否有效
		if ( G_TIMESTAMP > $data['expires_in'] )
		{
			if ( empty($result['secret']) ) return G::error(10102, "access_token had expired");

			# 重新获取
			$json = G::access_token($result['appid'], $result['secret']);
			if ( empty($json) )
			{
				return G::error(10101, "access_token get error");
			}
			
			# 存储到缓存中
			$save = array(
					'access_token' => $json['access_token'],
					'access_token_fetchtime' => G_TIMESTAMP,
					'access_token_expiresin' => $json['expires_in'],
				);
			Sql::assistant(G::$ds)->update(G::TABLE, $save, array('id'=> $result['id']));

			$data['access_token'] = $json['access_token'];
			$data['expires_in'] = G_TIMESTAMP + $json['expires_in'] - 100;
		}
		$data['expires_in'] = date('Y-m-d H:i:s', $data['expires_in']);

		# 更新计数器
		G::incr_count('access_token_fetchcount', $result['id']);

		if ($return) return $data;
		return G::success($data);
	}

	static function read_jsticket(array $result, $return=false)
	{
		$data = array(
				'ticket' 	=> $result['jsapi_ticket'],
				'expires_in'	=> $result['jsapi_ticket_fetchtime'] + $result['jsapi_ticket_expirein'] - 100,
			);

		# 验证access_token是否有效
		if ( G_TIMESTAMP > $data['expires_in'] )
		{
			if ( empty($result['secret']) ) return G::error(10202, "ticket had expired");
			# 获取 access_token
			$json = G::read_access_token($result, false, true);
			
			# 获取 ticket
			$json = G::jsapi_ticket($json['access_token']);
			if ( empty($json) )
			{
				return G::error(10201, "ticket get error");
			}

			# 存储到缓存中
			$save = array(
					'jsapi_ticket' => $json['ticket'],
					'jsapi_ticket_fetchtime' => G_TIMESTAMP,
					'jsapi_ticket_expirein'	=> $json['expires_in'],
				);
			Sql::assistant(G::$ds)->update(G::TABLE, $save, array('id'=> $result['id']));

			$data['ticket'] = $json['ticket'];
			$data['expires_in'] = G_TIMESTAMP + $json['expires_in'] - 100;
		}
		$data['expires_in'] = date('Y-m-d H:i:s', $data['expires_in']);

		# 更新计数器
		G::incr_count('jsapi_ticket_fetchcount', $result['id']);

		if ($return) return $data;
		return G::success($data);
	}

}

function app_init()
{
	error_reporting(E_ALL | E_STRICT);
	date_default_timezone_set('Asia/Shanghai');
	session_start();
	header("Content-Type: text/html;charset=utf-8");

	# 兼容测试环境
	if ( defined('SAE_MYSQL_HOST_M') )
	{
		define('MYSQL_HOST',	SAE_MYSQL_HOST_M);
		define('MYSQL_PORT',	SAE_MYSQL_PORT);
		define('MYSQL_DB',		SAE_MYSQL_DB);
		define('MYSQL_USER',	SAE_MYSQL_USER);
		define('MYSQL_PASS',	SAE_MYSQL_PASS);
	}
	else if ( $_SERVER["HTTP_HOST"] == 'sql.oschina.mopaas.com' )
	{
		define('MYSQL_HOST',	'10.4.26.93');
		define('MYSQL_PORT',	'3306');
		define('MYSQL_DB',		'dc3dca8b59f4c4eeab85ebecbfb54f2d9');
		define('MYSQL_USER',	'uLbMTQ3Mw0ZHV');
		define('MYSQL_PASS',	'pBYPxXFNdDxN2');
	}
	else
	{
		define('MYSQL_HOST','localhost');
		define('MYSQL_PORT','3306');
		define('MYSQL_DB','wxjssdk');
		define('MYSQL_USER','root');
		define('MYSQL_PASS','root');
	}

	$dsn = array(
			'type' => 'mysql',

			'dbpath'  => sprintf('mysql:host=%s;port=%d;dbname=%s', 
					MYSQL_HOST,
					MYSQL_PORT,
					MYSQL_DB
				),
			'login'	=> MYSQL_USER,
			'password' => MYSQL_PASS,

			'initcmd' => array(
					"SET NAMES 'utf8'",
				),

			'attr'	=> array(
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_PERSISTENT => false,
				),
		);
	G::$ds = Sql::ds($dsn);
	
	$q = 'index';
	if ( !empty($_GET['q']) )
	{
		$q = trim( $_GET['q'], "+ \t\r\n\0\x0B" );
		unset( $_GET['q'] );
		$q = preg_replace('/[^a-z0-9\.]/', '', $q);
	}
	if ( empty($q) ) $q = 'index';
	if ( strtolower($q) == 'init' ){
		echo '去死吧,2货';exit;
	}
	$action = 'app_' . $q;
	if ( !is_callable($action) ) $action = 'app_index';
	
	if ( !empty($_GET['callback']) )
	{
		$_GET['callback'] = trim( $_GET['callback'], "+ \t\r\n\0\x0B" );
		$_GET['callback'] = preg_replace('/[^a-zA-Z0-9\_]/', '', $_GET['callback']);
	}
	else
	{
		$_GET['callback'] = 'jsonp';
	}

	$action();
}

app_init();

function app_index()
{
	$output = array(
		'site' => array(
			'title' => '微信JS SDK 中转助手',
			'author'	=> array(
					'name'	=> '仓鼠',
					'photo'	=> 'assets/i.jpg',
					'blog'	=> 'http://vb2005xu.iteye.com',
					'weixin'	=> '449211678',
					'qq'	=> '449211678',
				),
			'forkit'	=> 'http://git.oschina.net/eemcmc/sql.class.php',
			'thanks'	=> array(
					'sae'	=> 'http://sae.sina.com.cn/',
					'git'	=> 'http://git.oschina.net/',
				),
			),
		);

	## 统计数据
	$output['site']['stat'] = array(
			'user_count' => Sql::assistant(G::$ds)->count(G::TABLE),
			'token_fetchcount' => (int) G::$ds->one( 'select sum(access_token_fetchcount) from ' . G::TABLE ),
			'ticket_fetchcount' => (int) G::$ds->one( 'select sum(jsapi_ticket_fetchcount) from ' . G::TABLE ),
		);

	## 表结构
	$tables = G::$ds->col('show tables');
	if ( !empty($tables) )
	{
		$output['tables'] = array();
		foreach( $tables as $table )
		{
			$output['tables']["$table"] = G::$ds->all("show full columns from {$table}");
		}
	}

	## 接口信息
	$output['interfaces'] = array();

	# 注册接口
	$output['interfaces'][] = array(
			'name'	=> '注册',

			'url'		=> '?q=reg',
			'http-method'	=> 'GET',
			'jsonp'		=> 1,
			'params'	=> array(
					'must'	=> array(
							'name'	=> '用户的名称',
							'appid'	=> '微信appid',
							'secret'	=> '微信appid对应的secret',
							'domains'	=> '供用户配置能访问其专有数据的网站域名,值以`,`分隔,格式如 a.com www.a.com',
						),
					'optional'	=> array(
							'secret_save'	=> '注册时是否将secret留存在系统中,如果不留存,可能在后面会出现 10102的错误',
							'secure_code'	=> '用户更新信息时使用,比如修改',
							'callback'	=> 'jsonp函数名字,如果不指定则使用 jsonp',
						),
				),
			'response'	=> array(
					'success'	=> array(
							'code' => '用户的唯一标识,用于请求其它接口时使用,请妥善保管',
							'secure_code' 	=> '用户帐号的安全码,可用于变更/清除应用的留存secret,请妥善保管',
							'access_token' 	=> '从微信开放平台拿到的 access_token',
							'expires_in'	=> 'Y-m-d H:i:s 格式的 access_token 过期时间',
						),
					'error'		=> array(
							'errcode' => array(
									10001 => '参数错误',
									10101 => "access_token 提取失败[1. 无效的appid和secret, 2. 服务器网络问题]",
									10102 => "access_token 过期[用户注册时未保存其secret,使得过期之后不能调用接口自动更新]"
								),
							'errmsg'   => '报错信息',
						),	
				),
		); 	

	# access_token 提取接口
	$output['interfaces'][] = array(
			'name'	=> 'access_token 提取',

			'url'		=> '?q=accesstoken',
			'http-method'	=> 'GET',
			'jsonp'		=> 1,
			'params'	=> array(
					'must'	=> array(
							'code'	=> '用户的唯一标识',
						),
					'optional'	=> array(
							'callback'	=> 'jsonp函数名字,如果不指定则使用 jsonp',
						),
				),
			'response'	=> array(
					'success'	=> array(
							'access_token' 	=> '从微信开放平台拿到的 access_token',
							'expires_in'	=> 'Y-m-d H:i:s 格式的 access_token 过期时间',
						),
					'error'		=> array(
							'errcode' => array(
									10001 => '参数错误',
									10002 => 'refer 校验失败[请求接口的来源不在注册时指定的domains中]',
									10101 => "access_token 提取失败[1. 无效的appid和secret, 2. 服务器网络问题]",
									10102 => "access_token 过期[用户注册时未保存其secret,使得过期之后不能调用接口自动更新]"
								),
							'errmsg'   => '报错信息',
						),	
				),
		);

	# jsapi_ticket 提取接口
	$output['interfaces'][] = array(
			'name'	=> 'jsapi_ticket 提取',

			'url'		=> '?q=jsticket',
			'http-method'	=> 'GET',
			'params'	=> array(
					'must'	=> array(
							'code'	=> '用户的唯一标识',
						),
					'optional'	=> array(
							'callback'	=> 'jsonp函数名字,如果不指定则使用 jsonp',
						),
				),
			'response'	=> array(
					'success'	=> array(
							'ticket' 	=> '从微信开放平台拿到的 jsapi_ticket',
							'expires_in'	=> 'Y-m-d H:i:s 格式的 jsapi_ticket 过期时间',
						),
					'error'		=> array(
							'errcode' => array(
									10001 => '参数错误',
									10002 => 'refer 校验失败[请求接口的来源不在注册时指定的domains中]',
									10101 => "access_token 提取失败[1. 无效的appid和secret, 2. 服务器网络问题]",
									10102 => "access_token 过期[用户注册时未保存其secret,使得过期之后不能调用接口自动更新]",
									10201 => "jsapi_ticket 提取失败[1. 服务器网络问题, 2. access_token无效,可能是在其它系统中被重置过]",
								),
							'errmsg'   => '报错信息',
						),	
				),
		);

	# jsconfig 提取接口
	$output['interfaces'][] = array(
			'name'	=> '微信 jsapi 页面config数据 生成',

			'url'		=> '?q=jsconfig',
			'http-method'	=> 'GET',
			'jsonp'		=> 1,
			'params'	=> array(
					'must'	=> array(
							'code'	=> '用户的唯一标识',
							'url'	=> '要调用微信jssdk的页面url,必须是一个有效的url地址,且必须是url encode之后的',
						),
					'optional'	=> array(
							'callback'	=> 'jsonp函数名字,如果不指定则使用 jsonp',
						),
				),
			'response'	=> array(
					'success'	=> array(
							'appid' => '该页面使用的微信appid',
				            'timestamp' => '生成签名的时间戳',
				            'noncestr' => '生成签名的随机串',
				            'signature' => '最终的签名',
						),
					'error'		=> array(
							'errcode' => array(
									10001 => '参数错误',
									10002 => 'refer 校验失败[请求接口的来源不在注册时指定的domains中]',
									
									10101 => "access_token 提取失败[1. 无效的appid和secret, 2. 服务器网络问题]",
									10102 => "access_token 过期[用户注册时未保存其secret,使得过期之后不能调用接口自动更新]",
									10201 => "jsapi_ticket 提取失败[1. 服务器网络问题, 2. access_token无效,可能是在其它系统中被重置过]",
									
								),
							'errmsg'   => '报错信息',
						),	
				),
		);

	G::success($output);
}

function app_reg()
{
	$keys = explode(',', 'name,appid,secret,domains');
	$form = array();
	foreach ($keys as $key)
	{
		if ( isset($_GET[$key]) ) $form[$key] = trim($_GET[$key], "+ \t\r\n\0\x0B");
	}
	foreach ($keys as $key)
	{
		if ( empty($form[$key]) ) return G::error(10001, "参数错误: {$key}");
	}

	$form['domains'] = G::normalize($form['domains'], ',');
	if ( empty( $form['domains'] ) )
	{
		return G::error(10001, "参数错误: domains");
	}
	foreach ($form['domains'] as $domain) {
		if ( !G::is_domain($domain) ) return G::error(10001, "参数错误: domains");
	}

	$secret_save = empty($_GET['secret_save']) ? 1 : 0;

	$result = Sql::assistant(G::$ds)->select_row(G::TABLE,array('appid'=>$form['appid']));
	if ( !empty($result) )
	{
		if ( $form['secret'] == $result['secret'] )
		{
			return G::read_access_token($result, true);
		}
		# 防止 非法尝试
		if ( empty($_GET['secure_code']) || $_GET['secure_code'] != $result['secure_code'] )
		{
			return G::error(10001, "参数错误: secure_code");
		}
	}
	
	$json = G::access_token($form['appid'], $form['secret']);
	if ( empty($json) )
	{
		return G::error(10101, "access_token get error");
	}

	if ( empty($result) )
	{
		# 插入新用户
		$result = array(
			'name' 		=> $form['name'],
			'appid'		=> $form['appid'],
			'secret'	=> $secret_save == 1 ? $form['secret'] : '',
			'domains'	=> ',' . implode(',', $form['domains']) . ',',
			'access_token'	=> $json['access_token'],
			'access_token_fetchtime'	=> G_TIMESTAMP,
			'access_token_expirein'	=> $json['expires_in'],
			'access_token_fetchcount'	=> 0,
			'jsapi_ticket'	=> '',
			'jsapi_ticket_fetchtime'	=> 1,
			'jsapi_ticket_expirein'	=> 1,
			'jsapi_ticket_fetchcount'	=> 0,
			'secure_code'	=> G::fast_uuid(1),
			'created'	=> G_TIMESTAMP,
		);

		$result['code'] = md5( sha1("{$form['appid']}{$form['secret']}") . G::fast_uuid(5) );

		$result['id'] = Sql::assistant(G::$ds)->insert(G::TABLE, $result, true);
	}
	else
	{
		# 进行数据更新
		$result['name'] = $form['name'];
		$result['secret'] = $secret_save == 1 ? $form['secret'] : '';
		$result['domains'] = ',' . implode(',', $form['domains']) . ',';
		$result['access_token'] = $json['access_token'];
		$result['access_token_fetchtime'] = G_TIMESTAMP;
		$result['access_token_expirein'] = $json['expires_in'];
		Sql::assistant(G::$ds)->update(G::TABLE, $result, array('id'=> $result['id']));
	}

	# 更新计数器
	if ( $result['id'] > 0 ) G::incr_count('access_token_fetchcount', $result['id']);

	$data = array(
			'code' => $result['code'],
			'secure_code' 	=> $result['secure_code'],
			'access_token' 	=> $result['access_token'],
			'expires_in'	=> date('Y-m-d H:i:s', G_TIMESTAMP + $json['expires_in'] - 100),
		);

	return G::success($data);
}

function app_jsticket()
{
	$result = G::by_code();
	if ( empty($result) ) return G::error(10001, "参数错误: code");

	# 校验 refer
	if ( empty($_SERVER['HTTP_REFERER']) ) return G::error(10002, "refer error");
	$path = parse_url($_SERVER['HTTP_REFERER']);
	if ( empty($path['host']) ) return G::error(10002, "refer error");
	
	$domains = G::normalize($result['domains'], ',');
	foreach( $domains as $domain )
	{
		if ( strtolower($domain) == strtolower($path['host']) )
		{
			return G::read_jsticket($result, false);
		}
	}

	return G::error(10002, "refer error");
}

function app_jsconfig()
{
	if ( empty($_GET['url']) || !G::is_url($_GET['url']) )
	{
		return G::error(10001, "参数错误: url");
	}

	$_GET['url'] = urldecode($_GET['url']);

	# 将 url 进行分割 '#'
	$url = G::normalize($_GET['url'], '#');
	foreach ( $url as $s )
	{
		if ( !empty($s) ) 
		{
			$url = $s;break;
		}
	}

	$result = G::by_code();
	if ( empty($result) ) return G::error(10001, "参数错误: code");

	# 校验 refer
	if ( empty($_SERVER['HTTP_REFERER']) ) return G::error(10002, "refer error");
	$path = parse_url($_SERVER['HTTP_REFERER']);
	if ( empty($path['host']) ) return G::error(10002, "refer error");
	
	$domains = G::normalize($result['domains'], ',');
	foreach( $domains as $domain )
	{
		if ( strtolower($domain) == strtolower($path['host']) )
		{
			$data = G::read_jsticket($result, true);
			$data = G::jsconfig($result['appid'], $data['ticket'], $url);

			return G::success($data);
		}
	}

	return G::error(10002, "refer error");

}

function app_accesstoken()
{
	$result = G::by_code();
	if ( empty($result) ) return G::error(10001, "参数错误: code");

	# 校验 refer
	if ( empty($_SERVER['HTTP_REFERER']) ) return G::error(10002, "refer error");
	$path = parse_url($_SERVER['HTTP_REFERER']);
	if ( empty($path['host']) ) return G::error(10002, "refer error");
	
	$domains = G::normalize($result['domains'], ',');
	foreach( $domains as $domain )
	{
		if ( strtolower($domain) == strtolower($path['host']) )
		{
			return G::read_access_token($result, false);
		}
	}

	return G::error(10002, "refer error");
}
