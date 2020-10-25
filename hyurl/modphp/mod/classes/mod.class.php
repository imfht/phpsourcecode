<?php
/**
 * ModPHP 核心类，所有模块都继承于核心类，并以此获取对数据进行基本操作的能力。
 * Mod 类提供了一系列基本的操作数据记录的方法，如 mod::add()、mod::update() 等，
 * 子类可以重新定义某些方法，也可以直接使用，例如 user::add() 是直接继承的，
 * 而 file::add() 则是由 file 类重写的。
 */
class mod{
	const TABLE = ''; //当前数据表
	const PRIMKEY = ''; //主键

	/** __callStatic() 动态添加方法 */
	final static function __callStatic($method, $args){
		$api = get_class(new static).'.'.$method;
		if(is_callable(hooks($api))){
			do_hooks($api, $args[0]); //将挂钩函数当作类方法使用
			return $args[0];
		}else{
			trigger_error('Call to undefined method '.get_class(new static).'::'.$method.'()', E_USER_ERROR);
		}
	}

	/**
	 * relateTables() 自动将主表与从表的表名合并为字符串
	 * @return string        包含多个表名的字符串，各表名之间以 , 分隔，第一个表名为主表
	 */
	final protected static function relateTables(){
		static $tables = '';
		if($tables) return $tables;
		$database = database();
		foreach(database(static::TABLE) as $key){
			if(!$database) break;
			elseif($key == static::PRIMKEY) continue;
			foreach($database as $k => $v){
				if(isset($v[$key]) && stripos($v[$key], 'PRIMARY KEY')){
					$tables .= ','.$k;
					unset($database[$k]);
				}
			}
		}
		return $tables = static::TABLE.$tables;
	}

	/**
	 * tableRelated() 自动将从表和主表的表名合并为字符串
	 * @return string        包含多个表名的字符串，各表名之间以 , 分隔，第一个表名为从表
	 */
	final protected static function tableRelated(){
		static $tables = '';
		if($tables) return $tables;
		foreach(database(static::TABLE, true) as $key => $value){
			if(stripos($value, 'PRIMARY KEY')){
				foreach(database() as $k => $v){
					if(isset($v[$key]) && $k != static::TABLE){
						$tables .= ','.$k;
					}
				}
				break;
			}
		}
		return $tables = static::TABLE.$tables;
	}

	/** 
	 * relateWhere() 通过多个表名自动填充 where 条件，使用每个表的主键作为连接条件，在同时获取多表记录时使用
	 * @param  string $tables 多个表名组成的字符串，第一个表名作为主表
	 * @param  array  $where  包含 where 条件的数组
	 * @return array          返回填充后的数组
	 */
	final protected static function relateWhere($tables, $where = array()){
		$tables = explode(',', str_replace(' ', '', $tables));
		$count = count($tables);
		for ($i=1; $i < $count; $i++) { 
			foreach(database($tables[$i], true) as $k => $v){
                if(stripos($v, 'PRIMARY KEY')){
                    $where[$tables[$i].'.'.$k] = '{'.$tables[0].'.'.$k.'}';
                    break;
                }
            }
		}
		return $where;
	}

	/**
	 * userFilter() 过滤用户信息
	 * @param  array  &$data 包含用户信息的数据
	 * @return null
	 */
	final protected static function userFilter(&$data = array()){
		unset($data['user_password']); //过滤密码
		if(!empty($data['user_protect'])){
			if(!_user('me_id')){ //如果没有保存用户 Id 到内存，则将其获取并保存，此处会引发一次递归执行
				_user('me_id', me_id());
				_user('me_level', me_level());
			}
			$admin = config('user.level.admin'); //管理员级别
			foreach($data['user_protect'] as $key){ //过滤自定义保护字段，管理员除外
				if(_user('me_id') != $data['user_id'] && _user('me_level') != $admin)
					unset($data[$key]);
			}
		}
	}

