<?php
/* 
 * write cache for db
 * use temp tables for speed up writing to db
 * init by Xenxin@Pbtt
 * Thu, 24 Nov 2016 19:41:09 +0800
 */

if(!defined('__ROOT__')){
    define('__ROOT__', dirname(dirname(__FILE__)));
}
require_once(__ROOT__.'/inc/webapp.class.php');
require_once(__ROOT__.'/mod/logx.class.php');

class TableX extends WebApp {
    
    //- variables
    const Temp_Tag = 'temp';
    var $temptbl = '';
    var $pritbl = '';
    
    //- construct
    public function __construct($args){
        # self works
        
        parent::__construct($args);
        $this->Logx = new LogX($args);
    }
    
    //- destruct
    public function __destruct(){
        # @todo
    }
    
    //- methods
    //- create tmp write tbl for update
    //- Thu, 24 Nov 2016 09:25:19 +0800
    public function createTempTblFromPrimary($primaryTbl, $args=null){
        $rtn = true;
        $tblsuffix = '';
        $this->pritbl = $primaryTbl;
        if(isset($args['suffix'])){
            $tblsuffix = $args['suffix'];
            if(true || $tblsuffix == ''){
                $tblsuffix .= '_'.rand(100, 999999);
            }
        }
        $in_memory = false;
        if(isset($args['in_memory']) && $args['in_memory']){
            $in_memory = true;
        }
        $tmptbl = $this->pritbl.'_'.self::Temp_Tag;
        if($tblsuffix != ''){
            $tmptbl .= '_'.$tblsuffix;
        }
        $this->temptbl = $tmptbl;
        $hm = $this->execBy("drop table if exists $tmptbl", null);
        if($hm[0]){
            if($in_memory){
                $hm = $this->execBy("create temporary table $tmptbl like ".$this->pritbl, null);
            }
            else{
                $hm = $this->execBy("create table $tmptbl like ".$this->pritbl, null);
            }
            if($hm[0]){
                $hm = $this->execBy("lock tables $tmptbl write", null);
                if($hm[0]){
                    debug($msg=__FILE__." create tmptbl:[$tmptbl] succ and lock for writing.");
                    $this->Logx->say($msg);
                }
                else{
                    debug(__FILE__.": lock tmptbl:[$tmptbl] failed.");
                    $rtn = false;
                }
            }
            else{
                debug(__FILE__.": create tmptbl:[$tmptbl] failed.");
                $rtn = false;
            }
        }
        else{
            debug(__FILE__.": failed to drop old tmptbl:[$tmptbl].");
            $rtn = false;
        }
        return $rtn;
    }
    
    //- drop temp tbl
    //- Thu, 1 Dec 2016 09:41:16 +0800
    public function dropTempTbl($tmptbl=null){
        $rtn = true;
        if(!isset($tmptbl)){
            $tmptbl = $this->temptbl;
        }
        $dropHm = $this->execBy("drop table if exists $tmptbl", null);
        if($dropHm[0]){
            debug(__FILE__.": drop tmptbl:$tmptbl succ.");
        }
        else{
            $rtn = false;
            debug(__FILE__.": drop tmptbl:$tmptbl failed. rtn:[".$this->toString($dropHm)."]");
        }
        return $rtn;
    }
    
    //- flush temp data to primary table
    //- Thu, 24 Nov 2016 09:43:44 +0800
    public function flushToPrimaryTbl($fields, $where, $args=null){
        $rtn = array();
        $tmptbl = $this->temptbl;
        $pritbl = $this->pritbl;
        if(!is_array($fields)){
            $fields = explode(',', $fields);
        }
        $sql = "update ignore $pritbl as a, $tmptbl as b set ";
        foreach ($fields as $k=>$v){
            $v = trim($v);
            $sql .= "a.$v=b.$v, ";
        }
        $sql = substr($sql, 0, strlen($sql)-2).' '; # remove ', ';
        if(count($where) > 0){
            $sql .= "where 1=1 ";
            foreach($where as $k=>$v){
                $v = trim($v);
                $sql .= " and a.$v=b.$v";
            }
        }
        else{
            $rtn = array(false, array('errno'=>1611241219, 'errmsg'=>'no where for update.'));
            debug(__FILE__.": flush to primary failed due to empty where:[$where]");
            return $rtn;
        }
        $hm = $this->execBy('unlock tables', null);
        if($hm[0]){
            debug(__FILE__.": unlock tables succ.");
        }
        else{
            debug(__FILE__.": unlock tables failed.");
        }
        $hm = $this->execBy("select count(*) as totalcount from ".$this->temptbl, null);
        if($hm[0]){
            debug($msg=__FILE__.": temptbl:[".$this->temptbl."] count:[".$hm[1][0]['totalcount']."]");
            $this->Logx->say($msg);
        }
        $hm = $this->execBy($sql, null);
        sleep(1); # for what?
        if($hm[0]){
            debug($msg=__FILE__.": flush succ to primary sql:[$sql] rtn:[".$this->toString($hm)."].");
            $this->Logx->say($msg);
        }
        else{
            debug(__FILE__.": failed to flush to primary sql:[$sql].");
        }
        $this->dropTempTbl($tmptbl);
        $rtn = $hm;
        return $rtn;
    }
    
