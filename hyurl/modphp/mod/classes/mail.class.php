<?php
/**
 * mail 扩展是 ModPHP 提供的用于进行邮件事务管理的类。
 * 该类包含收发邮件两个部分，其中，接收邮件需要服务器开启 imap 扩展。
 * 即使你未登录发件服务器也可直接将邮件发送给收件人，mail 扩展会自行模拟一个发件服务器，
 * 但这种方式发送的邮件可能会被收件人的服务器拒收。
 */
final class mail{
	public  static $error = array(); //错误信息
	private static $imap = null; //收件服务器
	private static $smtp = null; //发件服务器
	private static $set = array( //设置选项
		'type'    => '', //服务器类型，可选值 imap, pop3, nntp, smtp
		'host'    => '', //主机地址
		'port'    => 0, //端口
		'username'=> '', //用户名
		'password'=> '', //登录密码
		'retries' => 0, //重试次数
		/** 以下选项仅针对发件服务器 */
		'auth'    => true, //需要登录
		'from'    => '', //发件地址
		'to'      => '', //收件地址
		'cc'      => '', //抄送人地址
		'bcc'     => '', //密送人地址
		'subject' => '', //邮件标题
		'body'    => '', //邮件主体
		'attachment' => '', //附件
		'timeout' => 5, //超时
		'debug'   => false, //调试模式
		'header'  => array(  //头部信息
			'MIME-Version' => '1.0', //MIME 版本
			'Content-Type' => 'text/plain; charset=UTF-8', //内容类型
			),
		/** 以下选项仅针对收件服务器 */
		'directory' => '', //默认文件夹，留空则为根目录
		'ssl'       => false, //使用 SSL
		'nocert'    => false, //不验证安全证书
		'readonly'  => false, //以只读方式打开连接
		'options'   => 0, //连接选项
		);
	private static $info = array(
		'imapSpec'   => '', //IMAP 连接说明
		'smtpAuthed' => false, //SMTP 授权状态
		'smtpMx'     => array(), //SMTP MX 记录
		'smtpDomain' => '', //SMTP 收件人域名
		'smtpError'  => false, //SMTP 遭遇错误
		);

	/** error() 设置错误信息 */
	private static function error($msg, $key = ''){
		if($key){
			self::$error[$key] = $msg;
		}else{
			self::$error[] = $msg;
		}
		self::debug($msg);
		return new self;
	}

	/** testImap() 检查 IMAP 功能是否可用 */
	private static function testImap(){
		if(!extension_loaded('imap'))
			trigger_error("Extension 'imap' is not loaded, cannot use mailbox capability.", E_USER_ERROR);
		return new self;
	}

	/** imapResult() 获取邮箱请求结果并尝试获取错误 */
	private static function imapResult($input){
		$error = imap_errors();
		if(is_array($error)) self::$error = array_merge(self::$error, $error);
		return $input;
	}

	/** imapGetHeader() 获取邮件头部信息 */
	private static function imapGetHeader($num){
		$_header = imap_headerinfo(self::$imap, $num);
		$header = array(
			'subject' => $_header->subject, //标题
			'from' => $_header->from[0]->mailbox.'@'.$_header->from[0]->host, //来信地址
			'to' => isset($_header->to) ? $_header->to[0]->mailbox.'@'.$_header->to[0]->host : '', //收信地址
			'cc' => isset($_header->cc) ? $_header->cc[0]->mailbox.'@'.$_header->cc[0]->host : '', //抄送地址
			'bcc' => isset($_header->bcc) ? $_header->bcc[0]->mailbox.'@'.$_header->bcc[0]->host : '', //密送地址
			'recent' => trim($_header->Recent) != '', //是否为最近邮件
			'unseen' => trim($_header->Unseen) != '', //是否未读
			'answered' => trim($_header->Answered) != '', //是否已回复
			'deleted' => trim($_header->Deleted) != '', //是否已删除
			'draft' => trim($_header->Draft) != '', //是否已标记
			'size' => $_header->Size, //大小
			'date' => $_header->date, //发送日期
			);
		if(preg_match('/=\?(.*)\?B\?(.*)\?=/i', $header['subject'], $match)){
			$header['subject'] = iconv($match[1], 'UTF-8', base64_decode($match[2])); //转码标题
		}
		return self::imapResult($header);
	}

