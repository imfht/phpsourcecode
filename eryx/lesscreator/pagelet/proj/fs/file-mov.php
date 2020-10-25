<?php

$path = preg_replace("/\/+/", "/", rtrim($this->req->path, '/'));

?>


<form id="c1qtiv" action="/lesscreator/proj/fs/file-mov" method="post">

  <div class="input-prepend" style="margin-left:2px">
    <span class="add-on">
        <img src="/lesscreator/static/img/folder_edit.png" class="h5c_icon" />
    </span>
    <input type="text" name="pathnew" value="<?php echo $path?>" class="k2tcrh" style="width:500px;" />
    <input type="hidden" name="pathpre" value="<?php echo $path?>" />
  </div>

</form>

<script type="text/javascript">

lessModalButtonAdd("fjbcw8", "<?php echo $this->T('Rename')?>", "_fs_file_mov()", "btn-inverse pull-left");
lessModalButtonAdd("y9e9be", "<?php echo $this->T('Cancel')?>", "lessModalClose()", "pull-left");

$(".k2tcrh").focus();

$("#c1qtiv").submit(function(event) {

    event.preventDefault();

    _fs_file_mov();
});

function _fs_file_mov()
{
    var pathpre = $("#c1qtiv").find("input[name=pathpre]").val();
    var pathnew = $("#c1qtiv").find("input[name=pathnew]").val();
    if (pathpre == pathnew) {
        hdev_header_alert('success', "<?php echo $this->T('Successfully Done')?>");
        return;
    }

    var req = {
        "access_token" : lessCookie.Get("access_token"),
        "data" : {
            "pathnew" : lessSession.Get("ProjPath") +"/"+ pathnew,
            "pathpre" : lessSession.Get("ProjPath") +"/"+ pathpre,
        }
    }
   
    //var refreshpre = pathpre.substring(0, pathpre.lastIndexOf('/'));
    var refreshnew = pathnew.substring(0, pathnew.lastIndexOf('/'));    

    var pathPreUrid = lessCryptoMd5(pathpre);

    lcEditor.IsSaved(pathPreUrid, function(ret) {

        if (!ret) {
            lessModalOpen("/lesscreator/editor/changes2save?urid="+ pathPreUrid, 
                1, 500, 200, '<?php echo $this->T('Save changes before rename')?>', null);
            return;
        }

        $.ajax({
            type    : "POST",
            url     : "/lesscreator/api?func=fs-file-mov",
            data    : JSON.stringify(req),
            timeout : 3000,
            success : function(rsp) {
    
                var obj = JSON.parse(rsp);
    
                if (obj.status == 200) {
    
                    _lcTabCloseClean(pathPreUrid);
    
                    hdev_header_alert('success', "<?php echo $this->T('Successfully Done')?>");
    
                    var path = pathpre.replace(/(^\/*)|(\/*$)/g, "");
                    path = path.replace(/(\/+)/g, "/");
                    var p = lessCryptoMd5(path);
                    $("#ptp"+p).remove();
                    $("#pt"+p).remove();
                    
                    path = "";
                    var ps = refreshnew.replace(/(^\/*)|(\/*$)/g, "").split('/');
                    for (var i in ps) {
    
                        path += "/"+ ps[i];
                        p = lessCryptoMd5(path);
                        if (!$("#pt"+p).html() || $("#pt"+p).html().length < 1) {
                            //console.log("load new "+ path);
                            _fs_tree_dir(path, 1);
                        }
                    }
    
                    _fs_tree_dir(refreshnew, 1);
    
                } else {
                    hdev_header_alert('error', obj.message);
                }
    
                lessModalClose();
            },
            error   : function(xhr, textStatus, error) {
                hdev_header_alert('error', textStatus+' '+xhr.responseText);
            }
        });
    });    
}

</script>

