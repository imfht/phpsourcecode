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
        $.getJSON('<?php echo __ADMINCP__;?>=apps_store&do=check_update&t=<?php echo time(); ?>',
            function(json){
                if(json.code=="0"){
                    return;
                }
                $("#store_update").removeClass('hide').text(json.count)
            }
        );
    <?php } ?>
});
</script>
