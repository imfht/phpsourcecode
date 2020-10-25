<?php
/* 
 * Zip/Encrypt/Encode (ZEE), with its unzip, decrypt and decode
 * Administator of,
 * v0.1
 * wadelau@ufqi.com fro GWA2
 * Sat, 24 Dec 2016 22:08:30 +0800
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__."/inc/config.class.php");
require_once(__ROOT__."/class/base62x.class.php");

class ZeeA {
	
    //- constants
    const Type_Gzip = 'gzip';
    const Type_Deflate = 'deflate';
    const Type_Compress = 'compress';
    const Type_Identity = 'identity';
    const Type_Br = 'br';
    const Type_Encode_Base62x = 'base62x';
    const Type_Encode_Base64 = 'base64';
    
    //- variables
    var $conf = null;
    
 	//- construct
	public function __construct($zeeConf=null){
		//- run as first access
		# @todo
		
	}

	//- desctruct
	public function __destruct(){
	    $this->close();
	}
	
	//- methods, public
	
	//-
	//- zip, with most kinds of algorithms, gzip, compress, deflate, br
	//-- 08:58 24 December 2016
	public static function zip($str, $args=null){
		$rtn = array();
		$unkn = 'unkn';
		$ztype = ''; # gzip, compress, defalte, identity, br
		if($args != null){
			if(isset($args['ziptype'])){
				$ztype = strtolower($args['ziptype']);
			}
		}
		$ns = '';
		$ztype = $ztype=='' ? self::Type_Gzip : $ztype;
		if($ztype == self::Type_Gzip){
			$ns = gzencode($str);
		}
		else if($ztype == self::Type_Deflate){
			$ns = gzdeflate($str);
		}
		else if($ztype == self::Type_Compress){
			$ns = gzcompress($str);
		}
		else{
			$ns = false;
			debug(__FILE__.": unknown ztype:[$ztype], let it be. 1612240909.");
		}
		if($ns !== false){
			$rtn = array(true, $ns);
		}
		else{
			$rtn = array(false, $str);
		}
        #debug(__FILE__.": zip: osize:[".strlen($str)."] nsize:[".strlen($ns)."]");
		return $rtn;
	}
	
	//- unzip most kinds of algorithms, gzip, compress, deflate, br
	//- https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Encoding
	//- Xenxin@ufqi.com Thu, 21 Dec 2016 22:24:42 +0800
	public static function unzip($str, $args=null){
	    $rtn = array();
	    $unkn = 'unkn';
	    $ztype = ''; # gzip, compress, defalte, identity, br
	    if($args != null){
	        if(isset($args['header'])){
	            $headers = $args['header'];
	            foreach($headers as $k=>$v){
	                if(strpos($v, 'Content-Encoding') !== false){
	                    if(stripos($v, self::Type_Gzip) !== false){
	                        $ztype = self::Type_Gzip;
	                        break;
	                    }
	                    else if(stripos($v, self::Type_Deflate) !== false){
	                        $ztype = self::Type_Deflate;
	                        break;
	                    }
	                    else if(stripos($v, self::Type_Compress) !== false){
	                        $ztype = self::Type_Compress;
	                        break;
	                    }
	                    else{
	                        $ztype = $unkn;
	                    }
	                }
	                else if($ztype == $unkn){
	                    break;
	                }
	            }
	        }
            if(isset($args['ziptype'])){
				$ztype = strtolower($args['ziptype']);
			}
	    }
	    $ztype = $ztype=='' ? self::Type_Gzip : $ztype;
	    $ns = '';
	    if($ztype == self::Type_Gzip){
	        $ns = gzdecode($str);
	    }
	    else if($ztype == self::Type_Deflate){
	        $ns = gzinflate($str);
	    }
	    else if($ztype == self::Type_Compress){
	        $ns = gzuncompress($str);
	    }
	    else{
	        $ns = false;
	        #debug(__FILE__.": unknown ztype:[$ztype], let it be. 1612222232.");
	    }
	    if($ns !== false){
	        $rtn = array(true, $ns);
	    }
	    else{
	        $rtn = array(false, $str);
	    }
	    #debug(__FILE__.": ztype:$ztype size_orig:[".strlen($str)."] size_rtn:[".strlen($rtn[1])."]", '',
	    #        'file:/www/log/bin/offersync/log/ou_offer_sync_');
	    return $rtn;
	}
	
	//-
	//- encode By Base62x, base64
	//- Xenxin, 10:59 24 December 2016
	public static function encode($str, $args=null){
		$rtn = array();
		$etype = '';
		if($args != null){
			if(isset($args['encodetype'])){
				$etype = strtolower($args['encodetype']);
			}
		}
		$etype = $etype=='' ? self::Type_Encode_Base62x : $etype;
		$ns = '';
		if($etype == self::Type_Encode_Base62x){
			$ns = Base62x::encode($str);
		}
		else if($etype == self::Type_Encode_Base64){
			$ns = base64_encode($str);
		}
		else{
			$ns = false;
		}
		if($ns !== false){
			$rtn = array(true, $ns);
		}
		else{
			$rtn = array(false, $str);
		}
        #debug(__FILE__.": encode: osize:[".strlen($str)."] nsize:[".strlen($ns)."]");
		return $rtn;
	}
	
	//-
	//- decode By Base62x, base64
	//- Xenxin, 11:19 24 December 2016
	public static function decode($str, $args=null){
		$rtn = array();
		$etype = '';
		$unkn = 'unkn';
		if($args != null){
			if(isset($args['encodetype'])){
				$etype = strtolower($args['encodetype']);
			}
		}
		$etype = $etype=='' ? self::Type_Encode_Base62x : $etype;
		$ns = '';
		if($etype == self::Type_Encode_Base62x){
			$ns = Base62x::decode($str);
		}
		else if($etype == self::Type_Encode_Base64){
			$ns = base64_decode($str);
		}
		else{
			$ns = false;
		}
		if($ns !== false){
			$rtn = array(true, $ns);
		}
		else{
			$rtn = array(false, $str);
		}
		return $rtn;
	}
	
	//-
	//- encrypt
	public static function encrypt($str, $args=null){
		$rtn = array();
		# @todo
		
		return $rtn;
	}
	
	//-
	//- decrypt
	public static function decrypt($str, $args=null){
		$rtn = array();
		# @todo
		return $rtn;
	}
	
	//- base62x numeric
	public static function base62xNenc($str, $base){
	    $rtn = '';
	    $rtn = Base62x::encode($str, $base);
	    return $rtn;
	}
	
	//-
	//-
	public static function base62xNdec($str, $base){
	    $rtn = '';
	    $rtn = Base62x::decode($str, $base);
	    return $rtn;
	}
	
	//- close
	public function close(){
	    //- @todo
	    # need sub class override.
	}
	
	//-- methods, private
	//-
	private function _somethingElse(){
	    $rtn = 0;
	    # @todo
	    return $rtn;
	}
	
 }
?>
