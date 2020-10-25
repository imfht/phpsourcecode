<?php
namespace Wpf\Common\Models;

class CommonModel extends \Phalcon\Mvc\Model{
    public static $__lastsql;//最后一次执行的SQL语句
    
    public static $__Descriptor;//数据库连接信息
    
    public static $__Source;//表名
    
    public static $__PrimaryKey;//主键
    
    public static $__cache;//缓存
    
    public static $__TableRef;//表的参照缓存名称
    
    public static $__cleanCache = false;
    
    
    public function initialize(){

        $this->setReadConnectionService('dbSlave');
        $this->setWriteConnectionService('dbMaster');
                
        $eventsManager = new \Phalcon\Events\Manager();        
        $eventsManager->attach('db', function($event, $connection) {
            
            //var_dump($connection->getSQLStatement());
            
            if ($event->getType() == 'afterQuery') {
                
                if($connection->getSqlVariables()){   
                    //var_dump($connection->getSQLStatement());
                    //$sqlstr = str_replace("?","'%s'",$connection->getSQLStatement());
                    //$sqlstr = str_replace(":AP0","%AP0\$s",$connection->getSQLStatement());
                    //var_dump($sqlstr);
                    //var_dump(array_map("addslashes",array_map("stripslashes",$connection->getSqlVariables())));
                    //var_dump($connection->getSqlVariables());
                    //$sqlstr = vsprintf($sqlstr,array_map("addslashes",array_map("stripslashes",$connection->getSqlVariables())));
                    
                    
                    $sqlstr = $connection->getSQLStatement();
                    foreach($connection->getSqlVariables() as $key=>$value){
                        if(strpos($key,":") === false){
                            $searchstr = ":".$key;
                        }else{
                            $searchstr = $key;
                        }
                        
                        if(is_numeric($value)){
                            $sqlstr = str_replace($searchstr,$value,$sqlstr);
                        }else{
                            $sqlstr = str_replace($searchstr,"'".$value."'",$sqlstr);
                        }
                        
                    }
                    
                    
                    
                    
                }else{
                    $sqlstr = $connection->getSQLStatement();
                }
                
                self::$__lastsql = $sqlstr;
                
                $logfile = LOGS_PATH."/sql/".date("Y")."/".date("m")."/".date("d").".log";
                
                $logpath = dirname($logfile);
                
                if(!is_dir($logpath)){
                    mkdir($logpath,0755,true);
                }
                
                $_logger = new \Phalcon\Logger\Multiple();
                
                $_logger->push(new \Phalcon\Logger\Adapter\File($logfile));                
                $_logger->push(new \Phalcon\Logger\Adapter\Firelogger());
                
                $_logger->log($sqlstr,\Phalcon\Logger::INFO);
                
            }
        });
        $this->getReadConnection()->setEventsManager($eventsManager);
        $this->getWriteConnection()->setEventsManager($eventsManager);      
        
        
        $this->useDynamicUpdate(true); 
    }
    
    
    
    
    
    public function onConstruct(){
        $primaryKey = $this->getModelsMetaData()->getPrimaryKeyAttributes($this);
        self::$__PrimaryKey = $primaryKey[0];
        
        
        
        self::$__Descriptor = array(
            "read" => $this->getReadConnection()->getDescriptor(),
            "write" =>  $this->getWriteConnection()->getDescriptor(),
        );
        
        self::$__Source =   $this->getSource();
        
        $this->setCache();
    }
    
    
    /**
     * 设置缓存
     * commonModel::setCache()
     * 
     * @return void
     */
    protected function setCache(){
        if($this->getDI()->has("modelsCache")){
            self::$__cache = $this->getDI()->get("modelsCache");
        }elseif($this->getDI()->has("cache")){
            self::$__cache = $this->getDI()->get("cache");
        }else{
            // 默认缓存时间为一天
            $frontCache = new \Phalcon\Cache\Frontend\Data($this->getDI()->get("config")->cache_lifttime->toArray());            
            self::$__cache = new \Phalcon\Cache\Backend\Redis($frontCache,$this->getDI()->get("config")->redis->toArray());
        }
        $this->setTableRef();
    }
    
