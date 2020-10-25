<?php

# invoked by trigger settings in xml
# e.g. <trigger>ALL::extraact::extra/sendmail.php::Offer入口调整修改id=THIS_ID</trigger>
if(true){
    $args = explode('::', $args);
    #
    sendMail($to='abc@example.com,efg@example.com,'.$user->getEmail(), 
                $title=$args[0], 
                $bodyMsg=$args[0].'@'.date('Y-m-d-H:i:s', time())
                    .' by '.$user->getEmail().'. sent from '.$_CONFIG['agentname'].'.'
                    .' Url: '.$thisUrl, 
                $from='', 
                $vialocal=1);
    
    debug($bodyMsg);
}

?>