	/** imapGetBody() 获取邮件主体 */
	private static function imapGetBody($num, $html = false){
		$struct = imap_fetchstructure(self::$imap, $num);
		$sec = 1;
		$encoding = $struct->encoding;
		if(isset($struct->parts)){
			if(count($struct->parts) > 1 && $html){
				foreach ($struct->parts as $i => $part) {
					if($part->subtype == 'HTML'){ //HTML 邮件
						$sec = $i + 1;
						$encoding = $part->encoding; //获取邮件编码方式
					}
				}
			}else{
				$encoding = $struct->parts[0]->encoding;
			}
		}
		$body = trim(imap_fetchbody(self::$imap, $num, $sec));
		$func = array('imap_utf7_decode', 'imap_utf8', 'imap_binary', 'base64_decode', 'imap_qprint', 'imap_utf8');
		if(isset($func[$encoding]) && is_callable($func[$encoding]))
			$body = @$func[$encoding]($body); //解码邮件
		return self::imapResult($body);
	}

	/** getBase64Addr() 获取 base64 编码的邮件地址 */
	private static function getBase64Addr($addr){
		$addrs = explode(',', $addr);
		foreach ($addrs as &$addr) {
			$addr = trim($addr);
			$addr = preg_replace_callback('/(.*)<(.*)>/Ui', function($match){
				return '"=?UTF-8?B?'.base64_encode(trim($match[1])).'?="<'.$match[2].'>';
			}, $addr);
		}
		return implode(', ', $addrs);
	}

	/** smtpSetHeader() 设置邮件头部信息 */
	private static function smtpSetHeader(){
		$set = self::$set;
		$header = 'Subject: =?UTF-8?B?'.base64_encode($set['subject'])."?=\r\n"; //设置标题
		$header .= 'From: '.self::getBase64Addr($set['from'])."\r\n"; //设置来信地址
		$header .= 'To: '.self::getBase64Addr($set['to'])."\r\n"; //设置收信地址
		if($set['cc']) $header .= 'Cc: '.self::getBase64Addr($set['cc'])."\r\n"; //抄送人
		if($set['bcc']) $header .= 'Bcc: '.self::getBase64Addr($set['bcc'])."\r\n"; //密送人
		foreach ($set['header'] as $k => $v) {
			$k = ucfirst($k);
			if(stripos($header, $k) === false){
				$header .= $k.': '.$v."\r\n"; //设置其他邮件头部信息
			}
		}
		if(!isset($set['header']['Date'])) $header .= 'Date: '.date('r')."\r\n"; //设置发信日期
		if(!isset($set['header']['Content-Transfer-Encoding']))
			$header .= "Content-Transfer-Encoding: base64\r\n"; //设置内容编码方式
		return $header;
	}

	/** smtpGetMx() 获取 MX 记录 */
	private static function smtpGetMx($recv){
		$domain = trim(substr($recv, strpos($recv, '@')+1), '<>');
		if(empty(self::$info['smtpMx'][$domain])){
			$dns = dns_get_record($domain, DNS_MX);
			self::$info['smtpDomain'] = $domain; //域名
			self::$set['type'] = 'smtp';
			self::$info['smtpMx'][$domain] = array(); //MX 记录
			foreach ($dns as $info) {
				self::$info['smtpMx'][$domain][] = $info['target']; //保存所有 MX 记录
			}
		}
		return new self;
	}

	/** smtpOpentStream() 打开发件服务器资源 */
	private static function smtpOpentStream($use = 0){
		$set = &self::$set;
		$domain = self::$info['smtpDomain'];
		$host = $set['host'] ?: self::$info['smtpMx'][$domain][$use];
		$port = $set['port'] ?: (!$set['host'] ? 25 : 0);
		if(!$set['host']) $set['auth'] = false;
		$tries = $set['retries'] + 1;
		for($i=0; $i < $tries; $i++){ //尝试连接服务器
			self::debug("Trying to connect $host...");
			if($set['ssl'] && stripos($host, 'ssl') !== 0) $host = 'ssl://'.$host; //使用 ssl
			self::$smtp = fsockopen($host, $port, $errno, $error, $set['timeout']); //打开连接
			if(!$errno) break;
			else self::error($error, $domain);
		}
		if(self::$smtp && !self::smtpResponseOk()){ //连接已打开，但未响应
			self::error('Error: SMTP stream has been opened, but gets no response from the server.', $domain);
		}elseif(!self::$smtp && !$set['host'] && $use != (count(self::$info['smtpMx']) - 1)){
			self::smtpOpentStream($use + 1); //使用另一个 MX 记录进行连接
		}
		return new self;
	}

