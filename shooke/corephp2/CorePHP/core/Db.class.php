<?php
namespace Core;
//模型类，加载了外部的数据库驱动类和缓存类
class Db{
    public static $readLink = NULL; // 从数据库操作对象
    public static $writeLink = NULL; // 主数据库操作对象
    public $slaveDb = FALSE;
	public $cache = NULL;	//缓存对象
	public $sql = '';	//sql语句，主要用于输出构造成的sql语句
	public  $pre = '';	//表前缀，主要用于在其他地方获取表前缀
	public $config =array(); //配置
    protected $options = array(); // 查询表达式参数	
	
    public function __construct( $config = array() ) {
		$this->config = array_merge(Config::get('DB'), $config);	//参数配置	
		$this->options['field'] = '*';	//默认查询字段
		$this->pre = $this->config['DB_PREFIX'];	//数据表前缀
		//判断是否支持主从
		$this->slaveDb = isset($this->config['DB_SLAVE']) && !empty($this->config['DB_SLAVE']);
		
    }
    //获取从数据库链接
    private function readLink() {
        if( isset( self::$readLink ) ) {
            return self::$readLink;
        } else {
            if( !$this->slaveDb ) {
                return $this->writeLink();
            } else {
                $slave_count = count($this->config['DB_SLAVE']);
                //遍历所有从机
                for($i = 0; $i < $slave_count; $i++) {
                    $db_all[] = array_merge($this->config, $this->config['DB_SLAVE'][$i]);
                }                
                //随机选择一台从机连接
                $rand =  mt_rand(0, $slave_count-1);
                array_unshift($db_all, $db_all[$rand]);
                foreach($db_all as $config) {
                    self::$readLink = self::getInstance($config);                    
                    if(self::$readLink->success()){//失败退出本次链接，进入下一个链接
                        return self::$readLink;
                    }else {//成功返回链接
                        continue;
                    }
                }
                //如果全部没有链接成功，调用主数据库
                return $this->writeLink();                
            }
        }
    }
    
    //获取主数据库链接
    private function writeLink() {
        if( isset( self::$writeLink ) ) {
            return self::$writeLink;
        } else{
            self::$writeLink = self::getInstance($this->config);
            if(self::$writeLink->success()){//成功返回链接
                return self::$writeLink;
            }else {//失败输出错误
                self::$writeLink->error();
            }
        }
    }    
	
	/**
	 * 取得数据库类实例
	 * 采用单例模式，每个配置只生成一个实例防止重复建立数据连接
	 * @static
	 * @access public
	 * @return mixed 返回数据库驱动类
	 */
	public static function getInstance($config = array()) {	    
	    static $_instance	=	array();
	    $dbkey	=	md5(json_encode($config));
	    if(!isset($_instance[$dbkey])){
	        $type = strtolower($config['DB_TYPE']);
	        $driver = strtolower($config['DB_DRIVE']);
	        $dbDriver = '\Core\Db\\'.ucfirst($type).ucfirst($driver);//定义数据驱动类名，包括命名空间
	        $_instance[$dbkey] = new $dbDriver( $config );	//实例化数据库驱动类
	    }
	    return $_instance[$dbkey];
	}
	
	/**
	 * 设置表，$ignore_prefix为true的时候忽略表前缀，false时添加表前缀，默认false
	 *
	 * @param unknown_type $table
	 * @param unknown_type $ignorePre
	 * @return unknown
	 * array('table1'=>'t1','table2'=>'t2') 变成table1 t1,table1 t2
	 */
	public function table($table, $ignorePre = false) {	
		$this->options['table'] = '';		
		if (is_array($table)) {			
			foreach ($table as $key=>$val){
				if (is_numeric($key)) {
					//array('table1','table2'); 转为pre_table1 table1,pre_table2 table2
					$this->options['table'] .= $ignorePre ? "$val $val ," : $this->config['DB_PREFIX']."$val $val ,";
				}else {
					//array('table1'=>'abbr1','table2'=>'abbr2'); 转为pre_table1 abbr1,pre_table2 abbr2
					$this->options['table'] .= $ignorePre ? "$key $val ," : $this->config['DB_PREFIX']."$key $val ,";
				}				
			}
			$this->options['table'] = substr($this->options['table'],0,-1);//去除最后的逗号 ,
		}else {
			$this->options['table'] .= $ignorePre ? $table : $this->config['DB_PREFIX'].$table;
		}
		return $this;
	}
	public function field($field='*'){
	    $tempField = array();
	    $this->options['field'] = '*';
	    if (is_array($field)) {
	        foreach ($field as $key=>$val){
	            if (is_numeric($key)) {	                
	                $tempField[] = $val;
	            }else {	                
	                $tempField[] = $key .' as '. $val;
	            }
	        }
	        $this->options['field'] = implode(',', $tempField);//去除最后的逗号 ,
	    }else {
	        $this->options['field'] = $field;
	    }
	    return $this;
	}
	
