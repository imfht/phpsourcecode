<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

class Session {
    private $db;
    const TIMEOUT = 7200;

    function __construct($db){
        $this->db = $db;
        $this->config = include DIR_CONF.'config.conf.php';
        if(ENVIRONMENT != 'production' && file_exists(DIR_CONF.ENVIRONMENT.'/'.'config.conf.php')){
            $this->config = include DIR_CONF.ENVIRONMENT.'/'.'config.conf.php';
        }
    }

    function generate_sid($cookie_key = 'sccookie'){
        global $key;
        $str = '';
        while(mb_strlen($str) < 32){
            $str .= mt_rand(0, mt_getrandmax());
        }
        $sid = empty($_COOKIE[$cookie_key]) ? '' : authcode($_COOKIE[$cookie_key], 'DECODE', $key);
        if(!empty($sid)){
            return $sid;
        }else {
            $sql = 'DELETE FROM `session` WHERE `last_time` < '.($_SERVER['REQUEST_TIME'] - self::TIMEOUT);
            $this->db->query($sql);

            $sid = md5($str.uniqid(true));
            $cookie_sid = authcode($sid, 'ENCODE', $key, self::TIMEOUT);
            if(setcookie($cookie_key, $cookie_sid, $_SERVER['REQUEST_TIME'] + self::TIMEOUT, $this->config['cookie_path'], $this->config['cookie_domain'])){
                return $sid;
            }else {
                return null;
            }
        }
    }

    function set_session($sid, $paras){
        $paras = serialize($paras);
        $arr = array(
            'session_id' => $sid,
            'last_time' => $_SERVER['REQUEST_TIME'],
            'content' => $paras
        );
        if($this->db->replace_into('session', $arr)){
            return true;
        }else {
            return false;
        }
    }

    function del_session($sid){
        $sql = 'DELETE FROM `session` WHERE `session_id` = \''.$sid.'\'';
        return $this->db->query($sql);
    }

    function get_session($sid){
        $sql = 'SELECT `content` FROM `session` WHERE `session_id` = \''.$sid.'\' LIMIT 1';
        $content = $this->db->fetchFirst($sql);
        return empty($content) ? NULL : unserialize($content['content']);
    }
}
