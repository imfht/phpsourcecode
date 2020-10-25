<?php
if(!defined('__ROOT__')){
    define('__ROOT__', dirname(dirname(__FILE__)));
}
require_once(__ROOT__.'/inc/webapp.class.php');
require_once(__ROOT__.'/mod/base62x.class.php');

class Poll extends WebApp{

    # global variables

	# construct
	function __construct(){
     
		if($_SESSION['language'] && $_SESSION['language'] == "en_US"){
			# disable multiple languages for now
            $this->setTbl(GConf::get('tblpre').'polltbl');
		}
		else{
			$this->setTbl(GConf::get('tblpre').'polltbl');
		}
		
		parent::__construct();
		 
    }

    function md5B62x($s){
        $md5 = md5($s);
        $b62x = $this->base62x(substr($md5,0,11), 0, '16').base62x(substr($md5,11,11), 0, '16').base62x(substr($md5,22,11), 0, '16');
        #print substr($md5,0,11)."--".substr($md5,11,11).'--'.substr($md5,22,11);
        return $b62x;
    }


    function base62x($s,$dec=0,$numType=''){
        # e.g. base62x('abcd', 0, '8');
        # e.g. base62x('abcd', 1, '16');
        $type = "-enc";
        if($dec == 1){
            $type = "-dec";
        }
        $s2 = '';
        if($type == "-enc"){
            $s2 = Base62x::encode($s, $numType);
        }
        else{
            $s2 = Base62x::decode($s, $numType);
        }
        return $s2;
    }

 	# get latest item
	function getLatestList(){
		$hm = array();
		$this->set('orderby', 'id desc');	
		$this->set('pagesize', '15');	
		$hm = $this->getBy('*', '');
		if($hm[0]){
			$hm = $hm[1];	
		}
		
		return $hm;

	}


}
?>