	 //回调方法，连贯操作的实现
    public function __call($method, $args) {
		$method = strtolower($method);
        if ( in_array($method, array('data','where','group','having','order','limit','cache')) ) {
            $this->options[$method] = $args[0];	//接收数据			
			return $this;	//返回对象，连贯查询
        } else{
			throw new Exception($method . '方法在Model.class.php类中没有定义');
		}
    }
	
	//执行原生sql语句，如果sql是查询语句，返回二维数组
    public function query($sql, $params = array(), $is_query = false) {
        if ( empty($sql) ) return false;
		$sql = str_replace('{pre}', $this->pre, $sql);	//表前缀替换
		$this->sql = $sql;
		//判断当前的sql是否是查询语句
		if ( $is_query || strpos(trim(strtolower($sql)), 'select') === 0 ) {
			$data = $this->_readCache();
			if ( !empty($data) ) return $data;
            $link = $this->readLink();
			$link->query($this->sql, $params);	
			$data = $link->fetchAll();			
			$this->_writeCache($data);
			return $data;				
		} else {
			return $this->writeLink()->execute($this->sql, $params); //不是查询条件，直接执行
		}
    }
	
	//统计行数
	public function count() {
		$table = $this->options['table'];	//当前表
		$field = $this->options['field'];//查询的字段
		$where = $this->_parseCondition(false);	//条件
		$this->sql = "SELECT count($field) FROM $table $where";	//这不是真正执行的sql，仅作缓存的key使用
		
		$data = $this->_readCache();
		if ( !empty($data) ) return $data;
		
		$link = $this->readLink();
		$data = $link->count($table, $where ,$field);
		$this->_writeCache($data);
		$this->sql = $link->getSql(); //从驱动层返回真正的sql语句，供调试使用
		return $data;
	}
	
	//只查询一条信息，返回一维数组	
    public function find() {
		$this->options['limit'] = 1;	//限制只查询一条数据
		$data = $this->select();
		return isset($data[0]) ? $data[0] : false;
     }
	 
	//查询多条信息，返回数组
     public function select() {
		$table = $this->options['table'];	//当前表
		$field = $this->options['field'];	//查询的字段
		$where = $this->_parseCondition(false);	//条件
		return $this->query("SELECT $field FROM $table $where", array(), true);
     }
	 //获取数据库内的所有表
	public function getTables(){
		$database = $this->config['DB_NAME'];
		$this->sql = "SHOW TABLES FROM `$database`";//这不是真正执行的sql，仅作缓存的key使用
	
		$data = $this->_readCache();
		if ( !empty($data) ) return $data;
		
		$link = $this->writeLink();
		$data = $link->getTables( $database );
		$this->_writeCache( $data );
		$this->sql = $link->getSql(); //从驱动层返回真正的sql语句，供调试使用
		return $data;		
	}
	 //获取一张表的所有字段
	 public function getFields() {
		$table = $this->options['table'];
		$this->sql = "SHOW FULL FIELDS FROM {$table}"; //这不是真正执行的sql，仅作缓存的key使用
	
		$data = $this->_readCache();
		if ( !empty($data) ) return $data;
		
		$link = $this->writeLink();
		$data = $link->getFields( $table );
		$this->_writeCache( $data );
		$this->sql = $link->getSql(); //从驱动层返回真正的sql语句，供调试使用
		return $data;
	}
	public function formatFields(){
	    $table = $this->options['table'];
	    $this->sql = "SHOW FORMAT FIELDS FROM {$table}"; //这不是真正执行的sql，仅作缓存的key使用
	    
	    $data = $this->_readCache();
	    if ( !empty($data) ) return $data;
	    
	    $link = $this->writeLink();
	    $data = $link->formatFields( $table );
	    $this->_writeCache( $data );
	    $this->sql = $link->getSql(); //从驱动层返回真正的sql语句，供调试使用
	    return $data;
	}
	 //插入数据
    public function insert( $replace = false ) {
		$table = $this->options['table'];	//当前表
		$data = $this->_parseData('add');	//要插入的数据
		$INSERT = $replace ? 'REPLACE' : 'INSERT';
        $this->sql = "$INSERT INTO $table $data" ;
        
        $link = $this->writeLink();
        $query = $link->execute($this->sql);
		if ( $link->affectedRows() ) {
			 $id = $link->lastId();
			 return empty($id) ? $link->affectedRows() : $id;
		}
        return false;
    }
	
