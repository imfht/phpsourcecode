<?php

namespace fluiex;

use fluiex\DB; 

class Application extends Object
{

    var $mem = null;
    var $session = null;
    var $config = array();
    var $var = array();
    var $cachelist = array();
    var $init_db = true;
    var $init_setting = true;
    var $init_user = true;
    var $init_session = true;
    var $init_cron = true;
    var $init_misc = true;
    var $init_mobile = true;
    var $initated = false;
    var $superglobal = array(
        'GLOBALS' => 1,
        '_GET' => 1,
        '_POST' => 1,
        '_REQUEST' => 1,
        '_COOKIE' => 1,
        '_SERVER' => 1,
        '_ENV' => 1,
        '_FILES' => 1,
    );

    static function &instance()
    {
        static $object;
        if (empty($object)) {
            $object = new static();
        }
        return $object;
    }

    public function __construct()
    {
        $this->_init_env();
        $this->_init_config();
    }
    
    public function run()
    {
        
    }

    public function init()
    {
        if (!$this->initated) {
            $this->_init_db();
            $this->_init_setting();
            
            $this->_init_misc();
        }
        $this->initated = true;
    }

    protected function _init_env()
    {

        error_reporting(E_ERROR);
        if (PHP_VERSION < '5.3.0') {
            set_magic_quotes_runtime(0);
        }

        define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
        define('ICONV_ENABLE', function_exists('iconv'));
        define('MB_ENABLE', function_exists('mb_convert_encoding'));
        define('EXT_OBGZIP', function_exists('ob_gzhandler'));

        define('TIMESTAMP', time());
        $this->timezone_set();

        if (!@include(__DIR__ . '/core.php')) {
            exit('core.php is missing');
        }

        if (function_exists('ini_get')) {
            $memorylimit = ini_get('memory_limit');
            if ($memorylimit && return_bytes($memorylimit) < 33554432 && function_exists('ini_set')) {
                ini_set('memory_limit', '128m');
            }
        }

        foreach ($GLOBALS as $key => $value) {
            if (!isset($this->superglobal[$key])) {
                $GLOBALS[$key] = null;
                unset($GLOBALS[$key]);
            }
        }

        global $_G;
        
        $_G = array(
            'uid' => 0,
            'username' => '',
            'adminid' => 0,
            'groupid' => 1,
            'sid' => '',
            'formhash' => '',
            'connectguest' => 0,
            'timestamp' => TIMESTAMP,
            'starttime' => microtime(true),
            'clientip' => '',
            'remoteport' => isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : 0,
            'referer' => '',
            'charset' => '',
            'gzipcompress' => '',
            'authkey' => '',
            'timenow' => array(),
            'widthauto' => 0,
            'disabledwidthauto' => 0,
            'PHP_SELF' => '',
            'siteurl' => '',
            'siteroot' => '',
            'siteport' => '',
            'pluginrunlist' => !defined('PLUGINRUNLIST') ? array() : explode(',', PLUGINRUNLIST),
            'config' => array(),
            'setting' => array(),
            'member' => array(),
            'group' => array(),
            'cookie' => array(),
            'style' => array(),
            'cache' => array(),
            'session' => array(),
            'lang' => array(),
            'my_app' => array(),
            'my_userapp' => array(),
            'fid' => 0,
            'tid' => 0,
            'forum' => array(),
            'thread' => array(),
            'rssauth' => '',
            'home' => array(),
            'space' => array(),
            'block' => array(),
            'article' => array(),
            'action' => array(
                'action' => APPTYPEID,
                'fid' => 0,
                'tid' => 0,
            ),
            'mobile' => '',
            'notice_structure' => array(
                'mypost' => array('post', 'pcomment', 'activity', 'reward', 'goods', 'at'),
                'interactive' => array('poke', 'friend', 'wall', 'comment', 'click', 'sharenotice'),
                'system' => array('system', 'myapp', 'credit', 'group', 'verify', 'magic', 'task', 'show', 'group', 'pusearticle', 'mod_member', 'blog', 'article'),
                'manage' => array('mod_member', 'report', 'pmreport'),
                'app' => array(),
            ),
            'mobiletpl' => array('1' => 'mobile', '2' => 'touch', '3' => 'wml', 'yes' => 'mobile'),
        );
        $_G['PHP_SELF'] = dhtmlspecialchars($this->_get_script_url());
        $_G['basescript'] = CURSCRIPT;
        $_G['basefilename'] = basename($_G['PHP_SELF']);

        $this->var = & $_G;
    }

