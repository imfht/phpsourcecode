<?php

$path = "/".trim($this->req->path, "/");
?>

<form id="ok8lnk" action="#" method="post">
  <div><?php echo $this->T('The target of Upload directory')?></div>
  <div class="input-prepend" style="margin-left: 2px;">
    <span class="add-on"><img src="/lesscreator/static/img/house.png" align="absmiddle" /></span>
    <input style="width:400px;" type="text" id="ok8lnk-path" value="<?php echo $path?>">
    <button class="btn hide" type="button" onclick="_fs_upl_chgdir()"><?php echo $this->T('Change directory')?></button>
  </div>

  <!-- <div>
    <img src="/lesscreator/static/img/house.png" align="absmiddle" />
    <span class="path"><?php echo $path?></span> /
    <input id="attachment" name="attachment" size="40" type="file" />
  </div> -->
</form>

<style type="text/css">
.h319l4 {
    margin: 10px 0;
    display: inline-block;
    height: 80px;
    width: 450px;
    color: #f30;
    padding: 10px; 
    border: 4px dashed #bce8f1;
    border-radius: 20px;
}
</style>

<div id="h319l4" class="h319l4">
    <?php echo $this->T('Drag and Drop your files or folders to here')?>
</div>
<div id="h319l4-status" class="alert alert-info hide" style="width:430px;">

</div>

<script type="text/javascript">

var _fs_upl_cnt = document.getElementById('h319l4');

function _fs_upl_traverse_tree(item, path)
{
    path = path || "";
  
    if (item.isFile) {
    
        // Get file
        item.file(function(file) {
            
            //console.log("File:", path + file.name);
            if (file.size > 10 * 1024 * 1024) {
                $('#h319l4-status').show().append("<div>"+ path +" Failed: File is too large to upload</div>");
                return;
            }

            _fs_file_upl_do(file, path + file.name);
        });

    } else if (item.isDirectory) {
        // Get folder contents
        var dirReader = item.createReader();
        dirReader.readEntries(function(entries) {
            for (var i = 0; i < entries.length; i++) {
                _fs_upl_traverse_tree(entries[i], path + item.name + "/");
            }
        });
    }
}

function _fs_upl_handler(evt)
{            
    evt.stopPropagation();
    evt.preventDefault();

    var items = evt.dataTransfer.items;
    for (var i=0; i<items.length; i++) {
        // webkitGetAsEntry is where the magic happens
        var item = items[i].webkitGetAsEntry();
        if (item) {
            _fs_upl_traverse_tree(item);
        }
    }

    var subdir = $("#ok8lnk-path").val();
    _fs_file_new_callback(subdir);
}

function handleDragEnter(evt)
{
    this.setAttribute('style', 'border-style:dashed;');
}

function handleDragLeave(evt)
{
    this.setAttribute('style', '');
}

function handleDragOver(evt)
{
    evt.stopPropagation();
    evt.preventDefault();
}

_fs_upl_cnt.addEventListener('dragenter', handleDragEnter, false);
_fs_upl_cnt.addEventListener('dragover', handleDragOver, false);
_fs_upl_cnt.addEventListener('drop', _fs_upl_handler, false);
_fs_upl_cnt.addEventListener('dragleave', handleDragLeave, false);


////////////////////////////////////////////////////////////////////////

//lessModalButtonAdd("zrkyom", "<?php echo $this->T('Upload')?>", "_fs_file_upl()", "btn-inverse pull-left");
lessModalButtonAdd("mqaayo", "<?php echo $this->T('Close')?>", "lessModalClose()", "pull-left btn-inverse");


var path = '<?php echo $path?>';


$("#ok8lnk").submit(function(event) {

    event.preventDefault(); 

    _fs_file_upl();
});

function _fs_file_upl_do(file, path)
{
    var reader = new FileReader();
    
    reader.onload = (function(file) {  
        
        return function(e) {
            
            if (e.target.readyState != FileReader.DONE) {
                return;
            }

            var subdir = $("#ok8lnk-path").val();
            //console.log($("#ok8lnk-path").val());

            var req = {
                "access_token" : lessCookie.Get("access_token"),
                "data" : {
                    "projid": lessSession.Get("projid"),
                    "path" : lessSession.Get("ProjPath") +"/"+ subdir +"/"+ path,
                    "size" : file.size,
                    "body" : e.target.result,
                }
            }
            // console.log(lessSession.Get("ProjPath") +"/"+ path);

            $.ajax({
                type    : "POST",
                url     : "/lesscreator/api?func=fs-file-upl",
                data    : JSON.stringify(req),
                timeout : 60000,
                success : function(rsp) {

                    var obj = JSON.parse(rsp);
                    if (obj.status == 200) {
                        $('#h319l4-status').show().append("<div>"+ path +" OK</div>");
                        //hdev_header_alert('success', "<?php echo $this->T('Successfully Done')?>");
                    } else {

                        $('#h319l4-status').show().append("<div>"+ path +" Failed 1</div>");
                        //hdev_header_alert('error', obj.message);
                    }

                    //_fs_file_new_callback(path);
                    
                    //lessModalClose();
                },
                error   : function(xhr, textStatus, error) {
                    //hdev_header_alert('error', textStatus+' '+xhr.responseText);
                    $('#h319l4-status').show().append("<div>"+ path +" Failed</div>");
                }
            });    

        };  
    })(file); 
    
    reader.readAsDataURL(file);
}

function _fs_file_upl()
{
    var files = document.getElementById('attachment').files;
    if (!files.length) {
        alert('<?php echo $this->T('Please select a file')?>');
        return;
    }

    for (var i = 0, file; file = files[i]; ++i) {
        
        if (file.size > 2 * 1024 * 1024) {
            hdev_header_alert('error', "<?php echo $this->T('The file is too large to upload')?>");
            return;
        }
                
        var reader = new FileReader();
        reader.onload = (function(file) {  
            return function(e) {
                if (e.target.readyState != FileReader.DONE) {
                    return;
                }

                var req = {
                    "access_token" : lessCookie.Get("access_token"),
                    "data" : {
                        "path" : lessSession.Get("ProjPath") +"/"+ path +"/"+ file.name,
                        "size" : file.size,
                        "body" : e.target.result,
                    }
                }

                $.ajax({
                    type    : "POST",
                    url     : "/lesscreator/api?func=fs-file-upl",
                    data    : JSON.stringify(req),
                    timeout : 3000,
                    success : function(rsp) {

                        var obj = JSON.parse(rsp);
                        if (obj.status == 200) {
                            hdev_header_alert('success', "<?php echo $this->T('Successfully Done')?>");
                        } else {
                            hdev_header_alert('error', obj.message);
                        }

                        _fs_file_new_callback(path);
                        
                        lessModalClose();
                    },
                    error   : function(xhr, textStatus, error) {
                        hdev_header_alert('error', textStatus+' '+xhr.responseText);
                    }
                });    

            };  
        })(file); 
        
        reader.readAsDataURL(file);
    }
}

</script>
