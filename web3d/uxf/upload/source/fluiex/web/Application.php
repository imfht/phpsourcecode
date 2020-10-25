<?php

namespace fluiex\web;

class Application extends \fluiex\Application
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_init_input();
        $this->_init_output();
    }
    
    public function init()
    {
        if (!$this->initated) {
            parent::init();
            
            $this->_init_user();
            $this->_init_session();
            $this->_init_mobile();
            $this->_init_cron();
            $this->_init_misc();
        }
        $this->initated = true;
    }
    
    protected function _init_env()
    {
        parent::_init_env();

        define('IS_ROBOT', UserAgent::checkRobot());


        global $_G;
        
        $_G['clientip'] = $this->_get_client_ip();
        
        $sitepath = substr($_G['PHP_SELF'], 0, strrpos($_G['PHP_SELF'], '/'));
        if (defined('IN_API')) {
            $sitepath = preg_replace("/\/api\/?.*?$/i", '', $sitepath);
        } elseif (defined('IN_ARCHIVER')) {
            $sitepath = preg_replace("/\/archiver/i", '', $sitepath);
        }
        $_G['isHTTPS'] = ($_SERVER['HTTPS'] && strtolower($_SERVER['HTTPS']) != 'off') ? true : false;
        $_G['siteurl'] = dhtmlspecialchars('http' . ($_G['isHTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $sitepath . '/');

        $url = parse_url($_G['siteurl']);
        $_G['siteroot'] = isset($url['path']) ? $url['path'] : '';
        $_G['siteport'] = empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ? '' : ':' . $_SERVER['SERVER_PORT'];

        if (defined('SUB_DIR')) {
            $_G['siteurl'] = str_replace(SUB_DIR, '/', $_G['siteurl']);
            $_G['siteroot'] = str_replace(SUB_DIR, '/', $_G['siteroot']);
        }
        $this->var = & $_G;
    }
    
    protected function _init_input()
    {
        if (isset($_GET['GLOBALS']) || isset($_POST['GLOBALS']) || isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
            system_error('request_tainting');
        }

        if (MAGIC_QUOTES_GPC) {
            $_GET = dstripslashes($_GET);
            $_POST = dstripslashes($_POST);
            $_COOKIE = dstripslashes($_COOKIE);
        }

        $prelength = strlen($this->config['cookie']['cookiepre']);
        foreach ($_COOKIE as $key => $val) {
            if (substr($key, 0, $prelength) == $this->config['cookie']['cookiepre']) {
                $this->var['cookie'][substr($key, $prelength)] = $val;
            }
        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $_GET = array_merge($_GET, $_POST);
        }

        if (isset($_GET['page'])) {
            $_GET['page'] = rawurlencode($_GET['page']);
        }

        if (!(!empty($_GET['handlekey']) && preg_match('/^\w+$/', $_GET['handlekey']))) {
            unset($_GET['handlekey']);
        }

        if (!empty($this->var['config']['input']['compatible'])) {
            foreach ($_GET as $k => $v) {
                $this->var['gp_' . $k] = daddslashes($v);
            }
        }

        $this->var['mod'] = empty($_GET['mod']) ? '' : dhtmlspecialchars($_GET['mod']);
        $this->var['inajax'] = empty($_GET['inajax']) ? 0 : (empty($this->var['config']['output']['ajaxvalidate']) ? 1 : ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' || $_SERVER['REQUEST_METHOD'] == 'POST' ? 1 : 0));
        $this->var['page'] = empty($_GET['page']) ? 1 : max(1, intval($_GET['page']));
        $this->var['sid'] = $this->var['cookie']['sid'] = isset($this->var['cookie']['sid']) ? dhtmlspecialchars($this->var['cookie']['sid']) : '';

        if (empty($this->var['cookie']['saltkey'])) {
            $this->var['cookie']['saltkey'] = random(8);
            Cookie::set('saltkey', $this->var['cookie']['saltkey'], 86400 * 30, 1, 1);
        }
        $this->var['authkey'] = md5($this->var['config']['security']['authkey'] . $this->var['cookie']['saltkey']);
    }

    protected function _init_output()
    {


        if ($this->config['security']['attackevasive'] && (!defined('CURSCRIPT') || !in_array($this->var['mod'], array('seccode', 'secqaa', 'swfupload')) && !defined('DISABLEDEFENSE'))) {
            require_once libfile('misc/security', 'include');
        }

        if (!empty($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === false) {
            $this->config['output']['gzip'] = false;
        }

        $allowgzip = $this->config['output']['gzip'] && empty($this->var['inajax']) && $this->var['mod'] != 'attachment' && EXT_OBGZIP;
        setglobal('gzipcompress', $allowgzip);

        /*if (!ob_start($allowgzip ? 'ob_gzhandler' : null)) {
            ob_start();
        }*/

        setglobal('charset', $this->config['output']['charset']);
        define('CHARSET', $this->config['output']['charset']);
        if ($this->config['output']['forceheader']) {
            @header('Content-Type: text/html; charset=' . CHARSET);
        }
    }

    protected function _init_session()
    {

        $sessionclose = !empty($this->var['setting']['sessionclose']);
        $this->session = $sessionclose ? new discuz_session_close() : new discuz_session();

        if ($this->init_session) {
            $this->session->init($this->var['cookie']['sid'], $this->var['clientip'], $this->var['uid']);
            $this->var['sid'] = $this->session->sid;
            $this->var['session'] = $this->session->var;

            if (!empty($this->var['sid']) && $this->var['sid'] != $this->var['cookie']['sid']) {
                Cookie::set('sid', $this->var['sid'], 86400);
            }

            if ($this->session->isnew) {
                if (ipbanned($this->var['clientip'])) {
                    $this->session->set('groupid', 6);
                }
            }

            if ($this->session->get('groupid') == 6) {
                $this->var['member']['groupid'] = 6;
                if (!defined('IN_MOBILE_API')) {
                    sysmessage('user_banned');
                } else {
                    mobile_core::result(array('error' => 'user_banned'));
                }
            }

            if ($this->var['uid'] && !$sessionclose && ($this->session->isnew || ($this->session->get('lastactivity') + 600) < TIMESTAMP)) {
                $this->session->set('lastactivity', TIMESTAMP);
                if ($this->session->isnew) {
                    if ($this->var['member']['lastip'] && $this->var['member']['lastvisit']) {
                        Cookie::set('lip', $this->var['member']['lastip'] . ',' . $this->var['member']['lastvisit']);
                    }
                    C::t('common_member_status')->update($this->var['uid'], array('lastip' => $this->var['clientip'], 'port' => $this->var['remoteport'], 'lastvisit' => TIMESTAMP));
                }
            }
        }
    }

    protected function _init_user()
    {
        if ($this->init_user) {
            if ($auth = getglobal('auth', 'cookie')) {
                $auth = daddslashes(explode("\t", authcode($auth, 'DECODE')));
            }
            list($discuz_pw, $discuz_uid) = empty($auth) || count($auth) < 2 ? array('', '') : $auth;

            if ($discuz_uid) {
                $user = getuserbyuid($discuz_uid, 1);
            }

            if (!empty($user) && $user['password'] == $discuz_pw) {
                if (isset($user['_inarchive'])) {
                    C::t('common_member_archive')->move_to_master($discuz_uid);
                }
                $this->var['member'] = $user;
            } else {
                $user = array();
                $this->_init_guest();
            }

            if ($user && $user['groupexpiry'] > 0 && $user['groupexpiry'] < TIMESTAMP) {
                $memberfieldforum = C::t('common_member_field_forum')->fetch($discuz_uid);
                $groupterms = dunserialize($memberfieldforum['groupterms']);
                if (!empty($groupterms['main'])) {
                    C::t("common_member")->update($user['uid'], array('groupexpiry' => 0, 'groupid' => $groupterms['main']['groupid'], 'adminid' => $groupterms['main']['adminid']));
                    $user['groupid'] = $groupterms['main']['groupid'];
                    $user['adminid'] = $groupterms['main']['adminid'];
                    unset($groupterms['main'], $groupterms['ext'][$this->var['member']['groupid']]);
                    $this->var['member'] = $user;
                    C::t('common_member_field_forum')->update($discuz_uid, array('groupterms' => serialize($groupterms)));
                } elseif ((getgpc('mod') != 'spacecp' || CURSCRIPT != 'home') && CURSCRIPT != 'member') {
                    dheader('location: home.php?mod=spacecp&ac=usergroup&do=expiry');
                }
            }

            if ($user && $user['freeze'] && (getgpc('mod') != 'spacecp' && getgpc('mod') != 'misc' || CURSCRIPT != 'home') && CURSCRIPT != 'member' && CURSCRIPT != 'misc') {
                dheader('location: home.php?mod=spacecp&ac=profile&op=password');
            }

            $this->cachelist[] = 'usergroup_' . $this->var['member']['groupid'];
            if ($user && $user['adminid'] > 0 && $user['groupid'] != $user['adminid']) {
                $this->cachelist[] = 'admingroup_' . $this->var['member']['adminid'];
            }
        } else {
            $this->_init_guest();
        }
        setglobal('groupid', getglobal('groupid', 'member'));
        !empty($this->cachelist) && loadcache($this->cachelist);

        if ($this->var['member'] && $this->var['group']['radminid'] == 0 && $this->var['member']['adminid'] > 0 && $this->var['member']['groupid'] != $this->var['member']['adminid'] && !empty($this->var['cache']['admingroup_' . $this->var['member']['adminid']])) {
            $this->var['group'] = array_merge($this->var['group'], $this->var['cache']['admingroup_' . $this->var['member']['adminid']]);
        }

        if ($this->var['group']['allowmakehtml'] && isset($_GET['_makehtml'])) {
            $this->var['makehtml'] = 1;
            $this->_init_guest();
            loadcache(array('usergroup_7'));
            $this->var['group'] = $this->var['cache']['usergroup_7'];
            unset($this->var['inajax']);
        }

        if (empty($this->var['cookie']['lastvisit'])) {
            $this->var['member']['lastvisit'] = TIMESTAMP - 3600;
            Cookie::set('lastvisit', TIMESTAMP - 3600, 86400 * 30);
        } else {
            $this->var['member']['lastvisit'] = $this->var['cookie']['lastvisit'];
        }

        setglobal('uid', getglobal('uid', 'member'));
        setglobal('username', getglobal('username', 'member'));
        setglobal('adminid', getglobal('adminid', 'member'));
        setglobal('groupid', getglobal('groupid', 'member'));
        if ($this->var['member']['newprompt']) {
            $this->var['member']['newprompt_num'] = C::t('common_member_newprompt')->fetch($this->var['member']['uid']);
            $this->var['member']['newprompt_num'] = unserialize($this->var['member']['newprompt_num']['data']);
            $this->var['member']['category_num'] = helper_notification::get_categorynum($this->var['member']['newprompt_num']);
        }
    }

    protected function _init_guest()
    {
        $username = '';
        $groupid = 7;
        if (!empty($this->var['cookie']['con_auth_hash']) && ($openid = authcode($this->var['cookie']['con_auth_hash']))) {
            $this->var['connectguest'] = 1;
            $username = 'QQ_' . substr($openid, -6);
            $this->var['setting']['cacheindexlife'] = 0;
            $this->var['setting']['cachethreadlife'] = 0;
            $groupid = $this->var['setting']['connect']['guest_groupid'] ? $this->var['setting']['connect']['guest_groupid'] : $this->var['setting']['newusergroupid'];
        }
        setglobal('member', array('uid' => 0, 'username' => $username, 'adminid' => 0, 'groupid' => $groupid, 'credits' => 0, 'timeoffset' => 9999));
    }

    protected function _init_cron()
    {
        $ext = empty($this->config['remote']['on']) || empty($this->config['remote']['cron']) || APPTYPEID == 200;
        if ($this->init_cron && $this->init_setting && $ext) {
            if ($this->var['cache']['cronnextrun'] <= TIMESTAMP) {
                discuz_cron::run();
            }
        }
    }
    
    protected function _init_misc()
    {
        if (!$this->init_misc) {
            return false;
        }
        
        parent::_init_misc();

        if ($this->config['security']['urlxssdefend'] && !defined('DISABLEXSSCHECK')) {
            $this->_xss_check();
        }
        if ($this->init_setting && $this->init_user) {
            if (!isset($this->var['member']['timeoffset']) || $this->var['member']['timeoffset'] == 9999 || $this->var['member']['timeoffset'] === '') {
                $this->var['member']['timeoffset'] = $this->var['setting']['timeoffset'];
            }
        }

        $timeoffset = $this->init_setting ? $this->var['member']['timeoffset'] : $this->var['setting']['timeoffset'];
        $this->var['timenow'] = array(
            'time' => dgmdate(TIMESTAMP),
            'offset' => $timeoffset >= 0 ? ($timeoffset == 0 ? '' : '+' . $timeoffset) : $timeoffset
        );
        $this->timezone_set($timeoffset);

        $this->var['formhash'] = Form::hash();
        define('FORMHASH', $this->var['formhash']);

        if ($this->init_user) {
            $allowvisitflag = in_array(CURSCRIPT, array('member')) || defined('ALLOWGUEST') && ALLOWGUEST;
            if ($this->var['group'] && isset($this->var['group']['allowvisit']) && !$this->var['group']['allowvisit']) {
                if ($this->var['uid'] && !$allowvisitflag) {
                    if (!defined('IN_MOBILE_API')) {
                        showmessage('user_banned');
                    } else {
                        mobile_core::result(array('error' => 'user_banned'));
                    }
                } elseif ((!defined('ALLOWGUEST') || !ALLOWGUEST) && !in_array(CURSCRIPT, array('member', 'api')) && !$this->var['inajax']) {
                    if (!defined('IN_MOBILE_API')) {
                        dheader('location: member.php?mod=logging&action=login&referer=' . rawurlencode($this->var['siteurl'] . $this->var['basefilename'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '')));
                    } else {
                        mobile_core::result(array('error' => 'to_login'));
                    }
                }
            }
            if (isset($this->var['member']['status']) && $this->var['member']['status'] == -1 && !$allowvisitflag) {
                if (!defined('IN_MOBILE_API')) {
                    showmessage('user_banned');
                } else {
                    mobile_core::result(array('error' => 'user_banned'));
                }
            }
        }

        if ($this->var['setting']['ipaccess'] && !ipaccess($this->var['clientip'], $this->var['setting']['ipaccess'])) {
            if (!defined('IN_MOBILE_API')) {
                showmessage('user_banned');
            } else {
                mobile_core::result(array('error' => 'user_banned'));
            }
        }

        if ($this->var['setting']['bbclosed']) {
            if ($this->var['uid'] && ($this->var['group']['allowvisit'] == 2 || $this->var['groupid'] == 1)) {
                
            } elseif (in_array(CURSCRIPT, array('admin', 'member', 'api')) || defined('ALLOWGUEST') && ALLOWGUEST) {
                
            } else {
                $closedreason = C::t('common_setting')->fetch('closedreason');
                $closedreason = str_replace(':', '&#58;', $closedreason);
                if (!defined('IN_MOBILE_API')) {
                    showmessage($closedreason ? $closedreason : 'board_closed', NULL, array('adminemail' => $this->var['setting']['adminemail']), array('login' => 1));
                } else {
                    mobile_core::result(array('error' => $closedreason ? $closedreason : 'board_closed'));
                }
            }
        }

        if (CURSCRIPT != 'admin' && !(in_array($this->var['mod'], array('logging', 'seccode')))) {
            Form::checkPeriods('visitbanperiods');
        }

        if (defined('IN_MOBILE')) {
            $this->var['tpp'] = $this->var['setting']['mobile']['mobiletopicperpage'] ? intval($this->var['setting']['mobile']['mobiletopicperpage']) : 20;
            $this->var['ppp'] = $this->var['setting']['mobile']['mobilepostperpage'] ? intval($this->var['setting']['mobile']['mobilepostperpage']) : 5;
        } else {
            $this->var['tpp'] = $this->var['setting']['topicperpage'] ? intval($this->var['setting']['topicperpage']) : 20;
            $this->var['ppp'] = $this->var['setting']['postperpage'] ? intval($this->var['setting']['postperpage']) : 10;
        }

        if ($this->var['setting']['nocacheheaders']) {
            @header("Expires: -1");
            @header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
            @header("Pragma: no-cache");
        }

        if ($this->session->isnew && $this->var['uid']) {
            updatecreditbyaction('daylogin', $this->var['uid']);

            include_once libfile('function/stat');
            updatestat('login', 1);
            if (defined('IN_MOBILE')) {
                updatestat('mobilelogin', 1);
            }
            if ($this->var['setting']['connect']['allow'] && $this->var['member']['conisbind']) {
                updatestat('connectlogin', 1);
            }
        }
        if (isset($this->var['member']['conisbind']) && $this->var['member']['conisbind'] && $this->var['setting'] && $this->var['setting']['connect']['newbiespan'] !== '') {
            $this->var['setting']['newbiespan'] = $this->var['setting']['connect']['newbiespan'];
        }

        $lastact = TIMESTAMP . "\t" . dhtmlspecialchars(basename($this->var['PHP_SELF'])) . "\t" . dhtmlspecialchars($this->var['mod']);
        Cookie::set('lastact', $lastact, 86400);
        setglobal('currenturl_encode', base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));

        if ((!empty($_GET['fromuid']) || !empty($_GET['fromuser'])) && ($this->var['setting']['creditspolicy']['promotion_visit'] || $this->var['setting']['creditspolicy']['promotion_register'])) {
            require_once libfile('misc/promotion', 'include');
        }

        $this->var['seokeywords'] = !empty($this->var['setting']['seokeywords'][CURSCRIPT]) ? $this->var['setting']['seokeywords'][CURSCRIPT] : '';
        $this->var['seodescription'] = !empty($this->var['setting']['seodescription'][CURSCRIPT]) ? $this->var['setting']['seodescription'][CURSCRIPT] : '';
    }
    
    protected function _init_setting()
    {
        if ($this->init_setting) {
            if (empty($this->var['setting'])) {
                $this->cachelist[] = 'setting';
            }
            
            if (empty($this->var['style'])) {
                $this->cachelist[] = 'style_default';
            }

            if (!isset($this->var['cache']['cronnextrun'])) {
                $this->cachelist[] = 'cronnextrun';
            }
        }
        empty($this->cachelist) && loadcache($this->cachelist);

        if (!is_array($this->var['setting'])) {
            $this->var['setting'] = array();
        }
    }
    
    public function reject_robot()
    {
        if (IS_ROBOT) {
            exit(header("HTTP/1.1 403 Forbidden"));
        }
    }

    protected function _xss_check()
    {

        static $check = array('"', '>', '<', '\'', '(', ')', 'CONTENT-TRANSFER-ENCODING');

        if (isset($_GET['formhash']) && $_GET['formhash'] !== formhash()) {
            system_error('request_tainting');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $temp = $_SERVER['REQUEST_URI'];
        } elseif (empty($_GET['formhash'])) {
            $temp = $_SERVER['REQUEST_URI'] . file_get_contents('php://input');
        } else {
            $temp = '';
        }

        if (!empty($temp)) {
            $temp = strtoupper(urldecode(urldecode($temp)));
            foreach ($check as $str) {
                if (strpos($temp, $str) !== false) {
                    system_error('request_tainting');
                }
            }
        }

        return true;
    }

    protected function _get_client_ip()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }
    
    public function _init_style()
    {
        if (defined('IN_MOBILE')) {
            $mobile = max(1, intval(IN_MOBILE));
            if ($mobile && $this->var['setting']['styleid' . $mobile]) {
                $styleid = $this->var['setting']['styleid' . $mobile];
            }
        } else {
            $styleid = !empty($this->var['cookie']['styleid']) ? $this->var['cookie']['styleid'] : 0;
        }
        if (intval(!empty($this->var['forum']['styleid']))) {
            $this->var['cache']['style_default']['styleid'] = $styleid = $this->var['forum']['styleid'];
        } elseif (intval(!empty($this->var['category']['styleid']))) {
            $this->var['cache']['style_default']['styleid'] = $styleid = $this->var['category']['styleid'];
        }

        $styleid = intval($styleid);

        if ($styleid && $styleid != $this->var['setting']['styleid']) {
            loadcache('style_' . $styleid);
            if ($this->var['cache']['style_' . $styleid]) {
                $this->var['style'] = $this->var['cache']['style_' . $styleid];
            }
        }

        define('IMGDIR', $this->var['style']['imgdir']);
        define('STYLEID', $this->var['style']['styleid']);
        define('VERHASH', $this->var['style']['verhash']);
        define('TPLDIR', $this->var['style']['tpldir']);
        define('TEMPLATEID', $this->var['style']['templateid']);
    }

    protected function _init_mobile()
    {
        if (!$this->init_mobile) {
            return false;
        }

        if (!$this->var['setting'] || !$this->var['setting']['mobile']['allowmobile'] || !is_array($this->var['setting']['mobile']) || IS_ROBOT) {
            $nomobile = true;
            $unallowmobile = true;
        }


        $mobile = getgpc('mobile');
        $mobileflag = isset($this->var['mobiletpl'][$mobile]);
        if ($mobile === 'no') {
            Cookie::set('mobile', 'no', 3600);
            $nomobile = true;
        } elseif ($this->var['cookie']['mobile'] == 'no' && $mobileflag) {
            checkmobile();
            Cookie::set('mobile', '');
        } elseif ($this->var['cookie']['mobile'] == 'no') {
            $nomobile = true;
        } elseif (!($mobile_ = checkmobile())) {
            $nomobile = true;
        }
        if (!$mobile || $mobile == 'yes') {
            $mobile = isset($mobile_) ? $mobile_ : 2;
        }

        if (!$this->var['mobile'] && !$unallowmobile) {
            if ($mobileflag) {
                dheader("Location:misc.php?mod=mobile");
            }
        }

        if ($nomobile || (!$this->var['setting']['mobile']['mobileforward'] && !$mobileflag)) {
            if ($_SERVER['HTTP_HOST'] == $this->var['setting']['domain']['app']['mobile'] && $this->var['setting']['domain']['app']['default']) {
                dheader("Location:http://" . $this->var['setting']['domain']['app']['default'] . $_SERVER['REQUEST_URI']);
                return false;
            } else {
                return false;
            }
        }

        if (strpos($this->var['setting']['domain']['defaultindex'], CURSCRIPT) !== false && CURSCRIPT != 'forum' && !$_GET['mod']) {
            if ($this->var['setting']['domain']['app']['mobile']) {
                $mobileurl = 'http://' . $this->var['setting']['domain']['app']['mobile'];
            } else {
                if ($this->var['setting']['domain']['app']['forum']) {
                    $mobileurl = 'http://' . $this->var['setting']['domain']['app']['forum'] . '?mobile=yes';
                } else {
                    $mobileurl = $this->var['siteurl'] . 'forum.php?mobile=yes';
                }
            }
            dheader("location:$mobileurl");
        }
        if ($mobile === '3' && empty($this->var['setting']['mobile']['wml'])) {
            return false;
        }
        define('IN_MOBILE', isset($this->var['mobiletpl'][$mobile]) ? $mobile : '2');
        setglobal('gzipcompress', 0);

        $arr = array();
        foreach (array_keys($this->var['mobiletpl']) as $mobiletype) {
            $arr[] = '&mobile=' . $mobiletype;
            $arr[] = 'mobile=' . $mobiletype;
        }
        $arr = array_merge(array(strstr($_SERVER['QUERY_STRING'], '&simpletype'), strstr($_SERVER['QUERY_STRING'], 'simpletype')), $arr);
        $query_sting_tmp = str_replace($arr, '', $_SERVER['QUERY_STRING']);
        $this->var['setting']['mobile']['nomobileurl'] = ($this->var['setting']['domain']['app']['forum'] ? 'http://' . $this->var['setting']['domain']['app']['forum'] . '/' : $this->var['siteurl']) . $this->var['basefilename'] . ($query_sting_tmp ? '?' . $query_sting_tmp . '&' : '?') . 'mobile=no';

        $this->var['setting']['lazyload'] = 0;

        if ('utf-8' != CHARSET) {
            if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
                foreach ($_POST AS $pk => $pv) {
                    if (!is_numeric($pv)) {
                        $_GET[$pk] = $_POST[$pk] = $this->mobile_iconv_recurrence($pv);
                        if (!empty($this->var['config']['input']['compatible'])) {
                            $this->var['gp_' . $pk] = daddslashes($_GET[$pk]);
                        }
                    }
                }
            }
        }


        if (!$this->var['setting']['mobile']['mobilesimpletype']) {
            $this->var['setting']['imagemaxwidth'] = 224;
        }

        $this->var['setting']['regstatus'] = $this->var['setting']['mobile']['mobileregister'] ? $this->var['setting']['regstatus'] : 0;

        $this->var['setting']['thumbquality'] = 50;
        $this->var['setting']['avatarmethod'] = 0;

        $this->var['setting']['mobile']['simpletypeurl'] = array();
        $this->var['setting']['mobile']['simpletypeurl'][0] = $this->var['siteurl'] . $this->var['basefilename'] . ($query_sting_tmp ? '?' . $query_sting_tmp . '&' : '?') . 'mobile=1&simpletype=no';
        $this->var['setting']['mobile']['simpletypeurl'][1] = $this->var['siteurl'] . $this->var['basefilename'] . ($query_sting_tmp ? '?' . $query_sting_tmp . '&' : '?') . 'mobile=1&simpletype=yes';
        $this->var['setting']['mobile']['simpletypeurl'][2] = $this->var['siteurl'] . $this->var['basefilename'] . ($query_sting_tmp ? '?' . $query_sting_tmp . '&' : '?') . 'mobile=2';
        unset($query_sting_tmp);
        ob_start();
    }

    public function mobile_iconv_recurrence($value)
    {
        if (is_array($value)) {
            foreach ($value AS $key => $val) {
                $value[$key] = $this->mobile_iconv_recurrence($val);
            }
        } else {
            $value = diconv($value, 'utf-8', CHARSET);
        }
        return $value;
    }
}

