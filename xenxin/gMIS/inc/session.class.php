<?php
/* Session holder, handling users sessions in all apps
 * v0.1
 * wadelau@ufqi.com
 * Sat Jul 23 09:50:58 UTC 2011
 * Mon, 6 Mar 2017 22:19:03 +0800, implementing
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__."/inc/config.class.php");
require_once(__ROOT__."/inc/socket.class.php");
#require_once(__ROOT__."/inc/class.connectionpool.php");
require_once(__ROOT__."/inc/zeea.class.php");

class SESSIONX {
	 
    var $sid = '';
    var $sshm = array(); # session data holder
    const gMIS_Sid = 'gMIS-Sid';
    const gMIS_Sid2 = 'sid';
    const Data_Sep = '';
    const Uid_Tag = 'ui';
    var $Session_Private_Key = '';
    const Sign_Length = 19;
    const Ibase_16 = 16;
    const Zip_Type = 'compress';
    var $plaindata = '';
    
 	//- construct
	function __construct($args){
		//-
		$this->Session_Private_Key = Gconf::get('sign_key');
		// @todo
	}

	//-
	function __destruct(){
	    //- @todo
	}
	# @Todo: to be implemented when need 2nd standalone appserver

	//-
	function get($k){
	    
	}
 	
	//- set
	function set($k, $v){
	    
	}
	
	//- del
	function del($k){
	    
	}
	
	//- get sid
	function getSid($reqt){
	    $sid = '';
	    $sidtag = self::gMIS_Sid;
	    $sidtag2 = self::gMIS_Sid2;
		# imprv4ipv6
        global $_CONFIG;
        if($_CONFIG['is_ipv6'] == 1){
            $sidtag .= 'v6';
        }
	    if(isset($_COOKIE[$sidtag])){
	        $sid = $_COOKIE[$sidtag];
	    }
	    if($sid == '' && isset($reqt[$sidtag2])){
	        $sid = $reqt[$sidtag2];
	    }
	    $this->sid = $sid;
	    #debug(__FILE__.": getSid: read sid:$sid");
	    return $this->sid;
	}
	
	//- get plain text data
	function getData(){
	    return $this->plaindata;
	}
	
	//-
	function generateSid($user, $reqt){
	    $sid = '';
	    # string=a1=b1:::a2=b2;
	    # string=base62x(encrypt(zip(string)));
	    # string=string+md5(string+ip+ua+KEY+date)
	    $sep = self::Data_Sep;
	    $params = $user->getId();
	    $data = $this->_getSignData($params);
	    $md5sum = md5($data);
	    $origsid = $sid = $params.$sep.$this->_md5Remdy($md5sum);
	    $hm = ZeeA::zip($sid, $args=array('ziptype'=>self::Zip_Type));
	    if($hm[0]){ $sid = $hm[1]; }
	    $hm = ZeeA::encode($sid);
	    if($hm[0]){ $sid = $hm[1]; }
	    $this->sid = $sid;
	    #debug(__FILE__.": origsid:$origsid genSid-aft:$sid");
	    return $this->sid;
	}
	
	//- chk sid
	function chkSid($user, $reqt){
	    # string=a1=b1:::a2=b2; 
	    # string=base62x(encrypt(zip(string))); 
	    # string=string+md5(string+ip+ua+KEY+date) 
	    $rtn = false;
	    $sep = self::Data_Sep;
	    $key = $this->Session_Private_Key;
	    $slen = self::Sign_Length;
	    $mySid = $this->getSid($reqt);
	    if($mySid == null || $mySid == ''){
	        $rtn = false;
	        $this->sid = '';
	    }
	    else{
	       $hm = ZeeA::decode($mySid);
	       if($hm[0]){ $sid = $hm[1]; }
	       $hm = ZeeA::unzip($sid, $args=array('ziptype'=>self::Zip_Type));
	       if($hm[0]){ $sid = $hm[1]; }
	       $params = substr($sid, 0, strlen($sid)-$slen);
	       #debug(__FILE__.": read cki-aft-decode sid:[$sid] param:[$params] from [$mySid]");
	       $data = $this->_getSignData($params);
	       $md5sum = substr($sid, strlen($sid)-$slen);
	       $md5sum2 = $this->_md5Remdy(md5($data));
	       if($md5sum == $md5sum2){
	           # valid
	           $this->plaindata = $params;
	           $rtn = true;
	           #debug(__FILE__.": matched SUCC! md5:[$md5sum] md52:[$md5sum2]");
	       }
	       else{
	           # invalid
	           debug(__FILE__.": unmatched md5:[$md5sum] md5-2:[$md5sum2]");
	       }
	    }
	    return $rtn;
	}
	
	//-
	public function generateVerifyId(){
	    $letters = str_split('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz._,');
	    $llen = count($letters) - 1;
	    $charc = 100;
	    $verifycode = '';
	    for($i=0; $i<$charc; $i++){
	        $verifycode .= $letters[rand(0, $llen)];
	    }
	    return $verifycode;
	}
	
	//-
	private function _getSignData($params){
	    $sep = self::Data_Sep;
	    $key = $this->Session_Private_Key;
	    $data = $params.$sep.Wht::getIp().$sep.$_SERVER['HTTP_USER_AGENT']
	       .$sep.$key.$sep.date("Y-m-d", time());
	    return $data;
	    
	}
	
	//-
	private function _md5Remdy($md5){
	    return substr($md5, 1, self::Sign_Length);
	}
	
 }
?>
