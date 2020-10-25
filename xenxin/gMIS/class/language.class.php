<?php

/* Language class
 * v0.1,
 * wadelau@{ufqi, hotmail, gmail}.com
 * Sat Oct 26 03:15:37 UTC 2019
 */

if(!defined('__ROOT__')){
    define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__.'/inc/webapp.class.php');

class Language extends WebApp{

    const SID = 'sid';
	const LANG_PREFIX = 'language.pkg';
	const LANG_SUFFIX = 'json';
	const LogTag = "mod/Language ";
    var $ver = 0.01;
    var $fieldList = array();
	var $language = 'en';
	var $country = 'US';
	var $langDir =  '';                
	var $langList = array();
    
    #
    public function __construct($args=null){
        #$this->setTbl(GConf::get('tblpre').'mytbl');
        //- init parent
        parent::__construct($args);
        $this->currTime = date("Y-m-d H:i:s", time());
		$this->langDir = GConf::get('languagedir');

		if($args  == null){ $args = array(); }
		if(array_key_exists('language', $args)){
			$this->language = $args['language'];	
		}
		if(array_key_exists('country', $args)){
			$this->country = $args['country'];	
		}
		else{
			$this->country = $this->getDefaultCountryByLang($this->language);	
		}
		$langFile = __ROOT__.'/'.$this->langDir.'/'.self::LANG_PREFIX.'.'.$this->language.'_'.$this->country.'.'.self::LANG_SUFFIX;
		$contents = file_get_contents($langFile);
		if($contents == ''){ $contents = '{}'; }
		$this->langList = json_decode($contents, true);
		#debug(self::LogTag.'lang:'.$this->language.' coun:'.$this->country.' langlist:'.serialize($this->langList));
		# save $langList in cache for fast access next time?
		# @todo
    }

    # 
    public function __destruct(){
        $this->langList = null;
    }

    # public methods
    # get value by key
	public function get($k, $noExtra=null){
		$v = '';
		if(array_key_exists($k, $this->langList)){
			$v = $this->langList[$k];	
		}
		else{
			$v = $k;
			debug(self::LogTag.': get: unknown key:'.$k);
		}
		return $v;
	}

	#
	public function getTag(){
		return $this->language.'_'.$this->country;	
	}

    # private methods
    # get default country by lang
	private	function getDefaultCountryByLang($lang){
		$country = '';
		$lang2country = array('zh'=>'CN', 'en'=>'US', 
			'ja'=>'JP', 'ko'=>'KR', 'fr'=>'FR');
		if(array_key_exists($lang, $lang2country)){
			$country = $lang2country[$lang];	
		}
		else{
			$country = $this->country;	
		}
		return $country;
	}
    
    #
    private function _sayHi(){
        $rtn = '';
		# @todo
        return $rtn;
    }

}
?>