	/** smtpCmd() 发送 SMTP 命令  */
	private static function smtpCmd($cmd, $to = ''){
		if(!self::$info['smtpError'] && self::$smtp){
			self::debug("> $cmd");
			fputs(self::$smtp, $cmd."\r\n"); //发送命令到服务器
			$msg = stripos($cmd, 'AUTH LOGIN') === 0 ? 'Password:' : ''; //验证登录密码
			if(!self::smtpResponseOk($msg, $to) && !self::$info['smtpError']){
				self::error('Error: Command "'.$cmd.'" has been sent, but gets no response from the server.', $to);
			}
		}
		return new self;
	}

	/** smtpResponseOk() 判断发件服务器是否接受请求 */
	private static function smtpResponseOk($msg = '', $to = ''){
		$response = trim(fgets(self::$smtp)); //获取服务器响应内容
		$msg = $msg ?: $response;
		if(stripos($response, 'Error') !== false || !preg_match('/^[23]/', $response)){ //正确响应的代码以 2 或 3 开头
			self::$info['smtpError'] = true;
			self::error($response, $to);
			self::debug('> QUIT');
			fputs(self::$smtp, "QUIT\r\n"); //退出
			self::debug(trim(fgets(self::$smtp)));
			return false;
		}else{
			self::debug($msg);
		}
		return true;
	}

	/** autoSetPort() 自动设置端口 */
	private static function autoSetPort(){
		$set = &self::$set;
		if(!$set['port']){
			switch (strtolower($set['type'])) {
				case 'smtp':
					$set['port'] = $set['ssl'] ? 994 : 25;
					break;
				case 'imap':
					$set['port'] = $set['ssl'] ? 993 : 143;
					break;
				case 'pop3':
					$set['port'] = $set['ssl'] ? 995 : 110;
				case 'nntp':
					$set['port'] = $set['ssl'] ? 563 : 119;
					break;
			}
		}
		return new self;
	}

	/** autoSetType() 自动设置服务器类型 */
	private static function autoSetType(){
		$set = &self::$set;
		if(!$set['type']){
			if($set['port']){
				switch ($set['port']) {
					case 994:
					case 25:
						$set['type'] = 'smtp';
						break;
					case 993:
					case 143:
						$set['type'] = 'imap';
						break;
					case 995:
					case 110:
						$set['type'] = 'pop3';
						break;
					case 563:
					case 119:
						$set['type'] = 'nntp';
						break;
				}
			}elseif($set['host']){
				$type = strstr($set['host'], '.', true);
				switch (strtolower($type)) {
					case 'smtp':
						$set['type'] = 'smtp';
						break;
					case 'imap':
					case 'imap4':
						$set['type'] = 'imap';
						break;
					case 'pop':
					case 'pop3':
						$set['type'] = 'pop3';
						break;
					case 'nntp':
						$set['type'] = 'nntp';
						break;
				}
			}
		}
		return new self;
	}

	/** autoSetFromToCcBcc() 自动设置发信和收件地址 */
	private static function autoSetFromToCcBcc(){
		$set = &self::$set;
		if($set['type'] == 'smtp'){ //设置发信人地址
			$from = trim($set['from']);
			if($from){
				if(filter_var($from, FILTER_VALIDATE_EMAIL)){
					$from .= ' <'.$from.'>';
				}elseif(!preg_match('/[\w\-\.]+@[\w\-]+[\.\w+]+/', $from)){
					$from .= ' <'.$set['username'].'>'; //发信地址使用用户名作为邮件地址
				}elseif(strpos($from, '<') === false){
					$from .= ' <'.$from.'>';
				}
			}else{
				$from = $set['username'].' <'.$set['username'].'>'; //使用用户名作为发信地址
			}
			$set['from'] = trim(str_replace(array('<<', '>>', '<>'), array('<', '>', ''), $from));
			self::autoSetReceiver()->autoSetReceiver('cc')->autoSetReceiver('bcc'); //设置收件地址
		}
		return new self;
	}