	/**
	 * permissionChecker() 检查操作权限（不含获取）和主键
	 * @param  array  &$arg 请求参数
	 * @param  string $act  操作名
	 * @return array        如果发生错误，则返回错误(数组)，否则无返回值
	 */
	final protected static function permissionChecker(&$arg = array(), $act = 'add'){
		if(error()) return error(); //如果此前有错误，则不再继续执行
		$tb = static::TABLE;
		$primkey = static::PRIMKEY;
		$hasOwner = in_array('user_id', database($tb)) && $tb != 'user'; //判断模块记录是否存在所有者
		$langDinied = lang('mod.permissionDenied');
		if($hasOwner){ //存在所有者则检查登录状态
			if(!is_logined()) return error(lang('user.notLoggedIn'));
		}
		if($act != 'add'){
			if(empty($arg[$primkey])) return error(lang('mod.missingArguments'));
			$result = static::get(array($primkey=>$arg[$primkey])); //尝试获取请求的记录
			if($result['success']){ //检查是否有所有者、编辑或管理员权限
				if($hasOwner && me_id() != $result['data']['user_id'] && !is_editor() && !is_admin())
					return error($langDinied);
			}else{
				return error(); //记录不存在
			}
		}
	}

	/**
	 * dataSerializer() 序列化数据
	 * @param  array &$arg 请求参数
	 * @param  bool  $act  操作名
	 * @return null
	 */
	final protected static function dataSerializer(&$arg = array(), $act = ''){
		$keys = array();
		if($act == 'get'){ //获取操作时需要反序列化所有关联表的字段
			$tables = explode(',', static::relateTables());
			foreach($tables as $table){
				if($_keys = config($table.'.keys.serialize')){
					$keys = array_merge($keys, explode('|', str_replace(' ', '', $_keys))); //需要反序列化的字段
				}
			}
		}else{ //添加/更新操作时仅序列化本表的字段
			$keys = config(static::TABLE.'.keys.serialize'); //需要序列化的字段
		}
		if($keys){
			foreach($keys as $key){
				if(isset($arg[$key])){
					if(config('mod.jsonSerialize')){ //使用 JSON 进行(反)序列化
						$arg[$key] = ($act != 'get') ? json_encode($arg[$key]) : @json_decode($arg[$key], true);
					}else{ //使用 PHP 内置(反)序列化方式
						$arg[$key] = ($act != 'get') ? serialize($arg[$key]) : @unserialize($arg[$key]);
					}
				}
			}
		}
	}

	/**
	 * linkHandler() 处理自定义永久链接
	 * @param  array  &$arg  请求参数
	 * @param  bool   $act   操作名
	 * @return mixed
	 */
	final protected static function linkHandler(&$arg = array(), $act = ''){
		$link = static::TABLE.'_link';
		$primkey = static::PRIMKEY;
		if(!empty($arg[$link])){
			$hasRoot = path_starts_with($arg[$link], site_url()); //判断链接是否为绝对 URL 地址
			$index = config('mod.pathinfoMode') ? 'index.php/' : ''; //pathinfo 模式
			if($act != 'get'){
				if($hasRoot) //获取相对链接
					$arg[$link] = substr($arg[$link], strlen(site_url()));
				if(path_starts_with($arg[$link], 'index.php/'))
					$arg[$link] = substr($arg[$link], 10); //去掉链接的 index.php/ 前缀
				if(file_exists($arg[$link]))
					return error(lang('mod.linkUnavailable')); //链接不能与文件名冲突
				$modules = array();
				foreach(database() as $k => $v){
					if(isset($v[$k.'_link'])) $modules[] = $k; //获取使用链接功能的模块
				}
				foreach($modules as $module){
					$get_module = 'get_'.$module;
					$the_module = 'the_'.$module;
					if($get_module(array($module.'_link'=>$arg[$link]))){ //判断链接是否已被其他记录使用
						if(static::TABLE != $module || (!empty($arg[$primkey]) && $arg[$primkey] != $the_module($primkey)))
							return error(lang('mod.linkUnavailable'));
					}
				}
			}elseif(!$hasRoot){
				$arg[$link] = site_url($index).$arg[$link]; //在获取记录时将相对 URL 地址转为绝对地址
			}
		}
	}

