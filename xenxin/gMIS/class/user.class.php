<?php
/* User class for user management
 * v0.1,
 * wadelau@ufqi.com,
 * 2011-07-12
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}
require_once(__ROOT__.'/inc/webapp.class.php');
require_once(__ROOT__.'/inc/session.class.php');

class User extends WebApp{
    
    const Uid_Tag = 'ui';
    const Data_Sep = ':::';
    const gMIS_Sid = 'gMIS-Sid';
    
	var $sep = "|";
	var $eml = "email";
    var $objgrp = "objgrp";
    var $objid = "objid";
    var $objfield = "objfield";
    var $accessid = "accessid";
    var $accesstype = "accesstype";
    var $accessinfo = "accessinfo";
    var $accessLevel = array('deny'=>0,'read'=>1,'write'=>2,'delete'=>3);
    var $rgtArr = array('supacc'=>false,'r'=>false,'w'=>false,'d'=>false);
    var $specifyAcc = array(); # 记录针对当前用户或者当前组的特殊权限, Sun May 13 16:42:45 CST 2012
    var $session = null;

	//-
	function __construct($args=null){
		//-
		#$this->dba = new DBA();
        #$this->setTbl($_CONFIG['usertbl']);
        #$this->setTbl("hss_info_usertbl");
		# inherit parent's resrc
		parent::__construct($args);
		$this->session = new SESSIONX($args);
		$this->set('sid_tag', self::gMIS_Sid);
	}
	//-
	function getEmail(){
		return $this->get($this->eml);
	}

	function isLogin(){
		return $this->getId() != '';
	}

	function getLat(){
		return $this->get('lat');
	}
	
	function getLng(){
		return $this->get('lng');
	}
	
    function getGroup(){
        return $this->get('usergroup');
    }

	function setPassword($pwd){
		$this->hmf['password'] = SHA1($pwd);
	}

	//- chk whethere the current ip has been used for regi in n days
	function chkIpRegi(){
		$days = 2; # same ip within n days will need validating reCAPTCHA
		$hasexist = false;
		$hm = $this->getBy("id","DATE_ADD(createtime, INTERVAL $days DAY) > NOW() and ip=?",
			$withCache=array('key'=>'user-check-reg-ip-'.date("Y-m-d", time())));
		if($hm[0]){
			$hasexist = true;
		}
		return $hasexist;
	}
    
    function getObjId(){
        return $this->get($this->objid);
    }

	//- remedy to open-end policy, 16:44 Tuesday, April 14, 2020
    function chkAccess($req){
        #print "userid:[".$this->getId()."] usergroup:[".$this->getGroup()."]\n";
        $reason = '';
        $result = true;
        $tmptbl = GConf::get('tblpre').$req['tbl'];
        $tmphm = $this->execBy('select id as objid, objgroup from '.GConf::get('tblpre').'info_objecttbl where (tblname="'.addslashes($tmptbl).'" or tblname="'.addslashes(trim($req['tbl'])).'")', '',
			$withCache=array('key'=>'info_object-'.$tmptbl));
        #print_r($tmphm);
        $objgrp = ''; $objid = '';
        if($tmphm[0]){
            $tmphm = $tmphm[1];
            $objgrp = $tmphm[0]['objgroup'];
            $objid = $tmphm[0]['objid'];
            $this->set($this->objgrp, $objgrp);
            $this->set($this->objid, $objid);
        }
        #print "obj:[".$req['tbl']."] objgroup:[".$objgrp."] objid:[".$objid."]\n";
		//$objid = $objid=='' ? 'No_Such_Obj' : $objid;
		//$objgrp = $objgrp=='' ? 'No_Such_Grp' : $objgrp;
		$objid = $objid=='' ? 999999 : $objid; # error of int vs. chars in SQL
		$objgrp = $objgrp=='' ? 999999 : $objgrp;
		
		/*
        $sql = "select id,accesstype,objectfield,userid,usergroup from "
			.GConf::get('tblpre')."useraccesstbl where istate=1 and (userid='".$this->getId()
			."' or userid=0) and (usergroup='".$this->getGroup()."' or usergroup=0) and (objectid='"
			.$objid."' or objectid=0) and (objectgroup='".$objgrp."' or objectgroup=0) order by "
			.$this->getMyId()." desc, accesstype desc limit 100";
		*/
		$sql = "select id,accesstype,objectfield,userid,usergroup from "
			.GConf::get('tblpre')."useraccesstbl where istate=1 "
			." and (((userid='".$this->getId()."' or userid=0) " # overall match if correct data
				." and (usergroup='".$this->getGroup()."' or usergroup=0) "
				." and (objectid='".$objid."' or objectid=0) "
				." and (objectgroup='".$objgrp."' or objectgroup=0)) "
			." or (userid='".$this->getId()."' and objectid='".$objid."')) " # exact match without group
			." order by ".$this->getMyId()." desc, accesstype desc limit 100";
        
		//debug("mod/user: objtbl:$tmptbl sql:".$sql);
        $tmphm = $this->execBy($sql, null, $withCache=array('key'=>'useraccess-'.$this->getId().'-'.$this->getGroup().'-'.$objid.'-'.$objgrp.'-3'));
        //debug("mod/user: hmRule: ".json_encode($tmphm));
		
        if($tmphm[0]){
            $tmphm = $tmphm[1];
            #detailed access type will be evaluated in next canRead....
            $this->set($this->accessinfo, $tmphm);
            $maxAccessType = $tmphm[0]['accesstype'];
            $minField = $tmphm[0]['objectfield'];
            $tmpId = $tmphm[0]['id'];
            foreach($tmphm as $k=>$v){
                if($v['accesstype'] > $this->accessLevel['deny']){
                    if($v['userid'] != 0){
                        $this->specifyAcc[$v['userid']] = $v['accesstype'];
                    }
                    if($v['usergroup'] != 0){
                        $this->specifyAcc[$v['usergroup']] = $v['accesstype'];
                    }
                }
                if($v['accesstype'] > $maxAccessType){
                    $maxAccessType = $v['accesstype'];
                    $minField = $v['objectfield'];
                    $tmpId = $v['id'];
                }
            }
            if($maxAccessType > $this->accessLevel['deny']){
                $result =  true;
                $reason = $tmpId.',1427,';
            }
			else if($minField != ''){
                $result = true;
                $reason = $tmpId.',0417';
            }
			else{
                $result = false;
                $reason = $tmpId.',1204,';
            }
        }
		else{
            #$result = false;
			$result = true; # open-end policy
			$reason = 'Open4all. 201004141035.';
        }
        #print_r($this->specifyAcc);
        if(!$result){
            error_log(__FILE__.": access [".$result."]. 201203132129. sql:[$sql] url:[".$_SERVER['REQUEST_URI']."?"
				.$_SERVER['QUERY_STRING']."] reason:[$reason] rec:[".$this->toString($tmphm)."] maxlvl:[".$maxAccessType."]");
        }
        return array('result'=>$result,'reason'=>$reason);
    }
    //-
    function canRead($field, $obj='', $objgrp='', $reqId='', $currentId=''){
        $rtn = array('result'=>true,'reason'=>'');
        if($field != ''){
            if($reqId == $currentId && $this->rgtArr['supacc'] && $this->rgtArr['r']){
                $rtn['result'] = true;
                $rtn['reason'] = "superAcc, 2100";
            }
			else{
                $accessInfo = $this->get($this->accessinfo);
                if(!is_array($accessInfo)){
                    $accessInfo = array();
					$rtn['result'] = true;
					$rtn['reason'] = "Open4all, 1131";
                }
				else{
                foreach($accessInfo as $k=>$v){
                    if($v['accesstype'] < $this->accessLevel['read'] && $this->specifyAcc[$this->getId()] < 1
						&& $this->specifyAcc[$this->getGroup()] < 1 ){
                        if(strpos(",".$v['objectfield'].",", ",".$field.",") !== false){
                            $rtn['result'] = false;
                            $rtn['reason'] = $v['id'].",1341";
                            break;
                        }
                    }
                }
				}
            }
        }
        if(!$rtn['result']){
            error_log(__FILE__.": [$field] canRead [".$rtn['result']."]. 2012apr171939. sql:[$sql] url:["
				.$_SERVER['REQUEST_URI']."] reason:[".$rtn['reason']."] rec:[".$this->toString($tmphm)."]");
        }
        return $rtn['result'];
    }
	//-
    function canWrite($field, $obj='', $objgrp='', $reqId='', $currentId=''){
        $rtn = array('result'=>true,'reason'=>'');
        $accessInfo = $this->get($this->accessinfo);
        if($obj != '' && $field == ''){
            if($reqId == $currentId && $this->rgtArr['supacc'] && $this->rgtArr['w']){
                $rtn['result'] = true;
                $rtn['reason'] = "superAccess, 2059";
            }
			else{
                if(!is_array($accessInfo)){
                    $accessInfo = array();
					$rtn['result'] = true;
					$rtn['reason'] = "Open4all, 1648";
                }
				else{
                $maxLevel = $accessInfo[0]['accesstype'];
                $minField = $tmphm[0]['objectfield'];
                foreach($accessInfo as $k=>$v){
                    if($v['accesstype'] > $maxLevel){
                        $maxLevel = $v['accesstype'];
                        $minField = $tmphm[0]['objectfield'];
                    }
                }
                if($maxLevel < $this->accessLevel['write']
                        && $minField == ''){
                    $rtn['result'] = false;
                    $rtn['reason'] = $v['id'].", 2001";
                }
				}
            }
        }
		else if($field != ''){
            if($reqId == $currentId && $this->rgtArr['supacc'] && $this->rgtArr['w']){
                $rtn['result'] = true;
                $rtn['reason'] = "superAcc, 2108";
            }
			else{
                if(!is_array($accessInfo)){
                    $accessInfo = array();
					$rtn['result'] = true;
					$rtn['reason'] = "Open4all, 1749";
                }
				else{
                foreach($accessInfo as $k=>$v){
                    if($v['accesstype'] < $this->accessLevel['write']){
                        if(strpos(",".$v['objectfield'].",", ",".$field.",") !== false){
                            $rtn['result'] = false;
                            $rtn['reason'] = $v['id'].", 1959";
                            break;
                        }
                    }
                }
				}
            }
        }
        if(!$rtn['result']){
            error_log(__FILE__.": [$field] canWrite [".$rtn['result']."]. 201203132129. sql:[$sql] url:["
				.$_SERVER['REQUEST_URI']."] reason:[".$rtn['reason']."] rec:[".$this->toString($tmphm)."]");
        }
        return $rtn['result'];
    }
	//-
    function canDelete($obj, $objgrp='', $reqId='', $currentId=''){
        if($reqId == $currentId && $this->rgtArr['supacc'] && $this->rgtArr['d']){
            $rtn['result'] = true;
            $rtn['reason'] = "superAcc, 5827";
        }
		else{
            $rtn = array('result'=>true,'reason'=>'');
            $accessInfo = $this->get($this->accessinfo);
            if($obj != ''){
                if(!is_array($accessInfo)){
                    $accessInfo = array();
					$rtn['result'] = true;
					$rtn['reason'] = "Open4all, 1651";
                }
				else{
                $maxLevel = $accessInfo[0]['accesstype'];
                $minField = $tmphm[0]['objectfield'];
                foreach($accessInfo as $k=>$v){
                    if($v['accesstype'] > $maxLevel){
                        $maxLevel = $v['accesstype'];
                        $minField = $tmphm[0]['objectfield'];
                    }
                }
                if($maxLevel < $this->accessLevel['delete']
                        && $minField == ''){
                    $rtn['result'] = false;
                    $rtn['reason'] = $v['id'].", 2104";
                }
				}
            }
        }
        if(!$rtn['result']){
            error_log(__FILE__.": $obj canDelete [".$rtn['result']."]. 201203132129. sql:[$sql] url:["
				.$_SERVER['REQUEST_URI']."] reason:[".$rtn['reason']."] rec:[".$this->toString($tmphm)."]");
        }
        return $rtn['result'];
    }
	//-
    function setSupAcc($mod, $tf){
        error_log(__FILE__.": setSupAcc is called. mod:[$mod] tf:[$tf] \n");
        return $this->rgtArr[$mod] = $tf;
    }
    
    //-
    function getOperateArea($field=''){
        $str = '';
        $str = $opArea = $this->get('operatearea');
        /*
        $opAreaArr = explode(",", $opArea);
        foreach($opAreaArr as $k=>$v){
            $str .= $v."-OR-"; # refer to class/pagenavi.class.php
        }
        if($str != ''){
            $str = substr($str, 0, strlen($str)-4);
            $str = "pnsk".$field."=".$str;
        }
        */
        return $str;
    }

    //- user list
    //- Fri, 18 Nov 2016 19:13:07 +0800
    public function getUserList(){
        $list = array();
        $hm = $this->getBy('*', '1=1',  $withCache=array('key'=>'user-get-list'));
        if($hm[0]){
            $hm = $hm[1];
            if(is_array($hm)){
            foreach ($hm as $k=>$v){
                $list[$v['id']] = $v;
            }
            }
        }
        return $list;
    }
    
    //- get user id from session
    //- Mon, 6 Mar 2017 23:42:59 +0800
    public function getUserBySession($reqt){
        $sid = $this->session->getSid($reqt);
        $uid = '';
        $chk = $this->session->chkSid($this, $reqt);
        if($chk){
            $data = $this->session->getData();
            $uid = $data;
        }
        else{
            # no valid
            debug(__FILE__.": unkn session:[".$sid."]");
        }
        #debug(__FILE__.": get uid:[$uid]");
        return $uid;
    }
    
    //-
    public function getSid($reqt){
        $sid = $this->session->generateSid($this, $reqt);
        return $sid;
    }
    
    //-
    public function getVerifyId(){
        $vid = $this->session->generateVerifyId();
        return $vid;
    }
    
}
?>