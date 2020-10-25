<?php
############
# global constant configurations

ini_set("memory_limit","512M"); # memory limit avoding crush

if(true){
	
	$conf = array();

	$conf['siteid'] = 'default'; # super site id or template them code or id, e.g. 'site-18'
	$tblpre = "TABLE_PRE";
	$conf['tblpre'] 	= $tblpre;
	$conf['appname'] 	= '-GWA2';
	$conf['appchnname'] 	= '-通用网络应用架构';
	$conf['appdir']		= $appdir;

	$conf['rtvdir'] 	= $rtvdir;
	$conf['agentname'] 	= 'AGENT_NAME';
	$conf['agentalias']	= 'AGENT_ALIAS';
	$conf['smarty']		= $appdir.'/mod/Smarty';

	$conf['uploaddir']	= 'upld';
	$conf['septag']		= '_J_A_Z_';

	$conf['signkey']	= '-rw-r--r-- 1 bangco users 13 Jul 16 16:28 w.txt';
	$conf['adminemail'] = 'info@ufqi.com';

	# display style
	$conf['display_style_index']		= 0;
	$conf['display_style_smttpl']		= 1;

	# db info
	$conf['dbhost'] 	= 'DB_HOST'; # use 127.0.0.1 instead of localhost
	$conf['dbport'] 	= 'DB_PORT';
	$conf['dbuser'] 	= 'DB_USER';
	$conf['dbpassword'] 	= 'DB_PASSWORD';
	$conf['dbname'] 	= 'DB_NAME';
	$conf['dbdriver']	= 'MYSQL'; # 'MYSQL', 'MYSQLIX', 'PDOX', 'SQLSERVER', 'ORACLE' in support, UPCASE only
	$conf['db_enable_utf8_affirm'] = false; # append utf-8 affirm after db connection established, should be false in a all-utf-8 env.
	$conf['dbsock'] = '/www/bin/mysql/mysql.sock'; # use only if dbhost=localhost since php7.0+
	$conf['dbpersistent'] = true; # assume db connection pool per process is support
	
	# cache server
	$conf['enable_cache'] = 1; # or true for 1, false for 0
	$conf['cachehost'] = '127.0.0.1'; # '/www/bin/memcached/memcached.sock'; #  ip, domain or .sock, when making change here, it needs to restart httpd to clear old memcached server in server pool. Use ip if sharing cache service with java and others.
	$conf['cacheport'] = '11211'; # empty or '0' for linux/unix socket
	$conf['cachedriver'] = 'MEMCACHEDX'; # REDISX, XCACHEX
	$conf['cacheexpire'] = 1800; # 30 * 60;
	
	# file system
	$conf['enable_file'] = 1; # true for 1, false for 0 to init at entry stage
	$conf['filedriver'] = 'FileSystem'; # files operations, since 2016-11-05
	$conf['enable_filehandle_share'] = 1; # 17:31 10 November 2016
	
	# misc
	$conf['is_debug'] = 1;
	$conf['html_resp'] = '<!DOCTYPE html><html><head><title>RESP_TITLE</title></head><body>RESP_BODY</body></html>';
	$conf['entry_tag'] = 'i'; # application name or entry name for the application, leave as blank to disbale RESTFul url, added by wadelau@ufqi.com on Sun Jan 24 13:42:16 CST 2016
	
	$conf['auto_install'] = 'INSTALL_AUTO';
	$conf['no_sql_check'] = 'omit_sql_security_check'; # keep original form of sql in some cases, 14:24 Friday, May 20, 2016, refer: http://php.net/manual/en/mysqli-stmt.bind-param.php, "2 asb(.d o,t )han(a t)n i h e i(d.o_t)dk ¶"
	$conf['allow_run_from_cli'] = 0; # keep 0 for most, only if run php from command line, 21:41 02 June 2016

	$conf['maindb']		= $conf['dbname'];
	$conf['maintbl']	= $tblpre.'customertbl';
	$conf['usertbl']	= $tblpre.'info_usertbl';
	$conf['welcometbl']	= $tblpre.'info_welcometbl';
	$conf['operatelogtbl']	= $tblpre.'fin_operatelogtbl';
	
	$conf['ssl_verify_ignore'] = true;
	$conf['http_enable_gzip'] = false;
	
	# set to global container
	GConf::setConf($conf);
	
}
############

global $_CONFIG;
$_CONFIG = GConf::getConf();

# configuration container

class GConf{

	private static $conf = array();

	//-
	public static function get($key){
	    if(isset(self::$conf[$key])){
	        return self::$conf[$key];
	    }
	    else{
	        return '';
	    }
	}

	//-
	public static function set($key, $value){
		self::$conf[$key] = $value;
	}

	//-
	public static function getConf(){
		return self::$conf;
	}

	//-
	public static function setConf($conf){
		foreach($conf as $k=>$v){
			self::set($k, $v);
		}
	}
	
}


?>