	/**
	 * handler() 过滤和修饰数据
	 * @param  array  &$arg 请求参数
	 * @param  string $act  操作过程
	 * @return null
	 */
	final protected static function handler(&$arg = array(), $act = 'add'){
		if(error()) return error();
		$tb = static::TABLE;
		if($act != 'get'){
			if(!$arg) return error(lang('mod.missingArguments'));
			static::permissionChecker($arg, $act); //先检查权限
			if(error()) return error();
			if($act == 'add'){ //添加操作
				if(in_array('user_id', database($tb)) && $tb != 'user')
					$arg['user_id'] = me_id(); //填充用户 ID
				if(in_array($tb.'_time', database($tb)))
					$arg[$tb.'_time'] = time(); //填充时间戳
				if($keys = config($tb.'.keys.require')){
					$keys = explode('|', str_replace(' ', '', $keys));
					foreach($keys as $key){ //添加数据时检查必需字段
						if(empty($arg[$key])) return error(lang('mod.missingArguments'));
					}
				}
			}elseif($act == 'update'){ //更新操作
				if($keys = config($tb.'.keys.filter')){
					$keys = explode('|', str_replace(' ', '', $keys));
					foreach($keys as $key){ //更新数据时过滤字段
						if(!is_admin() || $key[0] == '*') //不对管理员进行过滤，除非字段前加 * 标记
							unset($arg[ltrim($key, '*')]);
					}
				}
			}
			if($act != 'delete'){
				$tags = config('mod.escapeTags'); //需要转义的 HTML 标签
				foreach($arg as $k => $v){
					if(!in_array($k, database($tb)))
						unset($arg[$k]); //过滤无效字段
					elseif($v && is_string($v))
						$arg[$k] = escape_tags($v, $tags); //转义 HTML 脚本标签
				}
				static::dataSerializer($arg);
				static::linkHandler($arg);
				unset($arg[static::PRIMKEY]); //过滤主键
			}
		}else{
			foreach ($arg as $k => $v) {
				if(is_string($v) && is_numeric($v) && (int)$v < 2147483647)
					$arg[$k] = (int)$v; //为确保平台兼容性，数字最大值不应超过 2147483646
			}
			static::dataSerializer($arg, 'get');
			static::userFilter($arg);
			static::linkHandler($arg, 'get');
		}
		if(error()) return error();
	}

	/**
	 * configFilter() 过滤无效配置
	 * @param  array  &$arg 请求参数
	 * @return null
	 */
	final private static function configFilter(&$arg = array()){
		$config = config2list(config(), '', '.', true);
		foreach($arg as $k => $v){
			if(!in_array($k, $config)) unset($arg[$k]);
		}
	}

	/**
	 * install() 安装系统
	 * @static
	 * @param  array  $arg 请求参数
	 * @return array       请求结果
	 */
	final static function install(array $arg){
		if(static::TABLE) return error(lang('mod.methodDenied', __method__));
		if(config('mod.installed')) return error(lang('mod.installed'));
		if(is_writable(__ROOT__.'user/config')){
			do_hooks('mod.install', $arg); //执行挂钩函数
			if(error()) return error();
			$username = $arg['user_name'];
			$password = $arg['user_password'];
			static::configFilter($arg);
			config($arg); //更新配置
			include __CORE__.'common/update.php'; //调用执行数据库更新程序
			if(error()) return error();
			/** 切换至用户模块以添加管理员用户 */
			$user = array(
				'user_name'     => $username,
				'user_password' => md5_crypt($password),
				'user_level'    => config('user.level.admin'),
				);
			database::open(0)->insert('user', $user); //添加超级管理员用户
			return success(lang('mod.installed'));
		}else{
			return error(lang('mod.directoryUnwritable', $path));
		}
	}

