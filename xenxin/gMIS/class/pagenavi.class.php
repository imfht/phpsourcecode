<?php
/* PageNavi class
 * v0.3
 * wadelau@ufqi.com
 * Tue Jan 24 12:25:56 GMT 2012
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__.'/inc/webapp.class.php');

class PageNavi extends WebApp{
	
	//- variables
    var $dummy = '';
    const SID = 'sid';
	const Omit_String = '----';
	var $lang = null;
	const Default_Page_Size = 30;
    
   public function __construct($args=null){
   
       $file = $_SERVER['PHP_SELF'];
       $query = $_SERVER['QUERY_STRING'];
       if(strpos($query, "act=list-") !== false){
            $query = preg_replace("/act=list\-([0-9a-z]*)/","act=list",$_SERVER['QUERY_STRING']);
            $this->hmf['neednewpntc'] = 1 ;
            $query = preg_replace("/&pntc=([0-9]*)/","", $query);
       }
       $url = $file."?".preg_replace("/&pnpn=([0-9]*)/","",$query);
       $this->hmf['url'] = $url;
       $para = array();
       $pdef = array('pnpn'=>1, 'pnps'=>self::Default_Page_Size, 'pntc'=>0);
       foreach($_REQUEST as $k=>$v){
           $para[$k] = ($v==''||$v==null)?$pdef[$k]:$v;
           #$this->hmf[$k]=$para[$k];
		   if($k == 'id'){ $this->setId($v);}
		   elseif($k == 'tbl'){ $this->setTbl($v); }
		   else{ $this->set($k, $v);  }
       }
       foreach($pdef as $k=>$v){
            $para[$k] = $para[$k]>0?$para[$k]:$pdef[$k];
            $this->hmf[$k]=$para[$k];
       }
	   
	   if(!is_array($args)){ $args = array(); }

	   #$this->dba = new DBA(); # added by wadelau@ufqi.com, Wed Jul 11 14:31:52 CST 2012
	   # call parent's constructor, explicitly
	   parent::__construct($args);
	   # lang
	   if(array_key_exists('lang', $args)){
			$this->lang = $args['lang'];   
			#debug("mod/pagenavi: lang:".serialize($this->lang)." welcome:".$this->lang->get('welcome'));
		}
		else{
			#debug("mod/pagenavi: lang: not config. try global?");
			global $lang;
			$this->lang = $lang; # via global?
		}

   }

	# ?
   function getNavi(){
       $para = $this->hmf;
       if($this->hmf['totalcount'] > 0){
            $para['pntc'] = $this->hmf['totalcount'];
            $this->hmf['url'] = preg_replace("/&pntc=([0-9]*)/","", $this->hmf['url']);
            $this->hmf['url'] .= "&pntc=".$para['pntc'];
            $para['url'] = $this->hmf['url'];
       }
	   else{
			#error_log(__FILE__.": pntc is null.");
	   }
	   # in case of POST parameters in request, Mar 28, 2018
	   if(true){
	       $tmpUrl = $para['url'];
	       foreach($_REQUEST as $k=>$v){
	           if(startsWith($k, 'op')
	                   && $v != self::Omit_String
	                   && !inString('&'.$k, $tmpUrl)){
	                       $para['url'] .= "&$k=$v";
	                       $kp = str_replace('op', '', $k);
	                       $para['url'] .= "&$kp=".$_REQUEST[$kp];
	           }
	       }
	       $para['url'] .= '&pnsm='.(isset($_REQUEST['pnsm'])?$_REQUEST['pnsm']:'and');
	   }
       #print_r($this->hmf);

       $totalpage = $para['pntc'] % $para['pnps'] == 0 ? ($para['pntc']/$para['pnps']) : ceil($para['pntc']/$para['pnps']);
       $navilen = 9;
       $str = "&nbsp;&nbsp;<b>".$this->lang->get("pagenavi_pageno").": &nbsp;<a href=\"javascript:pnAction('".$para['url']."&pnpn=1');\" title=\"".$this->lang->get("pagenavi_p1")."\">|&laquo;</a></b>&nbsp; ";

       for($i=$para['pnpn']-$navilen; $i<$para['pnpn'] + $navilen && $i<=$totalpage; $i++){
           if($i>0){
               if($i == $para['pnpn']){
                    $str .= " <span id=\"currentpage\" style=\"color:green;font-weight:bold;font-size:18px\">".$i."</span> ";
               }else{
                    $str .= " <a href=\"javascript:pnAction('".$para['url']."&pnpn=".$i
						."');\" style=\"font-size:14px;padding:3px;\" "
						." onmouseover=\"javascript:this.style.fontSize='26px';\" "
						." onmouseout=\"javascript:this.style.fontSize='14px';\">".$i."</a> ";
               }
           }
           #print "$i: [$str] totalpage:[$totalpage]\n";
       }
       $str .= " &nbsp;<b><a href=\"javascript:pnAction('".$para['url']."&pnpn=".$totalpage."');\" title=\"".$this->lang->get("pagenavi_plast")
	   		."\">&raquo;|</a> </b> &nbsp; &nbsp; <a href=\"javascript:void(0);\" title=\"".$this->lang->get("pagenavi_adjustps")
			."\" onclick=\"javascript:var pnps=window.prompt('".$this->lang->get("pagenavi_inputps").":','".$para['pnps']."'); if(pnps>0){ myurl='"
			.$para['url']."'; myurl=myurl.replace('&pnps=','&opnps='); doAction(myurl+'&pnps='+pnps);};\"><b>".number_format($para['pnps'])."</b> "
			.$this->lang->get("pagenavi_ps")."</a> &nbsp;  <b>".number_format($para['pntc'])."</b> ".$this->lang->get("pagenavi_record")." / <b>".number_format($totalpage)."</b> ".$this->lang->get("pagenavi_page")." &nbsp;";
       if($_REQUEST['isheader'] != '0'){
           $str .= "<button name=\"initbtn\" onclick=\"javascript:pnAction('".$this->getInitUrl()."');\">".$this->lang->get("pagenavi_pinit")."</button>&nbsp;";
           $str .= "<button name=\"initbtn2\" onclick=\"javascript:doAction('".str_replace("=list","=list-toexcel",$para['url']."&pnpn=".$para['pnpn'])
               ."&needautopickup=no');\" title=\"".$this->lang->get("func_exportxlsx_hint")."\">".$this->lang->get("func_exportxlsx")."</button>";
       }

       return $str;

   }
	
   #
   function getInitUrl(){
        $fieldlist = array('tbl','tit','db');
        $file = $_SERVER['PHP_SELF'];
        $query = "";
        foreach($_REQUEST as $k=>$v){
            if(in_array($k, $fieldlist)){
                $query .= "&".$k."=".$v;
            }
        }
        $query = "?".self::SID.'='.$_REQUEST[self::SID].'&'.substr($query,1);
        return $file.$query;
   }

	#
   function getOrder(){
        $order = "";
        foreach($_REQUEST as $k=>$v){
            if(strpos($k,"pnob") === 0){
                $order .= substr($k,4);
                if($v == 1){
                    $order .= " desc";
                }
                $order .= ",";
                #break; # allow multiple order fields
            }
        }
        if($order != ''){
            $order .= "1 "; # + "order by 1 ", compatible with this->get('isasc');
        }
        #debug(__FILE__.":getOrder:$order");
		$this->set('orderby', $order);
        return $order;
   }
	
	#
   function getAsc($field=''){
       $isasc = 0; # 0: 0->1, asc; 1: 1->0, desc
       if($field == ''){
			if(array_key_exists('isasc',$this->hmf)){
                $isasc = $this->hmf['isasc'];
            }
			else{
				# n/a
			}
       }
	   else{
           foreach($_REQUEST as $k=>$v){
               if($field == substr($k,4) && strpos($k,"pnob") === 0){
                   if($v == 1){
                       $isasc = 1;
                       $this->hmf['isasc'] = $isasc;
                       break;
                   }
               }
           }
       }
       return $isasc;
   }

	#
   function getCondition($gtbl, $user){
       $condition = "";
       $pnsm = $_REQUEST['pnsm'];
       $pnsm = ($pnsm=='' || $pnsm=='0') ? "or" : $pnsm;
       $pnsm = $pnsm=='1'? "and" : $pnsm;
       $hmfield = $gtbl->getFieldList();

	   $skiptag = Gconf::get('skiptag');
	   $hasId = $gtbl->get('hasid');
       $myId = $gtbl->getMyId();
	   $isTimeField = false;
       $hasPnskId = false;
       if(Wht::get($_REQUEST, "pnskid") != ''){
           $hasPnskId = true;
       }
	   
       $hidesk = $gtbl->getHideSk($user); # xml/fin_todotbl.xml
       if($hidesk != '' && !$hasid && !$hasPnskId){ # why so?
           $harr = explode("|", $hidesk);
           foreach($harr as $k=>$v){
               $harr2 = explode("::", $v);
               $tmpfield = $harr2[0];
               $tmpop = $harr2[1];
               $tmpval = $harr2[2];
			   if(!isset($_REQUEST['pnsk'.$tmpfield])){
               		$_REQUEST['oppnsk'.$tmpfield] = $tmpop;
					$_REQUEST['pnsk'.$tmpfield] = $tmpval;
			   }
			   else{
				   debug(" found hidesk:[$tmpfield] but override by user request:[".$_REQUEST['pnsk'.$tmpfield]."]");
			   }
           }
       }
	   # error_log(__FILE__.": req:".$this->toString($_REQUEST));
	   
       foreach($_REQUEST as $k=>$v){
            if($k != 'pnsk' && strpos($k,"pnsk") === 0){
                $field = substr($k, 4);
				#error_log(__FILE__.": k:$k, field:$field");
                $linkfield = $field;
                if(strpos($field,"=") !== false){
                    $arr = explode("=", $field);
                    $field = $arr[0];
                    $linkfield = $arr[1];
                }
				if(isset($_REQUEST[$field]) && $_REQUEST[$field] != ''
					&& $_REQUEST[$field] != $v){
					$v = $_REQUEST[$field];
				}
				if(strpos($hmfield[$field],'date') !== false
                        || strpos($hmfield[$field],'time') !== false){
                    $isTimeField = true;    
                }
				# op list
                if(strpos($v, "tbl:") === 0){
                    $condition .= " ".$pnsm." ".$field." in (".$this->embedSql($linkfield,$v).")";
                }
				else if(strpos($v, "in::") === 0){
					# <hidesk>tuanid=id::in::tbl:hss_tuanduitbl:operatearea=IN=USER_OPERATEAREA</hidesk>
                    #error_log(__FILE__.": k:$k, v:$v");
                    $tmparr = explode("::", $v);
                    $tmpop = $tmparr[0];
                    $tmpval = $tmparr[1];
                    if(strpos($tmpval,"tbl:") === 0){
                        $tmpval = $this->embedSql($linkfield, $tmpval);
                    }
					else{
                        $tmpval = $this->addQuote($tmpval);
                    }
                    $condition .= " $pnsm $field in ($tmpval)";
                }
				else{
                    # remedy on Sun Jun 17 07:54:59 CST 2012 by wadelau
                    $fieldopv = $_REQUEST['oppnsk'.$field]; # refer to ./class/gtbl.class.php: getLogicOp,
                    if($fieldopv == null || $fieldopv == ''){
                        $fieldopv = "=";
                    }
					else{
						if(startswith($fieldopv, '%')){
							$fieldopv = urldecode($fieldopv);
						}
                        $fieldopv = str_replace('&lt;', '<', $fieldopv);
                    }
					if($fieldopv == $skiptag){
						# omit...
						continue;
					}
                    if($fieldopv == 'inlist'){
                        if($this->isNumeric($hmfield[$field]) && strpos($hmfiled[$field],'date') === false){
                            # numeric
                        }else{
							$v = str_replace("，",",", $v);
                            $v = $this->addQuote($v);
                        }
                        $condition .= " ".$pnsm." $field in ($v)";
						$gtbl->del($field);
                    }
					else if($fieldopv == 'inrange'){
						$v = str_replace("，",",", $v);
                        $tmparr = explode(",", $v);
                        if(isset($tmparr[1])){
                            if(!$isTimeField){
                                $condition .= " ".$pnsm." ($field >= ".$tmparr[0]." and $field <= ".$tmparr[1].")";
                            }
                            else{
                                $condition .= " ".$pnsm." ($field >= '".$tmparr[0]."' and $field <= '".$tmparr[1]."')";
                            }
                        }
                        else{
                            if(!$isTimeField){
                                $condition .= " ".$pnsm." ($field >= ".$tmparr[0].")";
                            }
                            else{
                                $condition .= " ".$pnsm." ($field >= '".$tmparr[0]."')";
                            }
                        }
						$gtbl->del($field);
                    }
					else if($fieldopv == 'inrangelist'){
						$v = str_replace("，",",", $v);
                        $tmparr = explode(",", $v);
                        $conditionTmp = ' 1=0 '; 
                        $arrSize = count($tmparr); $ai = 0;
                        #foreach($tmparr as $tmpk=>$tmpv){
						while($ai < $arrSize){
							$tmpv = $tmparr[$ai];
							if(inString('~', $tmpv)){
                            	$tmpArr2 = explode('~', $tmpv);
							}
							else{
								# compatible with "a,b,c~d" , Sat Mar 28 12:05:31 CST 2020
								$tmpArr2 = array($tmpv, $tmparr[$ai+1]);	
								$ai++;
							}
                            if(count($tmpArr2) > 1 && $tmpArr2[1] !== ''){
                                $tmpbgn = $tmpArr2[0];
                                $tmpend = $tmpArr2[1];
                                if(!$isTimeField){
                                    $conditionTmp .= " or ($field >= ".$tmpbgn." and $field < ".$tmpend.")";
                                }
                                else{
                                    $conditionTmp .= " or ($field >= '".$tmpbgn."' and $field < '".$tmpend."')";
                                }
                            }
                            else{
                                $tmpbgn = $tmpArr2[0];
                                if(!$isTimeField){
                                    $conditionTmp .= " or ($field >= ".$tmpbgn.")";
                                }
                                else{
                                    $conditionTmp .= " or ($field >= '".$tmpbgn."')";
                                }
                            }
							$ai++;
                        }
                        $condition .= " $pnsm ($conditionTmp)";
						$gtbl->del($field);
                    }
					else if($fieldopv == 'contains'){
                        $condition .= " ".$pnsm." "."$field like ?";
                        $gtbl->set($field, "%".str_replace(' ','%',$v)."%");
                    }
					else if($fieldopv == 'notcontains'){
                        $condition .= " ".$pnsm." "."$field not like ?";
                        $gtbl->set($field, "%".str_replace(' ','%',$v)."%");
                    }
					else if($fieldopv == 'containslist'){
                        $isString = false;
                        if($this->isNumeric($hmfield[$field]) && strpos($hmfiled[$field],'date') === false){
                            # numeric
                        }
                        else{
                            $isString = true;
                        }
					    $v = str_replace("，", ",", $v);
                        $vArr = explode(",", $v);
                        $conditionTmp = " 1=0 ";
                        foreach($vArr as $vk=>$vv){
                            #$vv = $this->addQuote($vv);
                            $vv = trim($vv);
							if(startsWith($vv, 'b62x.')){
                                $vv = substr($vv, 5);
                                $vv = Base62x::decode($vv);
                                debug("\tfound base62x and decode:$vv aft");
                            }
                            $vv = addslashes($vv);
                            $vv = "%$vv%";
                            if($isString){ $vv = "'$vv'"; }
                            $conditionTmp .= " or $field like $vv";
                        }
                        $condition .= " $pnsm ($conditionTmp)";
						$gtbl->del($field);
                    }
					else if($fieldopv == 'notcontainslist'){
                        $isString = false;
                        if($this->isNumeric($hmfield[$field]) && strpos($hmfiled[$field],'date') === false){
                            # numeric
                        }
                        else{
                            $isString = true;
                        }
					    $v = str_replace("，",",", $v);
                        $vArr = explode(",", $v);
                        $conditionTmp = " 1=1 ";
                        foreach($vArr as $vk=>$vv){
                            #$vv = $this->addQuote($vv);
                            $vv = trim($vv);
                            if(startsWith($vv, 'b62x.')){
                                $vv = substr($vv, 5);
                                $vv = Base62x::decode($vv);
                                debug("\tfound base62x and decode:$vv aft");
                            }
                            $vv = addslashes($vv);
                            $vv = "%$vv%";
                            if($isString){ $vv = "'$vv'"; }
                            $conditionTmp .= " and $field not like $vv";
                        }
                        $condition .= " $pnsm ($conditionTmp)";
						$gtbl->del($field);
                    }
					else if($fieldopv == 'startswith'){
                        $condition .= " ".$pnsm." "."$field like ?";
                        $gtbl->set($field, $v."%");
                    }
					else if($fieldopv == 'endswith'){
                        $condition .= " ".$pnsm." "."$field like ?";
                        $gtbl->set($field, "%".$v);
					}
					else if($fieldopv == '!='){
                        $condition .= " ".$pnsm." "."$field <> ?";
                        $gtbl->set($field, $v);
                    }
					else if($fieldopv == 'notregexp'){
                        $condition .= " ".$pnsm." "."$field not regexp ?";
                        $gtbl->set($field, $v);
                    }
					else{
                        $condition .= " ".$pnsm." $field $fieldopv ?"; # this should be numeric only.
                        $gtbl->set($field, $v);
                    }
					if($hasId && $field == $myId && $fieldopv == '=' && $pnsm == 'and'){
					    # use primary or unique key to query precisely, 
						# June 27, 2018, Fri, 16 Dec 2016 19:29:44 +0800
						# updt Oct 30, 2018, + pnsm=and
						debug("field:$field/v:$v reached end.");
					    break;
					}
                }
            }
       }
       $condition = substr($condition, 4); # first pnsm seg
       #error_log(__FILE__.":getCondition: condition: $condition");
       $pnsc = $_REQUEST['pnsc'];
       if($pnsc != ''){
		    $pnsc = base62x($pnsc, $dec=1);
            $chkpnsc = $this->signPara($pnsc, $_REQUEST['pnsck']);
            if($chkpnsc){
                $condition = $pnsc;
            }
       }
       #error_log(__FILE__.":getCondition final: condition: $condition");
       return $condition;
   }

   //- sign a preset condition para, if given a $myk, validate it
   //- added on Sat May 12 17:46:10 CST 2012
   function signPara($para,$myk=''){
        $sharekey = 'Wadelau_20120512_*(&^&****)';
        $mydate = date("Y-m-d");
        $myk2 = substr(sha1($para.$sharekey.$mydate),0,8);
        if(!isset($myk) || $myk == ''){
            $myk = $myk2;

        }else{
            if($myk == $myk2){
                $myk = true;

            }else{
                $myk = false;
            }
        }
        return $myk;
   }

   //- add quote
   function addQuote($str){
       $tmpval = $str;
       if(strpos($str,",") !== false){
           $arr = explode(",", $str);
           $tmpval = '';
           foreach($arr as $k12=>$v12){
			   $v12 = trim($v12);
			   $v12 = addslashes($v12);
               $tmpval .= "'".$v12."',";
           }
           $tmpval = substr($tmpval, 0, strlen($tmpval)-1);
       }else{
		   $str = addslashes($str);
           $tmpval = "'".$str."'";
       }
       return $tmpval;
   }

   function embedSql($field,$v){
       $condition = "";
       $varr = explode(":",$v);
       $varr2 = explode("=",$varr[2]);
       $tmpop = "=";
       $tmpval = "'".$varr2[1]."'";
       if($varr2[1] == 'IN'){
            $tmpop = $varr2[1];
            $tmpval = $varr2[2];
            $tmpval = "(".$this->addQuote($tmpval).")";
       }
       # remedy for tablename.fieldname, Tue Nov 28 22:25:17 CST 2017
       # e.g. pnskistate=1&pnskiunion=in::tbl:unioninfo.unionname:allowex=1&pnsm=1&oppnskiunion=in
       if(inString('.', $varr[1])){
           $varr3 = explode('.', $varr[1]);
           $varr[1] = $varr3[0];
           $field = $varr3[1];
       }
       $condition .= "select $field from ".$varr[1]." where ".$varr2[0]." ".$tmpop." ".$tmpval." order by ".$this->getMyId()." desc";
       return $condition;
   }
}

