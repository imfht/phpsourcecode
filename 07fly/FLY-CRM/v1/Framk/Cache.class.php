<?php
/**
+------------------------------------------------------------------------------
* Framk PHP框架
+------------------------------------------------------------------------------
* @package  Framk
* @author   shawn fon <shawn.fon@gmail.com>
+------------------------------------------------------------------------------
*/

class Cache {

	private $cacheDir;
	private $cacheTime;
	/*
	构造函数，初始化缓存目录与缓存时间
	*/
	public
	function __construct( $cacheDir, $cacheTime ) {

		$this->cacheDir = $cacheDir; //缓存目录
		$this->cacheTime = $cacheTime; //缓存延迟时间，Framk框架对更新频率太高的数据，用户可以设置缓存时间来延迟更新数据					
	}
	/*
	php魔术函数，回调数据操作方法
	进行各种查询操作，包括单条记录、多条记录、记录数，用户可以根据需要在Database与Mysql中扩展更多方法
	*/
	public
	function __call( $method, $args ) {

		$sql = @$args[ 0 ];
		if ( !empty( $this->cacheDir ) ) {
			$data = $this->findByCache( $sql, $method ); //读取缓存
		} else {
			$data = $this->findByDB( $sql, $method ); //若缓存目录为空，说明不使用缓存技术，直接从数据库查询
		}
		return $data; //返回数据数组

	}
	/*
	直接从数据库查询
	*/
	private
	function findByDB( $sql, $method ) {
		// 使用前检查类是否存在
		$database = _instance( 'Database', '', 1 );
		if ( method_exists( $database, $method ) ) {
			return $database->$method( $sql );
		} else {
			_error( 'methodNotExist', '数据查询方法不存在:' . $method, true );
		}
	}
	/*
		判断缓存是否存在，若存在直接读取，否则生成缓存再读取
	*/
	private
	function findByCache( $sql, $method ) {

		$cacheDir = _mkdir( CACHE . str_replace( '/', S, $this->cacheDir ) . S ); //建立缓存目录	
		$timeFile = $cacheDir . str_replace( '/', '_', $this->cacheDir ) . '.txt'; //时间文件						 	
		$cacheFile = $cacheDir . md5( str_replace( ' ', '', $sql ) ) . '.php'; //缓存文件	

		if ( $this->isCacheCheck( $cacheFile, $timeFile ) ) { //如果需要生成缓存	
			$data = $this->findByDB( $sql, $method );
			$content = "<?php \r\n return " . var_export( $data, true ) . "; \r\n  ?>"; //组合数据内容,生成缓存文件
			if ( !file_put_contents( $cacheFile, $content ) )_error( 'writeError', '写入缓存失败请检查目录权限:' . $cacheFile, true );
		}

		if ( file_exists( $cacheFile ) ) {
			$cacheContent = require( $cacheFile ); //读取缓存文件内容并返回	
			return $cacheContent;
		} else {
			_error( 'readError', '读取缓存失败，请检查缓存文件是否存在:' . $cacheFile, true );
		}
		//读取缓存

	}
	/* 
	判断是否需要缓存更新
	*/
	private
	function isCacheCheck( $cacheFile, $timeFile ) {

		if ( !file_exists( $timeFile ) )fclose( fopen( $timeFile, "w" ) ); //时间文件不存则创建
		if ( !file_exists( $cacheFile ) || filemtime( $cacheFile ) + $this->cacheTime < filemtime( $timeFile ) ) {
			return true; //如果缓存文件不存在或缓存文件过期则返回真，否则假
		} else {
			return false;
		}
	}
	/* 
	更新数据,包括增、删、改、替操作统一用此方法，返回值为影响记录数或新增记录ID 或false
	*/
	public
	function update( $sql ) {
		$database = _instance( 'Database', '', 1 );
		$result  = $database->updt( $sql ); //执行更新的SQL语句
		$this->upt_cache_dir($result);
		return $result;
	}
	
	//更新数据
	public function modify( $table, $arrayDataValue, $where = '', $debug = false ) {
		$database = _instance( 'Database', '', 1 );
		$result  = $database->update( $table, $arrayDataValue, $where = '', $debug = false );//执行更新的SQL语句
		$this->upt_cache_dir($result);
		return $result; //返回执行结果，影响记录数或新增记录ID 或false		
		
	}
	
	//插入数据，返回值为影响记录数或新增记录ID 或false
	public function insert( $table, $arrayDataValue, $debug = false ){
		$database = _instance( 'Database', '', 1 );
		$result  = $database->insert( $table, $arrayDataValue, $debug = false ); //执行更新的SQL语句
		$this->upt_cache_dir($result);
		return $result; //返回执行结果，影响记录数或新增记录ID 或false
	}
	//覆盖插入数据，返回值为影响记录数或新增记录ID 或false
	public function replace( $table, $arrayDataValue, $debug = false ){
		$database = _instance( 'Database', '', 1 );
		$result  = $database->replace( $table, $arrayDataValue, $debug = false ); //执行更新的SQL语句
		$this->upt_cache_dir($result);
		return $result; //返回执行结果，影响记录数或新增记录ID 或false
	}
	
	//删除，返回值为影响记录数 或false
	public function delete( $table, $where = '', $debug = false ){
		$database = _instance( 'Database', '', 1 );
		$result  = $database->delete( $table, $where = '', $debug = false ); //执行更新的SQL语句
		$this->upt_cache_dir($result);
		return $result; //返回执行结果，影响记录数或新增记录ID 或false
	}	
	
	 //如果缓存目录不为空，并且更新结果大于零时才更新时间文件，防止更新数据时影响记录条数为零时也更新时间文件		
	public function upt_cache_dir($result){
		if ( !empty( $this->cacheDir ) && $result > 0 ) {
			$arr = explode( ',', $this->cacheDir );
			foreach ( $arr as $key => $value ) { //可更新不同缓存文件夹下的时间文件，以逗号','分开
				$cacheDir = _mkdir( CACHE . str_replace( '/', S, $value ) . S ); //创建缓存目录
				$timeFile = $cacheDir . str_replace( '/', '_', $value ) . '.txt'; //时间文件，对于如：'type/article'的目录，生成时间文件为type_article.txt
				fclose( fopen( $timeFile, "w" ) );
			}
		}	
	}


	/*  +------------------------------------------------------------------------------ */
} //

?>