	/**
	 * uninstall() 卸载系统
	 * @static
	 * @param  array  $arg [可选]请求参数
	 * @return array       请求结果
	 */
	final static function uninstall(array $arg = array()){
		if(static::TABLE) return error(lang('mod.methodDenied', __method__));
		if(!config('mod.installed')) return error(lang('mod.notInstalled'));
		if(is_writable(__ROOT__.'user/config')){
			do_hooks('mod.uninstall', $arg);
			if(error()) return error();
			if(!empty($arg['drop_database'])){ //清空数据库记录
				database::open(0);
				$key = 'Tables_in_'.config('mod.database.name');
				$sqlite = database::set('type') == 'sqlite'; //判断是否为 SQLite 数据库
				$sql = $sqlite ? "select name from sqlite_master where type = 'table'" : "SHOW TABLES";
				$result = database::query($sql); //查询数据库中的数据表
				while ($result && $table = $result->fetchObject()) {
					$name = $sqlite ? $table->name : $table->$key; //数据表名
					if(strpos($name, config('mod.database.prefix')) === 0){
						database::query("DROP TABLE `{$name}`"); //删除表
					}
				}
			}
			config('mod.installed', false); //更新配置，将其设置为未安装状态
			export(config(), __ROOT__.'user/config/config.php'); //导出配置
			return success(lang('mod.uninstalled'));
		}else{
			return error(lang('mod.directoryUnwritable', $path));
		}
	}

	/** 
	 * config() 更新配置
	 * @static
	 * @param  array  $arg 请求参数
	 * @return array       请求结果
	 */
	final static function config(array $arg){
		if(static::TABLE) return error(lang('mod.methodDenied', __method__));
		if(!config('mod.installed')) return error(lang('mod.notInstalled')); 
		if(is_writable(__ROOT__.'user/config')){
			do_hooks('mod.config', $arg);
			if(error()) return error();
			static::configFilter($arg); //过滤无效配置
			if(!$arg) return error(lang('mod.missingArguments'));
			config($arg); //应用配置
			export(config(), __ROOT__.'user/config/config.php'); //写出配置文件
			$config = array();
			foreach($arg as $k => $v){
				$k = "['".str_replace('.', "']['", $k)."']";
				eval('$config'.$k.' = null; $_config = &$config'.$k.';'); //通过引用的方式更新配置
				$_config = $v;
			}
			return success($config);
		}else{
			return error(lang('mod.directoryUnwritable', $path));
		}
	}

	/**
	 * add() 通用的添加记录方式
	 * @static
	 * @param  array  $arg 请求参数，可以包含所有的数据表字段
	 * @return array       刚添加的记录
	 */
	static function add(array $arg){
		$tb = static::TABLE;
		if(!$tb) return error(lang('mod.methodDenied', __method__));
		do_hooks($tb.'.add', $arg); //执行添加前挂钩函数
		static::handler($arg, 'add');
		if(error()) return error();
		if($arg && database::open(0)->insert($tb, $arg, $insertId)){ //插入数据库记录
			$result = static::get(array(static::PRIMKEY=>$insertId)); //获取刚插入的记录
			do_hooks($tb.'.add.complete', $result['data']); //执行添加完成挂钩函数
			return error() ?: $result; //将新纪录返回
		}else{
			return error(lang('mod.addFailed', lang($tb.'.label')));
		}
	}

