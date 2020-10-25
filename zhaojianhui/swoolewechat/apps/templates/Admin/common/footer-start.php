<!-- Mainly scripts -->
<script src="<?php echo $siteconf['cdnurl']?>/jquery/dist/jquery.min.js"></script>
<script src="<?php echo $siteconf['cdnurl']?>/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<?php if (strpos($this->currentUrl, '/admin/login') === false){?>
<!-- Custom and plugin javascript -->
<script src="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/inspinia.js"></script>
<script src="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/pace/pace.min.js"></script>
<?php }?>
<!-- Toastr script -->
<script src="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/toastr/toastr.min.js"></script>
<script>
    toastr.options = {
        "positionClass": "toast-top-center",
    };
    function showToastr(data) {
        isReload = arguments[1] ? arguments[1] : false;
        switch (data.status){
            case 'success':
                toastr.success(data.message, data.title);
                break;
            case 'error':
                toastr.error(data.message, data.title);
                break;
            case 'info':
                toastr.info(data.message, data.title);
                break;
            default:
                toastr.warning(data.message, data.title);
                break;
        }
        if (isReload && data.status == 'success' && data.redirectUrl){
            setTimeout(function(){
                window.location.href = data.redirectUrl;
            }, 1000);
        }
    }
</script>
<!-- Jquery Validate -->
<script src="<?php echo $siteconf['cdnurl']?>/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="<?php echo $siteconf['cdnurl']?>/jquery-validation/src/localization/messages_zh.js"></script>
<!-- Jquery Form -->
<script src="<?php echo $siteconf['cdnurl']?>/jquery-form/dist/jquery.form.min.js"></script>
<!-- Jquery-confirm -->
<script src="<?php echo $siteconf['cdnurl']?>/jquery-confirm2/dist/jquery-confirm.min.js"></script>