    /**
     * 设置表的参照缓存名称
     * commonModel::setTableRefKey()
     * 
     * @return void
     */
    protected function setTableRef($time = null){
        $key = self::__createKeyPrefix().":updatetime";   
        if($time){
            self::$__TableRef = $time;
            self::$__cache->save($key,self::$__TableRef,0);
        }else{
            self::$__TableRef = self::$__cache->get($key);
        }    
    }
    
    
    public function cleanCache(){
        self::$__cleanCache = true;
        return $this;
    }
    
    
    /**
     * 创建缓存key前缀
     * commonModel::__createKeyPrefix()
     * 
     * @param mixed $type
     * @return
     */
    protected static function __createKeyPrefix($type = null){
        $descriptor = self::$__Descriptor;
        $prefix = $descriptor['read']['dbname'].":".self::$__Source;
        
        if($type){
            $prefix .=  ":".$type;
        }
        
        return $prefix;
        
    }
    
    /**
     * 创建缓存key
     * 
     * commonModel::__createKey()
     * 
     * @param mixed $paramenters
     * @param mixed $type
     * @return
     */
    protected static function __createKey($paramenters=null,$type=null){
        $key = "";
        if(is_numeric($paramenters)){            
            $key = $paramenters;
        }elseif(is_string($paramenters)){
            $key = $paramenters;
        }elseif(is_array($paramenters)){
            $uniqueKey = array();
            foreach ($paramenters as $key => $value) {
                if (is_scalar($value)) {
                    $uniqueKey[] = $key . ':' . $value;
                } else {
                    if (is_array($value)) {
                        $uniqueKey[] = $key . ':[' . self::__createKey($value) .']';
                    }
                }
            }
            $key = join(',', $uniqueKey);
        }else{
            $key = md5($paramenters);
        }
        
        $key = md5($key);
        
        return self::__createKeyPrefix($type).":".$key;
    }
    
    /**
     * 根据ID得到单条数据，自动缓存,不受cleanCache方法影响
     * 
     * commonModel::getInfo()
     * 
     * @param bool $id
     * @return
     */
    public function getInfo($id = false){
        $id = (int) $id;
        if(!$id){
            return false;
        }
        
        $key = self::__createKey($id,__FUNCTION__);        
        
        if((!self::$__cache->exists($key)) || (self::$__cleanCache)){
            $value = array(
                "value" => $this->findFirst($id)
            );
            
            self::$__cache->save($key,$value,0);
            self::$__cleanCache = false;
        }
        
        $cache = self::$__cache->get($key);
        
        return $cache['value'];
    }
    
    /**
     * find方法相同，自动缓存，受cleanCache方法控制
     * 
     * commonModel::getList()
     * 
     * @param mixed $paramenters
     * @return
     */
    public function getList($paramenters=null){
        $cachekey = self::getCacheKey(__METHOD__,$paramenters);
        if((! self::$__cache->exists($cachekey)) || (! $cache = self::$__cache->get($cachekey)) || ($cache['updatetime'] < self::$__TableRef) || self::$__cleanCache){
            $data = $this->find($paramenters);
            $cache = array(
                "value" => $data,
                "updatetime"   =>  microtime(true)
            );
            
            self::$__cache->save($cachekey,$cache,0);
            
            self::$__cleanCache = false;
            return $cache['value'];
        }
        $cache = self::$__cache->get($cachekey);
        
        return $cache['value'];
    }
    
    
    /**
     * 复写find方法，自动缓存，受cleanCache方法控制
     * commonModel::find()
     * 
     * @param mixed $paramenters
     * @return
     */
    public static function find($paramenters=null)
    {
        
        //Convert the parameters to an array
        if (!is_array($paramenters)) {
            $paramenters = array($paramenters);
        }
        
        $cachekey = self::getCacheKey(__METHOD__,$paramenters);
        

        //Check if a cache key wasn't passed
        //and create the cache parameters
        if (!isset($paramenters['cache'])) {
            
            $cachekey .= $paramenters['cache']['key'];
            
            $cachelifetime = $paramenters['cache']['lifetime'];
            
            unset($paramenters['cache']);
        }else{
            $cachelifetime = 0;
        }
        

        
        if(1 || (! self::$__cache->exists($cachekey)) || (! $cache = self::$__cache->get($cachekey)) || ($cache['updatetime'] < self::$__TableRef) || self::$__cleanCache){
            $data = parent::find($paramenters);
            $cache = array(
                "value" => $data,
                "updatetime"   =>  microtime(true)
            );
            
            self::$__cache->save($cachekey,$cache,$cachelifetime);
            
            self::$__cleanCache = false;
            return $cache['value'];
        }

        $cache = self::$__cache->get($cachekey);
        
        return $cache['value'];
    }
    
