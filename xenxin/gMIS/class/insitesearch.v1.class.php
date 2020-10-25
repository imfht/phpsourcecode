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
    
    const SID = 'sid';

    #
    public function __construct($args=null){

        $this->setTbl(GConf::get('tblpre').'insitesearchtbl');

        //- init parent
        parent::__construct($args);


    }

    # 
    public function __destruct(){
    
    }

    #
    #
    public function saveInfo($list, $itbl, $idb){
        $rtn = 0;
        $isep = "\t\t";
        $targetList = array();
        $md5List = '"0"';
        foreach($list as $k=>$v){
            foreach($v as $k2=>$v2){
                $ifield = $k2;
                $v2 = trim($v2);
                if($v2 == ''){
                    #debug($msg="$k: [$itbl] - [$k2] - [$v2] -> empty, skip\n");
                    #$rtn .= $msg;
                    continue;
                }
                else if(is_numeric($v2)){
                    #debug($msg="$k: [$itbl] - [$k2] - [$v2] -> number, skip\n");
                    #$rtn .= $msg;
                    continue;
                }
                else if(startsWith($v2, 'http')){
                    continue;
                }
                #debug($msg=($counti++)."-$k: [$itbl] - [$k2] - [$v2] validated.\n");
                #$rtn .= $msg;
                $tmpVal = $idb.$isep.$itbl.$isep.$ifield.$isep.$v2; 
                $imd5 = md5($tmpVal);
                $md5List .= ',"'.$imd5.'"';
                $targetList[$imd5] = $tmpVal;
            }
        }
        # remove old
        $hm = $this->execBy($sql='select id, imd5 from '.$this->getTbl()
            ." where imd5 in ($md5List)", null, 
            null); # too long key? $withCache=array('key'=>'read-data-2-'.$md5List)
        #debug("read old sql:$sql");
        if($hm[0]){
            $hm = $hm[1];
            foreach($hm as $k=>$v){
                #debug("try to unset v:".serialize($v));
                unset($targetList[$v['imd5']]);
            }
        }
        else{
            debug("$idb $itbl read old return empty.");
        }
        # save new
        debug("reach targetList:".serialize($targetList));
        #print_r($targetList);
        foreach($targetList as $k=>$v){
            $imd5 = $k;
            $tmpArr = explode($isep, $v);
            $idb = $tmpArr[0];
            $itbl = $tmpArr[1];
            $ifield = $tmpArr[2];
            $ival = str_replace('\'', '\\\'', $tmpArr[3]);
            $hm = $this->execBy($sql="insert into ".$this->getTbl()
                ." set idb='$idb', itbl='$itbl',"
                ." ifield='$ifield', ivalue='$ival', imd5='$imd5'", null);
            debug("imd5:$imd5, insert sql:$sql\n");
            if($hm[0]){
                $rtn .= $v . " save succ\n";
            }
            else{
                $rtn .= $v . " save fail\n";
            }
        }
        return $rtn; 
    }

    # 
    #


 
}

?>
