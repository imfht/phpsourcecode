<?php
/*
 * refined with non-storage image verification,
 * Xenxin@ufqi.com, Tue, 7 Mar 2017 22:04:07 +0800
 */

require("../comm/header.inc.php");

$_REQUEST['tbl'] = $_CONFIG['tblpre'].'info_usertbl';

$act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$tbl = $_REQUEST['tbl'];
#$mydb = $_CONFIG['appname'].'db'; # defined in comm/header
$db = $_REQUEST['db']==''?$mydb:str_replace('<', '&lt;', $_REQUEST['db']);
$field = $_REQUEST['field'];
$url = $_SERVER['PHP_SELF']."?bkl=".str_replace('<', '&lt;', $_REQUEST['bkl']);

$smttpl = getSmtTpl(__FILE__, $act);
$islan = false;
$myip = Wht::getIp();
if(cidr_match($myip, '10.0.0.0/8') || cidr_match($myip, '172.16.0.0/12')
        || cidr_match($myip, '192.168.0.0/16')){
            $islan = true;
}

if($act == 'signin'){
    $smt->assign('action',$url.'&act=dosignin');
    $smt->assign('title', $lang->get('user_sign_in'));
	$smt->assign('bkl', $_REQUEST['bkl']);
	$smt->assign('verifyid', $user->getVerifyId());
	$smt->assign('islan', $islan);
    //- @todo: workspace list, see inc/config
    //- list workspace options when the list.length > 1
}
else if($act == 'dosignin'){
    $issucc = false;
    $nexturl = '';
    $verifycode = $verifycode2 = '';
    if(!$islan){
        $verifycode = strtolower(trim($_REQUEST['verifycode']));
        $verifycode2 = substr($md5=Base62x::encode(md5(($i=$_REQUEST['verifyid'])
                .$_CONFIG['sign_key']
                .($d=substr(date('YmdHi', time()-date('Z')),0,11))
                ).$_CONFIG['appchnname']), 1, 4); # same as comm/imagex
        $verifycode2 = strtolower($verifycode2);
    }
    if($islan || ($verifycode != '' && $verifycode == $verifycode2)){
         # verified bgn
    $user->set('email',$_REQUEST['email']);
    $user->set('password',$_REQUEST['password']);
    $hm = $user->getBy("*", "email=? and istate=1");
    $result = '';
    if($hm[0]){
        $hm = $hm[1][0]; # refer to /inc/dba.class.php for the return data structure
        if($hm['password'] == SHA1($user->get('password'))){
            $user->setId($hm['id']);
            $_CONFIG[UID] = $user->getId();
            $userid = $_CONFIG[UID];
            $sid = $user->getSid($_REQUEST);
			# imprv4ipv6
			#debug("extra/signinup: ipv6:[".$_CONFIG['is_ipv6']."]");
            if($_CONFIG['is_ipv6'] == 1){
                $user->set('sid_tag', $user->get('sid_tag').'v6');
            }
            # issue a valid ticket for this session
            # based on user, time, ip, browser and so on.
            $ckirtn = setcookie($ckiname=$user->get('sid_tag'), $ckivalue=$sid,
				time()+60*60*24, '/'); # 24 hrs, full site;
            $result .= '<br/><br/>很好! 登录成功！ 欢迎回来, '.$user->getEmail()." !";
			$bkl = Base62x::decode($_REQUEST['bkl']);
            if($bkl != ''){
                //- go to $thisurl
				$nexturl = $bkl;
            }
            else{
                //-
                $nexturl = $rtvdir."/";
            }
            $nexturl .= inString('?', $nexturl) ? '&' : '?';
            $nexturl .= SID."=".$sid;
            $issucc = true;
        }
        else{
            $result .= "login failed [账号/密码错误]. 1201302219. <!-- new:["
                    .SHA1($user->get('password'))."] -->";
        }
    }
    else{
        $result .= "login failed [账号/密码错误]. 1201302217."; 
        #error_log("login failed [账号/密码错误]. 1201302217. email:[".$_REQUEST['email']."] pwd:[".$_REQUEST['password']."] opwd:[".$hm['password']."]"." sha-1:[".SHA1($user->get('password'))."] hm:[".serialize($hm)."]");
    }
        # verified end
    }
    else{
        $result .= "login failed [验证码错误]. 1309151658.";
    }

    if(!$issucc){
        $nexturl = $url."&act=signin";
    }
    $smt->assign('title', $lang->get('user_sign_in_resp'));
    $smt->assign('result', $result);
    $smt->assign('nexturl', $nexturl);
}
else if($act == 'signout'){
    $user->setId('');
    $_CONFIG[UID] = $user->getId();
    $userid = $_CONFIG[UID];
    $smt->assign('result', $result = '成功退出系统, 欢迎下次再来.');
    $smt->assign('nexturl', $nexturl = $url.'&act=signin');
}
else if($act == 'resetpwd'){
    if($userid == Wht::get($_REQUEST, 'userid')){
        $issubmit = Wht::get($_REQUEST, 'issubmit');
        if($issubmit == 1){
            $newpwd = sha1($newpwdp=Wht::get($_REQUEST, 'newpwd'));
            $user->execBy("update ".$_CONFIG['tblpre']."info_usertbl set password='".$newpwd."' where id='"
                    .$userid."' limit 1", null);
            $result = "成功！ 用户 [userid:".$userid."] 的密码已经重置为:[".$newpwdp."].";
            $nexturl = $ido."&tbl=".$_CONFIG['tblpre']."info_usertbl&tit=&db=";
        }
        else{
            $nexturl = $ido."&tbl=".$_CONFIG['tblpre']."info_usertbl&tit=&db=";
            $result = "";
            $result .= " Loading.... <script type=\"text/javascript\">var newpwd=window.prompt('请输入新密码','');"
                    ."if(newpwd!=''&&newpwd!=null){window.top.location.href='".$rtvdir."/extra/signupin.php?act=resetpwd&userid="
                    .$userid."&issubmit=1&newpwd='+newpwd;}else{document.location.href='".$nexturl."';}</script>";
            $result .= "失败！ 重置密码失败，请重试. 201205092158.";
        }
    }
    else if($user->getGroup() == 1){ # admin group
        $newpwd = $newpwd_orig = rand(0,999).rand(0,999);
        $newpwd = SHA1($newpwd);
        $newuserid = $_REQUEST['userid'];
        if($newuserid != ''){
            $user->execBy("update ".$_CONFIG['tblpre']."info_usertbl set password='".$newpwd."' where id='"
                    .$newuserid."' limit 1", null);
            $result = "成功！ 用户 [userid:".$newuserid."] 的密码已经重置为:[".$newpwd_orig."].";
            $nexturl = $ido."&tbl=".$_CONFIG['tblpre']."info_usertbl&tit=&db=";
        }else{
            $result = "失败！ 重置密码失败，请重试. 201204291947.";
            $nexturl = $ido."&tbl=".$_CONFIG['tblpre']."info_usertbl&tit=&db=";
        }
    }
    else{
        $result = "失败！ 重置密码失败，请重试. 201204292008.";
        $nexturl = $ido."&tbl=".$_CONFIG['tblpre']."info_usertbl&tit=&db=";
    }
    $smt->assign('result', $result);
    $smt->assign('nexturl', $nexturl);
}
else if($act == 'checkverifycode'){
    #
    $verifyResult = false;
    $verifycode = $verifycode2 = '';
    if(true){
        $verifycode = strtolower(trim($_REQUEST['verifycode']));
        $verifycode2 = substr($md5=Base62x::encode(md5(($i=$_REQUEST['verifyid'])
                .$_CONFIG['sign_key']
                .($d=substr(date('YmdHi', time()-date('Z')),0,11))
                ).$_CONFIG['appchnname']), 1, 4); # same as comm/imagex
        $verifycode2 = strtolower($verifycode2);
    }
    if($verifycode != '' && $verifycode == $verifycode2){
        $verifyResult = true;
    }

    if($fmt == 'json'){
        $smttpl = '';
        $data['respobj'] = array('vfc'=>$verifycode, 'verifyresult'=>$verifyResult);
    }
}
else{
    $out .= "Oooops! Unknown act:[$act]. 1809060952.";
}

$smt->assign('rtvdir', $rtvdir);

# private ip identified, 20:50 07 December 2016
function cidr_match($ip, $range){
    list ($subnet, $bits) = explode('/', $range);
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
    return ($ip & $mask) == $subnet;
}

require("../comm/footer.inc.php");

?>
