<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/20/16
 * Time: 2:57 PM
 */
class Cache{
    /**
     * 操作句柄
     * @var string
     * @access protected
     */
    protected $handler    ;

    /**
     * 缓存连接参数
     * @var integer
     * @access protected
     */
    protected $options = array();

    /**
     * 连接缓存
     * @param string $type 缓存类型 默认为文件缓存
     * @param array $option 配置数组
     * @return mixed
     */
    public function connect($type='',$option=array()){
        if(empty($type))  $type = 'file';//默认文件缓存
        $class=ucwords($type).'Cache';
        if(class_exists($class)){
            $cache=new $class($option);
        }else{
            exit('请求出错,没有这个缓存'.$type.$class);
        }
        return $cache;
    }

    /**
     * 取得缓存类实例
     * @param string $type
     * @param array $option
     * @return mixed
     */
    static function getInstance($type='',$option=array()){
        static $_instance	=	array();
        $guid	=	$type.md5($option);
        if(!isset($_instance[$guid])){
            $obj	=	new Cache();
            $_instance[$guid]	=	$obj->connect($type,$option);
        }
        return $_instance[$guid];
    }

    /**
     * 获取值
     * @param $name
     * @return mixed
     */
    public function __get($name) {
        return $this->get($name);
    }

    /**
     * 设置值
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name,$value) {
        return $this->set($name,$value);
    }

    /**
     * 删除值
     * @param $name
     */
    public function __unset($name) {
        $this->rm($name);
    }

    /**
     * 设置配置信息
     * @param $name
     * @param $value
     */
    public function setOptions($name,$value) {
        $this->options[$name]   =   $value;
    }

    /**
     * 获取配置信息
     * @param $name
     * @return mixed
     */
    public function getOptions($name) {
        return $this->options[$name];
    }

    /**
     * 队列缓存
     * @access protected
     * @param string $key 队列名
     * @return mixed
     */
    //
    protected function queue($key) {
        static $_handler = array(
            'file'  =>  array('F','F'),
            'xcache'=>  array('xcache_get','xcache_set'),
            'apc'   =>  array('apc_fetch','apc_store'),
        );
        $queue      =   isset($this->options['queue'])?$this->options['queue']:'file';
        $fun        =   isset($_handler[$queue])?$_handler[$queue]:$_handler['file'];
        $queue_name =   isset($this->options['queue_name'])?$this->options['queue_name']:'think_queue';
        $value      =   $fun[0]($queue_name);
        if(!$value) {
            $value  =   array();
        }
        // 进列
        if(false===array_search($key, $value))  array_push($value,$key);
        if(count($value) > $this->options['length']) {
            // 出列
            $key =  array_shift($value);
            // 删除缓存
            $this->rm($key);
        }
        return $fun[1]($queue_name,$value);
    }

    public function __call($method,$args){
        //调用缓存类型自己的方法
        if(method_exists($this->handler, $method)){
            return call_user_func_array(array($this->handler,$method), $args);
        }else{
            exit(__CLASS__.':'.$method.'不存在');
            return;
        }
    }
}
class FileCache extends Cache{
    /**
     * 架构函数
     * @access public
     */
    public function __construct($options=array()) {
        if(!empty($options)) {
            $this->options =  $options;
        }
        $this->options['temp']      =   !empty($options['temp'])?   $options['temp']    :   './temp/static_caches/';
        $this->options['prefix']    =   isset($options['prefix'])?  $options['prefix']  :   '';
        $this->options['expire']    =   isset($options['expire'])?  $options['expire']  :   1800;//默认缓存半个小时
        $this->options['length']    =   isset($options['length'])?  $options['length']  :   0;//不开启队列
        if(substr($this->options['temp'], -1) != '/')    $this->options['temp'] .= '/';
        $this->init();
    }
    /**
     * 初始化检查
     * @access private
     * @return boolean
     */
    private function init() {
        // 创建应用缓存目录
        if (!is_dir($this->options['temp'])) {
            mkdir($this->options['temp']);
        }
    }
    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name) {
        $filename   =   $this->filename($name);
        if (!is_file($filename)) {
            return false;
        }
        $content    =   file_get_contents($filename);
        if( false !== $content) {
            $expire  =  (int)substr($content,8, 12);
            if($expire != 0 && time() > filemtime($filename) + $expire) {
                //缓存过期删除缓存文件
                unlink($filename);
                return false;
            }
            if(false) {//开启数据校验  暂时不开启
                $check  =  substr($content,20, 32);
                $content   =  substr($content,52, -3);
                if($check != md5($content)) {//校验错误
                    return false;
                }
            }else {
                $content   =  substr($content,20, -3);
            }
            if(false && function_exists('gzcompress')) {//不开启数据压缩
                //启用数据压缩
                $content   =   gzuncompress($content);
            }
            $content    =   unserialize($content);
            return $content;
        }
        else {
            return false;
        }
    }
    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param int $expire  有效时间 0为永久
     * @return boolean
     */
    public function set($name,$value,$expire=null) {
        if(is_null($expire)) {
            $expire =  $this->options['expire'];
        }
        $filename   =   $this->filename($name);
        $data   =   serialize($value);
        if( false && function_exists('gzcompress')) {//不压缩数据
            //数据压缩
            $data   =   gzcompress($data,3);
        }
        if(false) {//开启数据校验  暂时不开启
            $check  =  md5($data);
        }else {
            $check  =  '';
        }
        $data    = "<?php\n//".sprintf('%012d',$expire).$check.$data."\n?>";
        $result  =   file_put_contents($filename,$data);
        if($result) {
            if($this->options['length']>0) {
                // 记录缓存队列
                $this->queue($name);
            }
            clearstatcache();
            return true;
        }else {
            return false;
        }
    }
    /**
     * 取得变量的存储文件名
     * @access private
     * @param string $name 缓存变量名
     * @return string
     */
    private function filename($name) {
        $name	=	md5($name);
        if(false) {//先不使用子目录，以后可能会用到
            // 使用子目录
            $dir   ='';
            for($i=0;$i<1;$i++) {
                $dir	.=	$name{$i}.'/';
            }
            if(!is_dir($this->options['temp'].$dir)) {
                mkdir($this->options['temp'].$dir,0755,true);
            }
            $filename	=	$dir.$this->options['prefix'].$name.'.php';
        }else{
            $filename	=	$this->options['prefix'].$name.'.php';
        }
        return $this->options['temp'].$filename;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name) {
        return unlink($this->filename($name));
    }

    /**
     * 清除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function clear() {
        $path   =  $this->options['temp'];
        $files  =   scandir($path);
        if($files){
            foreach($files as $file){
                if ($file != '.' && $file != '..' && is_dir($path.$file) ){
                    array_map( 'unlink', glob( $path.$file.'/*.*' ) );
                }elseif(is_file($path.$file)){
                    unlink( $path . $file );
                }
            }
            return true;
        }
        return false;
    }
}