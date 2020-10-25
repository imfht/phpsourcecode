<?php

defined('IN_CART') or die;

class Session
{

    private $lifetime = 1800;
    private $session_name = '';
    private $session_id = '';
    private $session_time = '';
    private $session_md5 = '';
    private $time = "";
    private $initdata = array();

    /**
     *
     *  构造函数
     *
     */
    function __construct($session_name = 'sess', $session_id = '', $lifetime = 1800)
    {
        $GLOBALS['_session'] = array();
        $this->session_name = $session_name;
        $this->lifetime = $lifetime;
        $this->_time = time();
        //验证session_id
        $tmpsess_id = '';
        if ($session_id) {
            $tmpsess_id = $session_id;
        } else {
            $tmpsess_id = cgetcookie($session_name);
        }

        if ($tmpsess_id && $this->verify($tmpsess_id)) {
            $this->session_id = substr($tmpsess_id, 0, 32);
        }
        if ($this->session_id) {

            //session_id 存在，加载session
            $this->read_session();
        } else {

            //session_id 不存在,生成，写入到cookie
            $this->session_id = $this->gene_session_id();
            $this->init_session();
            csetcookie($this->session_name, $this->session_id . $this->gene_salt($this->session_id));
        }
        //关闭时执行gc
        register_shutdown_function(array(&$this, 'gc'));
    }

    /**
     *
     * DB中insert新的session
     *
     */
    private function init_session()
    {
        DB::getDB()->insert("session", array("session_id" => $this->session_id, "session_data" => serialize(array()), 'time' => $this->_time));
    }

    /**
     *
     * 生成session_id
     *
     */
    private function gene_session_id()
    {
        $id = strval(time());
        while (strlen($id) < 32) {
            $id .= mt_rand();
        }
        return md5(uniqid($id, true));
    }

    /**
     *
     * 生成salt,验证
     *
     */
    private function gene_salt($session_id)
    {
        $ip = getClientIp();
        return sprintf("%8u", crc32(SITEPATH . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "") . $ip . $session_id));
    }

    /**
     *
     * 生成salt,验证
     *
     */
    private function verify($tmpsess_id)
    {
        return substr($tmpsess_id, 32) == $this->gene_salt(substr($tmpsess_id, 0, 32));
    }

    /**
     *
     * 读出已知session
     *
     */
    private function read_session()
    {

        $session = DB::getDB()->selectrow("session", "*", "session_id = '" . $this->session_id . "'");
        if (empty($session)) { //session为空,
            $this->init_session();
            $this->session_md5 = md5('a:0:{}');
            $this->session_time = 0;
            $GLOBALS['_SESSION'] = array();
        } else {

            if (!empty($session['session_data']) && $session['time'] > $this->_time - $this->lifetime) {
                $this->session_md5 = md5($session['session_data']);
                $this->session_time = $session['time'];
                $GLOBALS['_SESSION'] = unserialize($session['session_data']);
            } else { //session过期
                $this->session_md5 = md5(serialize(array()));
                $this->session_time = 0;
                $GLOBALS['_SESSION'] = array();
            }
        }
    }

    /**
     *
     * 更新session
     *
     */
    private function write_session()
    {

        $data = serialize(!empty($GLOBALS['_SESSION']) ? $GLOBALS['_SESSION'] : array());
        $this->_time = time();


        //session未变化
        if (md5($data) == $this->session_md5 && $this->_time - $this->session_time < 10) {
            return true;
        }

        $ret = DB::getDB()->update("session", array("time" => $this->_time, "session_data" => addslashes($data)), "session_id='" . $this->session_id . "'", true);
        return $ret;
    }

    /**
     *
     * 执行gc
     *
     */
    public function gc()
    {
        $this->write_session();
        if ($this->_time % 2 == 0) {
            DB::getDB()->delete("session", "time<" . ($this->_time - $this->lifetime));
        }
        return true;
    }

}
