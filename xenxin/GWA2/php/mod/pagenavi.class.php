<?php
/* PageNavi class
 * v0.3
 * wadelau@ufqi.com
 * Tue Jan 24 12:25:56 GMT 2012
 * bugfix, 21:22 28 February 2017
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

   public function __construct(){

       $para = array();
       $pdef = array('pnpn'=>1,'pnps'=>100,'pntc'=>0, 'navilen'=>10); # 28 for development

       $file = $_SERVER['PHP_SELF'];
       $query = $_SERVER['QUERY_STRING'];
       
       if(strpos($query, "act/list-") !== false){
            $query = preg_replace("/&act=list\-([0-9a-z]*)/","&act=list",$_SERVER['QUERY_STRING']);
            $this->hmf['neednewpntc'] = 1 ;
            $query = preg_replace("/&pntc=([0-9]*)/","", $query);
       }

       $url = $file."?".preg_replace("/&pnpn=([0-9]*)/","",$query);
       
       #print __FILE__.": orig_url: $url";
       //$url=str_replace("&","/",$url); # for RESTful address
       //$url=str_replace("=","/",$url);

       $this->hmf['url'] = $url;
 
       foreach($_REQUEST as $k=>$v){
           $para[$k] = ($v==''||$v==null)?$pdef[$k]:$v;
           $this->hmf[$k]=$para[$k];
       }
       foreach($pdef as $k=>$v){
            $para[$k] = $para[$k]>0?$para[$k]:$pdef[$k];
            $this->hmf[$k]=$para[$k];
       }
       
       # Parent constructors are not called implicitly
       #     if the child class defines a constructor.
       # In order to run a parent constructor, a call to parent::__construct()
       #     within the child constructor is required.
       parent::__construct();

   }

   function getNavi(){
       $para = $this->hmf;
       if($this->hmf['totalcount'] > 0){
            $para['pntc'] = $this->hmf['totalcount'];
            $this->hmf['url'] = preg_replace("/&pntc\/([0-9]*)/","", $this->hmf['url']);
            //$this->hmf['url'] .= "/pntc/".$para['pntc'];
	    if(!inString('?', $this->hmf['url'])){
		    $this->hmf['url'] .= "?_0101=1";
	    }
            $this->hmf['url'] .= "&pntc=".$para['pntc'];
            $para['url'] = $this->hmf['url'];
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
       $navilen = $para['navilen'];
       $str = "&nbsp;&nbsp;<b>页号: &nbsp;<a href=\"javascript:pnAction('".$para['url']."&pnpn=1');\" title=\"第一页\">|&laquo;</a></b>&nbsp; ";

       for($i=$para['pnpn']-$navilen; $i<$para['pnpn'] + $navilen && $i<=$totalpage; $i++){
           if($i>0){
               if($i == $para['pnpn']){
                    $str .= " <span id=\"currentpage\" style=\"color:green;font-weight:bold;font-size:18px\">".$i."</span> ";
               }else{
                    $str .= " <a href=\"javascript:pnAction('".$para['url']."&pnpn=".$i."');\" style=\"font-size:14px\">".$i."</a> ";
               }
           }
			#print "$i: [$str] totalpage:[$totalpage]\n";
       }
       $str .= " &nbsp;<b><a href=\"javascript:pnAction('".$para['url']."&pnpn=".$totalpage."');\" title=\"最后一页\">&raquo;|</a> </b> &nbsp; &nbsp; <a href=\"javascript:void(0);\" title=\"改变显示条数\" onclick=\"javascript:var pnps=window.prompt('请输入新的每页显示条数:','".$para['pnps']."'); if(pnps>0){ myurl='".$para['url']."'; myurl=myurl.replace('/pnps/','/opnps/'); doAction(myurl+'&pnps='+pnps);};\"><b>".$para['pnps']."</b>条/页</a> &nbsp; 共 <b>".number_format($para['pntc'])."</b>条 / <b>".number_format($totalpage)."</b>页 &nbsp;";
       if($_REQUEST['isheader'] != '0'){
           $str .= "<button name=\"initbtn\" onclick=\"javascript:pnAction('".$this->getInitUrl()."');\">初始页</button>&nbsp;";
           $str .= "<button name=\"initbtn2\" onclick=\"javascript:doAction('".str_replace("&list","&list-toexcel",$para['url'])."');\">导出表</button>";
       }

       return $str;

   }

   function getNaviNum(){
       $para = $this->hmf;
			 #print __FILE__.":getNavi: ".$this->hmf['url'];
       #print_r($para);
       if($this->hmf['totalcount'] > 0){
            $para['pntc'] = $this->hmf['totalcount'];
            $this->hmf['url'] = preg_replace("/\/pntc\/([0-9]*)/","", $this->hmf['url']); # RESTful format
            if(!inString('?', $this->hmf['url'])){
		    $this->hmf['url'] .= "?_0101=1";
	    }
	    $this->hmf['url'] .= "&pntc=".$para['pntc'];
            $para['url'] = $this->hmf['url'];
       }

       #print_r($this->hmf);

       $totalpage = $para['pntc'] % $para['pnps'] == 0 ? ($para['pntc']/$para['pnps']) : ceil($para['pntc']/$para['pnps']);
       $navilen = $para['navilen'];

		$pageArr = array('totalpage'=>$totalpage, 'totalrecord'=>$para['pntc'], 'url'=>$para['url']);

        for($i=$para['pnpn']-$navilen; $i<$para['pnpn'] + $navilen && $i<=$totalpage; $i++){

            if($i>0){
                if($i == $para['pnpn']){
                    $pageArr['pnpn'] = $i;
                }
                $pageArr['pages'][] = $i;

            }
            #print "$i: [$str] totalpage:[$totalpage]\n";
        }
		if(!isset($pageArr['pnpn'])){
			$pageArr['pnpn'] = 1;
		}
        $pageArr['pnps'] = $para['pnps'];

       return $pageArr;

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
        $query = "?".substr($query,1);
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
			$order .= "1 "; # + "order by 1 ", compatible with this->get('isasc')
		}
        #error_log(__FILE__.":getOrder:$order");
        return $order;
   }

   function getAsc($field=''){
       $isasc = 0; # 0: 0->1, asc, 1: 1->0, desc
       if(array_key_exists('isasc',$this->hmf)){
            if($field == '' || ($field != '' && $this->getOrder() == $field)){
                $isasc = $this->hmf['isasc'];
            }
       }else{
           foreach($_REQUEST as $k=>$v){
               if(($field == '' || $field == substr($k,4)) && strpos($k,"pnob") === 0){
                   if($v == 1){
                       $isasc = 1;
                       break;
                   }
               }
           }
       }
       return $isasc;
   }

   function getCondition($gtbl, $user){
       $condition = "";
       $pnsm = $_REQUEST['pnsm']; $pnsm = $pnsm==''?"or":'and';
       $hmfield = array(); #  $gtbl->getFieldList(); # for -gMIS only
       if(false && count($hmfield) < 1){
       		$hmfield = array();
       		$tmpHm = $gtbl->execBy("desc ".$gtbl->getTbl());
			if($tmpHm[0]){
				$tmpHm = $tmpHm[1];
				foreach($tmpHm as $k=>$v){
					$field = $v['Field'];
					$fieldv = "fieldtype=".$v['Type'];
					if(strtolower($field)=='id'){
						$field = strtolower($field);
					}
					$hmfield[$field] = $fieldv;
				}
			}
       }

       $objpnps = $gtbl->get("pagesize");
       if($objpnps > 0){
        $this->hmf['pnps'] = $objpnps; # in case that pnps does not exists in URL,  remedy by wadelau, Mon Nov 19 11:18:52 CST 2012
       }

       $hidesk = ""; # $gtbl->getHideSk($user); # for -gMIS only
       if($hidesk != ''){
           $harr = explode("|", $hidesk);
           foreach($harr as $k=>$v){
               $harr2 = explode("::", $v);
               $tmpfield = $harr2[0];
               $tmpop = $harr2[1];
               $tmpval = $harr2[2];
               $_REQUEST['pnsk'.$tmpfield] = $tmpop."::".$tmpval;
           }
       }
       #error_log(__FILE__.": req:".$this->toString($_REQUEST));
	   $isTimeField = false;
       foreach($_REQUEST as $k=>$v){
            if($k != 'pnsk' && strpos($k,"pnsk") === 0){
                $field = substr($k, 4);
                $linkfield = $field;
                if(strpos($field,"=") !== false){
                    $arr = explode("=", $field);
                    $field = $arr[0];
                    $linkfield = $arr[1];
                }
				# for select
       	   		if(isset($_REQUEST[$field]) && $_REQUEST[$field] != ''
					&& $_REQUEST[$field] != $v){
       	   			$v = $_REQUEST[$field];
       	   		}
       	   		if(strlen($v) > 3 && startsWith($v, '%')){
       	   			$v = urldecode($v);
       	   		}
				if(strpos($hmfield[$field],'date') !== false
                        || strpos($hmfield[$field],'time') !== false){
                    $isTimeField = true;    
                }
				# operator list
                if(strpos($v,"tbl:") === 0){ #http://ufqi.com:81/dev/gtbl/ido.php?tbl=hss_dijietbl&tit=%E5%AF%BC%E6%B8%B8%E8%A1%8C%E7%A8%8B&db=&pnsktuanid=tbl:hss_findaoyoutbl:id=2
                    $condition .= " ".$pnsm." ".$field." in (".$this->embedSql($linkfield,$v).")";
                
                }
				else if(strpos($v,"in::") === 0){ # <hidesk>tuanid=id::in::tbl:hss_tuanduitbl:operatearea=IN=USER_OPERATEAREA</hidesk>
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
                    $condition .= " and $field in ($tmpval)";
                }
				else{
                    # remedy on Sun Jun 17 07:54:59 CST 2012 by wadelau
                    $fieldopv = $_REQUEST['oppnsk'.$field]; # refer to ./class/gtbl.class.php: getLogicOp,
                    if($fieldopv == null || $fieldopv == ''){
                        $fieldopv = "=";
                    }
					else{
						if(startsWith($fieldopv, '%')){
							$fieldopv = urldecode($fieldopv);
						}
                        $fieldopv = str_replace('&lt;', '<', $fieldopv);
						$fieldopv = str_replace('&gt;', '>', $fieldopv);
                    }
                    if($fieldopv == 'inlist'){
                        if($this->isNumeric($hmfield[$field])
							&& strpos($hmfield[$field],'date') === false){
                            # numeric
                        }
						else{
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
                        foreach($tmparr as $tmpk=>$tmpv){
                            $tmpArr2 = explode('~', $tmpv);
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
                        }
                        $condition .= " $pnsm ($conditionTmp)";
						$gtbl->del($field);
                    }
					else if($fieldopv == 'contains'){
                        $condition .= " ".$pnsm." "."$field like ?";
                        $gtbl->set($field, "%".$v."%");
                    }
					else if($fieldopv == 'notcontains'){
                        $condition .= " ".$pnsm." "."$field not like ?";
                        $gtbl->set($field, "%".$v."%");
                    }
					else if($fieldopv == 'containslist'){
                        $isString = false;
                        if($this->isNumeric($hmfield[$field]) && strpos($hmfiled[$field],'date') === false){
                            # numeric
                        }
                        else{
                            $isString = true;
                        }
					    $v = str_replace("，",",", $v);
                        $vArr = explode(",", $v);
                        $conditionTmp = " 1=0 ";
                        foreach($vArr as $vk=>$vv){
                            #$vv = $this->addQuote($vv);
                            $vv = trim($vv);
                            $vv = addslashes($vv);
                            $vv = "%$vv%";
                            if($isString){ $vv = "'$vv'"; }
                            $conditionTmp .= " or $field like $vv";
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
					else if($fieldopv == 'regexp'){
                        $condition .= " ".$pnsm." "."$field regexp ?";
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
                }
            }
       }

       $condition = substr($condition, 4); # first pnsm seg
       #error_log(__FILE__.":getCondition: condition: $condition");
       $pnsc = $_REQUEST['pnsc'];
       if($pnsc != ''){
            $chkpnsc = $this->signPara($pnsc, $_REQUEST['pnsck']);
            if($chkpnsc){
                $condition = $pnsc;
            }
       }
       #error_log(__FILE__.":getCondition -2 : condition: $condition");
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
               $tmpval .= "'".addslashes(trim($v12))."',";
           }
           $tmpval = substr($tmpval, 0, strlen($tmpval)-1);
       }
       else{
           $tmpval = "'".addslashes(trim($str))."'";
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
       # e.g. pnskstate=1&pnskunion=in::tbl:unioninfo.unionname:allowx=1&pnsm=1&oppnskunion=in
       if(inString('.', $varr[1])){
           $varr3 = explode('.', $varr[1]);
           $varr[1] = $varr3[0];
           $field = $varr3[1];
       }
       $condition .= "select $field from ".$varr[1]." where ".$varr2[0]." ".$tmpop." ".$tmpval." order by id desc";
       return $condition;
   }
   
   //-
   //- isNumeric
   # test whether a field is numeric or not
   # added on Mon Jun 18 20:43:55 CST 2012
   public function isNumeric($fieldtype){
	   	$isNumeric = 0;
	   	if(strpos($fieldtype, "int") !== false
	   			|| strpos($fieldtype, "float") !== false
	   			|| strpos($fieldtype, "double") !== false
	   			|| strpos($fieldtype, "date") !== false
	   			|| strpos($fieldtype, "decimal") !== false){
	   
	   				$isNumeric = 1;
	   	}
	   	return $isNumeric;
   }
   
    
   
}