	/**
	 * update() 通用的更新记录方式，也可以用来更新系统
	 * @static
	 * @param  array  $arg [可选]请求参数，可以包含所有的数据表字段(必须提供主键字段)
	 * @return array       更新后的记录或者更新结果(更新系统时)
	 */
	static function update(array $arg = array()){
		$tb = static::TABLE;
		$primkey = static::PRIMKEY;
		if(!$tb){ //更新系统
			$ok = false;
			do_hooks('mod.update', $arg); //执行系统更新前挂钩函数
			if(error()) return error();
			if(!empty($arg['upgrade'])){ //升级 ModPHP 版本
				if(empty($arg['src']) || empty($arg['md5']))
					return error(lang('mod.missingArguments'));
				$file = __ROOT__.(MOD_ZIP ?: 'modphp.zip');
				$tmp = null;
				if(ini_get('allow_url_fopen')){
					$tmp = @file_get_contents($arg['src']); //获取更新包
				}elseif(function_exists('curl')){
					$tmp = curl(array('url'=>$arg['src'], 'followLocation'=>2)); //使用 CURL 获取更新包
				}
				if($tmp){
					$len = @file_put_contents($file, $tmp); //写出更新包
					if($len && md5_file($file) == $arg['md5']){ //通过 MD5 验证安装包完整性
						$ok = MOD_ZIP ? true : (function_exists('zip_extract') && zip_extract($file, __ROOT__)); //解压安装包
						export(load_config_file('config.php'), __ROOT__.'user/config/config.php'); //更新配置
					}
				}
			}else{ //更新数据库
				include __CORE__.'common/update.php'; //调用执行数据库更新程序
				if(error()) return error();
				$ok = true;
			}
			if($ok) do_hooks('mod.update.complete', $arg); //执行系统更新后挂钩函数
			return $ok ? success(lang('mod.updated')) : error(lang('mod.updateFailed', ''));
		}else{ //更新模块数据库记录
			do_hooks($tb.'.update', $arg); //执行模块更新前挂钩函数
			$id = !empty($arg[$primkey]) ? $arg[$primkey] : 0;
			static::handler($arg, 'update');
			if(error()) return error();
			database::open(0);
			if($arg && database::update($tb, $arg, "`$primkey` = ".database::quote($id))){ //更新数据库记录
				$result = static::get(array($primkey=>$id)); //获取更新后的记录
				do_hooks($tb.'.update.complete', $result['data']); //执行模块更新后挂钩函数
				return error() ?: $result;
			}else{
				return error(lang('mod.updateFailed', lang($tb.'.label')));
			}
		}
	}

	/**
	 * delete() 通用的删除记录方式
	 * @static
	 * @param  array  $arg  请求参数，可以包含所有的数据表字段(必须提供主键字段)
	 * @return array        操作结果
	 */
	static function delete(array $arg){
		$tb = static::TABLE;
		$primkey = static::PRIMKEY;
		if(!$tb) return error(lang('mod.methodDenied', __method__));
		do_hooks($tb.'.delete', $arg); //执行模块删除记录前挂钩函数
		$id = !empty($arg[$primkey]) ? $arg[$primkey] : 0;
		static::handler($arg, 'delete');
		if(error()) return error();
		database::open(0);
		$tables = explode(',', static::tableRelated()); //获取从表
		foreach ($tables as $table) {
			if(!database::delete($table, "`$primkey` = ".database::quote($id)) && $i == 0)
				return error(lang('mod.deleteFailed', lang($tb.'.label')));
		}
		do_hooks($tb.'.delete.complete', $arg); //执行模块删除记录完成后挂钩函数
		return error() ?: success(lang('mod.deleted', lang($tb.'.label')));
	}

	/**
	 * get() 通用的获取单条记录方式
	 * @static
	 * @param  array  $arg 请求参数，可以包含除了外键外的所有数据表字段，但应提供具有唯一性的字段
	 * @return array       请求的记录或错误
	 */
	static function get(array $arg){
		$tb = static::TABLE;
		if(!$tb) return error(lang('mod.methodDenied', __method__));
		$extables = array_slice(explode(',', static::relateTables()), 1); //外表
		foreach($arg as $k => $v){
			$table = strstr($k, '_', true);
			if(!in_array($k, database($tb)) || ($table && $extables && in_array($table, $extables)))
				unset($arg[$k]); //删除无效参数
		}
		if(!$arg) return error(lang('mod.missingArguments'));
		$result = static::getMulti(array_merge($arg, array('limit'=>1))); //通过获取多记录的方法获取一条记录
		if($result['success']){
			return success($result['data'][0]); //返回获取的记录
		}else{ //返回错误
			$noData = lang('mod.noData', lang($tb.'.label'));
			$lang = $result['data'] != $noData ? $result['data'] : lang('mod.notExists', lang($tb.'.label')); //错误消息
			return error($lang);
		}
	}