	/** autoSetReceiver() 自动设置收信人地址 */
	private static function autoSetReceiver($set = 'to'){
		$to = self::set($set);
		if($to){
			$to = explode(',', $to);
			foreach ($to as $key => $value) {
				$value = trim($value);
				if(filter_var($value, FILTER_VALIDATE_EMAIL)){
					$to[$key] = $value.' <'.$value.'>';
				}else{
					$to[$key] = $value;
				}
			}
			$to = implode(', ', $to);
		}
		self::set($set, str_replace(array('<<', '>>'), array('<', '>'), $to));
		return new self;
	}

	/**
	 * open() 打开一个邮件服务器，该方法不会直接连接，只是预备连接参数
	 * @static
	 * @param  string $name 连接描述名称
	 * @return object       当前对象
	 */
	static function open($name){
		$set = &self::$set;
		if(strpos($name, '://') === false) $name = 'tcp://'.$name;
		extract(parse_url($name));
		$set['host'] = $host;
		$scheme = strtolower($scheme);
		if($scheme != 'tcp') $set['type'] = $scheme;
		if($scheme != 'smtp') self::testImap();
		if(isset($port)) $set['port'] = $port;
		if(isset($path) && $path != '/') $set['directory'] = ltrim($path, '/'); //打开收件服务器的子文件夹
		if(!empty($query)){
			parse_str($query, $query);
			foreach ($query as $k => $v) {
				if($v === '0' || $v === 'false') $v = false;
				if(isset($set[$k])) $set[$k] = $v; //设置连接选项
				else $set['header'][$k] = $v; //设置发送邮件的头部信息
			}
		}
		return new self;
	}

	/**
	 * set() 设置和获取连接选项
	 * @static
	 * @param  string $opt  [可选]选项名
	 * @param  mixed  $val  [可选]选项值
	 * @return mixed        如果设置选项值，则返回当前对象，否则返回查询的选项值或者全部选项值
	 */
	static function set($opt = null, $val = null){
		$set = &self::$set;
		if($opt === null){
			self::autoSetType()->autoSetPort();
			return $set;
		}elseif(is_string($opt) && $val === null){
			return isset($set[$opt]) ? $set[$opt] : false;
		}else{
			if(is_string($opt)) $opt = array($opt => $val);
			foreach ($opt as $key => $value) {
				if($key == 'host' && stripos($value, 'ssl://') === 0){
					$set['ssl'] = true; //使用 ssl 协议
					$set['host'] = substr($value, 6);
				}else $set[$key] = $value;
			}
		}
		return new self;
	}
	
	/** 快速设置和获取方法 */
	static function host($host = null){ return self::set('host', $host); }
	static function type($type = null){ return self::set('type', $type); }
	static function port($port = null){ return self::set('port', $port); }
	static function from($from = null){ return self::set('from', $from); }
	static function to($to = null){ return self::set('to', $to); }
	static function subject($subject = null){ return self::set('subject', $subject); }
	
	/** header() 涉资发送邮件的头部信息 */
	static function header($name, $val = null){
		$header = self::set('header');
		if($val){
			self::set('header', array_merge($header, array($name => $val)));
			return new self;
		}else{
			return isset($header[$name]) ? $header[$name] : false;
		}
	}

