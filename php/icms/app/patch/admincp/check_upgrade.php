<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
defined('iPHP') OR exit('What are you doing?');
?>
<script type="text/javascript">
$(function(){
    <?php if(members::is_superadmin()){?>
        window.setTimeout(function(){
            $.getJSON('<?php echo __ADMINCP__;?>=patch&do=check_upgrade&t=<?php echo time(); ?>',
                function(json){
                    if(json.code=="0"){
                        return;
                    }
                    iCMS.dialog({
                        content: json.msg,
                        okValue: '马上升级',
                        ok: function () { window.location.href=json.url;},
                        cancelValue: '以后在说',
                        cancel: function () {return true;}
                    });
                }
            );
        },1000);
    <?php } ?>
});
</script>