	/**
	 * getMulti() 通用的获取多条记录方式
	 * @static
	 * @param  array  $arg  [可选]请求参数，可以包含所有的数据表字段，以及下面这些额外的参数：
	 *                      [orderby] => 按指定的字段排序，默认主键
	 *                      [sequence] => 排序方式, asc 升序(默认)，desc 降序，rand 随机
	 *                      [limit] => 单页获取上限，默认 10
	 *                      [page] => 获取页码，默认 1
	 * @return array        符合条件的记录或错误
	 */
	static function getMulti(array $arg = array()){
		$tb = static::TABLE;
		if(!$tb) return error(lang('mod.methodDenied', __method__));
		$default = array(
			'orderby'=>static::PRIMKEY,
			'sequence'=>'asc',
			'limit'=>10,
			'page'=>1
			);
		$arg = array_merge($default, $arg);
		do_hooks($tb.'.get.before', $arg); //执行记录获取前挂钩函数
		if(error()) return error();
		//确保参数合法
		if(!in_array($arg['orderby'], database($tb)))
			$arg['orderby'] = static::PRIMKEY;
		if(!in_array($arg['sequence'], array('asc', 'desc', 'rand')))
			$arg['sequence'] = 'asc';
		$arg['limit'] = (int)$arg['limit'];
		$arg['page'] = (int)$arg['page'];
		$sqlite = database::open(0)->set('type') == 'sqlite'; //是否为 SQLite 数据库
		if(strtolower($arg['sequence']) == 'rand')
			$orderby = $sqlite ? 'random()' : 'rand()';
		else
			$orderby = $arg['orderby'].' '.$arg['sequence'];
		$where = array();
		foreach($arg as $k => $v){
			if(in_array($k, database($tb)) && $v !== null){
				$where[$tb.'.'.$k] = $extra[$k] = trim($v, '{}'); //组合 where 查询条件
			}
		}
		$_where = $where;
		$_limit = $arg['limit'];
		$tables = static::relateTables(); //获取从表
		$where = static::relateWhere($tables, $where); //组合从表的查询条件
		$limit = $arg['limit'] ? $arg['limit']*($arg['page']-1).",".$arg['limit'] : 0; //limit 条件
		return static::fetchMulti($tables, $where, $limit, $orderby, array(), $tb, $arg, $_where, $_limit);
	}

	/**
	 * search() 搜索记录，使用模糊查询，需要配置模块的搜索字段
	 * @static
	 * @param  array  $arg  请求参数，可以包含所有的数据表字段，以及下面这些额外的参数：
	 *                      [keyword] => 查询的关键字
	 *                      [orderby] => 按指定的字段排序，默认主键
	 *                      [sequence] => 排序方式, asc 升序(默认)，desc 降序，rand 随机
	 *                      [limit] => 单页获取上限，默认 10
	 *                      [page] => 获取页码，默认 1
	 * @return array        请求结果或错误
	 */
	static function search(array $arg){
		$tb = static::TABLE;
		if(!$tb) return error(lang('mod.methodDenied', __method__));
		$default = array(
			'keyword'=>'',
			'orderby'=>static::PRIMKEY,
			'sequence'=>'asc',
			'limit'=>10,
			'page'=>1
			);
		$arg = array_merge($default, $arg);
		if(!config($tb.'.keys.search'))
			return error(lang('mod.noSearchKeys', lang($tb.'.label')));
		if(!$arg['keyword'])
			return error(lang('mod.missingArguments'));
		$extra['keyword'] = $arg['keyword'];
		do_hooks($tb.'.get.before', $arg); //执行记录获取前挂钩函数
		if(error()) return error();
		//确保参数合法
		if(!in_array($arg['orderby'], database($tb)))
			$arg['orderby'] = static::PRIMKEY;
		if(!in_array($arg['sequence'], array('asc', 'desc', 'rand')))
			$arg['sequence'] = 'asc';
		$arg['limit'] = (int)$arg['limit'];
		$arg['page'] = (int)$arg['page'];
		$sqlite = database::open(0)->set('type') == 'sqlite'; //是否为 SQLite 数据库
		if(strtolower($arg['sequence']) == 'rand')
			$orderby = $sqlite ? 'random()' : 'rand()';
		else
			$orderby = $arg['orderby'].' ' . $arg['sequence'];
		$where = array();
		foreach($arg as $k => $v){
			if(in_array($k, database($tb)) && $v !== null){
				$where[$tb.'.'.$k] = $extra[$k] = trim($v, '{}'); //设置 where 条件
			}
		}
		$keyword = $arg['keyword'];
		if(is_string($keyword)) $keyword = array($keyword); //始终使用多关键字查询
		$a = $b = array();
		$keys = explode('|', str_replace(' ', '', config($tb.'.keys.search')));
		$count = count($keys);
		foreach($keyword as $v){
			$v = str_replace('%', '[%]', $v); //转义 %
			for($i=0; $i < $count; $i++){ 
				$a[$i][] = "`{$keys[$i]}` LIKE ".database::quote("%{$v}%"); //组合 like 条件
			}
		}
		foreach ($a as $_a) {
			$b[] = '('.implode(' AND ', $_a).')'; //组合 AND 语句
		}
		$_where = '('.implode(' OR ', $b).')'; //组合 OR 语句
		$_where = $where = $where ? $_where.' AND '.database::parseWhere($where) : $_where; //解析并组合 where 条件
		$_limit = $arg['limit'];
		$tables = static::relateTables();
		$__where = static::relateWhere($tables);
		if($__where) $where .= ' AND '.database::parseWhere($__where);
		$limit = $arg['limit'] ? $arg['limit']*($arg['page']-1).",".$arg['limit'] : 0; //limit 条件
		return static::fetchMulti($tables, $where, $limit, $orderby, $extra, $tb, $arg, $_where, $_limit);
	}
	