	/**
	 * login() 登录邮件服务器
	 * @static
	 * @param  string $user [可选]用户名
	 * @param  string $pass [可选]密码
	 * @return object       当前对象
	 */
	static function login($user = '', $pass = '', $to = ''){
		self::autoSetType()->autoSetPort(); //自动设置服务器类型和端口
		$set = &self::$set;
		$info = &self::$info;
		if($user) $set['username'] = $user;
		if($pass) $set['password'] = $pass;
		if(!$set['host']){
			$to = $to ?: strstr($set['to'], ',') ?: $set['to'];
			self::smtpGetMx($to);
		}
		if($set['host'] || $info['smtpMx']){
			if($set['type'] == 'smtp'){ //登录发件服务器
				if(self::$smtp) fclose(self::$smtp);
				self::smtpOpentStream();
				self::smtpCmd('HELO localhost', $to ?: $set['username']); //发送握手包
				if($set['auth']){ //进行登录
					self::smtpCmd('AUTH LOGIN '.base64_encode($set['username']), $set['username'])->smtpCmd(base64_encode($set['password']), $set['username']);
					$info['smtpAuthed'] = true;
				}
			}else{ //登录收件服务器
				self::testImap();
				$flag = array();
				array_push($flag, $set['type']);
				if($set['ssl']) array_push($flag, 'ssl'); //使用 SSL
				if($set['nocert']) array_push($flag, 'novalidate-cert'); //不验证证书
				if($set['readonly']) array_push($flag, 'readonly'); //以只读方式打开
				$flag = '/'.implode('/', array_unique($flag));
				$info['imapSpec'] = '{'.$set['host'].':'.$set['port'].$flag.'}'.$set['directory'];
				self::$imap = imap_open($info['imapSpec'], $set['username'], $set['password'], $set['options'], $set['retries']); //建立连接
				return self::imapResult(new self);
			}
		}
		return new self;
	}

	/** logout() 登出 */
	static function logout(){
		if(self::$set['type'] == 'smtp'){
			fclose(self::$smtp);
			self::$smtp = null;
		}elseif(extension_loaded('imap')){
			imap_close(self::$imap);
			self::$imap = null;
		}
		return new self;
	}

	/** connect() login() 的别名 */
	static function connect($user = '', $pass = '', $to = ''){
		return self::login($user, $pass, $to);
	}

	/** close() logout() 的别名 */
	static function close(){
		return self::logout();
	}

	/**
	 * mailboxStatus() 获取邮箱状态
	 * @static
	 * @param  int $key [可选]获取指定的信息
	 * @return array    状态信息
	 */
	static function mailboxStatus($key = ''){
		$status = self::testImap()->imapResult((array)imap_status(self::$imap, self::$info['imapSpec'], SA_ALL));
		return $key ? (isset($status[$key]) ? $status[$key] : false) : $status;
	}

	/**
	 * listmailbox() 获取邮箱列表
	 * @static
	 * @param  string $dir 开始目录
	 * @return array       文件夹名称
	 */
	static function listmailbox($dir = '*'){
		self::testImap();
		$list = imap_list(self::$imap, self::$info['imapSpec'], $dir);
		if(is_array($list)){
			foreach ($list as $key => $value) {
				$list[$key] = substr($value, strpos(self::$info['imapSpec'], '}') + 1);
			}
		}
		return self::imapResult($list);
	}

	/**
	 * get() 获取邮件
	 * @static
	 * @param  int    $num  邮件编号
	 * @param  bool   $html [可选]HTML 版本优先，如果有多个版本，默认 false
	 * @return array        邮件信息, 包含 header 和 body
	 */
	static function get($num, $html = false){
		self::testImap();
		return array(
			'header'=>self::imapGetHeader($num), //邮件头信息
			'body'=>self::imapGetBody($num, $html) //邮件主体
			);
	}

	/**
	 * search() 搜索邮件
	 * @static
	 * @param  string   $str     搜索语句
	 * @param  int|bool $num     [可选]获取指定邮件内容，设置为 true 则获取所有内容
	 * @param  string   $charset [可选]编码，默认 UTF-8
	 * @return array             搜索结果
	 */
	static function search($str, $num = false, $charset = 'UTF-8'){
		self::testImap();
		$nums = imap_search(self::$imap, $str, SE_FREE, $charset);
		if($num === true){
			$mails = array();
			foreach ($nums as $num) {
				$mails[] = self::get($num);
			}
			return self::imapResult($mails);
		}elseif(is_int($num)) {
			return isset($nums[$num]) ? self::imapResult(self::get($nums[$num])) : false;
		}else return self::imapResult($nums);
	}

	/** get*() 快速获取邮件 */
	static function getAll($num = false){ return self::search('ALL', $num); }
	static function getAnswered($num = false){ return self::search('ANSWERED', $num); }
	static function getDeleted($num = false){ return self::search('DELETED', $num); }
	static function getFlagged($num = false){ return self::search('FLAGGED', $num); }
	static function getNew($num = false){ return self::search('NEW', $num); }
	static function getOld($num = false){ return self::search('OLD', $num); }
	static function getRecent($num = false){ return self::search('RECENT', $num); }
	static function getSeen($num = false){ return self::search('SEEN', $num); }
	static function getUnanswered($num = false){ return self::search('UNANSWERED', $num); }
	static function getUndeleted($num = false){ return self::search('UNDELETED', $num); }
	static function getUnflagged($num = false){ return self::search('UNFLAGGED', $num); }
	static function getUnseen($num = false){ return self::search('UNSEEN', $num); }

