<?php

$basedir = $this->req->basedir;

?>

<ul class="nav nav-tabs" style="margin: 5px 0;">
  <li>
    <a href="javascript:_proj_open_recent()"><?php echo $this->T('Recent Projects')?></a>
  </li>
  <li class="active">
    <a href="#"><?php echo $this->T('From Directory')?></a>
  </li>
</ul>

<style>
a._proj_open_fs_href {
    padding: 3px; width: 100%;
    text-decoration: none;
}
a._proj_open_fs_href:hover {
    background-color: #999;
    color: #fff;
}
a._proj_open_fs_href_click {
    background-color: #0088cc;
    color: #fff;
}
#_proj_fs_body {
    padding: 5px; border:1px solid #ccc;
}
</style>
<div id="_proj_open_fs"></div>

<script>

if (lessModalPrevId() != null) {
    lessModalButtonAdd("vzypd5", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");
}

var _path = '<?php echo "{$basedir}/app";?>';
var _path_click = null;



function _proj_open_recent()
{
    var url = "/lesscreator/proj/open-recent?basedir="+ lessSession.Get("basedir");

    if (lessModalPrevId() == lessCryptoMd5("modal"+url)) {
        lessModalPrev();
    } else {
        lessModalNext(url, "<?php echo $this->T('Open Project')?>", null);
    }
}

function _proj_open_fs_open()
{
    h5cProjectOpen(_path +'/'+ _path_click);
    lessModalClose();
}

function _proj_open_fs_inlet(dir, force)
{
    if ($("#_proj_open_fs").is(':empty') || force == 1) {
        
        var url = "/lesscreator/proj/open-fs-inlet";
        url += "?path="+ dir +"&_="+ Math.random();
        
        $.get(url, function(rsp) {
            $('#_proj_open_fs').empty().html(rsp).show();
            lessModalButtonClean("phtswc");
        });

    }
}

<?php
echo "_proj_open_fs_inlet(_path, 1);";
?>
</script>