	//替换数据
	 public function replace() {
		return $this->insert( true );
    }
	
	//修改更新
    public function update() {
		$table = $this->options['table'];	//当前表
		$data = $this->_parseData('save');	//要更新的数据
		$where = $this->_parseCondition(true);	//更新条件
		if ( empty($where) ) return false; //修改条件为空时，则返回false，避免不小心将整个表数据修改了
			
        $this->sql = "UPDATE $table SET $data $where" ;
        $link = $this->writeLink();
	    $link->execute($this->sql);
		return $link->affectedRows();
    }
	
	//删除
    public function delete($delete_table=null) {
		$table = $this->options['table'];	//当前表
		$where = $this->_parseCondition(true);	//条件
		if ( empty($where) ) return false; //删除条件为空时，则返回false，避免数据不小心被全部删除
		
		if (is_array($delete_table)) {			
			$delete_table = implode(',',$delete_table);			
		}
		
		$this->sql = "DELETE $delete_table FROM $table $where";
		$link = $this->writeLink();
        $query = $link->execute($this->sql);
		return $link->affectedRows();
    }
	
	
	
	//返回sql语句
    public function getSql() {
        return $this->sql;
    }

	//删除数据库缓存
    public function clearCache() {
		if ( $this->initCache() ) {
			return $this->cache->clear();
		}
		return false;
    }
	
	 //初始化缓存类，如果开启缓存，则加载缓存类并实例化
	public function initCache() {		
		if (is_object($this->cache)) {
			return true;
		} else if ($this->config['DB_CACHE_ON']) {
			require_once( dirname(__FILE__) . '/Cache.class.php' );
			$this->cache = new Cache($this->config, $this->config['DB_CACHE_TYPE']);
			return true;
		} else {
			return false;
		}
	}
	
	//读取缓存
	private  function _readCache() {
		isset($this->options['cache']) or $this->options['cache'] = $this->config['DB_CACHE_TIME'];
		//缓存时间为0，不读取缓存
		if ($this->options['cache'] == 0)
			return false;
		if ($this->initCache()) {
			$data = $this->cache->get($this->sql);
			if ( !empty($data) ) {
				unset($this->options['cache']);
				return $data;
			}
		}
		return false;
	}
	
	//写入缓存
	private function _writeCache($data) {
		//缓存时间为0，不设置缓存
		if ( $this->options['cache'] == 0)
			return false;		
		if ( $this->initCache() ) {				
			$expire = $this->options['cache'];
			unset($this->options['cache']);
			return $this->cache->set($this->sql, $data, $expire);	
		}
		return false;	
	}
	
	//解析数据  
	private function _parseData($type) {
		$data = $this->writeLink()->parseData($this->options, $type);
		$this->options['data'] = '';
		return $data;
	}
	
	//解析条件
	private function _parseCondition($is_master=true) {
	    //主数据库用写链接 从数据库用读链接
	    $link = $is_master ? $this->writeLink() : $this->readLink();
		$condition = $link->parseCondition($this->options);
		$this->options['where'] = '';
		$this->options['group'] = '';
		$this->options['having'] = '';
		$this->options['order'] = '';
		$this->options['limit'] = '';
		$this->options['field'] = '*';		
		return $condition;		
	}
	//事务开始
	public function begin(){
	    $this->writeLink()->beginTransaction();
	}
	//事务提交
	public function commit(){
	    $this->writeLink()->commit();
	}
	//事务回滚
	public function rollBack(){
	    $this->writeLink()->rollBack();
	}
	
}