	/**
	 * send() 发送邮件
	 * @static
	 * @param  string  $msg [可选]消息内容
	 * @return boolean      发送结果
	 */
	static function send($msg = ''){
		$set = &self::$set;
		if($set['to']){
			if(!self::$info['smtpAuthed'] && self::$set['auth']) self::connect(); //登录邮件服务器
			self::autoSetFromToCcBcc();
			if($msg) $set['body'] = $msg;
			$body = preg_replace("/(^|(\r\n))(\.)/", "/\1.\3/", $set['body']);
			$body = self::prepareMsg($body); //预备邮件主体
			$header = self::smtpSetHeader(); //预备邮件头
			$from = trim(strstr($set['from'], '<'), '<>');
			$recv = $set['to'];
			if($set['cc'])  $recv .= ', '.$set['cc'];
			if($set['bcc']) $recv .= ', '.$set['bcc'];
			$recv = explode(', ', $recv);
			foreach ($recv as $to) { //将邮件同时发送给收件人、抄送人、密送人
				self::$info['smtpError'] = false;
				$to = trim(strstr($to, '<'), '<>');
				if(!$set['host']) self::connect('', '', $to); //直接连接收件人的邮件服务器
				self::smtpCmd("MAIL FROM: <$from>", $to)
					->smtpCmd("RCPT TO: <$to>", $to)
					->smtpCmd('DATA', $to)
					->smtpCmd($header."\r\n".$body."\r\n.", $to); //传送邮件
				if(!$set['host']) self::smtpCmd('QUIT', $to);
			}
			if($set['host']) self::smtpCmd('QUIT', $from); //登出邮件服务器
			self::$info['smtpAuthed'] = false;
		}
		return new self;
	}

	/** prepareMsg() 准备邮件 */
	private static function prepareMsg($text){
		$attacs = &self::$set['attachment'];
		$html = strip_tags($text) != $text; //判断是否为 HTML 邮件
		if(!$attacs){ //无附件的邮件
			self::header('Content-Type', 'text/'.($html ? 'html' : 'plain').'; charset=UTF-8');
			return base64_encode($text);
		}else{ //带附件的邮件
			$bdr1 = "------=_001_=----"; //附件边界符
			$bdr2 = "------=_002_=----"; //文本边界符
			$body = array();
			$body[] = $bdr1; //全文开始
			$body[] = "Content-Type: multipart/alternative; boundary=\"----=_002_=----\";\r\n";
			$body[] = $bdr2; //文本开始
			$body[] = 'Content-Type: text/'.($html ? 'html' : 'plain').'; charset=UTF-8';
			$body[] = "Content-Transfer-Encoding: base64\r\n";
			$body[] = base64_encode($text)."\r\n";
			$body[] = "------=_002_=------\r\n"; //文本结束
			if(!is_array($attacs)) $attacs = array($attacs);
			foreach ($attacs as $file) { //处理多个附件
				$filename = basename($file);
				$content = base64_encode(file_get_contents($file));
				$body[] = "------=_001_=----"; //附件开始
				$body[] = "Content-Type: application/octet-stream; charset=UTF-8";
				$body[] = "Content-Disposition: attachment; filename=\"$filename\"";
				$body[] = "Content-Transfer-Encoding: base64\r\n";
				$body[] = implode("\r\n", str_split($content, 72))."\r\n";
			}
			$body[] = "------=_001_=------\r\n"; //附件结束
			self::header('Content-Type', 'multipart/mixed; boundary="----=_001_=----"'); //修改邮件内容类型
			return implode("\r\n", $body);
		}
	}

	/** debug() 设置和显示调试信息 */
	static function debug($msg){
		if(is_bool($msg) || $msg === 1 || $msg === 0){
			self::set('debug', $msg); //设置调试状态
		}elseif($msg !== null){
			if(self::set('debug')){
				print_r($msg); //输出调试信息
				echo "\n";
			}
		}
		return new self;
	}
}