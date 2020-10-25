<?php

/**
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Client
{
    ////获得访客浏览器类型
    public function getBrowser()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $br = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/MSIE/i', $br)) {
                $br = 'MSIE';
            } elseif (preg_match('/Firefox/i', $br)) {
                $br = 'Firefox';
            } elseif (preg_match('/Chrome/i', $br)) {
                $br = 'Chrome';
            } elseif (preg_match('/Safari/i', $br)) {
                $br = 'Safari';
            } elseif (preg_match('/Opera/i', $br)) {
                $br = 'Opera';
            } else {
                $br = 'Other';
            }
        }
        return $br;
    }

    public function getIp()
    {
        if (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = "未知IP";
        }
        return $ip;
    }

    public function getLang()
    {
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $lang = substr($lang, 0, 5);
            if (preg_match("/zh-cn/i", $lang)) {
                $lang = "简体中文";
            } elseif (preg_match("/zh/i", $lang)) {
                $lang = "繁体中文";
            } else {
                $lang = "English";
            }
            return $lang;
        }
    }

    public function getOs()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $OS = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/win/i', $OS)) {
                $OS = 'Windows';
            } elseif (preg_match('/mac/i', $OS)) {
                $OS = 'MAC';
            } elseif (preg_match('/linux/i', $OS)) {
                $OS = 'Linux';
            } elseif (preg_match('/unix/i', $OS)) {
                $OS = 'Unix';
            } elseif (preg_match('/bsd/i', $OS)) {
                $OS = 'BSD';
            } else {
                $OS = '未知系统';
            }
            return $OS;
        }
    }

    public function getAdd()
    {
        if ($this->getIp() == "未知IP") {
            $add = "未知地址";
        } else {
            $ipadd = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' .
                $this->getIp()); //根据新浪api接口获取
            if (!empty($ipadd)) {
                $ipadd = json_decode($ipadd);
                if (!is_object($ipadd) or $ipadd->ret == '-1') {
                    $add = '未知地址';
                } elseif (is_object($ipadd) and $ipadd->ret <> '-1') {
                    $add = $ipadd->province . $ipadd->isp;
                } else {
                    $add = '未知地址';
                }
            } else {
                $add = '未知地址';
            }
        }
        return $add;
    }

    public function getViewMethod()
    {
        if (strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML") > 0) {
            $msg = 'wap';
        } elseif (preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i',
        $_SERVER['HTTP_USER_AGENT'])) {
            $msg = 'wap';
        } else {
            $msg = 'web';
        }
        return $msg;
    }

    public function getRef()
    {
        $Urs = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '未知来源';
        return $Urs;
    }

    public function getQuery()
    {
        if (isset($_SERVER["QUERY_STRING"])) {
            $uquery = addslashes($_SERVER["QUERY_STRING"]);
        } else {
            $uquery = null;
        }
        return $uquery;
    }

    public function getAgent()
    {
        if (isset($_SERVER["HTTP_USER_AGENT"])) {
            $uagent = addslashes($_SERVER["HTTP_USER_AGENT"]);
        } else {
            $uagent = "未知终端";
        }
        return $uagent;
    }

    public function getAll()
    {
        $client = array();
        $client["ip"] = $this->getIp();
        $client["browser"] = $this->getBrowser();
        $client["lang"] = $this->getLang();
        $client["os"] = $this->getOs();
        $client["add"] = $this->getAdd();
        $client["ver"] = $this->getViewMethod();
        $client["referer"] = $this->getRef();
        $client["query"] = $this->getQuery();
        $client["agent"] = $this->getAgent();
        return $client;
    }
}
?>