    protected function _get_script_url()
    {
        if (!isset($this->var['PHP_SELF'])) {
            $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
            if (basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
                $this->var['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
            } else if (basename($_SERVER['PHP_SELF']) === $scriptName) {
                $this->var['PHP_SELF'] = $_SERVER['PHP_SELF'];
            } else if (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
                $this->var['PHP_SELF'] = $_SERVER['ORIG_SCRIPT_NAME'];
            } else if (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
                $this->var['PHP_SELF'] = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
            } else if (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0) {
                $this->var['PHP_SELF'] = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
                $this->var['PHP_SELF'][0] != '/' && $this->var['PHP_SELF'] = '/' . $this->var['PHP_SELF'];
            } else {
                throw new Exception('request_tainting');
            }
        }
        return $this->var['PHP_SELF'];
    }

    protected function _init_config()
    {

        $_config = array();
        @include DISCUZ_ROOT . './config/config_global.php';
        if (empty($_config)) {
            throw new Exception('config_notfound');
        }

        if (empty($_config['security']['authkey'])) {
            $_config['security']['authkey'] = md5($_config['cookie']['cookiepre'] . $_config['db'][1]['dbname']);
        }

        if (empty($_config['debug']) || !file_exists(libfile('function/debug'))) {
            define('DISCUZ_DEBUG', false);
            error_reporting(0);
        } elseif ($_config['debug'] === 1 || $_config['debug'] === 2 || !empty($_REQUEST['debug']) && $_REQUEST['debug'] === $_config['debug']) {
            define('DISCUZ_DEBUG', true);
            error_reporting(E_ERROR);
            if ($_config['debug'] === 2) {
                error_reporting(E_ALL);
            }
        } else {
            define('DISCUZ_DEBUG', false);
            error_reporting(0);
        }
        define('STATICURL', !empty($_config['output']['staticurl']) ? $_config['output']['staticurl'] : 'static/');
        $this->var['staticurl'] = STATICURL;

        $this->config = & $_config;
        $this->var['config'] = & $_config;

        if (substr($_config['cookie']['cookiepath'], 0, 1) != '/') {
            $this->var['config']['cookie']['cookiepath'] = '/' . $this->var['config']['cookie']['cookiepath'];
        }
        $this->var['config']['cookie']['cookiepre'] = $this->var['config']['cookie']['cookiepre'] . substr(md5($this->var['config']['cookie']['cookiepath'] . '|' . $this->var['config']['cookie']['cookiedomain']), 0, 4) . '_';
    }

    protected function _init_db()
    {
        if ($this->init_db) {
            $driver = getglobal('config/db/driver');
            if (getglobal('config/db/slave')) {
                $driver .= 'Slave';
            }
            DB::init($driver, $this->config['db']);
        }
    }

    protected function _init_misc()
    {

        if (!$this->init_misc) {
            return false;
        }
        lang('core');

        $timeoffset = $this->var['setting']['timeoffset'];
        $this->var['timenow'] = array(
            'time' => dgmdate(TIMESTAMP),
            'offset' => $timeoffset >= 0 ? ($timeoffset == 0 ? '' : '+' . $timeoffset) : $timeoffset
        );
        $this->timezone_set($timeoffset);
    }

    protected function _init_setting()
    {
        if ($this->init_setting) {
            if (empty($this->var['setting'])) {
                $this->cachelist[] = 'setting';
            }
        }
        !empty($this->cachelist) && loadcache($this->cachelist);

        if (!is_array($this->var['setting'])) {
            $this->var['setting'] = array();
        }
    }

    public function timezone_set($timeoffset = 0)
    {
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set('Etc/GMT' . ($timeoffset > 0 ? '-' : '+') . (abs($timeoffset)));
        }
    }

}
