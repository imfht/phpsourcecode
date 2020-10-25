<?php
namespace app\common\util;

use think\Db;

/**
 * Redis缓存
 */
class Cache2{
    
    private static $redis = null;
    
    private static $compress = true;    //是否压缩缓存内容
    
    /**
     * 初始化链接
     */
	public static function init(){
	    if(self::$redis!==null){
	        return self::$redis;
	    }
	    $cfg = config('cache');
	    
	    if (ucfirst($cfg['type'])!='Redis') {
	        self::$compress = false;   //mysql不支持存放压缩后的代码
	        self::$redis = new Mysql_redis();
	        return self::$redis;
	        //showerr('你服务器的PHP环境不支持 Redis 扩展!');
	    }
	    
	    if (!extension_loaded('redis')) {
	        showerr('你服务器的PHP环境不支持 Redis 扩展!');
	    }
	    
	    $cfg['port'] || $cfg['port'] = '6379';
	    $cfg['host'] || $cfg['host'] = '127.0.0.1';
	    
	    self::$redis = new \Redis();
	    self::$redis->connect($cfg['host'], $cfg['port']);
	    $cfg['password'] && self::$redis->auth( $cfg['password'] );
	    self::$redis->select(1);
	    return self::$redis;
	}
	
	/**
	 * 返回 Redis 开发者可以使用更多的功能
	 * @return \Redis
	 */
	public static function db(){	    
	    return self::init();
	}
	
	protected static function getkey($key=''){
	    return $key;
	    //return config('cache.prefix').$key;
	}
	
	
	/**
	 * 添加数据
	 * @param string $key
	 * @param string $value
	 */
	public static function set($key='',$value='',$time=0){
	    self::init();
	    if (is_array($key)) {
	        self::$redis->multi();
	        if($value!=='' && $value!==null && isset($key[0])){    //插入一批数据
	            foreach($key AS $v){
	                self::$redis->lpush(self::getkey($value),self::_set($v));
	            }
	        }else{
	            foreach($key AS $k=>$v){
	                if ($v===null || $v==='') {
	                    self::$redis->delete(self::getkey($k));
	                }else{
	                    if ($time>0) {
	                        self::$redis->setex(self::getkey($k),$time,self::_set($v));
	                    }else{
	                        self::$redis->set(self::getkey($k),self::_set($v));
	                    }	                    
	                }
	            }
	        }
	        self::$redis->exec();
	    }elseif($value===null || $value===''){
	        if( substr($key, -1,1)=='*' ){	            
	            $detail = self::$redis->keys(self::getkey($key));
	            self::$redis->multi();
	            foreach($detail AS $ks){
	                self::$redis->delete($ks);
	            }
	            self::$redis->exec();
	        }else{
	            self::$redis->delete(self::getkey($key));
	        }	        
	    }else{
	        if ($time>0) {
	            self::$redis->setex(self::getkey($key),$time,self::_set($value));
	        }else{
	            self::$redis->set(self::getkey($key),self::_set($value));
	        }	        
	    }
	}
	
	/**
	 * 获取数据
	 * @param string $key
	 * @param string $type
	 * @return void|string|unknown
	 */
	public static function get($key='',$type=''){
	    self::init();
	    if ($type=='lpop' || $type=='rpop') {
	        return self::_get(self::$redis->$type(self::getkey($key)));
	    }
	    if(substr($key, -1,1)=='*'){
	        return self::$redis->keys(self::getkey($key));
	    }else{
	        return self::_get(self::$redis->get(self::getkey($key)));
	    }
	}
	
	private static function _set($data=''){
	    if (is_array($data)||is_object($data)) {
	        $data = 'is_serialize:'.serialize($data);
	    }
	    if ( self::$compress && function_exists('gzcompress') ) {
	        $data = gzcompress($data, 3);
	    }
	    return $data;
	}
	
	private static function _get($data=''){
	    if (empty($data)) {
	        return ;
	    }
	    if ( self::$compress && function_exists('gzcompress') ) {
	        $data = gzuncompress($data);
	    }
	    if (substr($data,0,13)=='is_serialize:') {
	        $data = substr($data, 13);
	        $data = unserialize($data);
	    }
	    return $data;
	}

}

/**
 *兼容没有安装redis 数据库的情况
 */
class Mysql_redis{
    private $data = null;
    public function set($k='',$v=''){
        $v = addslashes($v);
        Db::execute("REPLACE INTO  `".config('database.prefix')."redis_index` (  `k` ,  `v` ) VALUES ('{$k}',  '{$v}')");
    }
    
    public function setex($k='',$t=0,$v=''){
        $t = time()+$t;
        $v = addslashes($v);
        Db::execute("REPLACE INTO  `".config('database.prefix')."redis_index` (  `k` ,  `v`  ,  `t` ) VALUES ('{$k}',  '{$v}',  '{$t}')");
    }
    
    public function get($k=''){
        $info = Db::name('redis_index')->where('k',$k)->find();
        if ($info['t'] && $info['t']<time()) {
            Db::name('redis_index')->where('k',$k)->delete();
            return ;
        }
        if ($info) {
            return $info['v'];
        }
    }
    
    public function delete($k=''){
        Db::name('redis_index')->where('k',$k)->delete();
    }
    
    public function keys($key=''){
        if ($key=='') {
            return Db::name('redis_index')->column('k');
        }else{
            return Db::name('redis_index')->where('k','like',str_replace('*', '%', $key))->column('k');
        }
    }
    
    public function lpush($k='',$v=''){
        $v = addslashes($v);
        $this->data[] = [
            'k'=>$k,
            'v'=>$v,
        ];
    }
    
    public function rpop($k=''){
        $info = Db::name('redis_list')->where('k',$k)->order('id asc')->find();
        if ($info){
            Db::name('redis_list')->where('id',$info['id'])->delete();
            return $info['v'];
        }
    }
    
    public function lpop($k=''){
        $info = Db::name('redis_list')->where('k',$k)->order('id desc')->find();
        if ($info){
            Db::name('redis_list')->where('id',$info['id'])->delete();
            return $info['v'];
        }
    }
    //返回集合中存储值的数量 为空返回0
    public function ssize($k=''){
        return Db::name('redis_list')->where('k',$k)->order('id desc')->count('id');
    }
    public function multi(){
        $this->data = null;
    }
    public function exec(){
        if ($this->data){
            Db::name('redis_list')->insertAll($this->data);
        }
    }
    
}


