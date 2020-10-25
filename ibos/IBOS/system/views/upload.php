<!-- 上传附件管理窗口 -->
<link rel="stylesheet" href="<?php echo STATICURL; ?>/js/app/dialogUpload/ibos.dialogUpload.css">
<div style="width: 600px;">
    <div class="fill-sn bglb bdbs">
        <div class="webuploader-wrap dib">
            <div id="datt_upload_placeholder" class="webuploader-container_nobg" style="position: absolute; left: 0; top: 0; right: 0; bottom: 0;"></div>
            <button type="button" class="btn btn-primary">添加文件</button>
        </div>
        <button type="button" class="btn pull-right" id="datt_clear">清空列表</button>
    </div>
    <div>
        <ul class="datt" id="datt_list"></ul>
    </div>
</div>
<script src="<?php echo STATICURL; ?>/js/app/dialogUpload/ibos.dialogUpload.js"></script>
<script>
    (function () {
        $("#datt_clear").on("click", function () {
            $("#datt_list li").filter(".complete, .error").remove();
        });
    })();
</script>
