<?php
/* GTbl class for general management information system
 * v0.1,
 * wadelau@ufqi.com,
 * Mon Jan 23 02:58:52 GMT 2012
 * note: general table config transactions, created on 20090304, wadelau@hotmail.com, updated....
 * v0.2, Sat Apr  7 09:53:53 CST 2012
 * Mon Jul 28 15:39:14 CST 2014
 * javascript async, 19:12 Thursday, 15 March, 2018
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__.'/inc/webapp.class.php');
require_once(__ROOT__.'/inc/config.class.php');

class GTbl extends WebApp{
	
	public $sep = '';
	private $hmfield = array();
	private $hmconf = array();
	private $tbl = "";
	private $prttbl = "";
	private $rotatetag = '';
	private $tblrotate = '';
	public $taglist = array(
			'table'=>'table',
			'field'=>'field',
			'chnname'=>'chnname',
			'inputtype'=>'inputtype',
			'selectoption'=>'selectoption',
			'extrainput'=>'extrainput',
			'displayorder'=>'displayorder', # disable since Thu Jan 26 04:09:32 GMT 2012
			'memo'=>'memo',
			'charset'=>'charset',
			'dbname'=>'dbname',
			'relatedref'=>'relatedref', # related functions of a table
			'listfieldcount'=>'listfieldcount', # how many fields display in list view
			'listview'=>'listview', # hide in list view or not
			'singlerow'=>'singlerow', # display in a single row
			'printref'=>'printref',
			'reftable'=>'reftable',
			'jsaction'=>'jsaction',
			'delayjsaction'=>'delayjsaction',
			'check'=>'check', # business check logic...
			'orderby'=>'orderby', # explicitly specify a field for ordering...
			'defaultvalue'=>'defaultvalue', # default value during add/modify...
			'managemode'=>'managemode', # managemode for a table, r(read),w(write),d(delete)
			'accept'=>'accept', # front-end validator, e.g. "lt=100,gt=1000"
			'trigger'=>'trigger', #  trigger sth when meeting some prequisters
			'readonly' => 'readonly', # some fields do not need input by users, but by programs
			'href' => 'href', # href of a field
			'selectmultiple' => 'selectmultiple', # is multiple of a select
			'hidesk' => 'hidesk', # default search key
			'css' => 'css', # css of a field or the table
			'superaccess' => 'superaccess', # access control over system settings
			'stat' => 'stat', # computing methods: sum|count|average
			'input2select' => 'input2select', # filter much more select options to a few of them...., Mon Jul 28 15:12:17 CST 2014
			'rotatespan'=>'rotatespan', # table names contains variable datetime, e.g. _201412, _201501, Mon Jan  5 15:31:29 CST 2015
			'myid'=>'myid', # get table's self-defined id, see inc/webapp.class, e.g. product_id, article_id, Wed Jun  8 13:26:07 CST 2016
			'srcprefix'=>'srcprefix', # set for files and/or images
			'searchbytimefield' => 'searchbytimefield', # provide timebased search buttons
			'actoption' => 'actoption', # more act options for all records, Oct 20, 2018
			);

	private static $MAX_FIELD_LIST = 99;
	const RESULTSET = 'resultset';
	const SID = 'sid';
	public $PRIUNI = 'primaryunique';
	public $db = ''; # data db
	public $mydb = ''; # the app, -gMIS runs on it, may differ with $db
	private $skiptag = ''; # Gconf::get('skiptag');
    private $intOperatorList = array();
    private $strOperatorList = array();
	public $lang = null;

	//-
	function __construct($tbl, $hmconf, $sep, $tblrotate=null){
		//-
		//$this->dba = new DBA(); # see parent::__construct() below.
		$hmconf = $hmconf==null ? array() : $hmconf;
		$mydb = $hmconf['mydb'];
		$db = $hmconf['db'];
		$args = array('dbconf'=>($db==GConf::get('maindb')?'':$db));
		# other args options
	    parent::__construct($args);
	    $this->mydb = $mydb==null?GConf::get('maindb'):$mydb;
		$this->set('args_to_parent', $args); # for share with other objects, e.g. class/PickUp
	    # restore db after gMIS init.
	    $reqdb = trim($_REQUEST['db']);
	    if($reqdb != $mydb){
	        $this->db = $db = $hmconf['db'] = $reqdb;
	    
	    }
	    #debug(__FILE__.": db:$db mydb:$mydb");
		
		$this->hmconf = $hmconf;
		$this->sep = $sep;
		$this->resultset = self::RESULTSET;
		
		$this->prttbl = $tbl;
		$tbl .= $this->getTblRotateName($tblrotate);
		if($hmconf[$this->taglist['table'].$this->sep.$this->prttbl] != $this->prttbl){
		  if(startsWith($this->prttbl, GConf::get('tblpre'))){
		      $this->prttbl = str_replace(GConf::get('tblpre'), '', $this->prttbl);
		  }
		  else{
		      #error_log(__FILE__.":gtbl.class: error with tblname:[".$this->prttbl."].");
		  }
		}

		$this->setTbl($tbl);
		$this->tbl = $tbl;
		$this->setMyId($this->getMyIdName());
		#debug(__FILE__.": get id name:[".$this->getMyId()."]");
		# lang
	    if(array_key_exists('lang', $hmconf)){
			$this->lang = $hmconf['lang'];   
			#debug("mod/pagenavi: lang:".serialize($this->lang)." welcome:".$this->lang->get('welcome'));
		}
		else{
			#debug("mod/pagenavi: lang: not config. try global?");
			global $lang;
			$this->lang = $lang; # via global?
		}
		
		$this->skiptag = Gconf::get('skiptag');
        $this->intOperatorList = array( '='=>$this->lang->get("op_equal"), $this->skiptag=>$this->lang->get("op_skip"),
			'!='=>$this->lang->get("op_notequal"),
			'>'=>$this->lang->get("op_gt"),
			'>='=>$this->lang->get("op_gte"),
			'<'=>$this->lang->get("op_lt"),
			'<='=>$this->lang->get("op_lte"),
			'inlist'=>$this->lang->get("op_inlist"),
			'inrange'=>$this->lang->get("op_inrange"),
			'contains' => $this->lang->get("op_contains"),
			'containslist'=>$this->lang->get("op_containslist"),
			'notcontainslist'=>$this->lang->get("op_notcontainslist"),);
        $this->strOperatorList = array( 'contains'=>$this->lang->get("op_contains"), $this->skiptag=>$this->lang->get("op_skip"),
			'='=>$this->lang->get("op_equal"),
			'!='=>$this->lang->get("op_notequal"),
			'notcontains'=>$this->lang->get("op_notcontains"),
			'containslist'=>$this->lang->get("op_containslist"),
			'notcontainslist'=>$this->lang->get("op_notcontainslist"),
			'inlist'=>$this->lang->get("op_inlist"),
			'startswith'=>$this->lang->get("op_startswith"),
			'endswith'=>$this->lang->get("op_endswith"),
			'regexp'=>$this->lang->get("op_regexp"),
			'notregexp'=>$this->lang->get("op_notregexp"),);
	}

	public function getTblCHN(){
		$tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['chnname']];
		return $tmpstr = $tmpstr==null?$tbl:$tmpstr;
	}

    public function getTblCHK(){
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['check']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        $tblchk = array();
        if($tmpstr != ''){
            $tmparr = explode("|",$tmpstr);
            foreach($tmparr as $k=>$v){
                $subarr = explode(":",$tmparr[$k]);
                $tblchk[$subarr[0]] = $subarr[1];
            }
        }
        return $tblchk;
    }

    public function getTblCharset(){
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['charset']];
        return $tmpstr = $tmpstr==null?'utf-8':$tmpstr;
    }
	
	public function getTblRotateName($tblrotate=null){ # Mon Jan  5 15:34:26 CST 2015
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['rotatespan']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
		$spantbl = '';
		if($tmpstr != ''){
		$spantag = strtoupper(substr($tmpstr, -1));
		if($spantag != ''){
			$this->rotatetag = $spantag;
			if($tblrotate != ''){
				if(strlen($tblrotate) > 6){ # YYYYmm
                    $tblrotate = date("Ym", strtotime($tblrotate));
                }
				$this->tblrotate = $tblrotate;
				$spantbl .= '_'.$tblrotate;
			}
			else if($spantag == "M"){ #month
				$spantbl .= "_".date("Ym");
			}
			else if($spantag == "Y"){
				$spantbl .= "_".date("Y");
			}
			else if($spantag == "D"){
				$spantbl .= "_".date("Ymd");
			}
			else if($spantag == "W"){
				$spantbl .= "_".date("YW");
			}
			else{
				error_log(__FILE__.": found rotatespan:[$rotatespan] match failed.");
			}
		}
		}
		#error_log(__FILE__.": chn:[".$this->getTblCHN()."] prttbl:[".$this->prttbl."] tmpstr:[$tmpstr]");
		return $spantbl;
				
    }

    public function getOrderBy(){ # <orderby>statdate desc</orderby>
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['orderby']];
        return $tmpstr = $tmpstr==null?$this->getMyId():$tmpstr;
    }

    public function getPrintRef($after=0){
        $tmpstr = $this->hmconf[$this->taglist['printref'].$this->sep.($after==0?'before':'after').$this->sep.$this->taglist['reftable']];
        return $tmpstr = $tmpstr==null?'':$tmpstr;
    }

    public function getMode(){
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['managemode']];
        $tmpstr = $tmpstr==null?'':$tmpstr;

        if(strpos($tmpstr,"fromtable") === 0){
            $arr = explode("::",$tmpstr);
            $tbl = $arr[1];
            $dispfield = $arr[2];
         
            $oldhmf = $this->hmf;
            $tmpId = $_REQUEST['id'];
            #print "tmpId:$tmpId\n";
            $this->hmf = array();
            $this->setTbl($tbl);
            if(isset($arr[3])){
                $secArr = explode(",",$arr[3]);
                $wherestr = "";
                foreach($secArr as $k=>$v){
                    $trdArr = explode("=",$v);
                    $tmpfieldv = $trdArr[1];
                    if($trdArr[1] == 'THIS_TABLE'){
                       $tmpfieldv = $oldhmf['tbl'];
                    }else if($trdArr[1] == 'THIS_ID'){
                        $tmpfieldv = $tmpId;
                    }
                    $this->set($trdArr[0],$tmpfieldv);
                    $wherestr .= " ".$trdArr[0]."='".$tmpfieldv."' and";
                }
            }
            $hm = $this->getBy("id,$dispfield", $wherestr." 1=1",
				$withCache=array('key'=>$tbl.'-select-'.$wherestr));
            if($hm[0]){
                $hm = $hm[1];
                $tmpstr = $hm[0][$dispfield];
            }else{
                $tmpstr = '';
            }
            $this->hmf = $oldhmf;
        }
        return $tmpstr;
    }

    # js for whole form, added by wadelau on Wed Apr  4 17:45:09 CST 2012
    public function getJsActionTbl(){
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['jsaction']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        $jsact = '';
        if($tmpstr != ''){
            $mularr = explode("|",$tmpstr);
            foreach($mularr as $k=>$jsstr){
                $arr = explode("::", $jsstr);
                $jsact .= " ".$arr[0]."=\"javascript:".$arr[1].";\" ";
            }
        }
        return $jsact;
    }

    public function getListFieldCount(){
        $default = 7; # six fields  # seven, since Sat Jul 25 17:23:50 CST 2015
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['listfieldcount']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        return $tmpstr = ($tmpstr==''|| $tmpstr < $default)?$default:$tmpstr;
    }

    public function getFieldList()
    {
        return $this->hmfield;
    }

    public function getRelatedRef($url=''){ # ref to xml/info_objecttbl.xml
        $refArr = array();
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['relatedref']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        if($tmpstr != ''){
            if($url != '' && strpos($tmpstr,"THIS_URL") !== false){
                $tmpstr = str_replace("THIS_URL", $url, $tmpstr);
            }
            if($url != '' && strpos($tmpstr,"THIS_TBL") !== false){
                $tmpstr = str_replace("THIS_TBL", $this->getTbl(), $tmpstr);
            }
            $arr = explode("|", $tmpstr);
            foreach($arr as $k=>$v){
                $arr2 = explode("::", $v);
                $refArr[] = array("name"=>$arr2[0], "href"=>$arr2[1],"target"=>$arr2[2]);
            }
        }
		# check search by time field
        if(true){
            $timefield = $this->getSearchByTime();
			$today = $this->lang->get("todayis");
			$yestday = $this->lang->get("yesterday");
			$thisweek = $this->lang->get("thisweek");
			$lastweek = $this->lang->get("lastweek");
            if($timefield != ''){
                $refArr[] = array('name'=>$today, 'href'=>'JS',
                        'target'=>'getUrlByTime(\''.$url.'\', \''.$timefield.'\', \'inrange\', \'TODAY\');');
                $refArr[] = array('name'=>$yestday, 'href'=>'JS',
                        'target'=>'getUrlByTime(\''.$url.'\', \''.$timefield.'\', \'inrange\', \'YESTERDAY\');');
                $refArr[] = array('name'=>$thisweek, 'href'=>'JS',
                        'target'=>'getUrlByTime(\''.$url.'\', \''.$timefield.'\', \'inrange\', \'THIS_WEEK\');');
                $refArr[] = array('name'=>$lastweek, 'href'=>'JS',
                        'target'=>'getUrlByTime(\''.$url.'\', \''.$timefield.'\', \'inrange\', \'LAST_WEEK\');');
            }
        }
		# check rotatespan, Mon Jan  5 16:55:48 CST 2015
		$rotatespan = $this->rotatetag;
		#error_log(__FILE__.": rotatespan:$rotatespan");
		if($rotatespan != ''){
			$tmpArr = array('M'=>'month', 'Y'=>'year', 'D'=>'day', 'W'=>'week');
			$tmpArr1 = array('M'=>'Ym', 'Y'=>'Y', 'D'=>'Ymd', 'W'=>'YW');
			$tmpArr2 = array();
			$tmptag = $tmpArr[$rotatespan];
			for($tmpi=1; $tmpi<4; $tmpi++){
				$mytag = date($tmpArr1[$rotatespan], strtotime("-$tmpi $tmptag"));
				$refArr[] = array("name"=>$mytag, "href"=>'jdo.php?tbl='.$this->prttbl
				        .'&amp;act=list&amp;tblrotate='.$mytag.'&amp;db='.$this->db, 'target'=>'actarea');
			}
			#print_r($tmpArr2);
			
		}
        #print_r($refArr);
        return $refArr;
    }

    public function getHideSk($user){
        # see xml/hss_tuanduitbl.xml
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['hidesk']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        #error_log(__FILE__.": 111 tmpstr:$tmpstr");
        if(strpos($tmpstr,"USER_OPERATEAREA") !== false){
            $tmpstr = str_replace( "USER_OPERATEAREA", $user->getOperateArea(), $tmpstr);
        }
		if(inString('THIS_USER', $tmpstr)){
            $tmpstr = str_replace('THIS_USER', $user->getId(), $tmpstr);
        }
        if(inString('THIS_GROUP', $tmpstr)){
            $tmpstr = str_replace('THIS_GROUP', $user->getGroup(), $tmpstr);
        }
        #error_log(__FILE__.": 222 tmpstr:$tmpstr");
        return $tmpstr;
    }

	# get src prefix for files and images
	# 17:59 09 November 2016
	public function getSrcPrefix(){
		$default = ''; #
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['srcprefix']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
		#debug(__FILE__.": get srcprefix:[$tmpstr]");
        return $tmpstr=='' ? $default : $tmpstr;
	}
	
	//-
    public function getSearchByTime(){
        $default = ''; #
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl
                .$this->sep.$this->taglist['searchbytimefield']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        return $tmpstr=='' ? $default : $tmpstr;
    }

	//- Action options in popup menu and/or act/view for every single row
    //- retrieve in ido, act/view
    //- see xml/fin_todotbl
    public function getActOption($result=null){
        $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['actoption']];
        $tmpstr = $tmpstr==null?'':$tmpstr;

        $actArr = array(); $tUrl = "";
        $title = ""; $needJsConfirm = 0; $needBlank = 0;
        if($result != null){
		    $this->set($this->resultset, $result);
        }
        if($tmpstr != ""){ # see xml/info_usertbl.xml
    
        $tmpStrArr = explode('|', $tmpstr);
        foreach ($tmpStrArr as $k=>$tmpstr){
            $tUrl = '';
            $vArr = explode("::", $tmpstr);
			if(startsWith($vArr[0],"javascript:")){
				$tUrl = $vArr[0];
				$title = $vArr[1];
				if(strpos($tUrl,"THIS") > -1){
					$tUrl = $this->fillThis($tUrl, $field);
				}
			}
			else{
                $file = $vArr[0];
                if($file == 'THIS'){
                    $file = $result[$field];
                }
    			else if(strpos($file, 'THIS') !== false){
    				$file = str_replace('THIS',$result[$field], $file); # <href>http://THIS::a=1::跳转登录::blank=1</href>
    			}
                $pArr = explode(",", $vArr[1]);
                $title = $vArr[2];
				$title = $this->fillThis($title, $field);
                foreach($pArr as $k=>$v){
                    $para = explode("=", $v);
                    $tUrl .= $para[0].'=';
                    if(count($para) > 2){
                        $para[1] = $para[1]."=".$para[2];
                    }
					if(inString('THIS', $para[1])){
						$tUrl = $this->fillThis($tUrl.$para[1], $field=1); # trigger THIS_xxxx
					}
					else if(strpos($para[1], "'") === 0){
                        $tUrl .= substr($para[1], 1, strlen($para[1])-2);
                    }
					else{
                        $tUrl .= $result[$para[1]];
                    }
                    $tUrl .= "&";
                }
                $tUrl = $file."?".substr($tUrl, 0, strlen($tUrl)-1);
				$tUrl = $this->appendSid($tUrl);
                $fourthPara = $vArr[3];
                if($fourthPara != ''){
                    if(strpos($fourthPara,"confirm=1") !== false){
                        $needJsConfirm = 1;
                    }
                    if(strpos($fourthPara,"blank=1") !== false){
                        $needBlank = 1;
                    }
                    else if(strpos($fourthPara,"blank=2") !== false){
                        $needBlank = 2;
                    }
                }
                if($needJsConfirm == 1){
                    $tUrl = "javascript:if(window.confirm('".$this->lang->get("notice_confirm")."')){document.location.href='".$tUrl."';}";
                }
			}
			if($needBlank == 1){
			    $needBlank = "_blank";
			}
			else if($needBlank == 2){
			    $needBlank = "_top";
			}
			else{
			    $needBlank = "_self";
			}
            #debug(__FILE__." tUrl:$tUrl");
            #return array($tUrl, $title, $needBlank);
            $actArr[] = array($tUrl, $title, $needBlank);
        }

        } # end tmpstr

        #debug($actArr);
        return $actArr;
    }
	
	#
    # functions based on $field, below
	#
    public function getCHN($field){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['chnname']];
        return $tmpstr = $tmpstr==null?$field:$tmpstr;
    }

    public function getFieldPrint($field){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['printref']];
        return $tmpstr = $tmpstr==null?'':$tmpstr;
    }

    public function getInputType($field){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['inputtype']];
        if($tmpstr == null){
            $selectoption = $this->getSelectOption($field,'');
            if($selectoption != null && $selectoption !=''){
                $tmpstr = 'select'; # added on Sun Mar 18 15:57:20 CST 2012
            }
        }
        return $tmpstr = $tmpstr==null?'input':$tmpstr;
    }

    public function getDefaultValue($field){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['defaultvalue']];
        return $tmpstr = $tmpstr==null?'':$tmpstr;
    }

    public function getListView($field){ # '0': not disp, '1' or '': disp, '2': force to disp
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['listview']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        if($tmpstr == ''){
            $tmpstr = 1;
        }
        return $tmpstr ;
    }

    public function getSingleRow($field){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['singlerow']];
        return $tmpstr = $tmpstr==null?'':$tmpstr;
    }

    public function getJsAction($field, $result=null){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['jsaction']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
		$tmpstr = $this->fillThis($tmpstr, $field);
        $jsact = '';
        if($tmpstr != ''){
            $mularr = explode("|",$tmpstr);
            foreach($mularr as $k=>$jsstr){
                $arr = explode("::", $jsstr);
                $jsact .= " ".$arr[0]."=\"javascript:".$arr[1].";\" ";
            }
        }
        return $jsact;
    }

    public function getAccept($field){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['accept']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        if($tmpstr != ''){
            return " accept=\"".$tmpstr."\" ";
        }else{
            return '';
        }
    }

	# remedy on 12:39 Jun 06, 2018
	# bugfix for lazyLoad, 16:33 Monday, August 26, 2019
    public function getSelectOption($field, $defaultval, $tagpre='', $needv=0, $ismultiple=0){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep
			.$this->taglist['selectoption']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        if($tmpstr == ''){
            if(inList($field, 'istate,state,status,istatus')){
                $tmpstr = $this->lang->get("form_state_list"); #"1:正常|0:停用";
            }else{
                return $tmpstr;
            }
        }
        $optionlist = '<option value="" title="'.$this->lang->get('op_select').'">-'.$this->lang->get('op_select').'-</option>';
        $selectval = '';
        $selectval_mul = '';
		$lazyLoadStr = '';
        if($tmpstr == ''){
            # ?
        }else if(strpos($tmpstr,"fromtable") === 0){
            $arr = explode("::",$tmpstr);
            $tbl = $arr[1]; $theTbl = $tbl; # support "dbname.tblname" with access
            $dispfield = $arr[2];
            if(isset($arr[3])){
                #$dispfield .= ",".$arr[3];
            }
			$hmoption = array();
			$optval = 'id'; $dispname = '';
			if(isset($arr[4]) && $arr[4] != ''){ $optval = $arr[4]; }  
			# alternative 'id', # see xml/fwn_sitetbl, sitetype, Fri Dec 12 13:43:36 CST 2014
			$hasExist = 0; $maxInitSelectCount = 512; $optioni = 0;
			if(isset($this->hmconf['selectoption_'.$field])){
				$optionlist = $this->hmconf['selectoption_'.$field]; $hasExist = 1;
				$hmoption = $this->hmconf['selectoption_hm_'.$field];
			}
			else{
				$oldhmf = $this->hmf;
				$this->hmf = array();
				$this->setTbl($tbl);
                $tmpWhere = $arr[3];
                if($tmpWhere==''){ $tmpWhere='1=1'; }
                $tmpWhere .= " order by CONVERT($dispfield USING gbk)";
				$hm = $this->getBy("$optval,$dispfield", $tmpWhere,
					$withCache=array('key'=>$tbl.'-select-$optval-$dispfield-'.$arr[3]));
					# anti for various fields from the same src table
				if($hm[0]){
					$hmoption = $hm[1]; # $this->hmconf['selectoption_'.$field] = $hmoption;
				}
				$this->hmf = $oldhmf;
			}
			$optionlist = '<option value="" title="'.$this->lang->get('op_select').'">-'.$this->lang->get('op_select').'-</option>';
			foreach($hmoption as $k=>$rec){
				$dispname = $rec[$arr[2]];
				if(strpos($dispfield, ",") !== false){ #! Sat Nov 29 07:35:39 CST 2014
					$tmparr = explode(",", $dispfield);
					foreach($tmparr as $k2=>$v2){
						$dispname .= "-".$rec[$v2];
					}
				}
				$optionlist .= "<option value=\"".$rec[$optval]."\"";
				if($defaultval !== null){
					if($rec[$optval] == $defaultval 
						|| strpos(",".$defaultval.",", ",".$rec[$optval].",") !== false){
						$optionlist .= " selected";
						$selectval = $dispname.(isset($arr[3])?"-".$rec[$arr[3]]:"")." (".$rec[$optval].")";
						if($needv == 1){
							$selectval_mul .= $dispname.(isset($arr[3])?"-".$rec[$arr[3]]:"")
								." (".$rec[$optval]."),";
						}
					}
				}
			   $optionlist .=">".$dispname.(isset($arr[3])?"-".$rec[$arr[3]]:"")." (".$rec[$optval].")</option>\n";
			   if($optioni++ > $maxInitSelectCount){ break; }
			}
			$this->hmconf['selectoption_'.$field] = $optionlist;
			$this->hmconf['selectoption_hm_'.$field] = $hmoption;
            //$this->setTbl($oldtbl);
			
			if($hasExist == 0 && $optioni > $maxInitSelectCount){
				$lazyLoadStr = "<script type='text/javascript' async>parent.lazyLoad('".$field
					."','select','extra/readtblfield.php?objectid=0&logicid=".$dispfield
					."&tbl=".$theTbl."&field=".$field."');</script><input name='pnsk_".$field
					."_optionlist' id='pnsk_".$field."_optionlist' value='' type='hidden' />";
				#error_log(__FILE__.": field:$field set lazy load...... optioni:$optioni");
			}
			if($defaultval !== null && $selectval == ''){
				$hmSelect = array();
				if(isset($this->hmconf['selectoption_sel_'.$field])){
					$hmSelect = $this->hmconf['selectoption_sel_'.$field];
				}
				else{
					foreach($hmoption as $k=>$rec){
						$dispname = $rec[$arr[2]]; $selectval = '';
						if(strpos($dispfield, ",") !== false){ #! Sat Nov 29 07:35:39 CST 2014
							$tmparr = explode(",", $dispfield);
							foreach($tmparr as $k2=>$v2){
								$dispname .= "-".$rec[$v2];
							}
						}
						if($rec[$optval] == $defaultval 
							|| strpos(",".$defaultval.",", ",".$rec[$optval].",") !== false){
							$selectval = $dispname.(isset($arr[3])?"-".$rec[$arr[3]]:"")." (".$rec[$optval].")";
							if($needv == 1){
								$selectval_mul .= $dispname.(isset($arr[3])?"-".$rec[$arr[3]]:"")
									." (".$rec[$optval]."),";
							}
						}
						$hmSelect[$rec[$optval]] = $selectval==''?($dispname."-(".$rec[$optval].")"):$selectval;
						# multiple support?
					}
					$this->hmconf['selectoption_sel_'.$field] = $hmSelect;
				}
				if($selectval == ''){ $selectval = $hmSelect[$defaultval]; }
				#error_log(__FILE__.": defval:[".$defaultval."] selectval:[".$selectval."]");
			}
        }
		else if(strpos($tmpstr,"|") > 0){
            $varlist = explode("|", $tmpstr);
            $tmpstr = "";
			$hmoption = $varlist;
            foreach($varlist as $k=>$v){
                $arr = explode(":", $v);
                $optionlist .= "<option value=\"".$arr[0]."\"";
                if($defaultval !== null){
                    if($arr[0] != '' && ($arr[0] == $defaultval || strpos($defaultval, $arr[0]) !== false)){
                        $optionlist .= " selected";
                        $selectval = $arr[1];
                        if($needv == 1){
                            $selectval_mul .= $arr[1].",";
                        }
                    }
                }
                $optionlist .=">".$arr[1]."(".$arr[0].")</option>\n";
            }
        }
        if($this->getReadOnly($field,'select') == 'disabled'){
            $tmpstr = "<select id=\"".$tagpre.$field."\" name=\"".$tagpre.$field."\" "
				.$this->getJsAction($field)." ".$this->getAccept($field)." disabled>"
				.$optionlist."</select> <input type=\"hidden\" id=\"".$tagpre.$field."\" name=\""
				.$tagpre.$field."\" value=\"".$defaultval."\" />";
        }
		else{
            if($ismultiple == 0){
                $tmpstr = "<select id=\"".$tagpre.$field."\" name=\"".$tagpre.$field."\" "
					.$this->getJsAction($field)." ".$this->getAccept($field)." "
					.$this->getCss($field).">".$optionlist."</select>";
            }
			else{
                #$tmpstr = "<select id=\"".$tagpre.$field."\" name=\"".$tagpre.$field."[]\" ".$this->getJsAction($field)." ".$this->getAccept($field)." multiple=\"multiple\">".$optionlist."</select>";
				$tmpstr = "";
                $oi = 1;
                #print __FILE__;
                #print_r($hmoption);
                $hmTmp = array();
                foreach($hmoption as $kh=>$vh){
                	$hmTmp[$vh[$optval]] = $vh;
                }
                sort($hmTmp);
                foreach($hmTmp as $ko=>$vo){
                	$tmpstr .= ($oi++).".<input type='checkbox' name='".$tagpre.$field."[]' value='"
						.$vo[$optval]."'".(inList($vo[$optval], $defaultval) ? " checked" : "")."/> "
						.$vo[$arr[2]]."(".$vo[$optval].")&nbsp;&nbsp;&nbsp;";
                	if(($oi-1) % 5 == 0){
                		$tmpstr .= "<br/>";
                	}
                }
			}
        }
        if($needv != 1){
            return $tmpstr.$lazyLoadStr;
        }
		else{
            #error_log(__FILE__.": field:$field, selectval:$selectval , selectval_mul:$selectval_mul");
            if($ismultiple == 1){
                return substr($selectval_mul,0, strlen($selectval_mul)-1);
            }
			else{
                return $selectval==''?$defaultval:$selectval; #  = $defaultval;
            }
        }

    }

    public function getExtraInput($field,$result=null){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['extrainput']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        if($tmpstr != ''){
        	if(strpos($tmpstr, "THIS_") !== false){

                $tmpstr = $this->fillThis($tmpstr, $field);
                /*
				if($tmpcount=preg_match_all("/THIS_([^&]+)/", $tmpstr, $matches)){
					#print_r($matches);
					foreach($matches[1] as $k=>$v){
						#print_r($v);
						#print __FILE__.": matched:[".$v."],,,\n";
						if($v == 'TABLE'){
							$tmpstr = str_replace("THIS_$v", $this->hmf['tbl'], $tmpstr);
						}
						else{
							$tmpstr = str_replace("THIS_$v", $result[$v], $tmpstr);
						}
					}
                }
                 */
			}
		}
        return $tmpstr ;
    }
    
    public function getReadOnly($field, $inputtype=''){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['readonly']];
        $tmpstr = $tmpstr ==null ? '':$tmpstr;
        if($tmpstr == '1'){
            $tmpstr = "readonly";
            if($inputtype == 'select'){
                $tmpstr =  'disabled';
            }
        }else{
            $tmpstr = '';
        }
        return $tmpstr;
    }
    
    public function getMemo($field){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['memo']];
        return $tmpstr = $tmpstr==null?'':$tmpstr;
    }

    public function getCss($field, $fieldv=''){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['css']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        if($tmpstr != ''){
            $arr = explode("|", $tmpstr); # need to fiter by state
            foreach($arr as $k=>$v){
                $arr2 = explode("::", $v);
                if($fieldv != ''){
                    if($fieldv == $arr2[0]){
                        $tmpstr = "class=\"".$arr2[1]."\"";
                        break;
                    }else{

                    }
                }else{
                    #$tmpstr = "class=\"".$arr2[1]."\"";
                    $tmpstr = "";
                    break;
                }
            }
        }
        return $tmpstr;
    }


    public function getTrigger($field=''){
        $tmpstr = '';
        if($field == ''){
            $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['trigger']];
        }else{
            $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['trigger']];
    	}
		$tmpstr = $tmpstr==null ? '' : $tmpstr;
		$tmpstr = $this->fillThis($tmpstr, $field);
        return $tmpstr;
    }

    public function setTrigger($field,$trigger){
        $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['trigger']] = $trigger;
    }

    public function getHref($field, $result=null){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['href']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
        $tUrl = "";
        $title = ""; $needJsConfirm = 0; $needBlank = 0;
        if($tmpstr != ""){ # see xml/info_usertbl.xml
            $vArr = explode("::", $tmpstr);
			if(startsWith($vArr[0], "javascript:")){
				$tUrl = $vArr[0];
				$title = $vArr[1];
				if(strpos($tUrl,"THIS") > -1){
					$tUrl = $this->fillThis($tUrl, $field);
				}
			}
			else{
                $file = $vArr[0];
                if($file == 'THIS'){
                    $file = $result[$field];
                }
    			else if(strpos($file, 'THIS') !== false){
    				$file = str_replace('THIS',$result[$field], $file); # <href>http://THIS::a=1::跳转登录::blank=1</href>
    			}
                $pArr = explode(",", $vArr[1]);
                $title = $vArr[2];
				$title = $this->fillThis($title, $field);
                foreach($pArr as $k=>$v){
                    $para = explode("=", $v);
                    $tUrl .= $para[0].'=';
                    if(count($para) > 2){
                        $para[1] = $para[1]."=".$para[2];
                    }
					if(inString('THIS', $para[1])){
						$tUrl = $this->fillThis($tUrl.$para[1], $field);
					}
					else if(strpos($para[1], "'") === 0){
                        $tUrl .= substr($para[1], 1, strlen($para[1])-2);
                    }
					else{
                        $tUrl .= $result[$para[1]];
                    }
                    $tUrl .= "&";
                }
                $tUrl = $file."?".substr($tUrl, 0, strlen($tUrl)-1);
				$tUrl = $this->appendSid($tUrl);
                $fourthPara = $vArr[3];
                if($fourthPara != ''){
                    if(strpos($fourthPara,"confirm=1") !== false){
                        $needJsConfirm = 1;
                    }
                    if(strpos($fourthPara,"blank=1") !== false){
                        $needBlank = 1;
                    }
                    else if(strpos($fourthPara,"blank=2") !== false){
                        $needBlank = 2;
                    }
                }
                if($needJsConfirm == 1){
                    $tUrl = "javascript:if(window.confirm('".$this->lang->get('notice_confirm')."')){document.location.href='".$tUrl."';}";
                }
			}
			if($needBlank == 1){
			    $needBlank = "_blank";
			}
			else if($needBlank == 2){
			    $needBlank = "_top";
			}
			else{
			    $needBlank = "_self";
			}
            return array($tUrl, $title, $needBlank);
        }
        return array();
    }

    public function getSelectMultiple($field){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['selectmultiple']];
        return $tmpstr = $tmpstr==null?'':$tmpstr;
    }

    public function getStat($field){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['stat']];
        return $tmpstr = $tmpstr==null?'':$tmpstr;
    }

    public function getField($fieldi){
        $tmpstr = $this->hmfieldsort[$fieldi];
        return $tmpstr = $tmpstr==null?'':$tmpstr;
    }

    public function setFieldSort($hmfieldsort, $hmsize, $hmi){
        if($hmi < $MAX_FIELD_LIST){
            $ibala = $MAX_FIELD_LIST - $hmi;
            $mini = $hmsize - $ibala;
            for($myi=$mini; $myi<$mini+$ibala; $myi++){
                $hmi++;
                $obj = $hmfieldsort[$hmi];
                $hmfieldsort[$myi] = $obj;
                unset($hmfieldsort[$hmi]);
            }
        }
        $this->hmfieldsort = $hmfieldsort;
    
    }

    public function setFieldList($hmfield){
        $this->hmfield = $hmfield;
		$this->hmfieldinfo = $this->hmfield;
    }

    public function getDelayJsAction($field, $result=null){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['delayjsaction']];
        $tmpstr = $tmpstr==null?'':$tmpstr;
		if($result == null){
            $result = $this->get($this->resultset);
        }
        else{
            $this->set($this->resultset, $result);
        }
        if(is_array($result)){
            $tmpstr = $this->fillThis($tmpstr, $field);
        }
        else{
            # @todo
        }
		$jsact = "";
        if($tmpstr != ""){
            $arr = explode("|", $tmpstr);
            foreach($arr as $k=>$v){
                $arr2 = explode("::", $v);
                $jsact .= "parent.registerAct({'status':'".$arr2[0]."','delaytime':".$arr2[1]
					.",'action':'".Base62x::encode($arr2[2])."'});";
            }
        }
        return $jsact==''?'':"<script type=\"text/javascript\" async>".$jsact."</script>";
    }

    public function getSuperAccess($field=''){
        $tmpstr = '';
        if($field == ''){
            $tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['superaccess']];
        }else{
            $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['delayjsaction']];
        }
        return $tmpstr==null?'':$tmpstr;
    }

    public function getLogicOp($field, $defaultval=null){
		$skiptag = Gconf::get('skiptag');
        $intop = $this->intOperatorList;
        $strop = $this->strOperatorList;
        $rtn = "";
        $hmfield = $this->hmfield;
        $isint = 0;
        if($this->isNumeric($hmfield[$field])){
            $isint = 1;
			if(isset($defaultval) && $defaultval == 'contains'){
                $defaultval = '=';
            }
        }
		else{
			if($defaultval == $skiptag){
                $tmpOpv = Wht::get($_REQUEST, 'pnsk'.$field);
                if($tmpOpv != ''){ $defaultval = 'contains'; }
            }
		}
        $targetArr = $intop;
        if(!$isint){
            $targetArr = $strop;
        }
        foreach($targetArr as $k=>$v){
            $reqfieldv = $_REQUEST["oppnsk".$field];
			$reqfieldv = isset($reqfieldv) ? $reqfieldv : $defaultval;
            $selected = "";
            if($k == $reqfieldv){
                $selected = " selected";
            }
            $rtn .= "<option value=\"$k\" title=\"".$v."\"".$selected.">$k</option>";
        }
        return $rtn;
    }

    # inner functions for this object

    //- max depth: 4
    public static function xml2hash($xmlpath, $sep, $db, $tbl){
        $i = 0;
        $hm = array('db'=>$db);
        $hmsortinxml = array();
        $attribute = array('name','type');
        if($xmlpath == ''){
            debug(__FILE__.": xmlpath is empty. [1201231150]", 2);
            return $hm;
        }else if($sep == ''){
            debug(__FILE__.": separator is empty. [1201231153]", 2);
            return $hm;
        }
		# @todo
		# check $lang for specified xml file
		# 
		$tblpre = GConf::get('tblpre');
		$tblconf = str_replace($tblpre, "", $tbl);
        if(file_exists($xmlpath."/".$tblconf.".xml")){
            libxml_use_internal_errors(true);
            $xmlobj = simplexml_load_file($xmlpath."/".$tblconf.".xml");
            if($xmlobj === false){
                $xmlError = libxml_get_errors();
                debug("xml error:".serialize($xmlError)." with $xmlpath/$tblconf.xml");
            }
			libxml_clear_errors();
            foreach($xmlobj as $key=>$value){
                #print "leve-0: $key: [$value], name: [".$value['name']."] type:[".$value['type']."] typeof:[".gettype($value)."]\n";
                $sortk = (String)$value['name'];
                if(!array_key_exists($sortk, $hmsortinxml)){
                    $hmsortinxml[$sortk] = $i; $i++;
                }
				$tmpname = (String)$value['name'];
				if($key == 'table'){
					if(substr($tmpname, 0, strlen($tblpre)) !==  $tblpre){
						#$value['name'] = $tblpre.$tmpname;
						$value['name'] = $tmpname;
					}
				}
				$hm[$key.$sep.$value['name']] = $tmpname;

				foreach($value as $key1=>$value1){
					#print "leve-1: $key1: [$value1] typeof:[".gettype($value1)."]\n";
					$tmpkey1 = $key.$sep.$value['name'].$sep.$key1;
					if(!array_key_exists($tmpkey1,$hm)){
						$hm[$tmpkey1] = (String)$value1;
					}else{
						$hm[$tmpkey1] = $hm[$tmpkey1]."|".(String)$value1;
					}

					foreach($value1 as $key2=>$value2){
						#print "leve-2: $key2: [$value2] typeof:[".gettype($value2)."]\n";
						$tmpkey2 = $key.$sep.$value['name'].$sep.$key1.$value1['name'].$sep.$key2;
						if(!array_key_exists($tmpkey2,$hm)){
							$hm[$tmpkey2] = (String)$value2;
						}else{
							$hm[$tmpkey2] = $hm[$tmpkey2]."|".(String)$value2;
						}

						foreach($value2 as $key3=>$value3){
							#print "leve-3: $key3: [$value3] typeof:[".gettype($value3)."]\n";
							$tmpkey3 = $key.$sep.$value['name'].$sep.$key1.$value1['name'].$sep.$key2.$sep.$value2['name'].$sep.$key3;
							if(!array_key_exists($tmpkey3,$hm)){
								$hm[$tmpkey3] = (String)$value3;
							}else{
								$hm[$tmpkey3] = $hm[$tmpkey3]."|".(String)$value3;
							}
						}
					}
				}
			}
        }
		else{
            error_log(__FILE__.": ".$xmlpath."/".$tblconf.".xml was not found.");
        }
        #print_r($hmsortinxml);
        #print_r($hm);
        return array($hm,$hmsortinxml);
    }

	//-
    function filterHiddenField($field, $opfield, $timefield){
       $ishidden = false;
       if($field == null || $field == ''
            || $field == 'id' || $this->getInputType($field) == 'hidden'
            || in_array($field, array_merge($opfield, $timefield))){
            
           if($field != '' && $this->getListView($field) == 2){
                # force to disp
           }else{
                $ishidden = true;
                #print "field:[".$field."] is hidden!\n";
           }
       }
       return $ishidden;
    }
	
	//-
    function getFieldType(){
        $fieldtype = "";
        foreach($this->hmfield as $k=>$v){
            $type = $this->getInputType($k);
            $fieldtype .= ",".$k.":".$type;
        }
        return substr($fieldtype,1);
    }

	# filter much more options to a few of them
	# Mon Jul 28 15:14:01 CST 2014
	function getInput2Select($field){
        $tmpstr = $this->hmconf[$this->taglist['field'].$this->sep.$field.$this->sep.$this->taglist['input2select']];
        return $tmpstr = $tmpstr==null?'':$tmpstr;
	}
    
	//-
	public function getMyIdName(){
		$tmpstr = $this->hmconf[$this->taglist['table'].$this->sep.$this->prttbl.$this->sep.$this->taglist['myid']];
		return $tmpstr = $tmpstr==null?'id':$tmpstr;
	}

	//- @override, test a table with or without $_CONFIG['tblpre']
	//- Xenxin, Thu Jun 16 17:03:49 CST 2016
	//- support "dbname.tblname" , 12:30 6/21/2020 
	public function setTbl($tbl=''){
		$realtbl = '';
		if($tbl == null || $tbl == ''){
			$tbl = parent::getTbl();
		}
		if(inString('.', $tbl)){
			$realtbl = $tbl;
		}
		else{
		$tblpre = GConf::get('tblpre');
		$hasTblpre = startsWith($tbl, $tblpre);
		$hm = parent::execBy('show tables like "%'.$tbl.'" ', null,
			$withCache=array('key'=>$tbl."-simillar")); # just prefix
		if($hm[0]){
			$tmpv = '';
			if(is_array($hm[1])){
			foreach($hm[1] as $rk=>$rv){
				foreach($rv as $vk=> $vv){
				    if(!$hasTblpre && startsWith($vv, $tblpre)){
				        $tmpv = $vv;
				        break;
				    }
					else if($hasTblpre && substr($vv, strpos($vv, '_')+1) == $tbl){
                        $tmpv = $vv;
                        break;
                    }
				    else if($vv == $tbl){
				        $tmpv = $vv;
				        break;
				    }
				}
			}
			}
			if($tmpv != ''){
			    $realtbl = $tmpv;
			}
			else{
			    debug(__FILE__.": unable to find real tbl for [$tbl].");
			}
			#debug(__FILE__.": get result: real tbl:[".$realtbl."]");
		}
		else{
			if(startsWith($tbl, $tblpre)){ # remove prefix
				$realtbl = str_replace($tblpre, "", $tbl);
			}
			else{ # add prefix
				$realtbl = $tblpre.$tbl;
			}
		}
		}
		parent::setTbl($realtbl);
		return $realtbl;

	}
	
	//-
	//- replace THIS* in settings, 14:23 27 September 2016
	public function fillThis($tmpstr, $field=null){
		if($tmpstr != '' && strpos($tmpstr, 'THIS') !== false){
			if($result == null){
				$result = $this->get($this->resultset);
			}
            if(!is_array($result)){
                $result = array();
            }
			#debug(__FILE__.": tmpstr:[$tmpstr] resultset:");
			#debug($result);
			$tmpstr = str_replace('THISNAME', $field, $tmpstr);
            $tmpstr = str_replace('THIS_NAME', $field, $tmpstr);
            $tmpstr = str_replace('THIS_TBL', $this->getTbl(), $tmpstr);
            $tmpstr = str_replace('THIS_TABLE', $this->getTbl(), $tmpstr);
            $tmpstr = str_replace('THIS_DB', trim($_REQUEST['db']), $tmpstr);
            $tmpstr = str_replace('THIS_SID', $_REQUEST[self::SID], $tmpstr);
			if(is_array($result) && count($result)>0){
				$tmpstr = str_replace('THIS_ID', $result[$this->getMyId()], $tmpstr);	
				if($field != null && $field != ''){
					if(preg_match_all('/THIS_([a-zA-Z]+)/', $tmpstr, $matchArr)){
						$v = $matchArr[1];
						foreach ($v as $k1=>$v1){
							//debug("k1:$k1 v1:$v1 tmpstr:$tmpstr");
                            # due to 'field' 'fieldx' 'fieldxxxx'
							$tmpstr = preg_replace('/THIS_'.$v1.'([&|\'|$]*)/', $result[$v1].'\1', $tmpstr);
							//debug("aft k1:$k1 v1:$v1 tmpstr:$tmpstr");
						}
					}
                    else{
                        //debug("umatched? tmpstr:$tmpstr field:$field");
                    }
                    //- @todo: why this?
					$tmpstr = str_replace('THIS', $result[$field], $tmpstr);
				}
			}
		}
        #debug(__FILE__.":field:$field tmpstr:[$tmpstr] result:".serialize($result));
		return $tmpstr;
	}

	//- unique parameters for non id
	//- Xenxin@Ufqi, Fri, 16 Dec 2016 20:10:24 +0800
	public function getUniquePara($rec){
	    $rtn = '';
	    $priuni = $this->get($this->PRIUNI);
	    #debug(__FILE__.": priuni: ".$this->toString($priuni));
	    $fields = isset($priuni['PRI']) ? $priuni['PRI'] : $priuni['UNI'];
	    foreach ($fields as $k=>$v){
	        $rtn .= "$v=".$rec[$v].'&';
	    }
	    if($rtn != ''){
	        $rtn = substr($rtn, 0, strlen($rtn)-1);
	    }
	    return $rtn;
	}
	
	//-
	//- fill sid
	public function appendSid($url){
	    // return $url
	    $sidstr = self::SID.'='.$_REQUEST[self::SID];
	    if(inString('?sid=', $url) || inString('&sid=', $url)){
	        # good
	    }
	    else{
	        if(startsWith($url, 'http')){
	            # outside
	        }
	        else{
	            $hasFilled = false;
	            $fileArr = array('ido.php', 'jdo.php', './', 'index.php');
	            foreach($fileArr as $k=>$v){
	                if(inString($v.'?', $url)){
	                    $url = str_replace($v.'?', $v.'?'.$sidstr.'&', $url);
	                    $hasFilled = true;
	                    break;
	                }
	                else if(inString($v, $url)){
	                    $url = str_replace($v, $v.'?'.$sidstr, $url);
	                    $hasFilled = true;
	                    break;
	                }
	            }
	            if(!$hasFilled){
	                if(inString('?', $url)){
	                    $url .= '&'.$sidstr;
	                }
	                else{
	                    $url .= '?'.$sidstr;
	                }
	            }
	        }
	    }
		//- append &db=
		if(inString('&db=', $url)){
			# goood
		}
		else{
			$url .= "&db=".$this->db;
		}
	    return $url;
	}
	
}
?>