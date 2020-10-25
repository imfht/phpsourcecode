<?php

/* In-site Search class
 * v0.1,
 * wadelau@ufqi.com
 * Tue May 29 19:58:37 CST 2018
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__.'/inc/webapp.class.php');

class InSiteSearch extends WebApp{

    var $ver = 0.01;
    var $currTime = '';
    
    const SID = 'sid';

    #
    public function __construct($args=null){

        $this->setTbl(GConf::get('tblpre').'insitesearchtbl');

        //- init parent
        parent::__construct($args);

        $this->currTime = date("Y-m-d H:i:s", time());

    }

    # 
    public function __destruct(){
    
    }

    #
    #
    public function saveInfo($rowList, $itbl, $idb){
        $rtn = 0;
        $isep = "\t\t";
        $targetList = array();
        $md5List = '"0"';
        $fieldList = array();
        # rows
        foreach($rowList as $k=>$v){
            # fields
            foreach($v as $k2=>$v2){
                $ifield = $k2;
                if(!isset($fieldList[$ifield])){
                    $fieldList[$ifield] = 1;
                    $targetList[$ifield]['emptyc'] = 0;
                    $targetList[$ifield]['numberc']++;
                    $targetList[$ifield]['inblacklist'] = 0;
                }
                $v2 = trim($v2);
                if($v2 == ''){
                    $targetList[$ifield]['emptyc']++;
                    continue;
                }
                else if(is_numeric($v2)){
                    $targetList[$ifield]['numberc']++;
                    continue;
                }
                else if(startsWith($v2, 'http')){
                    $targetList[$ifield]['inblacklist'] = 1;
                    continue;
                }
            }
        }
        # remove unwelcome
        #debug("bfr trim targetList:".serialize($fieldList));
        $rowCount = count($rowList);
        $tmpFieldList = array();
        foreach($fieldList as $k=>$v){
            $ifield = $k;
            $needRm = false;
            if($targetList[$ifield]['inblacklist'] == 1){
                $needRm = true;
            }
            else if($targetList[$ifield]['emptyc'] > $rowCount * 0.8){ # why .6?
                $needRm = true;
            }
            else if($targetList[$ifield]['numberc'] > $rowCount * 0.5){ # why .1?
                $needRm = true;
            }
            if(!$needRm){
                $tmpFieldList[$ifield] = 1;
            }
        } 
        $fieldList = $tmpFieldList;
        # save new
        #debug("reach targetList:".serialize($fieldList));
        #print_r($fieldList);
        $succi = 0;
        foreach($fieldList as $k=>$v){
            $ifield = $k;
            $imd5 = md5($tmps=$idb.$isep.$itbl.$isep.$ifield);
            $hm = $this->execBy($sql="select id from ".$this->getTbl()
                    ." where imd5='".$imd5."' limit 1", null);
            if($hm[0]){
                $hm = $this->execBy($sql="update ignore ".$this->getTbl()
                    ." set updatetime='".$this->currTime."'"
                    ." where imd5='$imd5'", null);
                debug("imd5:$imd5, update sql:$sql\n");

                #$rtn .= $tmps . " save succ\n";
                debug($tmps . " update succ\n");
                $succi++;
            }
            else{
                $hm2 = $this->execBy($sql="insert ignore into ".$this->getTbl()
                    ." set idb='$idb', itbl='$itbl',"
                    ." ifield='$ifield', ivalue='',"
                    ." imd5='$imd5', updatetime='".$this->currTime."'"
                    ." ", null);
                if($hm2[0]){
                    debug($tmps . " update failed. try to insert, succc.");
                }
                else{
                    debug($tmps . " update failed. try to insert,but failed again.");
                }

            }
        }
        $rtn = $succi;
        return $rtn; 
    }

    # 
    public function rmOldField(){
        $rtn = 0;
        $hm = $this->execBy($sql="delete from ".$this->getTbl()
            ." where updatetime < '".$this->currTime."'", null);
        if($hm[0]){
            $hm = $hm[1];
            debug("rmOld: succ with sql:$sql\n");
        }
        else{
            debug("rmOld: failed with sql:$sql\n");
        }
    }

    # 
    #


 
}

?>
