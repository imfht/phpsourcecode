
<ul class="nav nav-tabs" style="margin: 5px 0;">
  <li id="_nav_recent" class="active">
    <a href="javascript:_proj_open_recent()"><?php echo $this->T('Recent Projects')?></a>
  </li>
  <li id="_nav_fs">
    <a href="javascript:_proj_fs('', 0)"><?php echo $this->T('From Directory')?></a>
  </li>
</ul>

<table id="_proj_open_recent" width="100%" class="table table-condensed">

<?php

$basedir = $this->req->basedir;

$pjc = $basedir .'/conf/lesscreator/projlist.json';


$pjs = lesscreator_fs::FsFileGet($pjc);
$pjs = json_decode($pjs->data->body, true);
if (!is_array($pjs)) {
    $pjs = array();
}

foreach ($pjs as $projid => $val) {

    $noinfo = "";

    $rs = lesscreator_fs::FsFileGet($val['path']."/lcproject.json");
    if ($rs->status != 200) {
        $noinfo = '<font color="red">'.$this->T('This project no longer exists').'</font>';
    }
?>
<tr id="_proj_<?php echo $projid?>">
  <td valign="middle" width="18">
    <img src="/lesscreator/static/img/app-t3-16.png" align="absmiddle" />
  </td>
  <td>
    <strong><a href="javascript:_proj_open_recent_open('<?=$val['path']?>')"><?=$val['name']?></a></strong>
    <font color="gray">( <?=$val['path']?> ) <?=$noinfo?></font>
  </td>
  <td align="right">
    <button type="button" class="close" title="Clean out" onclick="_proj_open_recent_del('<?php echo $projid?>')">&times;</button>
  </td>
</tr>
<?php
}
?>
</table>

<script type="text/javascript">
if (lessModalPrevId() != null) {
    lessModalButtonAdd("jwyztd", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");
}

function _proj_open_recent()
{
    $("#_nav_recent").addClass("active");
    $("#_nav_fs").removeClass("active");
    
    $('#_proj_open_fs').hide();
    $('#_proj_open_recent').show();

    //lessModalButtonCleanAll();
    lessModalButtonAdd("p5ke7m", "<?php echo $this->T('Close')?>", "lessModalClose()", "");
}

function _proj_open_recent_open(path)
{
    h5cProjectOpen(path);
    lessModalClose();
}

function _proj_open_recent_del(projid)
{
    $.ajax({
        type: "POST",
        url: '/lesscreator/proj/open-recent?basedir='+ lessSession.Get("basedir"),
        data: {'func':'del', 'projid':projid},
        success: function(data) {
            if (data == "OK") {
                $("#_proj_"+ projid).remove();
            } else {
                alert(data);
            }
        }
    });
}

</script>