	/** fetchMulti() 获取多记录 */
	final protected static function fetchMulti($tables, $where, $limit, $orderby, $extra, $tb, $arg, $_where, $_limit){
		$result = database::select($tables, '*', $where, $limit, $orderby); //获取符合条件的记录
		$data = array();
		while($result && $single = $result->fetch()){
			static::handler($single, 'get');
			do_hooks($tb.'.get', $single); //执行记录获取时挂钩函数
			if(error()) return error(); //发生错误，不再执行
			$data[] = $single;
		}
		if(empty($data)) return error(lang('mod.noData', lang($tb.'.label')));
		$extra['orderby'] = $arg['orderby'];
		$extra['sequence'] = $arg['sequence'];
		$extra['limit'] = $arg['limit'];
		$extra['page'] = $arg['page'];
		$extra['total'] = database::select($tb, 'COUNT(*) AS total', $_where)
								  ->fetchObject()->total; //符合条件的记录总数
		$extra['pages'] = $_limit ? ceil($extra['total'] / $_limit) : 1; //符合条件的总页码数
		return success($data, $extra);
	}

	/**
	 * getPrev() 通用的获取上一条记录方式
	 * @static
	 * @param  array  $arg  请求参数，可以包含所有的数据表字段(必须提供主键字段)，以及下面这些额外的参数：
	 *                      [orderby] => 按指定的字段排序，默认主键
	 *                      [sequence] => 排序方式, asc 升序(默认)，desc 降序
	 * @return array        请求的记录或错误
	 */
	static function getPrev(array $arg, $sign = '>='){
		$tb = static::TABLE;
		$primkey = static::PRIMKEY;
		if(!$tb) return error(lang('mod.methodDenied', $sign == '>=' ? __method__ : 'getNext'));
		$default = array(
			'orderby'=>static::PRIMKEY,
			'sequence'=>'asc',
			);
		$arg = array_merge($default, $arg);
		do_hooks($tb.'.get.before', $arg); //执行记录获取前挂钩函数
		if(empty($arg[$primkey])) //获取相邻的记录必须提供主键字段
			return error(lang('mod.missingArguments'));
		$id = (int)$arg[$primkey];
		unset($arg[$primkey]);
		//确保传入参数合法
		if(!in_array($arg['orderby'], database($tb)))
			$arg['orderby'] = $primkey;
		if(!in_array($arg['sequence'], array('asc', 'desc')))
			$arg['sequence'] = 'asc';
		if($sign == '>=') //查询上一记录
			$sequence = $arg['sequence'] == 'asc' ? 'desc' : 'asc';
		else //查询下一记录
			$sequence = $arg['sequence'];
		$orderby = "`$primkey` $sign $id, {$arg['orderby']} $sequence";
		foreach($arg as $k => $v){
			if(in_array($k, database($tb)) && $v !== null){
				$where[$tb.'.'.$k] = $extra[$k] = trim($v, '{}'); //组合 where 条件
			}
		}
		if(!isset($where)) $where = array();
		$tables = static::relateTables();
		$where = static::relateWhere($tables, $where) ?: 1;
		$result = database::open(0)->select($tables, '*', $where, 1, $orderby)->fetch();
		if(!$result || eval('return '.$result[$primkey]." $sign {$id};")) //获取记录的 ID >= 传入的 ID 都表示没有获取到
			return error(lang('mod.noData', lang($tb.'.label')));
		static::handler($result, 'get');
		do_hooks($tb.'.get', $result); //执行记录获取时挂钩函数
		return error() ?: success($result);
	}

