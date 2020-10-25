<?php

/* PickUp class
 * v0.1,
 * wadelau@{ufqi, hotmail, gmail}.com
 * Wed Sep 19 CST 2018
 */

if(!defined('__ROOT__')){
    define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__.'/inc/webapp.class.php');

class PickUp extends WebApp{

    const SID = 'sid';
    const PICK_TOP_N = 12;
    const PICK_MAX_FIELD_LENGTH = 12;
    var $ver = 0.01;
    var $fieldList = array();
    
    #
    public function __construct($args=null){

        #$this->setTbl(GConf::get('tblpre').'mytbl');

        //- init parent
        parent::__construct($args);

        $this->currTime = date("Y-m-d H:i:s", time());

    }

    # 
    public function __destruct(){
        # @todo
    }

    # public methods
    # get option list by field
    public function getOptionList($field, $fieldinputtype){
        $options = array();
        $hmfield = $this->fieldList;
        if(count($hmfield) < 1){
            $this->fieldList = $this->get('fieldlist');
        }
        $fieldtype = $hmfield[$field];
        $tbl = $this->getTbl();
        $myId = $this->get('myid');
        $prtFieldType = 'string';
        $isTimeField = false; 
        if(inString('time', $fieldtype) || inString("date", $fieldtype)){ 
            $isTimeField = true; 
        }
        $fieldDefineLength = $this->_getFieldDefineLength($field, $fieldtype);
        if($fieldDefineLength > self::PICK_MAX_FIELD_LENGTH * 20){ # why 20? <255?
            #debug("\tfield:$field has too long:$fieldDefineLength skip....\n");
        }
        else if(inString("char", $fieldtype) || $fieldinputtype == 'select'){

            $fieldUniq = $field.'_uniq_all';
            $hm = $this->execBy("select substr($field, 1, ".self::PICK_MAX_FIELD_LENGTH
                    .") as $fieldUniq, count($myId) as icount from $tbl "
                    ." where 1=1 group by $fieldUniq order by icount desc limit ".self::PICK_TOP_N, null, 
                $withCache=array('key'=>"read-pickup-$tbl-$field")); 
            if($hm[0]){
                $options = $hm[1];
            }
            #debug("\t read tbl:$tbl field:$field type:$fieldtype hm:[".serialize($hm)."]");
            $prtFieldType = 'string';
        }
        else if($field == $myId || $isTimeField|| inString('int', $fieldtype)
                || inString('decimal', $fieldtype) || inString('float', $fieldtype)
                || inString('double', $fieldtype)){

            $imax = 1;
            $imin = 0;
            $hm = $this->execBy("select max($field) as imax, min($field) as imin from $tbl "
                        ." where 1=1", null, 
                    $withCache=array('key'=>"read-pickup-$tbl-$field")); 
            if($hm[0]){
                $hm = $hm[1][0];
                if($isTimeField){ $imax = strtotime($hm['imax']); }else{ $imax = $hm['imax']; }
                if($isTimeField){ $imin = strtotime($hm['imin']); }else{ $imin = $hm['imin'];  }
            }
            $istep = ($imax - $imin) / self::PICK_TOP_N;
            $valueUniq = array();
            for($i=$imin; $i<$imax; $i+=$istep){
                if($imin > 1 || $field == $myId || inString('int', $fieldtype)){
                    $val = ceil($i);
                    if($isTimeField){ $val = date("Y-m-d", $val); }
                    if(isset($valueUniq[$val])){
                        # @todo 
                    }
                    else{
                        $options[] = array($field=>$val);
                        $valueUniq[$val] = 1;
                    }
                }
                else{
                    $val = $i;
                    if($isTimeField){ $val = date("Y-m-d", $val); }
                    else{ $val = sprintf("%0.1f", $val); }
                    $options[] = array($field=>$val);
                }
            }
            #debug("\t read tbl:$tbl field:$field type:$fieldtype imax:$imax imin:$imin istep:$istep hm:[".serialize($options)."]");
            $prtFieldType = 'number';
        }
        else{
            debug("unsupported tbl:$tbl field:$field fieldtype:$fieldtype. 1809191556.");
        }
        $options = array($options, $prtFieldType);
        return $options;
    }

    # private methods
    #
    private function _getFieldDefineLength($field, $fieldtype){
        $len = 0;
        if(preg_match("/char\(([0-9]+)\)/", $fieldtype, $matchArr)){
            $len = $matchArr[1];
            #debug("found char f:$field length:$len with fieldtype:[$fieldtype]\n");
        }
        return $len;
    }

    #
    private function _sayHi(){
        $rtn = '';

        return $rtn;
    }

}

?>
