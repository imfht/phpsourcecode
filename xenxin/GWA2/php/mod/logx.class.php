<?php
/*
 * app's log handler
 * init by Xenxin@Pbtt
 * Thu, 24 Nov 2016 19:53:45 +0800
 */

if(!defined('__ROOT__')){
    define('__ROOT__', dirname(dirname(__FILE__)));
}
require_once(__ROOT__.'/inc/webapp.class.php');

class LogX extends WebApp {
    
    //- variables
    var $logf = '';
    const Max_Message_Length = 1024;
    
    //- construct
    public function __construct($args){
        # self works
        if(isset($args['logfile'])){
            $this->logf = $args['logfile'];
        }
        else{
            $this->logf = GConf::get('logfile');
        }
        
        parent::__construct($args);
    }
    
    //- destruct
    public function __destruct(){
        # @todo
    }
    
    //- methods
    public function say($msg, $args=null){
        $rtn = true;
        $logf = $this->logf;
        if($msg == null || $msg == ''){
            return $rtn;
        }
        else if(strlen($msg) > self::Max_Message_Length){
            $msg = substr($msg, 0, self::Max_Message_Length);
        }
        $mytime = time();
        $logf .= date("Ymd", $mytime).'.log';
        $hm = $this->setBy('file:', array('target'=>$logf, 'reuse'=>true,
                'isappend'=>true,
                'content'=>date("Y-m-d-H:i:sO", $mytime).' '.$msg."\n",
            ));
        return $rtn;
    }
    
    //- inner facility

}
?>