	/**
	 * getNext() 通用的获取下一条记录方式
	 * @static
	 * @param  array  $arg  请求参数，可以包含所有的数据表字段(必须提供主键字段)，以及下面这些额外的参数：
	 *                      [orderby] => 按指定的字段排序，默认主键
	 *                      [sequence] => 排序方式, asc 升序(默认)，desc 降序
	 * @return array        请求的记录或错误
	 */
	static function getNext(array $arg){
		return static::getPrev($arg, '<='); //调用获取上一记录的方法，只将排序反转
	}

	/**
	 * trash() 操作无效的数据库记录
	 * @static
	 * @param  string $action get 或者 delete 操作
	 * @return array          获取的记录或删除操作成功提示
	 */
	final protected static function trash($action = 'get'){
		$tb = static::TABLE;
		$deny = lang('mod.methodDenied', $action == 'get' ? 'getTrash' : 'cleanTrash');
		if(!$tb) return error($deny);
		$result = database::open(0)->select($tb, '*', 1, 0); //获取模块的所有记录
		$count = 0;
		$data = array();
		$invalidId = array();
		$tables = explode(',', static::relateTables());
		while($result && $single = $result->fetch()){
			foreach ($tables as $table) {
				$keys = database($table); //从表结构
				$primkey = get_primkey_by_table($table); //从表主键
				$where = array();
				foreach($single as $key => $value){
					if(in_array($key, $keys) && $key == $primkey && $value)
						$where[$table.'.'.$key] = trim($value, '{}'); //组合每一个从表的 where 条件
				}
				$valid = database::select($table, 'count(*)', $where)
								 ->fetchColumn(); //判断记录的外键值是否有效
				if(!$valid && !in_array($single[static::PRIMKEY], $invalidId)){
					//外键值无效，则该记录无效
					$invalidId[] = $single[static::PRIMKEY];
					static::handler($single, 'get');
					do_hooks($tb.'.get', $single); //执行获取记录时挂钩函数
					if($action == 'get'){ //获取记录
						if(error()) return error();
						$data[] = $single;
					}elseif($action == 'delete'){ //删除记录
						do_hooks($tb.'.cleanTrash', $single); //执行删除垃圾时挂钩函数
						if(error()) return error();
						database::delete($tb, $primkey.' = '.$single[$primkey]); //删除该记录
						$count++;
					}
				}
			}
		}
		if($action == 'get'){
			return $data ? success($data) : error(lang('mod.noData', lang($tb.'.label')));
		}elseif($action == 'delete'){
			$func = $count ? 'success' : 'error';
			return $func(lang('mod.countDelete', $count));
		}
	}

	/** getTrash() 获取无效数据库记录 */
	final static function getTrash(){
		return static::trash('get');
	}

	/** cleanTrash() 清除无效数据库记录 */
	final static function cleanTrash(){
		return static::trash('delete');
	}
}