    //- write a single record to temp tbl
    //-- parameters: $fields=array, $values=hashref, with pair
    //- Thu, 24 Nov 2016 10:03:57 +0800
    public function insertIntoTempTbl($fields, $values){
        $rtn = array();
        $tmptbl = $this->temptbl;
        if($tmptbl != ''){
            #$this->setTbl('');
            #$this->setTbl($tmptbl);
            $this->set($values);
            if(is_array($fields)){
                $fields = implode(',', $fields);
            }
            if($this->getId() != ''){
                $this->setId(''); # prevent GWA2 from generating an UPDATE, see insertIntoTempTblWithId
            }
            $hm = $this->setBy($fields, null); # for insert?
            if($hm[0]){
                debug($msg=__FILE__.": insert into tmptbl:[$tmptbl] succ.");
                #$this->Logx->say($msg);
                $ret = $hm[1]['affectedrows'] = $hm[1]['insertid'];
            }
            else{
                debug(__FILE__.": insert into tmptbl:[$tmptbl] failed. 1611241014.");
            }
            $rtn = $hm;
        }
        else{
            $rtn = array(false, array('errno'=>1611241157,'errmsg'=>'empty table name.'));
            debug(__FILE__.": insert into tmptbl:[$tmptbl] failed.");
        }
        return $rtn;
    }
    
    //-
    //- default WebApp::setBy cannot insert with primary field 'id'
    public function insertIntoTempTblWithId($fields, $values){
        $rtn = array();
        # self orgnize sql with id filed with insert
        # to overwrite WebApp::setBy @todo
        $tmptbl = $this->temptbl;
        if($tmptbl != ''){
            $this->set($values);
            if(!is_array($fields)){
                $fields = explode(',', $fields);
            }
            $sql = "insert into $tmptbl set ";
            foreach ($fields as $k=>$v){
                $sql .= "$v=?,";
            }
            $sql = substr($sql, 0, strlen($sql)-1);
            $hm = $this->execBy($sql, null);
            if($hm[0]){
                debug($msg=__FILE__.": insertwithid into tmptbl:$tmptbl succ.");
            }
            else{
                debug($msg=__FILE__.": insertwithid into tmptbl:$tmptbl failed. sql:[$sql] rtn:["
                        .$this->toString($hm)."] fields:[".$this->toString($fields)."] values:[".$this->toString($values)."]");
            }
            $rtn = $hm;
        }
        return $rtn;
    }
    
    
    //- get field values list from temp tbl
    //- Thu, 1 Dec 2016 09:41:50 +0800
    public function getList($field, $condition=null, $tmptbl=null){
        $rtn = array();
        if(!isset($tmptbl)){
            $tmptbl = $this->temptbl;
        }
        $oldps = $this->get('pagesize');
        $this->set('pagesize', 0); # unlimited
        debug(__FILE__.": call getList: fields:[$field]");
        $hm = $this->getBy($field, $condition);
        if($hm[0]){
            $hm = $hm[1];
            $rtn = $hm;
        }
        debug($msg=__FILE__.": getlist from tmptbl:$tmptbl, count:[".count($rtn)."]");
        $this->Logx->say($msg);
        debug($rtn);
        $this->set('pagesize', $oldps);
        return $rtn;
    }
    
    //- override setBy
    //- Thu, 24 Nov 2016 18:01:19 +0800
    public function setBy($fields, $conditions){
        $this->setTbl($this->temptbl);
        $hm = parent::setBy($fields, $conditions);
        return $hm;
    }
    
    //- override getBy
    //- Tue, 29 Nov 2016 15:42:16 +0800
    public function getBy($fields, $conditions){
        $this->setTbl($this->temptbl);
        debug(__FILE__.": call self::getBy... fields:[$fields]");
        $hm = parent::getBy($fields, $conditions);
        return $hm;
    }
        
    //- inner facility
    
}
?>