    /**
     * 复写findFirst方法，自动缓存，受cleanCache方法控制
     * commonModel::findFirst()
     * 
     * @param mixed $paramenters
     * @return
     */
    public static function findFirst($paramenters=null)
    {
        
        //Convert the parameters to an array
        if (!is_array($paramenters)) {
            $paramenters = array($paramenters);
        }
        
        $cachekey = self::getCacheKey(__METHOD__,$paramenters);
        

        //Check if a cache key wasn't passed
        //and create the cache parameters
        if (!isset($paramenters['cache'])) {
            
            $cachekey .= $paramenters['cache']['key'];
            
            $cachelifetime = $paramenters['cache']['lifetime'];
            
            unset($paramenters['cache']);
        }else{
            $cachelifetime = 0;
        }
        
        //self::$__cache->flush();
        
        if(1 || (! self::$__cache->exists($cachekey)) || (! $cache = self::$__cache->get($cachekey)) || ($cache['updatetime'] < self::$__TableRef) || self::$__cleanCache){
            $data = parent::findFirst($paramenters);
            $cache = array(
                "value" => $data,
                "updatetime"   =>  microtime(true)
            );
            
            self::$__cache->save($cachekey,$cache,$cachelifetime);
            
            self::$__cleanCache = false;
            return $cache['value'];
            
        }

        $cache = self::$__cache->get($cachekey);
        
        return $cache['value'];
    }
    
    
    /**
     * 数据库写入操作失败时中止程序，打印错误
     * commonModel::onValidationFails()
     * 
     * @return void
     */
    public function onValidationFails(){
        $error =  "<pre>";
        foreach ($this->getMessages() as $message) {
            $error .= $message.PHP_EOL;
        }
        $error .= "</pre>";
        
        $s = new \Wpf\Common\Controllers\CommonController();
        $s->error($error);
    }
    
    
    /**
     * 根据类，函数名、参数得到对应的缓存名称
     * commonModel::getCacheKey()
     * 
     * @param mixed $keyname
     * @param mixed $paramenters
     * @return
     */
    public static function getCacheKey($keyname=null,$paramenters=null){
        if(! $keyname){
            $keyname = __METHOD__;
        }
        
        $keyname = self::__createKey($paramenters,$keyname); 
        
        return $keyname;
    }
    
    
    
    
    /**
     * 当写入事件完成后，更新对应缓存（单条缓存和表参照缓存）
     * commonModel::afterSave()
     * 
     * @return void
     */
    public function afterSave(){
        $this->updateRelevantCache();
    }
    
    public function afterDelete(){
        $this->updateRelevantCache();
    }
    
    
    public function updateRelevantCache(){
        if(! is_object(self::$__cache)){
            $this->setCache();
        }
        
        //var_dump($this->id);
        
        /*清除getinfo方法的单条缓存*/        
        $pk = self::$__PrimaryKey;
        $id = $this->$pk;
        
        
        $key = self::__createKey($id,"getInfo");
        self::$__cache->delete($key);
        
        /*更新表参照时间*/
        self::$__TableRef = microtime(true);
        $this->setTableRef(self::$__TableRef);
    }
}