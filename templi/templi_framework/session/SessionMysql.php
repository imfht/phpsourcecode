<?php
/**
 *  session mysql 数据库存储类
 * 
 */
namespace framework\session;
use framework\core\Model,
    framework\core\Common,
    \Templi;

class SessionMysql
{
    /**
     * @var  Model session 存储model
     */
    private $_model;
    /**
     * 构造函数
     * 
     */
    public function __construct($modelName, $lifetime=null) {
        $this->_model = new Model();
        $this->_model->table($modelName);
        if (!empty($lifetime)) {
            ini_set('session.gc_maxlifetime', $lifetime);
        }
    	session_set_save_handler(
            array(&$this,'open'), array(&$this,'close'),
            array(&$this,'read'), array(&$this,'write'),
            array(&$this,'destroy'), array(&$this,'gc')
        );
    	session_start();
    }
    /**
     * session_set_save_handler  open方法
     * @param string $savePath
     * @param string $sessionName
     * @return true
     */
    public function open($savePath, $sessionName) {
        return true;
    }
    /**
     * session_set_save_handler  close方法
     * @return bool
     */
    public function close() {
        return true;
    } 
    /**
     * 读取session_id
     * session_set_save_handler  read方法
     * @param $sessionId sessionId
     * @return string 读取session_id
     */
    public function read($sessionId) {
        $r = $this->_model->where(array('session_id'=>$sessionId))
            ->limit(1)
            ->fetchOne();
        return $r ? $r['user_data'] : '';
    } 
    /**
     * 写入session_id 的值
     * 
     * @param $sessionId session
     * @param $data 值
     * @return mixed query 执行结果
     */
    public function write($sessionId, $data) {
        $ip = Common::getIp();
        $sessionData = array(
            'session_id' => $sessionId,
            'ip_address' => $ip,
            'last_activity' => SYS_TIME,
            'user_data' => $data,
        );
        return $this->_model->insert($sessionData, 1,1);
    }
    /** 
     * 删除指定的session_id
     * 
     * @param $sessionId session
     * @return bool
     */
    public function destroy($sessionId) {
        return $this->_model->delete(array('session_id'=>$sessionId));
    }
    /**
     * 删除过期的 session
     *
     * @param $lifetime 存活期时间
     * @return bool
     */
   public function gc($lifetime) {
        $lastActivity = time() - $lifetime;
        return $this->_model->delete("`last_activity`<$lastActivity");
    }
}