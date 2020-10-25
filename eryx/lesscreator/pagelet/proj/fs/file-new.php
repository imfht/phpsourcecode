<?php

$path = preg_replace("/\/+/", "/", '/'.$this->req->path.'/');
$type = $this->req->type;
$file = $this->req->file;

$readonly = "";
if (isset($this->req->readonly) && $this->req->readonly == 1) {
    $readonly = "readonly='readonly'";
}
?>


<form id="egj3zj" action="/lesscreator/proj/fs/file-new" method="post">

  <div class="input-prepend" style="margin-left:2px">
    <span class="add-on">
        <img src="/lesscreator/static/img/folder_add.png" class="h5c_icon" />
        <?php echo $path?>
    </span>
    <input type="text" name="name" value="<?php echo $file?>" class="span2 hutjzx" <?php echo $readonly?>/>
    <input type="hidden" name="path" value="<?php echo $path?>" />
    <input type="hidden" name="type" value="<?php echo $type?>" />
  </div>

</form>

<script type="text/javascript">

lessModalButtonAdd("k8wf2g", "<?php echo $this->T('Create')?>", "_fs_file_new()", "btn-inverse pull-left");
lessModalButtonAdd("nnjyyb", "<?php echo $this->T('Cancel')?>", "lessModalClose()", "pull-left");

//$(".hutjzx").focus();

$("#egj3zj").submit(function(event) {

    event.preventDefault();

    _fs_file_new();
});

function _fs_file_new()
{    
    var path = lessSession.Get("ProjPath");
    path += "/"+ $("#egj3zj").find("input[name=path]").val();
    path += "/"+ $("#egj3zj").find("input[name=name]").val();

    var req = {
        "access_token" : lessCookie.Get("access_token"),
        "data" : {
            "type" : $("#egj3zj").find("input[name=type]").val(),
            "path" : path,
        }
    }

    $.ajax({
        type    : "POST",
        url     : "/lesscreator/api?func=fs-file-new",
        //dataType: 'json',
        data    : JSON.stringify(req),
        timeout : 3000,
        success : function(rsp) {

            var obj = JSON.parse(rsp);
            if (obj.status == 200) {
                hdev_header_alert('success', "<?php echo $this->T('Successfully Done')?>");
            } else {
                hdev_header_alert('error', obj.message);
            }

            if (typeof _plugin_yaf_cvlist == 'function') {
                _plugin_yaf_cvlist();
            }
            
            _fs_file_new_callback($("#egj3zj").find("input[name=path]").val());
            lessModalClose();
        },
        error   : function(xhr, textStatus, error) {
            hdev_header_alert('error', textStatus+' '+xhr.responseText);
        }
    });
}

</script>

