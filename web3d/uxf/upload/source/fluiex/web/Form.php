<?php

namespace fluiex\web;

/**
 * 表单相关工具类
 */
class Form
{
    /**
     * 生成表单hash
     * @global array $_G
     * @param string $specialadd
     * @return string
     */
    public static function hash($specialadd = '')
    {
        global $_G;

        $hashadd = defined('IN_ADMINCP') ? 'Only For Discuz! Admin Control Panel' : '';
        return substr(md5(substr($_G['timestamp'], 0, -7) . $_G['username'] . $_G['uid'] . $_G['authkey'] . $hashadd . $specialadd), 8, 8);
    }

    public static function checkPeriods($periods, $showmessage = 1)
    {
        global $_G;
        if (($periods == 'postmodperiods' || $periods == 'postbanperiods') && ($_G['setting']['postignorearea'] || $_G['setting']['postignoreip'])) {
            if ($_G['setting']['postignoreip']) {
                foreach (explode("\n", $_G['setting']['postignoreip']) as $ctrlip) {
                    if (preg_match("/^(" . preg_quote(($ctrlip = trim($ctrlip)), '/') . ")/", $_G['clientip'])) {
                        return false;
                        break;
                    }
                }
            }
            if ($_G['setting']['postignorearea']) {
                $location = $whitearea = '';
                require_once libfile('function/misc');
                $location = trim(convertip($_G['clientip'], "./"));
                if ($location) {
                    $whitearea = preg_quote(trim($_G['setting']['postignorearea']), '/');
                    $whitearea = str_replace(array("\\*"), array('.*'), $whitearea);
                    $whitearea = '.*' . $whitearea . '.*';
                    $whitearea = '/^(' . str_replace(array("\r\n", ' '), array('.*|.*', ''), $whitearea) . ')$/i';
                    if (@preg_match($whitearea, $location)) {
                        return false;
                    }
                }
            }
        }
        if (!$_G['group']['disableperiodctrl'] && $_G['setting'][$periods]) {
            $now = dgmdate(TIMESTAMP, 'G.i', $_G['setting']['timeoffset']);
            foreach (explode("\r\n", str_replace(':', '.', $_G['setting'][$periods])) as $period) {
                list($periodbegin, $periodend) = explode('-', $period);
                if (($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($periodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
                    $banperiods = str_replace("\r\n", ', ', $_G['setting'][$periods]);
                    if ($showmessage) {
                        showmessage('period_nopermission', NULL, array('banperiods' => $banperiods), array('login' => 1));
                    } else {
                        return TRUE;
                    }
                }
            }
        }
        return FALSE;
    }

}
