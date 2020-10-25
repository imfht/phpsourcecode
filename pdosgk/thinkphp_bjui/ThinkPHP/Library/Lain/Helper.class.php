<?php

namespace Lain;

/** 
 * @author Lain
 * 
 */
class Helper {
    public static function getBrowser()
    {
        if(empty($_SERVER['HTTP_USER_AGENT'])) return 'unknow';
    
        $agent = $_SERVER["HTTP_USER_AGENT"];
        if(strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0'))
        {
            return "ie";
        }
        else if(strpos($agent, 'Firefox') !== false)
        {
            return "firefox";
        }
        else if(strpos($agent, 'Chrome') !== false)
        {
            return "chrome";
        }
        else if(strpos($agent, 'Opera') !== false)
        {
            return 'opera';
        }
        else if((strpos($agent, 'Chrome') == false) && strpos($agent, 'Safari') !== false)
        {
            return 'safari';
        }
        else
        {
            return 'unknown';
        }
    }
    
    /**
     * Get browser version.
     *
     * @access public
     * @return string
     */
    public static function getBrowserVersion()
    {
        if(empty($_SERVER['HTTP_USER_AGENT'])) return 'unknow';
    
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs))
        {
            return $regs[1];
        }
        else if(preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs))
        {
            return $regs[1];
        }
        else if(preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs))
        {
            return $regs[1];
        }
        else if(preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs))
        {
            return $regs[1];
        }
        else if((strpos($agent,'Chrome') == false) && preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs))
        {
            return $regs[1];
        }
        else if(preg_match('/rv:(\d+)\..*/i', $agent, $regs))
        {
            return $regs[1];
        }
        else
        {
            return 'unknow';
        }
    }
    
    /**
     * Get client os from agent info.
     *
     * @static
     * @access public
     * @return string
     */
    public static function getOS()
    {
        if(empty($_SERVER['HTTP_USER_AGENT'])) return 'unknow';
    
        $osList = array(
                '/windows nt 10/i'      =>  'Windows 10',
                '/windows nt 6.3/i'     =>  'Windows 8.1',
                '/windows nt 6.2/i'     =>  'Windows 8',
                '/windows nt 6.1/i'     =>  'Windows 7',
                '/windows nt 6.0/i'     =>  'Windows Vista',
                '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                '/windows nt 5.1/i'     =>  'Windows XP',
                '/windows xp/i'         =>  'Windows XP',
                '/windows nt 5.0/i'     =>  'Windows 2000',
                '/windows me/i'         =>  'Windows ME',
                '/win98/i'              =>  'Windows 98',
                '/win95/i'              =>  'Windows 95',
                '/win16/i'              =>  'Windows 3.11',
                '/macintosh|mac os x/i' =>  'Mac OS X',
                '/mac_powerpc/i'        =>  'Mac OS 9',
                '/linux/i'              =>  'Linux',
                '/ubuntu/i'             =>  'Ubuntu',
                '/iphone/i'             =>  'iPhone',
                '/ipod/i'               =>  'iPod',
                '/ipad/i'               =>  'iPad',
                '/android/i'            =>  'Android',
                '/blackberry/i'         =>  'BlackBerry',
                '/webos/i'              =>  'Mobile'
        );
    
        foreach ($osList as $regex => $value)
        {
            if(preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) return $value;
        }
    
        return 'unknown';
    }
    
    /**
     * Get remote ip.
     *
     * @access public
     * @return string
     */
    public static function getRemoteIp()
    {
        $ip = '';
        if(!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
    
        if(strpos($ip, ',') !== false)
        {
            $ipList = explode(',', $ip);
            $ip = $ipList[0];
        }
    
        return $ip;
    }
    
    /**
     * check ip is in network.
     *
     * @param  string $ip
     * @param  string $network
     * @access public
     * @return void
     */
    public static function checkIpScope($ip, $network)
    {
        if(strpos($network, '/') === false) return $ip == $network;
    
        $ip = (double) (sprintf("%u", ip2long($ip)));
        $s  = explode('/', $network);
        $networkStart = (double) (sprintf("%u", ip2long($s[0])));
        $networkLen = pow(2, 32 - $s[1]);
        $networkEnd = $networkStart + $networkLen - 1;
    
        if ($ip >= $networkStart && $ip <= $networkEnd)
        {
            return true;
        }
        return false;
    }
    
    /**
     * Check ip avaliable.
     *
     * @param  string $ip
     * @access public
     * @return bool
     */
    public static function checkIP($ip)
    {
        $ip = trim($ip);
        if(strpos($ip, '/') !== false)
        {
            $s = explode('/', $ip);
            preg_match('/^(((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3})$/', $s[0], $matches);
            if(!empty($matches) and $s[1] > 0 and $s[1] < 36) return true;
        }
        else
        {
            preg_match('/^(((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3})$/', $ip, $matches);
            if(!empty($matches)) return true;
        }
        return false;
    }
    
    /**
     * Create random string.
     *
     * @param  int    $length
     * @param  string $skip A-Z|a-z|0-9
     * @static
     * @access public
     * @return void
     */
    public static function createRandomStr($length, $skip = '')
    {
        $str  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $skip = str_replace('A-Z', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', $skip);
        $skip = str_replace('a-z', 'abcdefghijklmnopqrstuvwxyz', $skip);
        $skip = str_replace('0-9', '0123456789', $skip);
        for($i = 0; $i < strlen($skip); $i++)
        {
        $str = str_replace($skip[$i], '', $str);
        }
    
        $strlen = strlen($str);
        while($length > strlen($str)) $str .= $str;
    
        $str = str_shuffle($str);
        return substr($str,0,$length);
    }
    
}

?>